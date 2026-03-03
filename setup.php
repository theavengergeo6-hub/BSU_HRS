<?php
/**
 * One-time setup: Create database and default admin
 * Run once: http://localhost/BSU_HRS/setup.php
 * Delete this file after setup for security.
 */

$hname = "localhost";
$uname = "root";
$pass = "";
$conn = new mysqli($hname, $uname, $pass);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$conn->query("CREATE DATABASE IF NOT EXISTS hostelweb");
$conn->select_db("hostelweb");

// Run schema
$sql = file_get_contents(__DIR__ . '/database/hostelweb.sql');
$conn->multi_query($sql);
while ($conn->next_result()) {; }

// Create or update admin (username: admin, password: admin123)
$hash = password_hash('admin123', PASSWORD_DEFAULT);
$stmt = $conn->prepare("INSERT INTO admin_cred (admin_name, admin_pass, user_email) VALUES ('admin', ?, 'admin@hostel.local') ON DUPLICATE KEY UPDATE admin_pass = ?");
$stmt->bind_param("ss", $hash, $hash);
$stmt->execute();

echo "<h1>Setup Complete</h1>";
echo "<p>Admin login: username <strong>admin</strong>, password <strong>admin123</strong></p>";
echo "<p><a href='admin/'>Go to Admin Panel</a> | <a href='index.php'>Go to Homepage</a></p>";
echo "<p><strong>Delete setup.php for security.</strong></p>";
