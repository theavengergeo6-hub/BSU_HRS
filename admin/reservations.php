<?php
$pageTitle = 'Reservations Calendar';
require_once __DIR__ . '/inc/header.php';

// ── View + month/year params ─────────────────────────────────────────────────
$view  = isset($_GET['view']) && $_GET['view'] === 'guest' ? 'guest' : 'function';
$month = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('m');
$year  = isset($_GET['year'])  ? (int)$_GET['year']  : (int)date('Y');

// Clamp to valid range
if ($month < 1 || $month > 12) { $month = (int)date('m'); }
if ($year  < 2020 || $year > 2040) { $year = (int)date('Y'); }

$prev_month = $month == 1  ? 12      : $month - 1;
$prev_year  = $month == 1  ? $year-1 : $year;
$next_month = $month == 12 ? 1       : $month + 1;
$next_year  = $month == 12 ? $year+1 : $year;

$start_date = sprintf('%04d-%02d-01', $year, $month);
$end_date   = date('Y-m-t', strtotime($start_date));

$month_names = [
    1=>'January',2=>'February',3=>'March',4=>'April',
    5=>'May',6=>'June',7=>'July',8=>'August',
    9=>'September',10=>'October',11=>'November',12=>'December'
];
$month_name    = $month_names[$month];
$first_day     = mktime(0,0,0,$month,1,$year);
$days_in_month = (int)date('t', $first_day);
$day_of_week   = (int)date('w', $first_day);
$today         = date('Y-m-d');

