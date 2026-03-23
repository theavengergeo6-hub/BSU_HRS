<?php
require_once 'c:/xampp/htdocs/BSU_HRS/inc/db_config.php';

$venue_id = 4;
$requested_start = '2026-03-25 00:00:00';
$requested_end   = '2026-03-25 23:59:59';

echo "Requested End: $requested_end, Requested Start: $requested_start\n";

$sql = "SELECT id, start_datetime, end_datetime, DATE_ADD(end_datetime, INTERVAL 1 HOUR) as buffer_end
        FROM facility_reservations 
        WHERE venue_id = ? 
        AND start_datetime <= ? 
        AND DATE_ADD(end_datetime, INTERVAL 1 HOUR) > ?
        AND status = 'approved'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iss", $venue_id, $requested_end, $requested_start);
$stmt->execute();
$res = $stmt->get_result();

while($row = $res->fetch_assoc()){
    echo "FR match: " . json_encode($row) . "\n";
}

$sql2 = "SELECT rv.reservation_id, rv.start_datetime, rv.end_datetime, DATE_ADD(rv.end_datetime, INTERVAL 1 HOUR) as buffer_end
        FROM reservation_venues rv
        JOIN facility_reservations fr ON rv.reservation_id = fr.id
        WHERE rv.venue_id = ?
        AND rv.start_datetime <= ?
        AND DATE_ADD(rv.end_datetime, INTERVAL 1 HOUR) > ?
        AND fr.status = 'approved'";
$stmt2 = $conn->prepare($sql2);
$stmt2->bind_param("iss", $venue_id, $requested_end, $requested_start);
$stmt2->execute();
$res2 = $stmt2->get_result();
while($row = $res2->fetch_assoc()){
    echo "RV match: " . json_encode($row) . "\n";
}
?>
