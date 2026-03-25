<?php
require_once __DIR__ . '/../../inc/db_config.php';
require_once __DIR__ . '/../inc/auth.php';

if (!isAdminLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');

// Get the latest reservation ID from the client, if provided
$last_fac_id = isset($_GET['last_fac_id']) ? (int)$_GET['last_fac_id'] : 0;
$last_guest_id = isset($_GET['last_guest_id']) ? (int)$_GET['last_guest_id'] : 0;

try {
    // Check max IDs
    $fac_stmt = $conn->query("SELECT MAX(id) as max_id FROM facility_reservations");
    $current_max_fac = (int)($fac_stmt->fetch_assoc()['max_id'] ?? 0);

    $guest_stmt = $conn->query("SELECT MAX(id) as max_id FROM guest_room_reservations");
    $current_max_guest = (int)($guest_stmt->fetch_assoc()['max_id'] ?? 0);

    $has_new = false;
    $new_fac_count = 0;
    $new_guest_count = 0;

    // Only flag as new if the client sent valid last IDs > 0, and current is greater
    if ($last_fac_id > 0 && $current_max_fac > $last_fac_id) {
        $has_new = true;
        // Count how many new facility reservations
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM facility_reservations WHERE id > ?");
        $stmt->bind_param("i", $last_fac_id);
        $stmt->execute();
        $new_fac_count = (int)($stmt->get_result()->fetch_assoc()['count'] ?? 0);
    }

    if ($last_guest_id > 0 && $current_max_guest > $last_guest_id) {
        $has_new = true;
        // Count how many new guest reservations
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM guest_room_reservations WHERE id > ?");
        $stmt->bind_param("i", $last_guest_id);
        $stmt->execute();
        $new_guest_count = (int)($stmt->get_result()->fetch_assoc()['count'] ?? 0);
    }

    echo json_encode([
        'success' => true,
        'has_new' => $has_new,
        'new_fac_count' => $new_fac_count,
        'new_guest_count' => $new_guest_count,
        'current_max_fac' => $current_max_fac,
        'current_max_guest' => $current_max_guest
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database error'
    ]);
}
