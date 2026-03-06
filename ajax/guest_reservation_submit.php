<?php
require_once __DIR__ . '/../inc/db_config.php';
require_once __DIR__ . '/../inc/essentials.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Generate booking and reservation numbers
$booking_no = 'GBK-' . date('Ymd') . '-' . rand(1000, 9999);
$reservation_no = 'GRV-' . date('Ymd') . '-' . rand(10000, 99999);

// Get and sanitize input
$last_name = clean($_POST['last_name'] ?? '');
$first_name = clean($_POST['first_name'] ?? '');
$middle_initial = clean($_POST['middle_initial'] ?? '');
$date_of_birth = $_POST['date_of_birth'] ?? '';
$address = clean($_POST['address'] ?? '');
$email = clean($_POST['email'] ?? '');
$contact_number = clean($_POST['contact_number'] ?? '');
$other_guests = $_POST['other_guests'] ?? '{}';
$arrival_date = $_POST['arrival_date'] ?? '';
$departure_date = $_POST['departure_date'] ?? '';
$checkin_time = $_POST['checkin_time'] ?? '';
$checkout_time = $_POST['checkout_time'] ?? '';
$adults_count = (int)($_POST['adults_count'] ?? 1);
$kids_count = (int)($_POST['kids_count'] ?? 0);
$room_id = (int)($_POST['room_id'] ?? 0);
$room_type = clean($_POST['room_type'] ?? 'Guest Room');
$remarks = clean($_POST['remarks'] ?? '');
$registered_by = clean($_POST['registered_by'] ?? '');
$data_privacy_consent = (int)($_POST['data_privacy_consent'] ?? 0);
$digital_signature = clean($_POST['digital_signature'] ?? '');

// Validate required fields
if (empty($last_name) || empty($first_name) || empty($date_of_birth) || empty($address) || 
    empty($email) || empty($contact_number) || empty($arrival_date) || empty($departure_date) ||
    empty($checkin_time) || empty($checkout_time) || empty($room_id) || empty($registered_by) ||
    empty($digital_signature)) {
    echo json_encode(['success' => false, 'message' => 'Please fill in all required fields.']);
    exit;
}

// Validate dates
if (strtotime($departure_date) <= strtotime($arrival_date)) {
    echo json_encode(['success' => false, 'message' => 'Departure date must be after arrival date.']);
    exit;
}

// Check if room is available for the selected dates
$check_query = $conn->prepare("
    SELECT id FROM guest_reservations 
    WHERE room_id = ? 
    AND status IN ('confirmed', 'pending')
    AND (
        (arrival_date <= ? AND departure_date >= ?) OR
        (arrival_date <= ? AND departure_date >= ?) OR
        (? <= arrival_date AND ? >= arrival_date)
    )
");
$check_query->bind_param("issssss", $room_id, $departure_date, $arrival_date, $arrival_date, $departure_date, $arrival_date, $departure_date);
$check_query->execute();
$check_result = $check_query->get_result();

if ($check_result->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Room is not available for the selected dates.']);
    exit;
}

// Insert reservation
$query = $conn->prepare("
    INSERT INTO guest_reservations (
        booking_no, reservation_no, last_name, first_name, middle_initial,
        date_of_birth, address, email, contact_number, other_guests,
        arrival_date, departure_date, checkin_time, checkout_time,
        adults_count, kids_count, room_id, room_type, remarks,
        registered_by, data_privacy_consent, digital_signature, status
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
");

$query->bind_param(
    "sssssssssssssssiisssss",
    $booking_no, $reservation_no, $last_name, $first_name, $middle_initial,
    $date_of_birth, $address, $email, $contact_number, $other_guests,
    $arrival_date, $departure_date, $checkin_time, $checkout_time,
    $adults_count, $kids_count, $room_id, $room_type, $remarks,
    $registered_by, $data_privacy_consent, $digital_signature
);

if ($query->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Guest reservation submitted successfully!',
        'booking_no' => $booking_no
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
}