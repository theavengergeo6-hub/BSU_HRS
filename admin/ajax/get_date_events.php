<?php
require_once __DIR__ . '/../../inc/db_config.php';
require_once __DIR__ . '/../inc/auth.php';

if (!isAdminLoggedIn()) { http_response_code(401); exit; }

$date   = $_GET['date']   ?? '';
$status = $_GET['status'] ?? 'approved';

if (!$date) {
    echo '<p class="text-muted">No date specified.</p>';
    exit;
}

// Build status filter
$statuses = [];
if ($status === 'all')      $statuses = ['approved', 'pending', 'pencil_booked'];
elseif ($status === 'none') $statuses = [];
else                          $statuses = explode(',', $status);

if (empty($statuses)) {
    echo '<div class="text-center py-4 text-muted"><i class="bi bi-funnel fs-2"></i><p class="mt-2">No filter selected. Tick a checkbox to see events.</p></div>';
    exit;
}

$placeholders = implode(',', array_fill(0, count($statuses), '?'));
$types        = str_repeat('s', count($statuses) + 2); // +2 for the two date params

// UNION: primary date + reservation_venues date
$sql = "
    SELECT r.id, r.booking_no, r.activity_name, r.status,
           r.start_datetime, r.end_datetime,
           r.last_name, r.first_name, r.participants_count,
           r.office_type_id, r.external_office_name,
           v.name as venue_name, v.floor,
           CONCAT(r.last_name, ', ', r.first_name) as requester,
           ot.name as office_type_name,
           CASE WHEN r.office_type_id = 4 THEN r.external_office_name ELSE o.name END as office_name
    FROM facility_reservations r
    JOIN venues v ON r.venue_id = v.id
    LEFT JOIN office_types ot ON r.office_type_id = ot.id
    LEFT JOIN offices o ON r.office_id = o.id
    WHERE DATE(r.start_datetime) = ?
      AND r.status IN ($placeholders)

    UNION

    SELECT r.id, r.booking_no, r.activity_name, r.status,
           r.start_datetime, r.end_datetime,
           r.last_name, r.first_name, r.participants_count,
           r.office_type_id, r.external_office_name,
           v.name as venue_name, v.floor,
           CONCAT(r.last_name, ', ', r.first_name) as requester,
           ot.name as office_type_name,
           CASE WHEN r.office_type_id = 4 THEN r.external_office_name ELSE o.name END as office_name
    FROM reservation_venues rv
    JOIN facility_reservations r ON rv.reservation_id = r.id
    JOIN venues v ON r.venue_id = v.id
    LEFT JOIN office_types ot ON r.office_type_id = ot.id
    LEFT JOIN offices o ON r.office_id = o.id
    WHERE DATE(rv.start_datetime) = ?
      AND r.status IN ($placeholders)

    ORDER BY status DESC, start_datetime ASC
";

$stmt = $conn->prepare($sql);
// Bind: date, ...statuses, date, ...statuses
$bind_values = array_merge([$date], $statuses, [$date], $statuses);
$bind_refs   = [];
$bind_types  = str_repeat('s', count($bind_values));
foreach ($bind_values as $k => $v) $bind_refs[$k] = &$bind_values[$k];
array_unshift($bind_refs, $bind_types);
call_user_func_array([$stmt, 'bind_param'], $bind_refs);
$stmt->execute();
$result = $stmt->get_result();

// Deduplicate by id
$rows = [];
$seen = [];
while ($row = $result->fetch_assoc()) {
    if (empty($seen[$row['id']])) {
        $rows[] = $row;
        $seen[$row['id']] = true;
    }
}

if (empty($rows)) {
    echo '<div class="text-center py-5"><i class="bi bi-calendar-x fs-1 text-muted"></i><p class="mt-2">No events scheduled for this day.</p></div>';
    exit;
}

foreach ($rows as $row):
    $start       = new DateTime($row['start_datetime']);
    $end         = new DateTime($row['end_datetime']);
    $status_class = strtolower($row['status']);
    if ($status_class === 'approved') $pill_class = 'pill-approved';
    elseif ($status_class === 'pending') $pill_class = 'pill-pending';
    elseif ($status_class === 'pencil_booked') $pill_class = 'pill-pencil';
    else $pill_class = 'pill-pending';
?>
<div class="event-card <?= $status_class ?>">
    <div class="event-header">
        <span class="event-title"><?= htmlspecialchars($row['activity_name']) ?></span>
        <span class="event-status-pill <?= $pill_class ?>"><?= strtoupper(str_replace('_', ' ', $row['status'])) ?></span>
    </div>
    <div class="event-time"><i class="bi bi-clock me-1"></i><?= $start->format('g:i A') ?> – <?= $end->format('g:i A') ?></div>
    <div class="event-details">
        <div class="event-detail-item"><i class="bi bi-building"></i><span><?= htmlspecialchars($row['venue_name'] . ' (' . $row['floor'] . ')') ?></span></div>
        <div class="event-detail-item"><i class="bi bi-person"></i><span><?= htmlspecialchars($row['requester']) ?></span></div>
        <div class="event-detail-item"><i class="bi bi-people"></i><span><?= $row['participants_count'] ?> participants</span></div>
        <div class="event-detail-item"><i class="bi bi-briefcase"></i><span><?= htmlspecialchars($row['office_type_name'] ?? 'N/A') ?><?php if (!empty($row['office_name'])): ?> – <?= htmlspecialchars($row['office_name']) ?><?php endif; ?></span></div>
    </div>
    <div class="view-link"><a href="reservation_details.php?id=<?= $row['id'] ?>">View Details <i class="bi bi-arrow-right"></i></a></div>
</div>
<?php endforeach; ?>
