<?php
require_once __DIR__ . '/../../inc/db_config.php';
require_once __DIR__ . '/../inc/auth.php';

if (!isAdminLoggedIn()) {
    http_response_code(401);
    exit;
}

$query = $_GET['q'] ?? '';
$view  = $_GET['view'] ?? 'function';

if (strlen($query) < 2) {
    echo json_encode([]);
    exit;
}

$results = [];
$q = "%$query%";

if ($view === 'function') {
    $sql = "SELECT id, activity_name as title, start_datetime, end_datetime, status, 
                   CONCAT(last_name, ', ', first_name) as requester
            FROM facility_reservations 
            WHERE (activity_name LIKE ? OR CONCAT(last_name, ' ', first_name) LIKE ? OR booking_no LIKE ?)
              AND status != 'cancelled'
            ORDER BY start_datetime DESC 
            LIMIT 10";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sss', $q, $q, $q);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $row['type'] = 'facility';
        $row['formatted_date'] = date('M d, Y', strtotime($row['start_datetime']));
        $results[] = $row;
    }
} else {
    $sql = "SELECT gr.id, gr.guest_name as title, gr.check_in_date as start_datetime, gr.check_out_date as end_datetime, gr.status,
                   g.room_name as requester
            FROM guest_room_reservations gr
            LEFT JOIN guest_rooms g ON gr.guest_room_id = g.id
            WHERE (gr.guest_name LIKE ? OR gr.guest_email LIKE ?)
              AND gr.deleted = 0
              AND gr.status != 'cancelled'
            ORDER BY gr.check_in_date DESC 
            LIMIT 10";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $q, $q);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $row['type'] = 'guest';
        $row['formatted_date'] = date('M d, Y', strtotime($row['start_datetime']));
        $results[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($results);
