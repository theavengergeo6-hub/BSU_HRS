<?php
$conn = new mysqli('localhost', 'root', '', 'bsu_hrs_schema');
if ($conn->connect_error) {
    echo "Connection failed: " . $conn->connect_error;
    exit;
}
$r = $conn->query("SELECT id, name, is_active FROM venues WHERE name LIKE '%Function%'");
if ($r) {
    while ($row = $r->fetch_assoc()) {
        echo $row['id'] . ': ' . $row['name'] . ' (' . $row['is_active'] . ")\n";
    }
    echo "Total: " . $r->num_rows . "\n";
} else {
    echo "Query failed: " . $conn->error;
}
?>
