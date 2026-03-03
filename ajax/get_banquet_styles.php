<?php
require_once __DIR__ . '/../inc/db_config.php';

header('Content-Type: application/json');

try {
    $result = $conn->query("SELECT id, image, name, description FROM banquet ORDER BY name");
    
    if (!$result) {
        throw new Exception($conn->error);
    }
    
    $styles = [];
    while ($row = $result->fetch_assoc()) {
        $styles[] = [
            'id' => (int)$row['id'],
            'image' => $row['image'],
            'name' => $row['name'],
            'description' => $row['description']
        ];
    }
    
    echo json_encode($styles);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>