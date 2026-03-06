<?php
require_once __DIR__ . '/../../inc/db_config.php';
require_once __DIR__ . '/../inc/auth.php';
if (!isAdminLoggedIn()) { http_response_code(401); exit; }

$date   = $_GET['date']   ?? '';
$status = $_GET['status'] ?? 'approved';
if (!$date) { echo '<p class="text-muted">No date specified.</p>'; exit; }

$statuses = [];
if ($status === 'all')       $statuses = ['approved','pending','pencil_booked'];
elseif ($status === 'none')  $statuses = [];
else                           $statuses = array_filter(explode(',', $status));

if (empty($statuses)) {
    echo '<div class="text-center py-4 text-muted"><i class="bi bi-funnel fs-2 d-block mb-2"></i>No filter selected. Tick a checkbox to see events.</div>';
    exit;
}
$ph   = implode(',', array_fill(0, count($statuses), '?'));
$sql = "
    SELECT r.id, r.booking_no, r.activity_name, r.status,
           r.start_datetime, r.end_datetime,
           r.last_name, r.first_name, r.participants_count,
           r.office_type_id, r.external_office_name,
           v.name AS venue_name, v.floor,
           CONCAT(r.last_name,', ',r.first_name) AS requester,
           ot.name AS office_type_name,
           CASE WHEN r.office_type_id = 4 THEN r.external_office_name ELSE o.name END AS office_name
    FROM facility_reservations r
    JOIN venues v ON r.venue_id = v.id
    LEFT JOIN office_types ot ON r.office_type_id = ot.id
    LEFT JOIN offices o ON r.office_id = o.id
    WHERE DATE(r.start_datetime) = ? AND r.status IN ($ph)
    UNION
    SELECT r.id, r.booking_no, r.activity_name, r.status,
           r.start_datetime, r.end_datetime,
           r.last_name, r.first_name, r.participants_count,
           r.office_type_id, r.external_office_name,
           v.name AS venue_name, v.floor,
           CONCAT(r.last_name,', ',r.first_name) AS requester,
           ot.name AS office_type_name,
           CASE WHEN r.office_type_id = 4 THEN r.external_office_name ELSE o.name END AS office_name
    FROM reservation_venues rv
    JOIN facility_reservations r ON rv.reservation_id = r.id
    JOIN venues v ON rv.venue_id = v.id
    LEFT JOIN office_types ot ON r.office_type_id = ot.id
    LEFT JOIN offices o ON r.office_id = o.id
    WHERE DATE(rv.start_datetime) = ? AND r.status IN ($ph)
    ORDER BY status DESC, start_datetime ASC
";
$stmt = $conn->prepare($sql);
$vals = array_merge([$date], $statuses, [$date], $statuses);
$types = str_repeat('s', count($vals));
$refs  = []; foreach ($vals as $k => $v) $refs[$k] = &$vals[$k];
array_unshift($refs, $types);
call_user_func_array([$stmt, 'bind_param'], $refs);
$stmt->execute();
$res  = $stmt->get_result();
$rows = []; $seen = [];
while ($row = $res->fetch_assoc()) {
    if (empty($seen[$row['id']])) { $rows[] = $row; $seen[$row['id']] = true; }
}
if (empty($rows)) {
    echo '<div class="text-center py-5"><i class="bi bi-calendar-x fs-1 text-muted d-block mb-2"></i>No events scheduled for this day.</div>';
    exit;
}
foreach ($rows as $row):
    $sc  = strtolower($row['status']);
    $pil = $sc==='approved'?'pill-approved':($sc==='pending'?'pill-pending':'pill-pencil');
    $s   = new DateTime($row['start_datetime']);
    $e   = new DateTime($row['end_datetime']);
?>
<div class="event-card <?= $sc ?>">
    <div class="event-header">
        <span class="event-title"><?= htmlspecialchars($row['activity_name']) ?></span>
        <span class="event-status-pill <?= $pil ?>"><?= strtoupper(str_replace('_',' ',$row['status'])) ?></span>
    </div>
    <div class="event-time"><i class="bi bi-clock me-1"></i><?= $s->format('g:i A') ?> – <?= $e->format('g:i A') ?></div>
    <div class="event-details">
        <div class="event-detail-item"><i class="bi bi-building"></i><span><?= htmlspecialchars($row['venue_name'].' ('.$row['floor'].')')?></span></div>
        <div class="event-detail-item"><i class="bi bi-person"></i><span><?= htmlspecialchars($row['requester']) ?></span></div>
        <div class="event-detail-item"><i class="bi bi-people"></i><span><?= $row['participants_count'] ?> participants</span></div>
        <div class="event-detail-item"><i class="bi bi-briefcase"></i><span><?= htmlspecialchars($row['office_type_name']??'N/A') ?><?php if (!empty($row['office_name'])): ?> – <?= htmlspecialchars($row['office_name']) ?><?php endif; ?></span></div>
    </div>
    <div class="view-link"><a href="reservation_details.php?id=<?= $row['id'] ?>">View Details <i class="bi bi-arrow-right"></i></a></div>
</div>
<?php endforeach; ?>
