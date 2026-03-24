<?php
// =============================================================================
// ajax/guest_reservation_submit.php
// Inserts a new guest room reservation into guest_room_reservations.
// Column names match the ACTUAL schema (bsu_hrs_schema__1_.sql).
// =============================================================================
ob_start();
require_once __DIR__ . '/../inc/db_config.php';
require_once __DIR__ . '/../inc/essentials.php';
ob_clean();
header('Content-Type: application/json');
error_reporting(0);

function jsonOut(array $a): void { echo json_encode($a); exit; }

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonOut(['success' => false, 'message' => 'Invalid request method']);
}

// ── Collect inputs (map form field names → DB column names) ──────────────────
// Form sends      → DB column
// last_name         guest_name (combined below)
// first_name        guest_name (combined below)
// middle_initial    (part of guest_name)
// date_of_birth   → guest_dob
// address         → guest_address
// email           → guest_email
// contact_number  → guest_contact
// room_id         → guest_room_id
// kids_count      → children_count
// checkin_time    → check_in_time
// checkout_time   → check_out_time
// arrival_date    → check_in_date
// departure_date  → check_out_date

$last_name      = clean($_POST['last_name']      ?? '');
$first_name     = clean($_POST['first_name']     ?? '');
$middle_initial = clean($_POST['middle_initial'] ?? '');
$guest_dob      =       $_POST['date_of_birth']  ?? '';
$guest_address  = clean($_POST['address']        ?? '');
$guest_email    = clean($_POST['email']          ?? '');
$guest_contact  = clean($_POST['contact_number'] ?? '');
$other_guests   =       $_POST['other_guests']   ?? '[]';
$check_in_date  =       $_POST['arrival_date']   ?? '';
$check_out_date =       $_POST['departure_date'] ?? '';
$check_in_time  =       $_POST['checkin_time']   ?? '14:00';
$check_out_time =       $_POST['checkout_time']  ?? '12:00';
$adults_count   = max(1, (int)($_POST['adults_count'] ?? 1));
$children_count = max(0, (int)($_POST['kids_count']   ?? 0));
$guest_room_id  = (int)($_POST['room_id'] ?? 0);
$special_requests = clean($_POST['remarks']       ?? '');
$data_privacy   = (int)($_POST['data_privacy_consent'] ?? 0);
$digital_sig    = clean($_POST['digital_signature']    ?? '');
// "Registered By" on the PDF must match the principal guest's name format:
//   First name + Middle initial + Last name (ALL CAPS)
$miClean   = rtrim(strtoupper(trim((string)$middle_initial)), '.');
$miDot     = $miClean !== '' ? ($miClean . '.') : '';
$terms_by  = strtoupper(trim($first_name . ' ' . $miDot . ' ' . $last_name));
$terms_accepted_at = date('Y-m-d H:i:s');

// Build guest_name in a consistent, parseable format:
//   "LAST NAME, FIRST NAME MI."
// Example: "DE GUZMAN, GEO MAR C."
$guest_name = trim(
    $last_name .
    ', ' .
    $first_name .
    ($middle_initial ? ' ' . $middle_initial . '.' : '')
);

// Auto-calculate guest counts based on Other Guests list + Principal Guest
$decoded_others = json_decode($other_guests, true) ?: [];
$named_count = 0;
foreach($decoded_others as $og) {
    if (!empty($og['name'])) $named_count++;
}

// source_adults is from line 48: $adults_count = max(1, (int)($_POST['adults_count'] ?? 1));
// source_kids is from line 49: $children_count = max(0, (int)($_POST['kids_count']   ?? 0));

$list_adults_count = 1 + $named_count; // Principal + Others

// We take the maximum of the manually entered count and the list count
$adults_count = max($adults_count, $list_adults_count);
// preserve children_count from the form
$total_guests = $adults_count + $children_count;


