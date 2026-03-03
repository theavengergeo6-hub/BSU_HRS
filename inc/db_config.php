<?php
$hname = 'localhost';
$uname = 'root';
$pass = '';
$db = 'bsu_hrs_schema';  // Changed from 'hostelweb'

$conn = new mysqli($hname, $uname, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
date_default_timezone_set("Asia/Manila");
