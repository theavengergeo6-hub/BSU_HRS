<?php
require 'c:/xampp/htdocs/BSU_HRS/inc/db_config.php';
$res = $conn->query('SELECT rv.*, v.name FROM reservation_venues rv JOIN venues v ON rv.venue_id = v.id WHERE rv.start_datetime LIKE "2026-05%" ORDER BY rv.start_datetime ASC LIMIT 100');
while($row = $res->fetch_assoc()) {
    print_r($row);
}
