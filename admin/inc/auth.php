<?php
require_once __DIR__ . '/../../inc/db_config.php';
require_once __DIR__ . '/../../inc/essentials.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// isAdminLoggedIn() is defined in essentials.php — do not redeclare here

function requireAdminLogin() {
    if (!isAdminLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

function getAdminInfo($conn) {
    if (!isset($_SESSION['admin_id'])) return null;
    $stmt = $conn->prepare("SELECT id, username, email, role, last_login FROM admin_users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['admin_id']);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function logAdminAction($conn, $action, $details = '') {
    $create_table_sql = "
        CREATE TABLE IF NOT EXISTS admin_logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            admin_id INT,
            action VARCHAR(255),
            details TEXT,
            ip_address VARCHAR(45),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (admin_id) REFERENCES admin_users(id) ON DELETE SET NULL
        );
    ";
    if (!$conn->query($create_table_sql)) {
        error_log("Failed to create or verify admin_logs table: " . $conn->error);
        return;
    }

    $admin_id = $_SESSION['admin_id'] ?? null;
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';

    $stmt = $conn->prepare("INSERT INTO admin_logs (admin_id, action, details, ip_address) VALUES (?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("isss", $admin_id, $action, $details, $ip_address);
        $stmt->execute();
        $stmt->close();
    } else {
        error_log("Failed to prepare admin_log statement: " . $conn->error);
    }
}
?>