// ── Validate required fields ──────────────────────────────────────────────────
$missing = [];
if (!$last_name)       $missing[] = 'Last Name';
if (!$first_name)      $missing[] = 'First Name';
if (!$guest_dob)       $missing[] = 'Date of Birth';
if (!$guest_address)   $missing[] = 'Address';
if (!$guest_email)     $missing[] = 'Email';
if (!$guest_contact)   $missing[] = 'Contact Number';
if (!$check_in_date)   $missing[] = 'Arrival Date';
if (!$check_out_date)  $missing[] = 'Departure Date';
if (!$check_in_time)   $missing[] = 'Check-in Time';
if (!$check_out_time)  $missing[] = 'Check-out Time';
if (!$guest_room_id)   $missing[] = 'Room';
if ($missing) {
    jsonOut(['success' => false, 'message' => 'Please fill in: ' . implode(', ', $missing)]);
}
if (strtotime($check_out_date) <= strtotime($check_in_date)) {
    jsonOut(['success' => false, 'message' => 'Departure date must be after arrival date.']);
}

// ── Get room price ────────────────────────────────────────────────────────────
$room_row = $conn->query("SELECT price_per_night, extra_bed_price FROM guest_rooms WHERE id = $guest_room_id AND is_active = 1")->fetch_assoc();
if (!$room_row) {
    jsonOut(['success' => false, 'message' => 'Selected room not found or inactive.']);
}
$price_per_night     = (float)$room_row['price_per_night'];
$extra_bed_price     = (float)$room_row['extra_bed_price'];
$nights              = max(1, (int)((strtotime($check_out_date) - strtotime($check_in_date)) / 86400));
$extra_bed_requested = $digital_sig ? 0 : 0; // placeholder; could wire to UI later
$extra_beds_count    = 0;
$subtotal            = $price_per_night * $nights;
$extra_bed_cost      = $extra_bed_price * $extra_beds_count * $nights;
$total_amount        = $subtotal + $extra_bed_cost;

