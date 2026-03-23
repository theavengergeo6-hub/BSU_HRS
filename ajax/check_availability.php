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
$requested_start = $date . ' 00:00:00';
$requested_end   = $date . ' 23:59:59';
$req_s = strtotime($requested_start);
$req_e = strtotime($requested_end) + 1; // exactly midnight next day

$query1 = "SELECT start_datetime, end_datetime, 
          DATE_ADD(end_datetime, INTERVAL 1 HOUR) as buffer_end,
          id as reservation_id
          FROM facility_reservations 
          WHERE venue_id = ? 
          AND start_datetime <= ? 
          AND DATE_ADD(end_datetime, INTERVAL 1 HOUR) > ?
          AND status = 'approved'"; 

$stmt1 = $conn->prepare($query1);
$stmt1->bind_param("iss", $venue_id, $requested_end, $requested_start);
$stmt1->execute();
$result1 = $stmt1->get_result();

while ($row = $result1->fetch_assoc()) {
    process_row($row, $req_s, $req_e, $booked_slots, $seen_slots);
}
$stmt1->close();

// 2. Check secondary venues in reservation_venues - ONLY APPROVED
$query2 = "SELECT rv.start_datetime, rv.end_datetime,
          DATE_ADD(rv.end_datetime, INTERVAL 1 HOUR) as buffer_end,
          r.id as reservation_id
          FROM reservation_venues rv
          JOIN facility_reservations r ON rv.reservation_id = r.id
          WHERE rv.venue_id = ? 
          AND rv.start_datetime <= ? 
          AND DATE_ADD(rv.end_datetime, INTERVAL 1 HOUR) > ?
          AND r.status = 'approved'"; 

$stmt2 = $conn->prepare($query2);
$stmt2->bind_param("iss", $venue_id, $requested_end, $requested_start);
$stmt2->execute();
$result2 = $stmt2->get_result();

while ($row = $result2->fetch_assoc()) {
    process_row($row, $req_s, $req_e, $booked_slots, $seen_slots);
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

function process_row($row, $req_s, $req_e, &$booked_slots, &$seen_slots) {
    // Treat the booking as a daily recurring event between start date and end date
    $db_start = strtotime($row['start_datetime']);
    $db_end = strtotime($row['end_datetime']);
    
    // Extract dates
    $start_date = date('Y-m-d', $db_start);
    $end_date   = date('Y-m-d', $db_end);
    $req_date   = date('Y-m-d', $req_s); // The requested date being queried (starts at 00:00:00)
    
    // If the requested date falls within the booking's date range (inclusive)
    if ($req_date >= $start_date && $req_date <= $end_date) {
        
        // Extract the TIME portions
        $start_time_str = date('H:i:s', $db_start);
        $end_time_str   = date('H:i:s', $db_end);
        
        // Base the times on today's requested date
        $effective_start = strtotime($req_date . ' ' . $start_time_str);
        
        // The original end time plus a 1-hour buffer
        $end_timestamp = strtotime($req_date . ' ' . $end_time_str);
        $effective_end = strtotime('+1 hour', $end_timestamp);
        
        // Now calculate slot in minutes
        $start_mins = ($effective_start - $req_s) / 60;
        $end_mins   = ($effective_end - $req_s) / 60;
        
        // Format to H:i
        $sh = floor($start_mins / 60);
        $sm = $start_mins % 60;
        $start_time = sprintf('%02d:%02d', $sh, $sm);
        
        $eh = floor($end_mins / 60);
        $em = $end_mins % 60;
        
        if ($end_mins >= 1440) { // >= 24 hours
            $buffer_end_time = '23:59';
        } else {
            $buffer_end_time = sprintf('%02d:%02d', $eh, $em);
        }
        
        $display_end = date('H:i', $end_timestamp);
        
        $slot_key = $start_time . '-' . $buffer_end_time;
        if (!isset($seen_slots[$slot_key])) {
            $booked_slots[] = [
                'start' => $start_time,
                'end' => $display_end,
                'buffer_end' => $buffer_end_time
            ];
            $seen_slots[$slot_key] = true;
        }
    }
}

function timeToMins($time) {
    $parts = explode(':', $time);
    return (int)$parts[0] * 60 + (int)$parts[1];
}
?>