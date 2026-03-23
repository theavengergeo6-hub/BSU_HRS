<?php
require_once 'c:/xampp/htdocs/BSU_HRS/inc/db_config.php';
$res = $conn->query("DESCUBE facility_reservations"); // Type DESCRIBE correctly this time
if(!$res) $res = $conn->query("DESCRIBE facility_reservations");
while($row = $res->fetch_assoc()){
    echo $row['Field'] . " (" . $row['Type'] . ")\n";
}
?>
