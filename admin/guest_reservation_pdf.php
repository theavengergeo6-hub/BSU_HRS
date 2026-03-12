<?php
require_once __DIR__ . '/inc/auth.php';
requireAdminLogin();
require_once __DIR__ . '/../inc/db_config.php';
require_once __DIR__ . '/../inc/essentials.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    http_response_code(400);
    echo 'Missing reservation id';
    exit;
}

$query = "SELECT 
            gr.*,
            g.room_name,
            g.floor,
            g.room_type,
            g.max_guests AS capacity
          FROM guest_room_reservations gr
          LEFT JOIN guest_rooms g ON gr.guest_room_id = g.id
          WHERE gr.id = ? AND gr.deleted = 0";
$stmt = $conn->prepare($query);
if (!$stmt) {
    http_response_code(500);
    echo 'DB error';
    exit;
}
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) {
    http_response_code(404);
    echo 'Reservation not found';
    exit;
}
$row = $res->fetch_assoc();
$stmt->close();

require_once __DIR__ . '/../inc/GuestRoomPDF.php';
try {
    $base = dirname(__DIR__);
    $p1 = '';
    $p2 = '';
    $p3 = '';
    foreach (['jpg','png','jpeg'] as $ext) {
        $c1 = $base . '/documents/guest_form_page1.' . $ext;
        $c2 = $base . '/documents/guest_form_page2.' . $ext;
        $c3 = $base . '/documents/guest_form_page3.' . $ext;
        if ($p1 === '' && file_exists($c1)) $p1 = $c1;
        if ($p2 === '' && file_exists($c2)) $p2 = $c2;
        if ($p3 === '' && file_exists($c3)) $p3 = $c3;
    }
    $pdfGen = new GuestRoomPDF($row, $p1, $p2, $p3);
    $pdf = $pdfGen->generate();
} catch (Throwable $e) {
    http_response_code(500);
    echo 'PDF generation failed: ' . $e->getMessage();
    exit;
}

$bookingNo = preg_replace('/[^A-Za-z0-9\\-]/', '', (string)($row['booking_no'] ?? 'guest'));
$filename = 'Guest-Registration-' . ($bookingNo ?: ('ID-' . $id)) . '.pdf';

header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="' . $filename . '"');
header('Content-Length: ' . strlen($pdf));
echo $pdf;