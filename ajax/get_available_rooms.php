<?php

ob_start();
require_once __DIR__ . '/../inc/db_config.php';
require_once __DIR__ . '/../inc/essentials.php';
ob_clean();
header('Content-Type: application/json');
error_reporting(0);

$check_in  = $_GET['check_in']   ?? '';
$check_out = $_GET['check_out']  ?? '';
$exclude   = (int)($_GET['exclude_id'] ?? 0);

// Both dates required
if (!$check_in || !$check_out) {
    echo json_encode(['success' => false, 'message' => 'Dates required']);
    exit;
}
if (strtotime($check_out) <= strtotime($check_in)) {
    echo json_encode(['success' => false, 'message' => 'Check-out must be after check-in']);
    exit;
}

// ── Get all active rooms ──────────────────────────────────────────────────────
$rooms_result = $conn->query("
    SELECT id, room_name AS name, floor, max_guests AS capacity,
           description, price_per_night AS price,
           extra_bed_available, extra_bed_price
    FROM   guest_rooms
    WHERE  is_active = 1
    ORDER  BY sort_order, room_name
");
if (!$rooms_result) {
    echo json_encode(['success' => false, 'message' => 'DB error: ' . $conn->error]);
    exit;
}

// ── Find which room IDs are blocked for the requested dates ──────────────────
// A room is blocked if it has a confirmed or checked_in reservation
// whose date range overlaps [check_in, check_out).
// Overlap condition: existing.check_in < requested.check_out
//                AND existing.check_out > requested.check_in
$exclude_clause = $exclude ? "AND id != $exclude" : "";

$block_sql = "
    SELECT DISTINCT guest_room_id
    FROM   guest_room_reservations
    WHERE  deleted = 0
      AND  status IN ('confirmed', 'checked_in')
      AND  check_in_date  < ?
      AND  check_out_date > ?
      $exclude_clause
";
$bstmt = $conn->prepare($block_sql);
$bstmt->bind_param("ss", $check_out, $check_in);
$bstmt->execute();
$blocked_res = $bstmt->get_result();
$blocked_ids = [];
while ($b = $blocked_res->fetch_assoc()) {
    $blocked_ids[$b['guest_room_id']] = true;
}
$bstmt->close();

// ── Build response ────────────────────────────────────────────────────────────
$rooms = [];
while ($room = $rooms_result->fetch_assoc()) {
    $id = (int)$room['id'];
    $rooms[] = [
        'id'                  => $id,
        'name'                => $room['name'],
        'floor'               => $room['floor'],
        'capacity'            => (int)$room['capacity'],
        'description'         => $room['description'],
        'price'               => (float)$room['price'],
        'extra_bed_available' => (bool)$room['extra_bed_available'],
        'extra_bed_price'     => (float)$room['extra_bed_price'],
        'available'           => !isset($blocked_ids[$id]),  // TRUE = can be booked
    ];
}

echo json_encode(['success' => true, 'rooms' => $rooms]);