// ── Availability check (only confirmed/checked_in block the room) ─────────────
$chk = $conn->prepare("
    SELECT id FROM guest_room_reservations
    WHERE guest_room_id = ?
      AND deleted = 0
      AND status IN ('confirmed', 'checked_in')
      AND check_in_date  < ?
      AND check_out_date > ?
");
if (!$chk) jsonOut(['success' => false, 'message' => 'DB prepare error: ' . $conn->error]);
$chk->bind_param("iss", $guest_room_id, $check_out_date, $check_in_date);
$chk->execute();
$chk->store_result();
if ($chk->num_rows > 0) {
    jsonOut(['success' => false, 'message' => 'This room is not available for the selected dates.']);
}
$chk->close();

// ── Generate booking number using booking_sequences table ─────────────────────
$conn->begin_transaction();
try {
    $year  = (int)date('Y');
    $month = (int)date('m');
    $conn->query("INSERT INTO booking_sequences (type, year, month, last_number)
                  VALUES ('guest', $year, $month, 1)
                  ON DUPLICATE KEY UPDATE last_number = last_number + 1");
    $seq = $conn->query("SELECT last_number FROM booking_sequences WHERE type='guest' AND year=$year AND month=$month")->fetch_assoc()['last_number'];
    $booking_no = sprintf('GBK-%04d%02d-%04d', $year, $month, $seq);
    $conn->commit();
} catch (Exception $e) {
    $conn->rollback();
    $booking_no = 'GBK-' . date('Ymd') . '-' . str_pad(rand(1,9999), 4, '0', STR_PAD_LEFT);
}

// ── Insert into guest_room_reservations ───────────────────────────────────────
$ins = $conn->prepare("
    INSERT INTO guest_room_reservations (
        booking_no,
        guest_name, guest_email, guest_contact, guest_address, guest_dob,
        check_in_date, check_out_date, check_in_time, check_out_time,
        adults_count, children_count, total_guests,
        guest_room_id,
        extra_bed_requested, extra_beds_count,
        room_price_per_night, extra_bed_price_per_night,
        subtotal, discount_amount, total_amount,
        other_guests, digital_signature, data_privacy_consent,
        terms_accepted, terms_accepted_by, terms_accepted_at,
        special_requests, status
    ) VALUES (
        ?,
        ?, ?, ?, ?, ?,
        ?, ?, ?, ?,
        ?, ?, ?,
        ?,
        0, 0,
        ?, 0.00,
        ?, 0.00, ?,
        ?, ?, ?,
        ?, ?, ?,
        ?, 'pending'
    )
");
if (!$ins) jsonOut(['success' => false, 'message' => 'DB prepare error: ' . $conn->error]);

// Type string: 27 params
// s  booking_no
// s  guest_name
// s  guest_email
// s  guest_contact
// s  guest_address
// s  guest_dob
// s  check_in_date
// s  check_out_date
// s  check_in_time
// s  check_out_time
// i  adults_count
// i  children_count
// i  total_guests
// i  guest_room_id
// d  room_price_per_night
// d  subtotal
// d  total_amount
// s  other_guests
// s  digital_signature
// i  data_privacy_consent
// i  terms_accepted
// s  terms_accepted_by
// s  terms_accepted_at
// s  special_requests
$terms_accepted = 1;

$ins->bind_param("ssssssssssiiiidddssiisss",
    $booking_no,
    $guest_name, $guest_email, $guest_contact, $guest_address, $guest_dob,
    $check_in_date, $check_out_date, $check_in_time, $check_out_time,
    $adults_count, $children_count, $total_guests,
    $guest_room_id,
    $price_per_night,
    $subtotal, $total_amount,
    $other_guests, $digital_sig, $data_privacy,
    $terms_accepted, $terms_by, $terms_accepted_at,
    $special_requests
);

if ($ins->execute()) {
    $insert_id = $conn->insert_id;
    
    // ----- PDF generation and email (do not fail reservation on error) -----
    try {
        $res_row = $conn->query("
            SELECT grr.*, gr.name AS room_name, gr.room_type
            FROM guest_room_reservations grr
            LEFT JOIN guest_rooms gr ON grr.guest_room_id = gr.id
            WHERE grr.id = $insert_id
        ")->fetch_assoc();

        if ($res_row) {
            $base = dirname(__DIR__);
            require_once $base . '/inc/GuestRoomPDF.php';
            require_once $base . '/inc/EmailSender.php';

            $pdfGen = new GuestRoomPDF($res_row);
            $pdfContent = $pdfGen->generate();
            $pdfFilename = 'Registration-' . $booking_no . '.pdf';

            $emailConfig = require $base . '/inc/email_config.php';
            $sender = new EmailSender($emailConfig);
            
            // Build body for guest email (reusing generic or custom method)
            $subject = 'Hostel Room Registration Confirmation - ' . $booking_no;
            $customerName = $res_row['guest_name'];
            $htmlBody = "<!DOCTYPE html><html><body style='font-family: Arial, sans-serif; padding: 20px;'>
                <div style='max-width: 600px; margin: 0 auto; background: #fff; border: 1px solid #eee; border-radius: 8px; overflow: hidden;'>
                <div style='background: #b71c1c; color: #fff; padding: 20px; text-align: center;'>
                    <h1 style='margin: 0;'>BATANGAS STATE UNIVERSITY</h1>
                    <p>BatStateU Hostel Room Registration</p>
                </div>
                <div style='padding: 20px;'>
                    <p>Dear <strong>$customerName</strong>,</p>
                    <p>Your room registration has been submitted successfully. Please find your registration form attached to this email.</p>
                    <p><strong>Booking Reference:</strong> $booking_no</p>
                    <p><strong>Check-in:</strong> " . date('M d, Y', strtotime($res_row['check_in_date'])) . " at " . date('h:i A', strtotime($res_row['check_in_time'])) . "</p>
                    <p><strong>Check-out:</strong> " . date('M d, Y', strtotime($res_row['check_out_date'])) . " at " . date('h:i A', strtotime($res_row['check_out_time'])) . "</p>
                    <br>
                    <p>For any inquiries, please contact us at hostel.nasugbu@g.batstate-u.edu.ph.</p>
                </div>
                <div style='padding: 10px; background: #f9f9f9; text-align: center; font-size: 12px; color: #777;'>
                    This is an automated message from BatStateU Hostel Reservation System.
                </div>
                </div></body></html>";

            $sent = $sender->sendWithAttachment($res_row['guest_email'], $subject, $htmlBody, $pdfContent, $pdfFilename);
        }
    } catch (Throwable $e) {
        // Log error but don't stop success response
        error_log("Guest PDF/Email Error: " . $e->getMessage());
    }

    jsonOut([
        'success'    => true,
        'message'    => 'Guest reservation submitted! You will be notified once confirmed.',
        'booking_no' => $booking_no
    ]);
} else {
    jsonOut(['success' => false, 'message' => 'Database error: ' . $conn->error]);
}
