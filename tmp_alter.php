<?php
require_once 'c:/xampp/htdocs/BSU_HRS/inc/db_config.php';
$conn->query("ALTER TABLE facility_reservations 
    ADD COLUMN requested_by_last_name VARCHAR(255), 
    ADD COLUMN requested_by_first_name VARCHAR(255), 
    ADD COLUMN requested_by_middle_initial VARCHAR(10)");
echo $conn->error ?: 'OK';
?>
