<?php
$pageTitle = 'Reservations Calendar';
require_once __DIR__ . '/inc/header.php';

$month = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('m');
$year  = isset($_GET['year'])  ? (int)$_GET['year']  : (int)date('Y');

$prev_month = $month == 1  ? 12 : $month - 1;
$prev_year  = $month == 1  ? $year - 1 : $year;
$next_month = $month == 12 ? 1  : $month + 1;
$next_year  = $month == 12 ? $year + 1 : $year;

$start_date = "$year-$month-01";
$end_date   = date('Y-m-t', strtotime($start_date));

// ---------------------------------------------------------------
// Build events_by_date for approved, pending, and pencil_booked
// Each entry: { id, activity_name, status, ... }
// ---------------------------------------------------------------
function fetchEventsByDate($conn, $start_date, $end_date, $status) {
    $events = [];
    $seen   = [];

    // Source 1: primary start_datetime
    $s1 = $conn->prepare("
        SELECT r.id, r.activity_name, r.status,
               r.start_datetime, r.end_datetime,
               v.name as venue_name, v.floor,
               CONCAT(r.last_name, ', ', r.first_name) as requester,
               DATE(r.start_datetime) as event_date
        FROM facility_reservations r
        JOIN venues v ON r.venue_id = v.id
        WHERE r.status = ?
          AND DATE(r.start_datetime) BETWEEN ? AND ?
        ORDER BY r.start_datetime ASC
    ");
    $s1->bind_param("sss", $status, $start_date, $end_date);
    $s1->execute();
    $r1 = $s1->get_result();
    while ($row = $r1->fetch_assoc()) {
        $date = $row['event_date'];
        if (!isset($events[$date])) $events[$date] = [];
        $events[$date][] = $row;
        $seen[$date][$row['id']] = true;
    }
    $s1->close();

    // Source 2: reservation_venues (multi-date bookings)
    $s2 = $conn->prepare("
        SELECT r.id, r.activity_name, r.status,
               r.start_datetime, r.end_datetime,
               v.name as venue_name, v.floor,
               CONCAT(r.last_name, ', ', r.first_name) as requester,
               DATE(rv.start_datetime) as event_date
        FROM reservation_venues rv
        JOIN facility_reservations r ON rv.reservation_id = r.id
        JOIN venues v ON rv.venue_id = v.id
        WHERE r.status = ?
          AND DATE(rv.start_datetime) BETWEEN ? AND ?
        ORDER BY rv.start_datetime ASC
    ");
    $s2->bind_param("sss", $status, $start_date, $end_date);
    $s2->execute();
    $r2 = $s2->get_result();
    while ($row = $r2->fetch_assoc()) {
        $date = $row['event_date'];
        if (!isset($events[$date])) $events[$date] = [];
        if (empty($seen[$date][$row['id']])) {
            $events[$date][] = $row;
            $seen[$date][$row['id']] = true;
        }
    }
    $s2->close();

    return $events;
}

$approved_by_date = fetchEventsByDate($conn, $start_date, $end_date, 'approved');
$pending_by_date  = fetchEventsByDate($conn, $start_date, $end_date, 'pending');
$pencil_by_date   = fetchEventsByDate($conn, $start_date, $end_date, 'pencil_booked');

$pending_count = $conn->query("SELECT COUNT(*) as c FROM facility_reservations WHERE status = 'pending'")->fetch_assoc()['c'];
$pencil_count = $conn->query("SELECT COUNT(*) as c FROM facility_reservations WHERE status = 'pencil_booked'")->fetch_assoc()['c'];

$month_names = [
    1=>'January',2=>'February',3=>'March',4=>'April',
    5=>'May',6=>'June',7=>'July',8=>'August',
    9=>'September',10=>'October',11=>'November',12=>'December'
];
$month_name    = $month_names[$month];
$first_day     = mktime(0,0,0,$month,1,$year);
$days_in_month = date('t', $first_day);
$day_of_week   = date('w', $first_day);
$today         = date('Y-m-d');
?>

<style>
:root {
    --calendar-bg: #ffffff;
    --calendar-border: #e9ecef;
    --bsu-red: #b71c1c;
    --bsu-red-dark: #8b0000;
    --pencil-purple: #5e3c8b;
    --pencil-light: #e2d5f1;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
    margin-bottom: 1.5rem;
}
.stat-card {
    background: white; border-radius: 12px; padding: 1rem 1.25rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.03); border-left: 4px solid var(--bsu-red);
    transition: all 0.2s ease; display: flex; align-items: center; justify-content: space-between;
}
.stat-card:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(183,28,28,0.1); }
.stat-info { flex: 1; }
.stat-title { color: #6c757d; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.3px; margin-bottom: 0.25rem; }
.stat-number { font-size: 1.5rem; font-weight: 700; color: #212529; line-height: 1.2; }
.stat-icon { font-size: 1.8rem; color: var(--bsu-red); opacity: 0.3; }

.pending-banner {
    background: linear-gradient(135deg,#fff3cd,#ffeaa7); border-radius: 12px;
    padding: 1rem 1.5rem; margin-bottom: 1.5rem;
    display: flex; justify-content: space-between; align-items: center;
    border-left: 4px solid #ffc107;
}
.pending-banner .banner-content { display: flex; align-items: center; gap: 1rem; }
.pending-banner .banner-icon { width:40px;height:40px;background:white;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#ffc107;font-size:1.2rem; }
.pending-banner .banner-text h3 { font-size:1rem;margin:0;color:#856404; }
.pending-banner .banner-text p  { margin:0;color:#856404;font-size:0.85rem;opacity:0.8; }

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
.calendar-title h2 { font-size:1.5rem;font-weight:600;color:#212529;margin:0; }
.calendar-nav { display:flex;gap:0.5rem; }
.calendar-nav-btn {
    width:36px;height:36px;border-radius:8px;border:1px solid #dee2e6;
    background:white;color:#495057;font-size:1rem;cursor:pointer;
    transition:all 0.2s ease;display:flex;align-items:center;justify-content:center;
    text-decoration:none;
}
.calendar-nav-btn:hover { border-color:var(--bsu-red);color:var(--bsu-red); }

/* Filter bar */
.calendar-filters {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    margin-bottom: 1.25rem;
    padding: 0.75rem 1rem;
    background: #f8f9fa;
    border-radius: 10px;
    border: 1px solid #e9ecef;
    flex-wrap: wrap;
}
.filter-label {
    font-size: 0.8rem;
    font-weight: 600;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    margin-right: 0.25rem;
}
.filter-checkbox-wrap {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    user-select: none;
}
.filter-checkbox-wrap input[type="checkbox"] {
    width: 16px;
    height: 16px;
    cursor: pointer;
    accent-color: var(--bsu-red);
}
.filter-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    display: inline-block;
    flex-shrink: 0;
}
.filter-dot.approved { background: #28a745; }
.filter-dot.pending  { background: #ffc107; border: 1px solid #e0a800; }
.filter-dot.pencil   { background: #5e3c8b; }
.filter-text {
    font-size: 0.875rem;
    color: #495057;
    font-weight: 500;
}

.calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 6px;
}
.calendar-weekday {
    text-align:center;padding:0.5rem;font-weight:600;
    color:#6c757d;font-size:0.85rem;text-transform:uppercase;
}
.calendar-day {
    background:white;border:1px solid #e9ecef;border-radius:10px;
    min-height:85px;padding:0.5rem;transition:all 0.2s ease;cursor:pointer;position:relative;
}
.calendar-day:hover { border-color:var(--bsu-red);background:#fff8f8;transform:translateY(-1px);box-shadow:0 4px 8px rgba(183,28,28,0.1); }
.calendar-day.today { border-color:var(--bsu-red);background:#fff8f8;box-shadow:0 0 0 2px rgba(183,28,28,0.1); }
.calendar-day.other-month { background:#f8f9fa;color:#adb5bd;opacity:0.6; }
.day-number { font-size:0.9rem;font-weight:600;color:#495057;margin-bottom:0.25rem;display:flex;justify-content:space-between;align-items:center;gap:2px; }
.event-badges { display:flex;gap:3px;flex-wrap:wrap; }
.event-count-badge {
    font-size: 0.65rem;
    padding: 0.1rem 0.35rem;
    border-radius: 20px;
    font-weight: 600;
    line-height: 1.4;
    display: none; /* shown by JS */
}
.event-count-badge.approved-badge { background: #d4edda; color: #155724; }
.event-count-badge.pending-badge  { background: #fff3cd; color: #856404; }
.event-count-badge.pencil-badge   { background: #e2d5f1; color: #5e3c8b; }
.event-indicator { display:flex;flex-wrap:wrap;gap:2px;margin-top:0.3rem; }
.event-dot { width:7px;height:7px;border-radius:50%; }
.event-dot.approved-dot { background:#28a745; }
.event-dot.pending-dot  { background:#ffc107;border:1px solid #e0a800; }
.event-dot.pencil-dot   { background:#5e3c8b; }

/* Modal */
.event-details-modal {
    position:fixed;inset:0;background:rgba(0,0,0,0.5);
    display:none;align-items:center;justify-content:center;
    z-index:9999;backdrop-filter:blur(4px);
}
.event-details-modal.show { display:flex; }
.modal-card {
    background:white;border-radius:20px;width:90%;max-width:620px;
    max-height:85vh;overflow-y:auto;
    box-shadow:0 20px 40px rgba(0,0,0,0.2);animation:slideUp 0.3s ease;
}
@keyframes slideUp { from{transform:translateY(30px);opacity:0} to{transform:translateY(0);opacity:1} }
.modal-header {
    padding:1.25rem 1.5rem;
    background:linear-gradient(135deg,var(--bsu-red),var(--bsu-red-dark));
    color:white;display:flex;justify-content:space-between;align-items:center;
    border-radius:20px 20px 0 0;
}
.modal-header h3 { margin:0;font-size:1.2rem;font-weight:600; }
.modal-close { background:none;border:none;color:white;font-size:1.5rem;cursor:pointer;opacity:0.8;transition:opacity 0.2s; }
.modal-close:hover { opacity:1; }
.modal-body { padding:1.5rem; }

/* Event cards in modal */
.event-card {
    border:1px solid #e9ecef;border-radius:12px;padding:1rem;
    margin-bottom:1rem;transition:all 0.2s ease;border-left:4px solid #28a745;
}
.event-card.pending  { border-left-color:#ffc107; }
.event-card.approved { border-left-color:#28a745; }
.event-card.pencil_booked { border-left-color:#5e3c8b; }
.event-card.denied   { border-left-color:#dc3545; }
.event-card:hover { box-shadow:0 5px 15px rgba(0,0,0,0.05);transform:translateY(-2px); }
.event-header { display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:0.5rem;gap:0.5rem; }
.event-title { font-weight:600;color:#212529;font-size:1rem; }
.event-status-pill {
    font-size:0.7rem;font-weight:600;padding:0.2rem 0.6rem;
    border-radius:20px;white-space:nowrap;flex-shrink:0;
}
.pill-approved { background:#d4edda;color:#155724; }
.pill-pending  { background:#fff3cd;color:#856404; }
.pill-pencil   { background:#e2d5f1;color:#5e3c8b; }
.event-time { font-size:0.85rem;color:#6c757d;margin-bottom:0.5rem; }
.event-details { display:grid;grid-template-columns:repeat(2,1fr);gap:0.5rem;font-size:0.85rem; }
.event-detail-item { display:flex;align-items:center;gap:0.5rem;color:#495057; }
.event-detail-item i { color:var(--bsu-red);font-size:0.9rem;width:18px; }
.view-link { margin-top:0.75rem;text-align:right; }
.view-link a { color:var(--bsu-red);text-decoration:none;font-weight:500;font-size:0.85rem; }
.view-link a:hover { text-decoration:underline; }

/* Responsive */
@media(max-width:768px){
    .stats-grid{grid-template-columns:repeat(2,1fr);}
    .calendar-day{min-height:70px;padding:0.35rem;}
    .calendar-weekday{font-size:0.75rem;padding:0.35rem;}
    .modal-card{width:95%;}
    .calendar-filters{gap:0.75rem;}
}
@media(max-width:576px){
    .stats-grid{grid-template-columns:1fr;}
    .calendar-header{flex-direction:column;gap:0.75rem;align-items:flex-start;}
}
</style>

<?php
// Encode event data as JSON for JS consumption
$approved_json = json_encode($approved_by_date);
$pending_json  = json_encode($pending_by_date);
$pencil_json   = json_encode($pencil_by_date);
?>

<div class="content-area">
    <?php if ($pending_count > 0 || $pencil_count > 0): ?>
    <div class="pending-banner">
        <div class="banner-content">
            <div class="banner-icon"><i class="bi bi-bell"></i></div>
            <div class="banner-text">
                <h3>You have <?= $pending_count ?> pending and <?= $pencil_count ?> pencil booked reservation<?= ($pending_count + $pencil_count) > 1 ? 's' : '' ?></h3>
                <p>Click to review</p>
            </div>
        </div>
        <a href="reservations_pending.php?status=pending" class="btn btn-sm btn-primary">Review Now</a>
    </div>
    <?php endif; ?>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <?php
        $total_month        = $conn->query("SELECT COUNT(*) as c FROM facility_reservations WHERE MONTH(start_datetime)=$month AND YEAR(start_datetime)=$year")->fetch_assoc()['c'];
        $approved_month     = $conn->query("SELECT COUNT(*) as c FROM facility_reservations WHERE status='approved' AND MONTH(start_datetime)=$month AND YEAR(start_datetime)=$year")->fetch_assoc()['c'];
        $pencil_month       = $conn->query("SELECT COUNT(*) as c FROM facility_reservations WHERE status='pencil_booked' AND MONTH(start_datetime)=$month AND YEAR(start_datetime)=$year")->fetch_assoc()['c'];
        $pending_month      = $conn->query("SELECT COUNT(*) as c FROM facility_reservations WHERE status='pending' AND MONTH(start_datetime)=$month AND YEAR(start_datetime)=$year")->fetch_assoc()['c'];
        $unique_venues      = $conn->query("SELECT COUNT(DISTINCT venue_id) as c FROM facility_reservations WHERE MONTH(start_datetime)=$month AND YEAR(start_datetime)=$year")->fetch_assoc()['c'];
        $total_participants = $conn->query("SELECT SUM(participants_count) as c FROM facility_reservations WHERE MONTH(start_datetime)=$month AND YEAR(start_datetime)=$year")->fetch_assoc()['c'];
        ?>
        <div class="stat-card">
            <div class="stat-info"><div class="stat-title">Total This Month</div><div class="stat-number"><?= $total_month ?></div></div>
            <i class="bi bi-calendar-event stat-icon"></i>
        </div>
        <div class="stat-card">
            <div class="stat-info"><div class="stat-title">Approved</div><div class="stat-number"><?= $approved_month ?></div></div>
            <i class="bi bi-check-circle stat-icon"></i>
        </div>
        <div class="stat-card">
            <div class="stat-info"><div class="stat-title">Pencil</div><div class="stat-number"><?= $pencil_month ?></div></div>
            <i class="bi bi-pencil stat-icon"></i>
        </div>
        <div class="stat-card">
            <div class="stat-info"><div class="stat-title">Pending</div><div class="stat-number"><?= $pending_month ?></div></div>
            <i class="bi bi-hourglass-split stat-icon"></i>
        </div>
    </div>

    <!-- Calendar Wrapper -->
    <div class="calendar-wrapper">
        <div class="calendar-header">
            <div class="calendar-title"><h2><?= $month_name ?> <?= $year ?></h2></div>
            <div class="calendar-nav">
                <a href="?month=<?= $prev_month ?>&year=<?= $prev_year ?>" class="calendar-nav-btn"><i class="bi bi-chevron-left"></i></a>
                <a href="?month=<?= date('m') ?>&year=<?= date('Y') ?>" class="calendar-nav-btn"><i class="bi bi-calendar3"></i></a>
                <a href="?month=<?= $next_month ?>&year=<?= $next_year ?>" class="calendar-nav-btn"><i class="bi bi-chevron-right"></i></a>
            </div>
        </div>

        <!-- Filter bar - All checkboxes checked by default -->
        <div class="calendar-filters">
            <span class="filter-label">Show:</span>
            <label class="filter-checkbox-wrap">
                <input type="checkbox" id="filterApproved" checked onchange="renderCalendarDots()">
                <span class="filter-dot approved"></span>
                <span class="filter-text">Approved</span>
            </label>
            <label class="filter-checkbox-wrap">
                <input type="checkbox" id="filterPending" checked onchange="renderCalendarDots()">
                <span class="filter-dot pending"></span>
                <span class="filter-text">Pending</span>
            </label>
            <label class="filter-checkbox-wrap">
                <input type="checkbox" id="filterPencil" checked onchange="renderCalendarDots()">
                <span class="filter-dot pencil"></span>
                <span class="filter-text">Pencil Booked</span>
            </label>
        </div>

        <!-- Weekday Headers -->
        <div class="calendar-grid">
            <?php foreach (['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $d): ?>
            <div class="calendar-weekday"><?= $d ?></div>
            <?php endforeach; ?>
        </div>

        <!-- Calendar Days -->
        <div class="calendar-grid" id="calendarGrid">
            <?php
            for ($i = 0; $i < $day_of_week; $i++) {
                echo '<div class="calendar-day other-month"></div>';
            }
            for ($day = 1; $day <= $days_in_month; $day++) {
                $date      = sprintf("%04d-%02d-%02d", $year, $month, $day);
                $day_class = $date == $today ? 'today' : '';
                echo '<div class="calendar-day ' . $day_class . '" data-date="' . $date . '" onclick="showDateEvents(\'' . $date . '\')">';
                echo '<div class="day-number">' . $day;
                echo '<span class="event-badges">';
                echo '<span class="event-count-badge approved-badge" id="ab-' . $date . '"></span>';
                echo '<span class="event-count-badge pending-badge"  id="pb-' . $date . '"></span>';
                echo '<span class="event-count-badge pencil-badge"   id="cb-' . $date . '"></span>';
                echo '</span>';
                echo '</div>';
                echo '<div class="event-indicator" id="dots-' . $date . '"></div>';
                echo '</div>';
            }
            $remaining = 42 - ($day_of_week + $days_in_month);
            for ($i = 0; $i < $remaining; $i++) {
                echo '<div class="calendar-day other-month"></div>';
            }
            ?>
        </div>
    </div>
</div>

<!-- Date Events Modal -->
<div class="event-details-modal" id="dateModal">
    <div class="modal-card">
        <div class="modal-header">
            <h3><i class="bi bi-calendar-date me-2"></i>Events for <span id="selectedDate"></span></h3>
            <button class="modal-close" onclick="closeDateModal()">&times;</button>
        </div>
        <div class="modal-body" id="dateModalBody"></div>
    </div>
</div>

<script>
var approvedData = <?= $approved_json ?>;
var pendingData  = <?= $pending_json ?>;
var pencilData   = <?= $pencil_json ?>;

function renderCalendarDots() {
    var showApproved = document.getElementById('filterApproved').checked;
    var showPending  = document.getElementById('filterPending').checked;
    var showPencil   = document.getElementById('filterPencil').checked;

    // Collect all dates that appear in either dataset
    var allDates = new Set([
        ...Object.keys(approvedData),
        ...Object.keys(pendingData),
        ...Object.keys(pencilData)
    ]);

    allDates.forEach(function(date) {
        var dotsEl = document.getElementById('dots-' + date);
        var abEl   = document.getElementById('ab-' + date);
        var pbEl   = document.getElementById('pb-' + date);
        var cbEl   = document.getElementById('cb-' + date);
        if (!dotsEl) return;

        dotsEl.innerHTML = '';
        var approvedCount = (approvedData[date] || []).length;
        var pendingCount  = (pendingData[date]  || []).length;
        var pencilCount   = (pencilData[date]   || []).length;

        // Approved badge + dots
        if (showApproved && approvedCount > 0) {
            abEl.textContent = approvedCount + ' approved';
            abEl.style.display = 'inline-block';
            for (var i = 0; i < Math.min(approvedCount, 5); i++) {
                var dot = document.createElement('span');
                dot.className = 'event-dot approved-dot';
                dot.title = (approvedData[date][i] || {}).activity_name || '';
                dotsEl.appendChild(dot);
            }
        } else {
            abEl.style.display = 'none';
        }

        // Pending badge + dots
        if (showPending && pendingCount > 0) {
            pbEl.textContent = pendingCount + ' pending';
            pbEl.style.display = 'inline-block';
            for (var j = 0; j < Math.min(pendingCount, 5); j++) {
                var dot2 = document.createElement('span');
                dot2.className = 'event-dot pending-dot';
                dot2.title = (pendingData[date][j] || {}).activity_name || '';
                dotsEl.appendChild(dot2);
            }
        } else {
            pbEl.style.display = 'none';
        }

        // Pencil badge + dots
        if (showPencil && pencilCount > 0) {
            cbEl.textContent = pencilCount + ' pencil';
            cbEl.style.display = 'inline-block';
            for (var k = 0; k < Math.min(pencilCount, 5); k++) {
                var dot3 = document.createElement('span');
                dot3.className = 'event-dot pencil-dot';
                dot3.title = (pencilData[date][k] || {}).activity_name || '';
                dotsEl.appendChild(dot3);
            }
        } else {
            cbEl.style.display = 'none';
        }
    });
}

function showDateEvents(date) {
    var showApproved = document.getElementById('filterApproved').checked;
    var showPending  = document.getElementById('filterPending').checked;
    var showPencil   = document.getElementById('filterPencil').checked;

    var options = { month:'long', day:'numeric', year:'numeric' };
    document.getElementById('selectedDate').textContent =
        new Date(date + 'T00:00:00').toLocaleDateString('en-US', options);

    var params = '?date=' + date;
    var statuses = [];
    if (showApproved) statuses.push('approved');
    if (showPending) statuses.push('pending');
    if (showPencil) statuses.push('pencil_booked');
    
    if (statuses.length === 0) {
        params += '&status=none';
    } else if (statuses.length === 3) {
        params += '&status=all';
    } else {
        params += '&status=' + statuses.join(',');
    }

    fetch('ajax/get_date_events.php' + params)
        .then(function(r) { return r.text(); })
        .then(function(html) {
            document.getElementById('dateModalBody').innerHTML = html;
            document.getElementById('dateModal').classList.add('show');
        })
        .catch(function() {
            document.getElementById('dateModalBody').innerHTML = '<p class="text-danger">Error loading events.</p>';
            document.getElementById('dateModal').classList.add('show');
        });
}

function closeDateModal() {
    document.getElementById('dateModal').classList.remove('show');
}

document.addEventListener('keydown', function(e) { if (e.key==='Escape') closeDateModal(); });
document.getElementById('dateModal').addEventListener('click', function(e) { if (e.target===this) closeDateModal(); });

// Initial render on page load
renderCalendarDots();
</script>

<?php
if (!file_exists(__DIR__ . '/ajax')) mkdir(__DIR__ . '/ajax', 0777, true);

// Always overwrite get_date_events.php
file_put_contents(__DIR__ . '/ajax/get_date_events.php', '<?php
require_once __DIR__ . \'/../../inc/db_config.php\';
require_once __DIR__ . \'/../inc/auth.php\';

if (!isAdminLoggedIn()) { http_response_code(401); exit; }

$date   = $_GET[\'date\']   ?? \'\';
$status = $_GET[\'status\'] ?? \'approved\';

if (!$date) {
    echo \'<p class="text-muted">No date specified.</p>\';
    exit;
}

// Build status filter
$statuses = [];
if ($status === \'all\')      $statuses = [\'approved\', \'pending\', \'pencil_booked\'];
elseif ($status === \'none\') $statuses = [];
else                          $statuses = explode(\',\', $status);

if (empty($statuses)) {
    echo \'<div class="text-center py-4 text-muted"><i class="bi bi-funnel fs-2"></i><p class="mt-2">No filter selected. Tick a checkbox to see events.</p></div>\';
    exit;
}

$placeholders = implode(\',\', array_fill(0, count($statuses), \'?\'));
$types        = str_repeat(\'s\', count($statuses) + 2); // +2 for the two date params

// UNION: primary date + reservation_venues date
$sql = "
    SELECT r.id, r.booking_no, r.activity_name, r.status,
           r.start_datetime, r.end_datetime,
           r.last_name, r.first_name, r.participants_count,
           r.office_type_id, r.external_office_name,
           v.name as venue_name, v.floor,
           CONCAT(r.last_name, \', \', r.first_name) as requester,
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
           CONCAT(r.last_name, \', \', r.first_name) as requester,
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
$bind_types  = str_repeat(\'s\', count($bind_values));
foreach ($bind_values as $k => $v) $bind_refs[$k] = &$bind_values[$k];
array_unshift($bind_refs, $bind_types);
call_user_func_array([$stmt, \'bind_param\'], $bind_refs);
$stmt->execute();
$result = $stmt->get_result();

// Deduplicate by id
$rows = [];
$seen = [];
while ($row = $result->fetch_assoc()) {
    if (empty($seen[$row[\'id\']])) {
        $rows[] = $row;
        $seen[$row[\'id\']] = true;
    }
}

if (empty($rows)) {
    echo \'<div class="text-center py-5"><i class="bi bi-calendar-x fs-1 text-muted"></i><p class="mt-2">No events scheduled for this day.</p></div>\';
    exit;
}

foreach ($rows as $row):
    $start       = new DateTime($row[\'start_datetime\']);
    $end         = new DateTime($row[\'end_datetime\']);
    $status_class = strtolower($row[\'status\']);
    if ($status_class === \'approved\') $pill_class = \'pill-approved\';
    elseif ($status_class === \'pending\') $pill_class = \'pill-pending\';
    elseif ($status_class === \'pencil_booked\') $pill_class = \'pill-pencil\';
    else $pill_class = \'pill-pending\';
?>
<div class="event-card <?= $status_class ?>">
    <div class="event-header">
        <span class="event-title"><?= htmlspecialchars($row[\'activity_name\']) ?></span>
        <span class="event-status-pill <?= $pill_class ?>"><?= strtoupper(str_replace(\'_\', \' \', $row[\'status\'])) ?></span>
    </div>
    <div class="event-time"><i class="bi bi-clock me-1"></i><?= $start->format(\'g:i A\') ?> – <?= $end->format(\'g:i A\') ?></div>
    <div class="event-details">
        <div class="event-detail-item"><i class="bi bi-building"></i><span><?= htmlspecialchars($row[\'venue_name\'] . \' (\' . $row[\'floor\'] . \')\') ?></span></div>
        <div class="event-detail-item"><i class="bi bi-person"></i><span><?= htmlspecialchars($row[\'requester\']) ?></span></div>
        <div class="event-detail-item"><i class="bi bi-people"></i><span><?= $row[\'participants_count\'] ?> participants</span></div>
        <div class="event-detail-item"><i class="bi bi-briefcase"></i><span><?= htmlspecialchars($row[\'office_type_name\'] ?? \'N/A\') ?><?php if (!empty($row[\'office_name\'])): ?> – <?= htmlspecialchars($row[\'office_name\']) ?><?php endif; ?></span></div>
    </div>
    <div class="view-link"><a href="reservation_details.php?id=<?= $row[\'id\'] ?>">View Details <i class="bi bi-arrow-right"></i></a></div>
</div>
<?php endforeach; ?>
');
?>

<?php require_once __DIR__ . '/inc/footer.php'; ?>