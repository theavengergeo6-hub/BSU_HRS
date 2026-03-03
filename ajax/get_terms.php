<?php
require_once __DIR__ . '/../../inc/essentials.php';
require_once __DIR__ . '/../../inc/db_config.php';

header('Content-Type: application/json');

if (!isset($_GET['office_type_id']) || empty($_GET['office_type_id'])) {
    echo json_encode(['success' => false, 'message' => 'Office type ID required']);
    exit;
}

$office_type_id = (int)$_GET['office_type_id'];

// Map office type IDs to customer types
$type_mapping = [
    1 => 'college',        // College
    2 => 'office',         // Office
    3 => 'student_org',    // Student Organization
    4 => 'external'        // External
];

$customer_type = $type_mapping[$office_type_id] ?? 'external';

// Check if terms_and_conditions table exists
$table_check = $conn->query("SHOW TABLES LIKE 'terms_and_conditions'");
if ($table_check->num_rows == 0) {
    echo json_encode(['success' => false, 'message' => 'Terms table not found']);
    exit;
}

// Fetch terms from database
$query = "SELECT title, content, version FROM terms_and_conditions WHERE customer_type = ? AND is_active = 1 LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $customer_type);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $terms = $result->fetch_assoc();
    echo json_encode([
        'success' => true,
        'terms' => [
            'title' => $terms['title'],
            'content' => $terms['content'],
            'version' => $terms['version']
        ]
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'No terms found']);
}