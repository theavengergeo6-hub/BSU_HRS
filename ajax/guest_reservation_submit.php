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

    $special_requests = json_encode([
        'registered_by' => $registered_by,
        'dob'           => $date_of_birth,
        'address'       => $address,
        'adults'        => $adults_count,
        'kids'          => $kids_count,
        'checkin_time'  => $checkin_time,
        'checkout_time' => $checkout_time,
        'other_guests'  => json_decode($other_guests, true) ?: [],
        'data_privacy'  => $data_privacy,
        'remarks'       => $remarks,
    ]);

    // Corrected INSERT statement with proper column names from the schema.
    $ins = $conn->prepare(
        "INSERT INTO guest_room_reservations
            (guest_name, guest_email, guest_contact, guest_room_id, check_in_date, check_out_date, total_guests,
             special_requests, status)
         VALUES (?,?,?,?,?,?,?,?,'pending')"
    );
    if (!$ins) jsonOut(['success' => false, 'message' => 'DB prepare error: ' . $conn->error]);

    $ins->bind_param("sssissis",
        $guest_name, $email, $contact_number, $room_id,
        $arrival_date, $departure_date, $num_guests,
        $special_requests
    );

    if ($ins->execute()) {
        $new_id     = $conn->insert_id;
        $booking_no = 'GBK-'.date('Ymd').'-'.str_pad($new_id, 4, '0', STR_PAD_LEFT);
        jsonOut(['success' => true, 'message' => 'Guest reservation submitted successfully! You will be notified once it is confirmed.', 'booking_no' => $booking_no]);
    } else {
        // Correctly report the error from the statement, not the connection
        jsonOut(['success' => false, 'message' => 'Database error during submission: ' . $ins->error]);
    }

} catch (Throwable $e) {
    // Catch any other errors (including fatal ones) and report them as JSON
    jsonOut(['success' => false, 'message' => 'A server error occurred: ' . $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
}