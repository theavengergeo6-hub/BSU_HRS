<?php
require_once 'c:/xampp/htdocs/BSU_HRS/inc/db_config.php';
$res = $conn->query("SELECT 1");
echo $res ? 'OK' : 'ERR';
?>
