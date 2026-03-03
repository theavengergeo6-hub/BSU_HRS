<?php
require_once __DIR__ . '/../inc/db_config.php';
require_once __DIR__ . '/../inc/essentials.php';

header('Content-Type: application/json');

$venue_id = (int)($_GET['venue_id'] ?? 0);
$date = $_GET['date'] ?? '';

if (!$venue_id || !$date) {
    echo json_encode(['success' => false, 'message' => 'Missing parameters']);
    exit;
}

$booked_slots = [];
$seen_slots = []; // For deduplication

// 1. Check primary venue in facility_reservations - ONLY APPROVED
$query1 = "SELECT start_datetime, end_datetime, 
          DATE_ADD(end_datetime, INTERVAL 1 HOUR) as buffer_end,
          id as reservation_id
          FROM facility_reservations 
          WHERE venue_id = ? 
          AND DATE(start_datetime) = ? 
          AND status = 'approved'"; // Changed from IN to only 'approved'

$stmt1 = $conn->prepare($query1);
$stmt1->bind_param("is", $venue_id, $date);
$stmt1->execute();
$result1 = $stmt1->get_result();

while ($row = $result1->fetch_assoc()) {
    $start_time = date('H:i', strtotime($row['start_datetime']));
    $end_time = date('H:i', strtotime($row['end_datetime']));
    $buffer_end = date('H:i', strtotime($row['buffer_end']));
    
    $slot_key = $start_time . '-' . $end_time;
    
    if (!isset($seen_slots[$slot_key])) {
        $booked_slots[] = [
            'start' => $start_time,
            'end' => $end_time,
            'buffer_end' => $buffer_end
        ];
        $seen_slots[$slot_key] = true;
    }
}
$stmt1->close();

// 2. Check secondary venues in reservation_venues - ONLY APPROVED
$query2 = "SELECT rv.start_datetime, rv.end_datetime,
          DATE_ADD(rv.end_datetime, INTERVAL 1 HOUR) as buffer_end,
          r.id as reservation_id
          FROM reservation_venues rv
          JOIN facility_reservations r ON rv.reservation_id = r.id
          WHERE rv.venue_id = ? 
          AND DATE(rv.start_datetime) = ? 
          AND r.status = 'approved'"; // Changed from IN to only 'approved'

$stmt2 = $conn->prepare($query2);
$stmt2->bind_param("is", $venue_id, $date);
$stmt2->execute();
$result2 = $stmt2->get_result();

while ($row = $result2->fetch_assoc()) {
    $start_time = date('H:i', strtotime($row['start_datetime']));
    $end_time = date('H:i', strtotime($row['end_datetime']));
    $buffer_end = date('H:i', strtotime($row['buffer_end']));
    
    $slot_key = $start_time . '-' . $end_time;
    
    if (!isset($seen_slots[$slot_key])) {
        $booked_slots[] = [
            'start' => $start_time,
            'end' => $end_time,
            'buffer_end' => $buffer_end
        ];
        $seen_slots[$slot_key] = true;
    }
}
$stmt2->close();

// Generate all possible 30-min slots from 7:00 to 22:30
$all_slots = [];
for ($h = 7; $h <= 22; $h++) {
    for ($m = 0; $m < 60; $m += 30) {
        $slot = sprintf('%02d:%02d', $h, $m);
        $all_slots[] = $slot;
    }
}

// Calculate available start times
$available_starts = [];
foreach ($all_slots as $slot) {
    $slot_mins = timeToMins($slot);
    $is_available = true;
    
    foreach ($booked_slots as $booked) {
        $booked_start_mins = timeToMins($booked['start']);
        $booked_buffer_mins = timeToMins($booked['buffer_end']);
        
        if ($slot_mins >= $booked_start_mins && $slot_mins < $booked_buffer_mins) {
            $is_available = false;
            break;
        }
    }
    
    if ($is_available) {
        $available_starts[] = $slot;
    }
}

echo json_encode([
    'success' => true,
    'booked_slots' => $booked_slots,
    'available_starts' => $available_starts
]);

function timeToMins($time) {
    $parts = explode(':', $time);
    return (int)$parts[0] * 60 + (int)$parts[1];
}
?>