// ── Function room data ───────────────────────────────────────────────────────
if ($view === 'function') {
    function fetchFacilityByDate($conn, $start, $end, $status) {
        $events = [];
        $seen   = [];

        // Primary start_datetime
        $s1 = $conn->prepare("
            SELECT r.id, r.activity_name, r.status,
                   r.start_datetime, r.end_datetime,
                   v.name AS venue_name, v.floor,
                   CONCAT(r.last_name,', ',r.first_name) AS requester,
                   DATE(r.start_datetime) AS event_date
            FROM facility_reservations r
            JOIN venues v ON r.venue_id = v.id
            WHERE r.status = ?
              AND DATE(r.start_datetime) BETWEEN ? AND ?
            ORDER BY r.start_datetime ASC
        ");
        $s1->bind_param('sss', $status, $start, $end);
        $s1->execute();
        foreach ($s1->get_result()->fetch_all(MYSQLI_ASSOC) as $row) {
            $d = $row['event_date'];
            $events[$d][] = $row;
            $seen[$d][$row['id']] = true;
        }
        $s1->close();

        // reservation_venues (multi-date)
        $s2 = $conn->prepare("
            SELECT r.id, r.activity_name, r.status,
                   r.start_datetime, r.end_datetime,
                   v.name AS venue_name, v.floor,
                   CONCAT(r.last_name,', ',r.first_name) AS requester,
                   DATE(rv.start_datetime) AS event_date
            FROM reservation_venues rv
            JOIN facility_reservations r ON rv.reservation_id = r.id
            JOIN venues v ON rv.venue_id = v.id
            WHERE r.status = ?
              AND DATE(rv.start_datetime) BETWEEN ? AND ?
            ORDER BY rv.start_datetime ASC
        ");
        $s2->bind_param('sss', $status, $start, $end);
        $s2->execute();
        foreach ($s2->get_result()->fetch_all(MYSQLI_ASSOC) as $row) {
            $d = $row['event_date'];
            if (empty($seen[$d][$row['id']])) {
                $events[$d][] = $row;
                $seen[$d][$row['id']] = true;
            }
        }
        $s2->close();
        return $events;
    }

    $approved_by_date = fetchFacilityByDate($conn, $start_date, $end_date, 'approved');
    $pending_by_date  = fetchFacilityByDate($conn, $start_date, $end_date, 'pending');
    $pencil_by_date   = fetchFacilityByDate($conn, $start_date, $end_date, 'pencil_booked');

    $total_month    = (int)$conn->query("SELECT COUNT(*) AS c FROM facility_reservations WHERE MONTH(start_datetime)=$month AND YEAR(start_datetime)=$year")->fetch_assoc()['c'];
    $approved_month = (int)$conn->query("SELECT COUNT(*) AS c FROM facility_reservations WHERE status='approved' AND MONTH(start_datetime)=$month AND YEAR(start_datetime)=$year")->fetch_assoc()['c'];
    $pencil_month   = (int)$conn->query("SELECT COUNT(*) AS c FROM facility_reservations WHERE status='pencil_booked' AND MONTH(start_datetime)=$month AND YEAR(start_datetime)=$year")->fetch_assoc()['c'];
    $pending_month  = (int)$conn->query("SELECT COUNT(*) AS c FROM facility_reservations WHERE status='pending' AND MONTH(start_datetime)=$month AND YEAR(start_datetime)=$year")->fetch_assoc()['c'];

    $pending_total = (int)$conn->query("SELECT COUNT(*) AS c FROM facility_reservations WHERE status='pending'")->fetch_assoc()['c'];
    $pencil_total  = (int)$conn->query("SELECT COUNT(*) AS c FROM facility_reservations WHERE status='pencil_booked'")->fetch_assoc()['c'];
}

// ── Guest room data ──────────────────────────────────────────────────────────
if ($view === 'guest') {
    $gTotal     = (int)$conn->query("SELECT COUNT(*) AS c FROM guest_room_reservations WHERE deleted=0 AND MONTH(check_in_date)=$month AND YEAR(check_in_date)=$year")->fetch_assoc()['c'];
    $gConfirmed = (int)$conn->query("SELECT COUNT(*) AS c FROM guest_room_reservations WHERE deleted=0 AND status='confirmed' AND MONTH(check_in_date)=$month AND YEAR(check_in_date)=$year")->fetch_assoc()['c'];
    $gPending   = (int)$conn->query("SELECT COUNT(*) AS c FROM guest_room_reservations WHERE deleted=0 AND status='pending' AND MONTH(check_in_date)=$month AND YEAR(check_in_date)=$year")->fetch_assoc()['c'];
    $gCancelled = (int)$conn->query("SELECT COUNT(*) AS c FROM guest_room_reservations WHERE deleted=0 AND status='cancelled' AND MONTH(check_in_date)=$month AND YEAR(check_in_date)=$year")->fetch_assoc()['c'];

    // Load all active guest reservations that overlap this month
    $gr_result = $conn->query("
        SELECT gr.id,
               gr.guest_name,
               gr.guest_email   AS email,
               gr.guest_contact AS contact_number,
               gr.guest_room_id AS room_id,
               gr.check_in_date  AS arrival_date,
               gr.check_out_date AS departure_date,
               gr.total_guests   AS num_guests,
               gr.total_amount   AS total_price,
               gr.special_requests,
               gr.status,
               gr.created_at,
               g.room_name  AS room_name,
               g.floor      AS room_floor
        FROM guest_room_reservations gr
        JOIN guest_rooms g ON gr.guest_room_id = g.id
        WHERE gr.status IN ('confirmed','checked_in','pending')
          AND gr.deleted = 0
          AND gr.check_in_date  <= '$end_date'
          AND gr.check_out_date >= '$start_date'
        ORDER BY gr.check_in_date ASC
    ");

    // Build events_by_date: each date gets an array of reservations with event_type
    $guest_by_date = [];
    while ($row = $gr_result->fetch_assoc()) {
        $arr = $row['arrival_date'];
        $dep = $row['departure_date'];

        // Arrival day
        $guest_by_date[$arr][] = array_merge($row, ['event_type' => 'checkin']);

        // In-between days
        $cur = strtotime($arr . ' +1 day');
        $depTs = strtotime($dep);
        while ($cur < $depTs) {
            $mid = date('Y-m-d', $cur);
            $guest_by_date[$mid][] = array_merge($row, ['event_type' => 'stay']);
            $cur = strtotime('+1 day', $cur);
        }

        // Departure day (only add if different from arrival)
        if ($dep !== $arr) {
            $guest_by_date[$dep][] = array_merge($row, ['event_type' => 'checkout']);
        }
    }
}

// Ensure ajax directory and get_date_events.php exist (function room modal)
if (!file_exists(__DIR__ . '/ajax')) {
    mkdir(__DIR__ . '/ajax', 0777, true);
}
file_put_contents(__DIR__ . '/ajax/get_date_events.php', '<?php
require_once __DIR__ . \'/../../inc/db_config.php\';
require_once __DIR__ . \'/../inc/auth.php\';
if (!isAdminLoggedIn()) { http_response_code(401); exit; }

$date   = $_GET[\'date\']   ?? \'\';
$status = $_GET[\'status\'] ?? \'approved\';
if (!$date) { echo \'<p class="text-muted">No date specified.</p>\'; exit; }

$statuses = [];
if ($status === \'all\')       $statuses = [\'approved\',\'pending\',\'pencil_booked\'];
elseif ($status === \'none\')  $statuses = [];
else                           $statuses = array_filter(explode(\',\', $status));

if (empty($statuses)) {
    echo \'<div class="text-center py-4 text-muted"><i class="bi bi-funnel fs-2 d-block mb-2"></i>No filter selected. Tick a checkbox to see events.</div>\';
    exit;
}
$ph   = implode(\',\', array_fill(0, count($statuses), \'?\'));
$sql = "
    SELECT r.id, r.booking_no, r.activity_name, r.status,
           r.start_datetime, r.end_datetime,
           r.last_name, r.first_name, r.participants_count,
           r.office_type_id, r.external_office_name,
           v.name AS venue_name, v.floor,
           CONCAT(r.last_name,\', \',r.first_name) AS requester,
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
           CONCAT(r.last_name,\', \',r.first_name) AS requester,
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
$types = str_repeat(\'s\', count($vals));
$refs  = []; foreach ($vals as $k => $v) $refs[$k] = &$vals[$k];
array_unshift($refs, $types);
call_user_func_array([$stmt, \'bind_param\'], $refs);
$stmt->execute();
$res  = $stmt->get_result();
$rows = []; $seen = [];
while ($row = $res->fetch_assoc()) {
    if (empty($seen[$row[\'id\']])) { $rows[] = $row; $seen[$row[\'id\']] = true; }
}
if (empty($rows)) {
    echo \'<div class="text-center py-5"><i class="bi bi-calendar-x fs-1 text-muted d-block mb-2"></i>No events scheduled for this day.</div>\';
    exit;
}
foreach ($rows as $row):
    $sc  = strtolower($row[\'status\']);
    $pil = $sc===\'approved\'?\'pill-approved\':($sc===\'pending\'?\'pill-pending\':\'pill-pencil\');
    $s   = new DateTime($row[\'start_datetime\']);
    $e   = new DateTime($row[\'end_datetime\']);
?>
<div class="event-card <?= $sc ?>">
    <div class="event-header">
        <span class="event-title"><?= htmlspecialchars($row[\'activity_name\']) ?></span>
        <span class="event-status-pill <?= $pil ?>"><?= strtoupper(str_replace(\'_\',\' \',$row[\'status\'])) ?></span>
    </div>
    <div class="event-time"><i class="bi bi-clock me-1"></i><?= $s->format(\'g:i A\') ?> – <?= $e->format(\'g:i A\') ?></div>
    <div class="event-details">
        <div class="event-detail-item"><i class="bi bi-building"></i><span><?= htmlspecialchars($row[\'venue_name\'].\' (\'.$row[\'floor\'].\')\')?></span></div>
        <div class="event-detail-item"><i class="bi bi-person"></i><span><?= htmlspecialchars($row[\'requester\']) ?></span></div>
        <div class="event-detail-item"><i class="bi bi-people"></i><span><?= $row[\'participants_count\'] ?> participants</span></div>
        <div class="event-detail-item"><i class="bi bi-briefcase"></i><span><?= htmlspecialchars($row[\'office_type_name\']??\'N/A\') ?><?php if (!empty($row[\'office_name\'])): ?> – <?= htmlspecialchars($row[\'office_name\']) ?><?php endif; ?></span></div>
    </div>
    <div class="view-link"><a href="reservation_details.php?id=<?= $row[\'id\'] ?>">View Details <i class="bi bi-arrow-right"></i></a></div>
</div>
<?php endforeach; ?>
');
?>

<!-- ═══════════════════════════════════════════════════════════════════════════
     SHARED CSS
     ═══════════════════════════════════════════════════════════════════════════ -->
<style>
:root {
    --bsu-red:      #b71c1c;
    --bsu-red-dark: #8b0000;
    --pencil-purple:#5e3c8b;
    --pencil-light: #e2d5f1;
}

/* ── Tab switcher ─────────────────────────────────────────────────────────── */
.view-tabs {
    display: flex;
    gap: 0;
    background: white;
    border-radius: 12px;
    padding: 5px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    border: 1px solid #e9ecef;
    margin-bottom: 1.5rem;
    width: fit-content;
}
.view-tab {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.55rem 1.25rem;
    border-radius: 8px;
    font-size: 0.875rem;
    font-weight: 600;
    text-decoration: none;
    color: #6c757d;
    transition: all 0.2s ease;
    white-space: nowrap;
}
.view-tab:hover { color: var(--bsu-red); background: #fff5f5; }
.view-tab.active {
    background: var(--bsu-red);
    color: white;
    box-shadow: 0 2px 8px rgba(183,28,28,0.25);
}
.view-tab .tab-badge {
    background: rgba(255,255,255,0.25);
    color: white;
    font-size: 0.7rem;
    padding: 0.1rem 0.4rem;
    border-radius: 20px;
    font-weight: 700;
    min-width: 18px;
    text-align: center;
}
.view-tab:not(.active) .tab-badge {
    background: #f0f0f0;
    color: #666;
}

/* ── Stats grid ───────────────────────────────────────────────────────────── */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(4,1fr);
    gap: 1rem;
    margin-bottom: 1.5rem;
}
.stat-card {
    background: white; border-radius: 12px; padding: 1rem 1.25rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04); border-left: 4px solid var(--bsu-red);
    transition: all 0.2s ease; display: flex; align-items: center; justify-content: space-between;
}
.stat-card:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(183,28,28,0.1); }
.stat-info { flex: 1; }
.stat-title  { color: #6c757d; font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.3px; margin-bottom: 0.2rem; font-weight: 600; }
.stat-number { font-size: 1.6rem; font-weight: 700; color: #212529; line-height: 1.2; }
.stat-icon   { font-size: 1.8rem; color: var(--bsu-red); opacity: 0.22; }

/* ── Alert banner ─────────────────────────────────────────────────────────── */
.pending-banner {
    background: linear-gradient(135deg,#fff3cd,#ffeaa7); border-radius: 12px;
    padding: 0.9rem 1.5rem; margin-bottom: 1.5rem;
    display: flex; justify-content: space-between; align-items: center;
    border-left: 4px solid #ffc107;
}
.pending-banner .banner-content { display: flex; align-items: center; gap: 1rem; }
.pending-banner .banner-icon { width:38px;height:38px;background:white;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#ffc107;font-size:1.1rem; }
.pending-banner .banner-text h3 { font-size:0.95rem;margin:0;color:#856404; }
.pending-banner .banner-text p  { margin:0;color:#856404;font-size:0.8rem;opacity:0.8; }

/* ── Calendar wrapper ─────────────────────────────────────────────────────── */
.calendar-wrapper {
    background: white; border-radius: 16px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    padding: 1.5rem; margin-bottom: 1.5rem;
    border-left: 4px solid var(--bsu-red);
}
.calendar-header {
    display: flex; justify-content: space-between; align-items: center;
    margin-bottom: 1rem; padding-bottom: 0.75rem; border-bottom: 1px solid #e9ecef;
}
.calendar-title h2 { font-size:1.45rem;font-weight:600;color:#212529;margin:0; }
.calendar-nav { display:flex;gap:0.5rem; }
.calendar-nav-btn {
    width:36px;height:36px;border-radius:8px;border:1px solid #dee2e6;
    background:white;color:#495057;font-size:1rem;cursor:pointer;
    transition:all 0.2s ease;display:flex;align-items:center;justify-content:center;
    text-decoration:none;
}
.calendar-nav-btn:hover { border-color:var(--bsu-red);color:var(--bsu-red); }

/* ── Filter bar ───────────────────────────────────────────────────────────── */
.calendar-filters {
    display: flex; align-items: center; gap: 1.25rem;
    margin-bottom: 1.25rem; padding: 0.7rem 1rem;
    background: #f8f9fa; border-radius: 10px; border: 1px solid #e9ecef;
    flex-wrap: wrap;
}
.filter-label { font-size:0.78rem;font-weight:700;color:#6c757d;text-transform:uppercase;letter-spacing:0.4px; }
.filter-checkbox-wrap { display:flex;align-items:center;gap:0.45rem;cursor:pointer;user-select:none; }
.filter-checkbox-wrap input[type="checkbox"] { width:15px;height:15px;cursor:pointer;accent-color:var(--bsu-red); }
.filter-dot { width:10px;height:10px;border-radius:50%;display:inline-block;flex-shrink:0; }
.filter-dot.approved { background:#28a745; }
.filter-dot.pending  { background:#ffc107;border:1px solid #e0a800; }
.filter-dot.pencil   { background:#5e3c8b; }
.filter-dot.confirmed{ background:#28a745; }
.filter-text { font-size:0.875rem;color:#495057;font-weight:500; }

/* ── Calendar grid ────────────────────────────────────────────────────────── */
.calendar-grid { display:grid;grid-template-columns:repeat(7,1fr);gap:5px; }
.calendar-weekday { text-align:center;padding:0.45rem;font-weight:700;color:#6c757d;font-size:0.82rem;text-transform:uppercase;letter-spacing:0.5px; }
.calendar-day {
    background:white;border:1.5px solid #e9ecef;border-radius:10px;
    min-height:82px;padding:0.45rem;transition:all 0.18s ease;cursor:pointer;position:relative;
}
.calendar-day:hover { border-color:var(--bsu-red);background:#fff8f8;transform:translateY(-1px);box-shadow:0 4px 8px rgba(183,28,28,0.08); }
.calendar-day.today { border-color:var(--bsu-red);background:#fff8f8;box-shadow:0 0 0 2px rgba(183,28,28,0.12); }
.calendar-day.other-month { background:#f8f9fa;border-color:#f0f0f0;opacity:0.55;cursor:default; }
.calendar-day.other-month:hover { transform:none;box-shadow:none;border-color:#f0f0f0;background:#f8f9fa; }

.day-number { font-size:0.88rem;font-weight:600;color:#495057;margin-bottom:0.2rem;display:flex;justify-content:space-between;align-items:flex-start;gap:2px; }
.today .day-number { color:var(--bsu-red); }

.event-badges { display:flex;gap:2px;flex-wrap:wrap;align-items:center; }
.event-count-badge {
    font-size: 0.62rem; padding: 0.08rem 0.32rem;
    border-radius: 20px; font-weight: 700; line-height: 1.4;
    display: none;
}
.event-count-badge.approved-badge    { background:#d4edda;color:#155724; }
.event-count-badge.pending-badge     { background:#fff3cd;color:#856404; }
.event-count-badge.pencil-badge      { background:#e2d5f1;color:#5e3c8b; }
.event-count-badge.confirmed-badge   { background:#d4edda;color:#155724; }
.event-count-badge.gp-badge          { background:#fff3cd;color:#856404; }

.event-indicator { display:flex;flex-wrap:wrap;gap:2px;margin-top:0.3rem; }
.event-dot { width:7px;height:7px;border-radius:50%;flex-shrink:0; }
.event-dot.approved-dot  { background:#28a745; }
.event-dot.pending-dot   { background:#ffc107;border:1px solid #e0a800; }
.event-dot.pencil-dot    { background:#5e3c8b; }

/* Guest dot event type markers */
.guest-strip {
    display: flex;
    align-items: center;
    gap: 3px;
    margin-top: 2px;
    font-size: 0.6rem;
    line-height: 1;
}
.gs-dot {
    width: 7px; height: 7px; border-radius: 50%; flex-shrink: 0;
}
.gs-dot.confirmed { background: #28a745; }
.gs-dot.pending   { background: #ffc107; border: 1px solid #e0a800; }
.gs-label {
    font-size: 0.58rem; font-weight: 600; color: #6c757d;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 70px;
}

/* ── Modal ────────────────────────────────────────────────────────────────── */
.event-details-modal {
    position:fixed;inset:0;background:rgba(0,0,0,0.5);
    display:none;align-items:center;justify-content:center;
    z-index:9999;backdrop-filter:blur(4px);
}
.event-details-modal.show { display:flex; }
.modal-card {
    background:white;border-radius:20px;width:90%;max-width:640px;
    max-height:85vh;overflow-y:auto;
    box-shadow:0 20px 40px rgba(0,0,0,0.2);animation:slideUp 0.28s ease;
}
@keyframes slideUp { from{transform:translateY(30px);opacity:0} to{transform:translateY(0);opacity:1} }
.modal-header {
    padding:1.2rem 1.5rem;
    background:linear-gradient(135deg,var(--bsu-red),var(--bsu-red-dark));
    color:white;display:flex;justify-content:space-between;align-items:center;
    border-radius:20px 20px 0 0;position:sticky;top:0;z-index:1;
}
.modal-header h3 { margin:0;font-size:1.1rem;font-weight:600; }
.modal-close { background:none;border:none;color:white;font-size:1.5rem;cursor:pointer;opacity:0.8;transition:opacity 0.2s;line-height:1;padding:0 4px; }
.modal-close:hover { opacity:1; }
.modal-body { padding:1.5rem; }

/* ── Event cards in modal ─────────────────────────────────────────────────── */
.event-card {
    border:1px solid #e9ecef;border-radius:12px;padding:1rem;
    margin-bottom:1rem;transition:all 0.2s ease;border-left:4px solid #28a745;
}
.event-card.pending       { border-left-color:#ffc107; }
.event-card.approved      { border-left-color:#28a745; }
.event-card.pencil_booked { border-left-color:#5e3c8b; }
.event-card.confirmed     { border-left-color:#28a745; }
.event-card:hover { box-shadow:0 5px 15px rgba(0,0,0,0.06);transform:translateY(-1px); }
.event-header { display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:0.5rem;gap:0.5rem; }
.event-title  { font-weight:600;color:#212529;font-size:1rem; }
.event-status-pill { font-size:0.7rem;font-weight:700;padding:0.2rem 0.6rem;border-radius:20px;white-space:nowrap;flex-shrink:0; }
.pill-approved { background:#d4edda;color:#155724; }
.pill-pending  { background:#fff3cd;color:#856404; }
.pill-pencil   { background:#e2d5f1;color:#5e3c8b; }
.pill-cancelled{ background:#f8d7da;color:#721c24; }
.event-time    { font-size:0.85rem;color:#6c757d;margin-bottom:0.5rem; }
.event-details { display:grid;grid-template-columns:repeat(2,1fr);gap:0.4rem;font-size:0.85rem; }
.event-detail-item { display:flex;align-items:flex-start;gap:0.45rem;color:#495057; }
.event-detail-item i { color:var(--bsu-red);font-size:0.9rem;width:16px;margin-top:1px;flex-shrink:0; }
.view-link { margin-top:0.75rem;text-align:right; }
.view-link a { color:var(--bsu-red);text-decoration:none;font-weight:600;font-size:0.82rem;display:inline-flex;align-items:center;gap:0.3rem; }
.view-link a:hover { text-decoration:underline; }

/* Guest stay type badges in modal */
.stay-type-badge {
    display: inline-flex; align-items: center; gap: 0.3rem;
    font-size: 0.72rem; font-weight: 700; padding: 0.2rem 0.55rem;
    border-radius: 20px; margin-bottom: 0.5rem;
}
.stay-checkin  { background:#d4edda;color:#155724; }
.stay-stay     { background:#e2f0fb;color:#004085; }
.stay-checkout { background:#f8d7da;color:#721c24; }

/* ── Responsive ───────────────────────────────────────────────────────────── */
@media(max-width:900px)  { .stats-grid{grid-template-columns:repeat(2,1fr);} }
@media(max-width:768px)  {
    .calendar-day{min-height:62px;padding:0.3rem;}
    .calendar-weekday{font-size:0.72rem;padding:0.3rem;}
    .modal-card{width:96%;border-radius:14px;}
    .calendar-filters{gap:0.65rem;}
    .event-details{grid-template-columns:1fr;}
}
@media(max-width:576px)  {
    .stats-grid{grid-template-columns:1fr 1fr;}
    .calendar-header{flex-direction:column;gap:0.6rem;align-items:flex-start;}
    .view-tabs{width:100%;justify-content:center;}
}
@media(max-width:400px)  { .stats-grid{grid-template-columns:1fr;} }
</style>

<!-- ═══════════════════════════════════════════════════════════════════════════
     HTML OUTPUT
     ═══════════════════════════════════════════════════════════════════════════ -->
<div class="content-area">

    <!-- ── Tab Switcher ──────────────────────────────────────────────────── -->
    <div class="view-tabs">
        <a href="?view=function&month=<?= $month ?>&year=<?= $year ?>"
           class="view-tab <?= $view==='function'?'active':'' ?>">
            <i class="bi bi-building"></i>
            Function Rooms
            <?php if ($view==='function' && !empty($pending_total)): ?>
            <span class="tab-badge"><?= $pending_total ?></span>
            <?php endif; ?>
        </a>
        <a href="?view=guest&month=<?= $month ?>&year=<?= $year ?>"
           class="view-tab <?= $view==='guest'?'active':'' ?>">
            <i class="bi bi-door-open"></i>
            Guest Rooms
        </a>
    </div>

    <?php if ($view === 'function'): ?>
    <!-- ════════════════════════════════════════════════════════════════════
         FUNCTION ROOM CALENDAR
         ════════════════════════════════════════════════════════════════════ -->

    <?php if ($pending_total > 0 || $pencil_total > 0): ?>
    <div class="pending-banner">
        <div class="banner-content">
            <div class="banner-icon"><i class="bi bi-bell-fill"></i></div>
            <div class="banner-text">
                <h3><?= $pending_total ?> pending &amp; <?= $pencil_total ?> pencil-booked reservation<?= ($pending_total+$pencil_total)!=1?'s':'' ?> awaiting action</h3>
                <p>Click to review and approve</p>
            </div>
        </div>
        <a href="reservations_pending.php?status=pending" class="btn btn-sm btn-warning fw-semibold">Review Now</a>
    </div>
    <?php endif; ?>

    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-info"><div class="stat-title">Total This Month</div><div class="stat-number"><?= $total_month ?></div></div>
            <i class="bi bi-calendar-event stat-icon"></i>
        </div>
        <div class="stat-card">
            <div class="stat-info"><div class="stat-title">Approved</div><div class="stat-number"><?= $approved_month ?></div></div>
            <i class="bi bi-check-circle stat-icon"></i>
        </div>
        <div class="stat-card" style="border-left-color:#5e3c8b;">
            <div class="stat-info"><div class="stat-title">Pencil Booked</div><div class="stat-number"><?= $pencil_month ?></div></div>
            <i class="bi bi-pencil stat-icon" style="color:#5e3c8b;"></i>
        </div>
        <div class="stat-card" style="border-left-color:#ffc107;">
            <div class="stat-info"><div class="stat-title">Pending</div><div class="stat-number"><?= $pending_month ?></div></div>
            <i class="bi bi-hourglass-split stat-icon" style="color:#ffc107;"></i>
        </div>
    </div>

    <!-- Calendar -->
    <div class="calendar-wrapper">
        <div class="calendar-header">
            <div class="calendar-title"><h2><?= $month_name ?> <?= $year ?></h2></div>
            <div class="calendar-nav">
                <a href="?view=function&month=<?= $prev_month ?>&year=<?= $prev_year ?>" class="calendar-nav-btn" title="Previous month"><i class="bi bi-chevron-left"></i></a>
                <a href="?view=function&month=<?= date('m') ?>&year=<?= date('Y') ?>" class="calendar-nav-btn" title="Today"><i class="bi bi-calendar3"></i></a>
                <a href="?view=function&month=<?= $next_month ?>&year=<?= $next_year ?>" class="calendar-nav-btn" title="Next month"><i class="bi bi-chevron-right"></i></a>
            </div>
        </div>

        <!-- Filters -->
        <div class="calendar-filters">
            <span class="filter-label">Show:</span>
            <label class="filter-checkbox-wrap">
                <input type="checkbox" id="filterApproved" checked onchange="renderFacilityDots()">
                <span class="filter-dot approved"></span>
                <span class="filter-text">Approved</span>
            </label>
            <label class="filter-checkbox-wrap">
                <input type="checkbox" id="filterPending" checked onchange="renderFacilityDots()">
                <span class="filter-dot pending"></span>
                <span class="filter-text">Pending</span>
            </label>
            <label class="filter-checkbox-wrap">
                <input type="checkbox" id="filterPencil" checked onchange="renderFacilityDots()">
                <span class="filter-dot pencil"></span>
                <span class="filter-text">Pencil Booked</span>
            </label>
        </div>

        <!-- Weekday headers -->
        <div class="calendar-grid" style="margin-bottom:4px;">
            <?php foreach (['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $wd): ?>
            <div class="calendar-weekday"><?= $wd ?></div>
            <?php endforeach; ?>
        </div>

        <!-- Day cells -->
        <div class="calendar-grid" id="facCalGrid">
            <?php
            for ($i = 0; $i < $day_of_week; $i++) {
                echo '<div class="calendar-day other-month"></div>';
            }
            for ($day = 1; $day <= $days_in_month; $day++) {
                $date      = sprintf('%04d-%02d-%02d', $year, $month, $day);
                $cls       = ($date === $today) ? 'today' : '';
                echo '<div class="calendar-day '.$cls.'" data-date="'.$date.'" onclick="showFacilityEvents(\''.$date.'\')">';
                echo  '<div class="day-number">'.$day;
                echo   '<span class="event-badges">';
                echo    '<span class="event-count-badge approved-badge" id="fab-'.$date.'"></span>';
                echo    '<span class="event-count-badge pending-badge"  id="fpb-'.$date.'"></span>';
                echo    '<span class="event-count-badge pencil-badge"   id="fcb-'.$date.'"></span>';
                echo   '</span>';
                echo  '</div>';
                echo  '<div class="event-indicator" id="fdots-'.$date.'"></div>';
                echo '</div>';
            }
            $pad = 42 - ($day_of_week + $days_in_month);
            for ($i = 0; $i < $pad; $i++) {
                echo '<div class="calendar-day other-month"></div>';
            }
            ?>
        </div>
    </div><!-- .calendar-wrapper -->

    <!-- Facility Modal -->
    <div class="event-details-modal" id="facModal">
        <div class="modal-card">
            <div class="modal-header">
                <h3><i class="bi bi-calendar-event me-2"></i>Events on <span id="facModalDate"></span></h3>
                <button class="modal-close" onclick="closeFacModal()" aria-label="Close">&times;</button>
            </div>
            <div class="modal-body" id="facModalBody">
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-hourglass-split fs-1 d-block mb-2"></i>Loading…
                </div>
            </div>
        </div>
    </div>

    <?php
    // Encode event data for JavaScript
    $fac_approved_json = json_encode($approved_by_date, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
    $fac_pending_json  = json_encode($pending_by_date,  JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
    $fac_pencil_json   = json_encode($pencil_by_date,   JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
    ?>
    <script>
    (function () {
        'use strict';

        /* ── Raw data from PHP ───────────────────────────────────────────── */
        var approvedData = <?= $fac_approved_json ?>;
        var pendingData  = <?= $fac_pending_json ?>;
        var pencilData   = <?= $fac_pencil_json ?>;

        /* ── Render dots on every date cell ─────────────────────────────── */
        function renderFacilityDots() {
            var showA = document.getElementById('filterApproved').checked;
            var showP = document.getElementById('filterPending').checked;
            var showC = document.getElementById('filterPencil').checked;

            var allDates = new Set([
                ...Object.keys(approvedData),
                ...Object.keys(pendingData),
                ...Object.keys(pencilData)
            ]);

            allDates.forEach(function (date) {
                var dotsEl = document.getElementById('fdots-' + date);
                var abEl   = document.getElementById('fab-'   + date);
                var pbEl   = document.getElementById('fpb-'   + date);
                var cbEl   = document.getElementById('fcb-'   + date);
                if (!dotsEl) return;

                dotsEl.innerHTML = '';
                var aC = (approvedData[date] || []).length;
                var pC = (pendingData[date]  || []).length;
                var cC = (pencilData[date]   || []).length;

                if (showA && aC > 0) {
                    abEl.textContent = aC; abEl.style.display = 'inline-block';
                    for (var i = 0; i < Math.min(aC, 5); i++) {
                        var d = document.createElement('span');
                        d.className = 'event-dot approved-dot';
                        d.title = (approvedData[date][i] || {}).activity_name || '';
                        dotsEl.appendChild(d);
                    }
                } else { abEl.style.display = 'none'; }

                if (showP && pC > 0) {
                    pbEl.textContent = pC; pbEl.style.display = 'inline-block';
                    for (var j = 0; j < Math.min(pC, 5); j++) {
                        var d2 = document.createElement('span');
                        d2.className = 'event-dot pending-dot';
                        d2.title = (pendingData[date][j] || {}).activity_name || '';
                        dotsEl.appendChild(d2);
                    }
                } else { pbEl.style.display = 'none'; }

                if (showC && cC > 0) {
                    cbEl.textContent = cC; cbEl.style.display = 'inline-block';
                    for (var k = 0; k < Math.min(cC, 5); k++) {
                        var d3 = document.createElement('span');
                        d3.className = 'event-dot pencil-dot';
                        d3.title = (pencilData[date][k] || {}).activity_name || '';
                        dotsEl.appendChild(d3);
                    }
                } else { cbEl.style.display = 'none'; }
            });
        }

        /* ── Open date modal (fetch via AJAX) ────────────────────────────── */
        function showFacilityEvents(date) {
            var showA = document.getElementById('filterApproved').checked;
            var showP = document.getElementById('filterPending').checked;
            var showC = document.getElementById('filterPencil').checked;

            var statuses = [];
            if (showA) statuses.push('approved');
            if (showP) statuses.push('pending');
            if (showC) statuses.push('pencil_booked');

            var statusParam = statuses.length === 0 ? 'none'
                            : statuses.length === 3 ? 'all'
                            : statuses.join(',');

            var opts = { month: 'long', day: 'numeric', year: 'numeric' };
            document.getElementById('facModalDate').textContent =
                new Date(date + 'T00:00:00').toLocaleDateString('en-US', opts);
            document.getElementById('facModalBody').innerHTML =
                '<div class="text-center py-5 text-muted"><div class="spinner-border spinner-border-sm me-2"></div>Loading…</div>';
            document.getElementById('facModal').classList.add('show');

            fetch('ajax/get_date_events.php?date=' + date + '&status=' + statusParam)
                .then(function (r) { return r.text(); })
                .then(function (html) {
                    document.getElementById('facModalBody').innerHTML = html;
                })
                .catch(function () {
                    document.getElementById('facModalBody').innerHTML =
                        '<div class="text-center py-4 text-danger"><i class="bi bi-exclamation-triangle fs-2 d-block mb-2"></i>Could not load events. Please try again.</div>';
                });
        }

        function closeFacModal() {
            document.getElementById('facModal').classList.remove('show');
        }

        /* ── Keyboard + backdrop close ───────────────────────────────────── */
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeFacModal();
        });
        document.getElementById('facModal').addEventListener('click', function (e) {
            if (e.target === this) closeFacModal();
        });

        /* ── Expose for inline onclick ───────────────────────────────────── */
        window.renderFacilityDots   = renderFacilityDots;
        window.showFacilityEvents   = showFacilityEvents;
        window.closeFacModal        = closeFacModal;

        /* ── Init ────────────────────────────────────────────────────────── */
        renderFacilityDots();
    }());
    </script>

    <?php else: /* ─── GUEST ROOM CALENDAR ─────────────────────────────── */ ?>

    <!-- ════════════════════════════════════════════════════════════════════
         GUEST ROOM CALENDAR
         ════════════════════════════════════════════════════════════════════ -->

    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-info"><div class="stat-title">Total This Month</div><div class="stat-number"><?= $gTotal ?></div></div>
            <i class="bi bi-door-open stat-icon"></i>
        </div>
        <div class="stat-card">
            <div class="stat-info"><div class="stat-title">Confirmed</div><div class="stat-number"><?= $gConfirmed ?></div></div>
            <i class="bi bi-check-circle stat-icon"></i>
        </div>
        <div class="stat-card" style="border-left-color:#ffc107;">
            <div class="stat-info"><div class="stat-title">Pending</div><div class="stat-number"><?= $gPending ?></div></div>
            <i class="bi bi-hourglass-split stat-icon" style="color:#ffc107;"></i>
        </div>
        <div class="stat-card" style="border-left-color:#dc3545;">
            <div class="stat-info"><div class="stat-title">Cancelled</div><div class="stat-number"><?= $gCancelled ?></div></div>
            <i class="bi bi-x-circle stat-icon" style="color:#dc3545;"></i>
        </div>
    </div>

    <!-- Quick link to list view -->
    <div style="margin-bottom:1rem;text-align:right;">
        <a href="guest_reservations.php" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-list-ul me-1"></i>View List
        </a>
    </div>

    <!-- Calendar -->
    <div class="calendar-wrapper">
        <div class="calendar-header">
            <div class="calendar-title"><h2><?= $month_name ?> <?= $year ?></h2></div>
            <div class="calendar-nav">
                <a href="?view=guest&month=<?= $prev_month ?>&year=<?= $prev_year ?>" class="calendar-nav-btn" title="Previous month"><i class="bi bi-chevron-left"></i></a>
                <a href="?view=guest&month=<?= date('m') ?>&year=<?= date('Y') ?>" class="calendar-nav-btn" title="Today"><i class="bi bi-calendar3"></i></a>
                <a href="?view=guest&month=<?= $next_month ?>&year=<?= $next_year ?>" class="calendar-nav-btn" title="Next month"><i class="bi bi-chevron-right"></i></a>
            </div>
        </div>

        <!-- Filters -->
        <div class="calendar-filters">
            <span class="filter-label">Show:</span>
            <label class="filter-checkbox-wrap">
                <input type="checkbox" id="filterGuestConfirmed" checked onchange="renderGuestDots()">
                <span class="filter-dot confirmed"></span>
                <span class="filter-text">Confirmed</span>
            </label>
            <label class="filter-checkbox-wrap">
                <input type="checkbox" id="filterGuestPending" checked onchange="renderGuestDots()">
                <span class="filter-dot pending"></span>
                <span class="filter-text">Pending</span>
            </label>
            <!-- Legend -->
            <div style="margin-left:auto;display:flex;gap:0.9rem;align-items:center;font-size:0.78rem;color:#6c757d;">
                <span><i class="bi bi-box-arrow-in-right" style="color:#28a745;"></i> Check-in</span>
                <span><i class="bi bi-box-arrow-right"    style="color:#dc3545;"></i> Check-out</span>
                <span><i class="bi bi-moon-stars"          style="color:#004085;"></i> Staying</span>
            </div>
        </div>

        <!-- Weekday headers -->
        <div class="calendar-grid" style="margin-bottom:4px;">
            <?php foreach (['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $wd): ?>
            <div class="calendar-weekday"><?= $wd ?></div>
            <?php endforeach; ?>
        </div>

        <!-- Day cells -->
        <div class="calendar-grid" id="guestCalGrid">
            <?php
            for ($i = 0; $i < $day_of_week; $i++) {
                echo '<div class="calendar-day other-month"></div>';
            }
            for ($day = 1; $day <= $days_in_month; $day++) {
                $date = sprintf('%04d-%02d-%02d', $year, $month, $day);
                $cls  = ($date === $today) ? 'today' : '';
                echo '<div class="calendar-day '.$cls.'" data-date="'.$date.'" onclick="showGuestEvents(\''.$date.'\')">';
                echo  '<div class="day-number">'.$day;
                echo   '<span class="event-badges">';
                echo    '<span class="event-count-badge confirmed-badge" id="gcb-'.$date.'"></span>';
                echo    '<span class="event-count-badge gp-badge"        id="gpb-'.$date.'"></span>';
                echo   '</span>';
                echo  '</div>';
                echo  '<div class="event-indicator" id="gdots-'.$date.'"></div>';
                echo '</div>';
            }
            $pad = 42 - ($day_of_week + $days_in_month);
            for ($i = 0; $i < $pad; $i++) {
                echo '<div class="calendar-day other-month"></div>';
            }
            ?>
        </div>
    </div><!-- .calendar-wrapper -->

    <!-- Guest Modal -->
    <div class="event-details-modal" id="guestModal">
        <div class="modal-card">
            <div class="modal-header">
                <h3><i class="bi bi-door-open me-2"></i>Guest Stays on <span id="guestModalDate"></span></h3>
                <button class="modal-close" onclick="closeGuestModal()" aria-label="Close">&times;</button>
            </div>
            <div class="modal-body" id="guestModalBody"></div>
        </div>
    </div>

    <?php
    $guest_events_json = json_encode($guest_by_date, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
    ?>
    <script>
    (function () {
        'use strict';

        var guestData = <?= $guest_events_json ?>;

        /* Deduplicate events on a date by reservation id + event_type priority:
           If same id appears as checkin AND stay (straddles month boundary), keep checkin.
           Priority: checkin > checkout > stay */
        var TYPE_PRIO = { checkin: 0, checkout: 1, stay: 2 };

        function dedupeForDate(events) {
            var best = {};
            events.forEach(function (ev) {
                var id = ev.id;
                if (!best[id] || TYPE_PRIO[ev.event_type] < TYPE_PRIO[best[id].event_type]) {
                    best[id] = ev;
                }
            });
            return Object.values(best);
        }

        /* ── Render dots ─────────────────────────────────────────────────── */
        function renderGuestDots() {
            var showC = document.getElementById('filterGuestConfirmed').checked;
            var showP = document.getElementById('filterGuestPending').checked;

            Object.keys(guestData).forEach(function (date) {
                var dotsEl = document.getElementById('gdots-' + date);
                var cBadge = document.getElementById('gcb-'   + date);
                var pBadge = document.getElementById('gpb-'   + date);
                if (!dotsEl) return;

                dotsEl.innerHTML = '';
                var evs   = dedupeForDate(guestData[date] || []);
                var cEvs  = evs.filter(function (e) { return (e.status === 'confirmed' || e.status === 'checked_in'); });
                var pEvs  = evs.filter(function (e) { return e.status === 'pending';   });

                /* badge counts */
                if (showC && cEvs.length) {
                    cBadge.textContent = cEvs.length; cBadge.style.display = 'inline-block';
                } else { cBadge.style.display = 'none'; }

                if (showP && pEvs.length) {
                    pBadge.textContent = pEvs.length; pBadge.style.display = 'inline-block';
                } else { pBadge.style.display = 'none'; }

                /* dots – show up to 4, one per unique reservation, filtered by status */
                var shown = [];
                if (showC) shown = shown.concat(cEvs.slice(0, 4));
                if (showP) shown = shown.concat(pEvs.slice(0, Math.max(0, 4 - shown.length)));

                shown.forEach(function (ev) {
                    var dot = document.createElement('span');
                    dot.className = 'event-dot';
                    dot.style.background = (ev.status === 'confirmed' || ev.status === 'checked_in') ? '#28a745' : '#ffc107';
                    if (ev.status === 'pending') dot.style.border = '1px solid #e0a800';
                    dot.title = (ev.guest_name || '') + ' – ' + ev.room_name;
                    dotsEl.appendChild(dot);
                });
            });
        }

        /* ── Open guest modal ────────────────────────────────────────────── */
        function showGuestEvents(date) {
            var showC = document.getElementById('filterGuestConfirmed').checked;
            var showP = document.getElementById('filterGuestPending').checked;

            var opts = { month: 'long', day: 'numeric', year: 'numeric' };
            document.getElementById('guestModalDate').textContent =
                new Date(date + 'T00:00:00').toLocaleDateString('en-US', opts);

            var raw = guestData[date] || [];
            var evs = dedupeForDate(raw).filter(function (e) {
                var isConfirmedLike = (e.status === 'confirmed' || e.status === 'checked_in');
                return (showC && isConfirmedLike) ||
                       (showP && e.status === 'pending');
            });

            var body = '';
            if (evs.length === 0) {
                body = '<div class="text-center py-5 text-muted"><i class="bi bi-calendar-x fs-1 d-block mb-2"></i>No guest stays visible on this day.<br><small>Try enabling more filters.</small></div>';
            } else {
                /* Sort: checkin first, then checkout, then staying */
                evs.sort(function (a, b) {
                    return TYPE_PRIO[a.event_type] - TYPE_PRIO[b.event_type];
                });

                evs.forEach(function (ev) {
                    var isConfirmedLike = (ev.status === 'confirmed' || ev.status === 'checked_in');
                    var statusClass  = isConfirmedLike ? 'confirmed' : 'pending';
                    var pillClass    = isConfirmedLike ? 'pill-approved' : 'pill-pending';
                    var typeLabel    = ev.event_type === 'checkin'  ? '<i class="bi bi-box-arrow-in-right"></i> Check-in'
                                    : ev.event_type === 'checkout' ? '<i class="bi bi-box-arrow-right"></i> Check-out'
                                    :                                '<i class="bi bi-moon-stars"></i> Staying';
                    var typeClass    = ev.event_type === 'checkin'  ? 'stay-checkin'
                                    : ev.event_type === 'checkout' ? 'stay-checkout'
                                    :                                'stay-stay';
                    var guestFull    = esc(ev.guest_name || '(No name)');
                    var nightsDiff   = Math.round((new Date(ev.departure_date) - new Date(ev.arrival_date)) / 86400000);
                    var priceStr     = ev.total_price ? '₱' + parseFloat(ev.total_price).toLocaleString('en-PH', {minimumFractionDigits:2}) : '';

                    body += '<div class="event-card ' + statusClass + '">' +
                        '<div class="event-header">' +
                            '<span class="event-title">' + guestFull + '</span>' +
                            '<span class="event-status-pill ' + pillClass + '">' + ev.status.toUpperCase() + '</span>' +
                        '</div>' +
                        '<span class="stay-type-badge ' + typeClass + '">' + typeLabel + '</span>' +
                        '<div class="event-details">' +
                            '<div class="event-detail-item"><i class="bi bi-door-open"></i><span>' + esc(ev.room_name) + ' · ' + esc(ev.room_floor) + '</span></div>' +
                            '<div class="event-detail-item"><i class="bi bi-calendar2-check"></i><span>Check-in: ' + fmtDate(ev.arrival_date) + '</span></div>' +
                            '<div class="event-detail-item"><i class="bi bi-calendar2-x"></i><span>Check-out: ' + fmtDate(ev.departure_date) + '</span></div>' +
                            '<div class="event-detail-item"><i class="bi bi-moon-stars"></i><span>' + nightsDiff + ' night' + (nightsDiff !== 1 ? 's' : '') + '</span></div>' +
                            '<div class="event-detail-item"><i class="bi bi-people-fill"></i><span>' + (ev.num_guests || 1) + ' guest(s)</span></div>' +
                            (priceStr ? '<div class="event-detail-item"><i class="bi bi-cash"></i><span>' + priceStr + '</span></div>' : '') +
                        '</div>' +
                        '<div class="view-link"><a href="guest_reservation_details.php?id=' + ev.id + '">View Details <i class="bi bi-arrow-right"></i></a></div>' +
                    '</div>';
                });
            }

            document.getElementById('guestModalBody').innerHTML = body;
            document.getElementById('guestModal').classList.add('show');
        }

        function closeGuestModal() {
            document.getElementById('guestModal').classList.remove('show');
        }

        function esc(str) {
            if (!str) return '';
            var d = document.createElement('div');
            d.appendChild(document.createTextNode(String(str)));
            return d.innerHTML;
        }

        function fmtDate(ds) {
            if (!ds) return '—';
            var d = new Date(ds + 'T00:00:00');
            return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
        }

        /* ── Keyboard + backdrop close ───────────────────────────────────── */
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeGuestModal();
        });
        document.getElementById('guestModal').addEventListener('click', function (e) {
            if (e.target === this) closeGuestModal();
        });

        /* ── Expose for inline onclick ───────────────────────────────────── */
        window.renderGuestDots  = renderGuestDots;
        window.showGuestEvents  = showGuestEvents;
        window.closeGuestModal  = closeGuestModal;

        /* ── Init ────────────────────────────────────────────────────────── */
        renderGuestDots();
    }());
    </script>

    <?php endif; /* end view === 'guest' */ ?>

</div><!-- .content-area -->

<?php require_once __DIR__ . '/inc/footer.php'; ?>