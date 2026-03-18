<?php
/**
 * admin/function_room_pdf.php
 * Streams the Function Room Reservation Form PDF for a given reservation.
 *
 * URL: admin/function_room_pdf.php?id=X
 *      admin/function_room_pdf.php?id=X&download=1
 */
require_once __DIR__ . '/inc/auth.php';
requireAdminLogin();
require_once __DIR__ . '/inc/db_config.php';
require_once __DIR__ . '/inc/essentials.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    header('Location: reservations.php');
    exit;
}

// ── Fetch reservation ─────────────────────────────────────────────────────────
$stmt = $conn->prepare("
    SELECT fr.*,
           ot.name AS office_type_name,
           o.name  AS office_name,
           b.name  AS banquet_name,
           vs.name AS venue_setup_name
    FROM facility_reservations fr
    LEFT JOIN office_types ot ON fr.office_type_id = ot.id
    LEFT JOIN offices o       ON fr.office_id = o.id
    LEFT JOIN banquet b       ON fr.banquet_style_id = b.id
    LEFT JOIN venue_setups vs ON fr.venue_setup_id = vs.id
    WHERE fr.id = ?
");
if (!$stmt) {
    http_response_code(500);
    echo 'DB error: ' . $conn->error;
    exit;
}
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(404);
    echo 'Reservation not found.';
    exit;
}

$row = $result->fetch_assoc();
$stmt->close();

// ── Gather venue names ────────────────────────────────────────────────────────
$venue_names_q = $conn->query("
    SELECT v.name FROM reservation_venues rv
    JOIN venues v ON rv.venue_id = v.id
    WHERE rv.reservation_id = $id ORDER BY v.name
");
$venue_names = [];
if ($venue_names_q) {
    while ($vn = $venue_names_q->fetch_assoc()) {
        $venue_names[] = $vn['name'];
    }
}
$row['venue_names'] = implode(', ', $venue_names);

// ── Build office_display ──────────────────────────────────────────────────────
$row['office_display'] = ($row['office_type_name'] ?? '') === 'External'
    ? ($row['external_office_name'] ?? '')
    : trim(($row['office_type_name'] ?? '') . ' - ' . ($row['office_name'] ?? ''));

// ── Terms / signature info from miscellaneous_items or direct columns ─────────
$misc = json_decode($row['miscellaneous_items'] ?? '{}', true) ?: [];
$row['terms_agreed_by'] = $row['terms_agreed_by'] ?? ($misc['_terms_agreed_by'] ?? '');
$row['terms_position']  = $row['terms_position']  ?? ($misc['_terms_position']  ?? '');
$row['terms_date']      = $row['terms_date']       ?? ($misc['_terms_date']      ?? '');

// ── Logo paths ────────────────────────────────────────────────────────────────
$base       = dirname(__DIR__);
$logoHostel = $base . '/assets/images/hostel.jpg';
$logoBsu    = $base . '/assets/images/bsu-logo.jpg';
if (!file_exists($logoHostel)) $logoHostel = '';
if (!file_exists($logoBsu))   $logoBsu = '';

// ── Generate & stream ─────────────────────────────────────────────────────────
try {
    require_once $base . '/inc/FunctionRoomPDF.php';
    $gen      = new FunctionRoomPDF($row, $logoHostel, $logoBsu);
    $booking  = preg_replace('/[^A-Za-z0-9\-]/', '_', $row['booking_no'] ?? 'reservation');
    $filename = 'function_room_reservation_' . $booking . '.pdf';
    $gen->stream($filename, !empty($_GET['download']));
} catch (Throwable $e) {
    http_response_code(500);
    echo '<html><body style="font-family:sans-serif;padding:2rem;">';
    echo '<h2 style="color:#b71c1c;">PDF Generation Error</h2>';
    echo '<pre style="background:#f8f9fa;padding:1rem;border-radius:8px;">' . htmlspecialchars($e->getMessage()) . '</pre>';
    echo '<p><a href="reservation_details.php?id=' . $id . '">← Back to Reservation Details</a></p>';
    echo '</body></html>';
}
