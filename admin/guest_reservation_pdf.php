<?php
/**
 * admin/guest_reservation_pdf.php
 * Streams the 3-page Guest Room Registration PDF for a given reservation.
 *
 * URL:  admin/guest_reservation_pdf.php?id=X
 *       admin/guest_reservation_pdf.php?id=X&download=1
 */
require_once __DIR__ . '/inc/auth.php';
requireAdminLogin();

require_once __DIR__ . '/inc/db_config.php';
require_once __DIR__ . '/inc/essentials.php';
require_once __DIR__ . '/GuestRoomPDF.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    header('Location: guest_reservations.php');
    exit;
}

// ── Fetch reservation with room info ─────────────────────────────────────────
$stmt = $conn->prepare("
    SELECT
        gr.*,
        g.room_name,
        g.floor      AS room_floor,
        g.room_type,
        g.max_guests AS capacity
    FROM guest_room_reservations gr
    LEFT JOIN guest_rooms g ON gr.guest_room_id = g.id
    WHERE gr.id = ? AND gr.deleted = 0
");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error_message'] = 'Reservation not found.';
    header('Location: guest_reservations.php');
    exit;
}

$row = $result->fetch_assoc();
$stmt->close();

// ── Resolve registered_by from multiple possible sources ─────────────────────
$registeredBy = trim((string)($row['terms_accepted_by'] ?? ''));
if ($registeredBy === '') {
    $sr = $row['special_requests'] ?? '';
    if (is_string($sr) && $sr !== '' && ($sr[0] === '{' || $sr[0] === '[')) {
        $srArr = json_decode($sr, true);
        if (is_array($srArr) && !empty($srArr['registered_by'])) {
            $registeredBy = (string)$srArr['registered_by'];
        }
    }
}

// ── Build data array ──────────────────────────────────────────────────────────
$data = [
    'booking_no'        => $row['booking_no']       ?? '',
    'guest_name'        => $row['guest_name']        ?? '',
    'guest_dob'         => $row['guest_dob']         ?? '',
    'guest_address'     => $row['guest_address']     ?? '',
    'guest_email'       => $row['guest_email']       ?? '',
    'guest_contact'     => $row['guest_contact']     ?? '',
    'check_in_date'     => $row['check_in_date']     ?? '',
    'check_in_time'     => $row['check_in_time']     ?? '',
    'check_out_date'    => $row['check_out_date']    ?? '',
    'check_out_time'    => $row['check_out_time']    ?? '',
    'adults_count'      => $row['adults_count']      ?? 1,
    'children_count'    => $row['children_count']    ?? 0,
    'room_name'         => $row['room_name']         ?? '',
    'room_type'         => $row['room_type']         ?? '',
    'room_floor'        => $row['room_floor']        ?? '',
    'special_requests'  => $row['special_requests']  ?? '',
    'other_guests'      => $row['other_guests']      ?? '[]',
    'terms_accepted_by' => $registeredBy,
    'registered_by'     => $registeredBy,
];

// ── Generate and stream ───────────────────────────────────────────────────────
try {
    $gen      = new GuestRoomPDF($data);
    $booking  = preg_replace('/[^A-Za-z0-9\-]/', '_', $row['booking_no'] ?? 'registration');
    $filename = 'guest_registration_' . $booking . '.pdf';
    $gen->stream($filename, !empty($_GET['download']));

} catch (RuntimeException $e) {
    http_response_code(500);
    echo '<html><body style="font-family:sans-serif;padding:2rem;">';
    echo '<h2 style="color:#b71c1c;">PDF Generation Error</h2>';
    echo '<pre style="background:#f8f9fa;padding:1rem;border-radius:8px;">';
    echo htmlspecialchars($e->getMessage());
    echo '</pre>';
    echo '<p><a href="guest_reservation_details.php?id=' . $id . '">← Back to Reservation Details</a></p>';
    echo '</body></html>';
}