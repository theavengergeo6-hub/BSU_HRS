<?php
require 'c:/xampp/htdocs/BSU_HRS/inc/db_config.php';
$res = $conn->query('SELECT rv.*, v.name FROM reservation_venues rv JOIN venues v ON rv.venue_id = v.id ORDER BY rv.id DESC LIMIT 10');
while($row = $res->fetch_assoc()) {
    print_r($row);
}
