<?php
// Enable full error reporting for debugging.
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../inc/db_config.php';
require_once __DIR__ . '/../inc/essentials.php';

/**
 * Encodes an array to JSON, sends it to the client, and terminates the script.
 * Ensures the correct Content-Type header is set.
 * @param array $arr The array to encode and output.
 */
function jsonOut($arr){ 
    if (!headers_sent()) {
        header('Content-Type: application/json');
    }
    echo json_encode($arr); 
    exit; 
}

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        jsonOut(['success' => false, 'message' => 'Invalid request method.']);
    }

    $last_name      = clean($_POST['last_name']      ?? '');
    $first_name     = clean($_POST['first_name']     ?? '');
    $middle_initial = clean($_POST['middle_initial'] ?? '');
    $date_of_birth  = $_POST['date_of_birth']        ?? '';
    $address        = clean($_POST['address']        ?? '');
    $email          = clean($_POST['email']          ?? '');
    $contact_number = clean($_POST['contact_number'] ?? '');
    $other_guests   = $_POST['other_guests']         ?? '[]';
    $arrival_date   = $_POST['arrival_date']         ?? '';
    $departure_date = $_POST['departure_date']       ?? '';
    $checkin_time   = $_POST['checkin_time']         ?? '';
    $checkout_time  = $_POST['checkout_time']        ?? '';
    $adults_count   = (int)($_POST['adults_count']   ?? 1);
    $kids_count     = (int)($_POST['kids_count']     ?? 0);
    $room_id        = (int)($_POST['room_id']        ?? 0);
    $remarks        = clean($_POST['remarks']        ?? '');
    $registered_by  = clean($_POST['registered_by'] ?? '');
    $data_privacy   = (int)($_POST['data_privacy_consent'] ?? 0);
    $digital_signature = $_POST['digital_signature'] ?? '';

    $guest_name = trim($first_name . ($middle_initial ? ' ' . $middle_initial . '.' : '') . ' ' . $last_name);
    $num_guests = max(1, $adults_count + $kids_count);

    $missing = [];
    if (!$last_name)      $missing[] = 'Last Name';
    if (!$first_name)     $missing[] = 'First Name';
    if (!$date_of_birth)  $missing[] = 'Date of Birth';
    if (!$address)        $missing[] = 'Address';
    if (!$email)          $missing[] = 'Email';
    if (!$contact_number) $missing[] = 'Contact Number';
    if (!$arrival_date)   $missing[] = 'Arrival Date';
    if (!$departure_date) $missing[] = 'Departure Date';
    if (!$checkin_time)   $missing[] = 'Check-in Time';
    if (!$checkout_time)  $missing[] = 'Check-out Time';
    if (!$room_id)        $missing[] = 'Room Selection';
    if (!$registered_by)  $missing[] = 'Name of Person Registering';

    if ($missing) {
        jsonOut(['success' => false, 'message' => 'Please fill in all required fields: ' . implode(', ', $missing)]);
    }

    if (strtotime($departure_date) <= strtotime($arrival_date)) {
        jsonOut(['success' => false, 'message' => 'Departure date must be after the arrival date.']);
    }

    // Corrected availability check: finds any overlap using the correct column names.
    $chk = $conn->prepare("SELECT id FROM guest_room_reservations WHERE guest_room_id=? AND status IN ('confirmed', 'pending') AND NOT (check_out_date <= ? OR check_in_date >= ?)");
    if (!$chk) jsonOut(['success' => false, 'message' => 'Database query failed to prepare: ' . $conn->error]);
    
    $chk->bind_param("iss", $room_id, $arrival_date, $departure_date);
    $chk->execute();
    $chk->store_result();
    
    if ($chk->num_rows > 0) {
        jsonOut(['success' => false, 'message' => 'Sorry, the selected room is not available for the chosen dates.']);
    }
    $chk->close();

    // Normalize other guests JSON
    $other_guests_json = '[]';
    $decoded_other = json_decode($other_guests, true);
    if (is_array($decoded_other)) {
        $other_guests_json = json_encode($decoded_other, JSON_UNESCAPED_UNICODE);
    }

    // Pricing (basic): room price per night * nights (+ extra beds not handled here)
    $room_price_per_night = 0.00;
    $rp = $conn->prepare("SELECT price_per_night FROM guest_rooms WHERE id = ? LIMIT 1");
    if ($rp) {
        $rp->bind_param('i', $room_id);
        $rp->execute();
        $rpr = $rp->get_result()->fetch_assoc();
        if ($rpr && isset($rpr['price_per_night'])) $room_price_per_night = (float)$rpr['price_per_night'];
        $rp->close();
    }
    $nights = (int)((strtotime($departure_date) - strtotime($arrival_date)) / 86400);
    if ($nights < 1) $nights = 1;
    $subtotal = $room_price_per_night * $nights;
    $total_amount = $subtotal;

    // Booking number must exist (FK/UI expects this)
    $booking_no = 'GBK-' . date('Ymd') . '-' . str_pad((string)random_int(1, 9999), 4, '0', STR_PAD_LEFT);

    // Insert into the actual guest_room_reservations schema columns.
    $ins = $conn->prepare(
        "INSERT INTO guest_room_reservations
            (booking_no, guest_name, guest_email, guest_contact, guest_address, guest_dob,
             check_in_date, check_out_date, check_in_time, check_out_time,
             adults_count, children_count, total_guests, guest_room_id,
             room_price_per_night, subtotal, total_amount,
             other_guests, special_requests,
             terms_accepted_by, data_privacy_consent, digital_signature,
             status)
         VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,'pending')"
    );
    if (!$ins) jsonOut(['success' => false, 'message' => 'DB prepare error: ' . $conn->error]);

    $ins->bind_param(
        "ssssssssssiiiidddsssis",
        $booking_no,           // s
        $guest_name,           // s
        $email,                // s
        $contact_number,       // s
        $address,              // s
        $date_of_birth,        // s (date as string)
        $arrival_date,         // s
        $departure_date,       // s
        $checkin_time,         // s
        $checkout_time,        // s
        $adults_count,         // i
        $kids_count,           // i
        $num_guests,           // i
        $room_id,              // i
        $room_price_per_night, // d
        $subtotal,             // d
        $total_amount,         // d
        $other_guests_json,    // s
        $remarks,              // s
        $registered_by,        // s (terms_accepted_by)
        $data_privacy,         // i
        $digital_signature     // s
    );

    if ($ins->execute()) {
        jsonOut(['success' => true, 'message' => 'Guest reservation submitted successfully! You will be notified once it is confirmed.', 'booking_no' => $booking_no]);
    } else {
        // Correctly report the error from the statement, not the connection
        jsonOut(['success' => false, 'message' => 'Database error during submission: ' . $ins->error]);
    }

} catch (Throwable $e) {
    // Catch any other errors (including fatal ones) and report them as JSON
    jsonOut(['success' => false, 'message' => 'A server error occurred: ' . $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
}