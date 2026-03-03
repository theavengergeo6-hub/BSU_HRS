<?php

$pageTitle = 'Event Calendar';
require_once __DIR__ . '/inc/link.php';
require_once __DIR__ . '/inc/db_config.php';
require_once __DIR__. '/inc/header.php';

$base = rtrim(BASE_URL, '/');

$year = isset($_GET['y']) ? (int)$_GET['y'] : (int)date('Y');
$month = isset($_GET['m']) ? (int)$_GET['m'] : (int)date('n');
if ($month < 1) $month = 1;
if ($month > 12) $month = 12;

// Get filter parameter
$filter_venue = isset($_GET['venue']) ? (int)$_GET['venue'] : 0;

// Default colors array for venues
$default_colors = [
    '#b71c1c', // BSU Red
    '#2e7d32', // Green
    '#1565c0', // Blue
    '#f9a825', // Yellow
    '#6a1b9a', // Purple
    '#c2185b', // Pink
    '#00796b', // Teal
    '#5d4037', // Brown
    '#d32f2f', // Light Red
    '#1976d2', // Light Blue
    '#388e3c', // Light Green
    '#ff6f00'  // Amber
];

// Get all venues
$venue_query = $conn->query("SELECT id, name, floor FROM venues WHERE is_active = 1 ORDER BY name");
$rooms_filter_list = [];
$venues_colors = [];

$color_index = 0;
while ($row = $venue_query->fetch_assoc()) {
    $rooms_filter_list[] = $row;
    // Assign a default color based on index
    $venues_colors[$row['id']] = $default_colors[$color_index % count($default_colors)];
    $color_index++;
}

// Get all approved reservations for the month with their venues
$query = "
    SELECT r.*, 
           v.name as venue_name, 
           v.floor,
           et.name as event_type_name,
           CONCAT(r.first_name, ' ', r.last_name) as requester_name,
           ot.name as office_type_name,
           CASE WHEN r.office_type_id = 4 THEN r.external_office_name ELSE o.name END as office_name
    FROM facility_reservations r
    JOIN venues v ON r.venue_id = v.id
    LEFT JOIN event_types et ON r.event_type_id = et.id
    LEFT JOIN office_types ot ON r.office_type_id = ot.id
    LEFT JOIN offices o ON r.office_id = o.id
    WHERE r.status = 'approved'
    AND MONTH(r.start_datetime) = ? 
    AND YEAR(r.start_datetime) = ?
";

$params = [$month, $year];
$types = "ii";

if ($filter_venue > 0) {
    $query .= " AND r.venue_id = ?";
    $params[] = $filter_venue;
    $types .= "i";
}

$query .= " ORDER BY r.start_datetime ASC";

$stmt = $conn->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$reservations = $stmt->get_result();

// Get all reservation venues from the pivot table for multi-venue bookings
$reservation_ids = [];
$reservations_data = [];

// First pass: store main reservation data
while ($row = $reservations->fetch_assoc()) {
    $reservation_ids[] = $row['id'];
    $reservations_data[$row['id']] = $row;
    // Initialize venues array with main venue
    $reservations_data[$row['id']]['venues'] = [
        [
            'id' => $row['venue_id'],
            'name' => $row['venue_name'],
            'floor' => $row['floor'],
            'color' => $venues_colors[$row['venue_id']] ?? '#b71c1c'
        ]
    ];
}

// If there are reservations, fetch all venues from reservation_venues
if (!empty($reservation_ids)) {
    $ids_string = implode(',', $reservation_ids);
    $venues_query = $conn->query("
        SELECT rv.reservation_id, v.id as venue_id, v.name as venue_name, v.floor
        FROM reservation_venues rv
        JOIN venues v ON rv.venue_id = v.id
        WHERE rv.reservation_id IN ($ids_string)
        ORDER BY rv.start_datetime ASC
    ");
    
    if ($venues_query && $venues_query->num_rows > 0) {
        while ($venue_row = $venues_query->fetch_assoc()) {
            $res_id = $venue_row['reservation_id'];
            // Check if this venue is not already the main venue
            if (isset($reservations_data[$res_id])) {
                $is_main = ($reservations_data[$res_id]['venue_id'] == $venue_row['venue_id']);
                
                if (!$is_main) {
                    // Add to venues array
                    $reservations_data[$res_id]['venues'][] = [
                        'id' => $venue_row['venue_id'],
                        'name' => $venue_row['venue_name'],
                        'floor' => $venue_row['floor'],
                        'color' => $venues_colors[$venue_row['venue_id']] ?? '#b71c1c'
                    ];
                }
            }
        }
    }
}

// Group reservations by date
$events_by_date = [];
foreach ($reservations_data as $res_id => $reservation) {
    if (isset($reservation['start_datetime'])) {
        $date = date('Y-m-d', strtotime($reservation['start_datetime']));
        if (!isset($events_by_date[$date])) {
            $events_by_date[$date] = [];
        }
        $events_by_date[$date][] = $reservation;
    }
}

// Calendar calculations
$month_start = strtotime("$year-$month-01");
$first_dow = (int)date('w', $month_start);
$days_in_month = (int)date('t', $month_start);

$prev_month = $month - 1;
$prev_year = $year;
if ($prev_month < 1) { $prev_month = 12; $prev_year--; }
$next_month = $month + 1;
$next_year = $year;
if ($next_month > 12) { $next_month = 1; $next_year++; }

$month_name = date('F Y', $month_start);
$url_base = $base . '/calendar.php';
$today = date('Y-m-d');
?>

<style>
:root {
    --calendar-bg: #ffffff;
    --calendar-border: #e9ecef;
    --bsu-red: #b71c1c;
    --bsu-red-dark: #8b0000;
}

* {
    box-sizing: border-box;
}

.calendar-page {
    background: #f8f9fa;
    min-height: calc(100vh - 60px);
    padding: 1rem;
}

/* Mobile-First Layout */
.calendar-layout {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    max-width: 1200px;
    margin: 0 auto;
}

/* Sidebar Cards */
.calendar-sidebar {
    width: 100%;
}

.calendar-sidebar .card {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 16px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    padding: 1rem;
    margin-bottom: 1rem;
}

.calendar-sidebar .card-title {
    font-size: 1rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 0.75rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #f0f0f0;
}

/* Mini Calendar */
.cal-mini {
    width: 100%;
    overflow-x: auto;
}

.cal-mini table {
    width: 100%;
    min-width: 280px;
    font-size: 0.85rem;
    border-collapse: collapse;
}

.cal-mini th,
.cal-mini td {
    text-align: center;
    padding: 0.5rem 0.25rem;
}

.cal-mini th {
    color: #666;
    font-weight: 600;
    font-size: 0.75rem;
}

.cal-mini td a {
    color: #333;
    text-decoration: none;
    display: block;
    border-radius: 50%;
    width: 32px;
    height: 32px;
    line-height: 32px;
    margin: 0 auto;
    transition: all 0.2s ease;
}

.cal-mini td a:hover {
    background: #fdeae8;
    color: var(--bsu-red);
}

.cal-mini td.today a {
    background: var(--bsu-red);
    color: white;
}

.cal-mini td.other-month a {
    opacity: 0.4;
}

/* Filter List */
.filter-list {
    max-height: 250px;
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
}

.filter-list label {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    cursor: pointer;
    padding: 0.75rem 0;
    font-size: 0.95rem;
    color: #333;
    border-bottom: 1px solid #f0f0f0;
    -webkit-tap-highlight-color: transparent;
}

.filter-list label:active {
    background: #f8f9fa;
}

.filter-list input[type="radio"] {
    width: 20px;
    height: 20px;
    accent-color: var(--bsu-red);
    margin: 0;
}

.filter-list .dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    flex-shrink: 0;
}

.btn-filter {
    background: var(--bsu-red);
    color: white;
    border: none;
    padding: 0.875rem 1rem;
    border-radius: 12px;
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.2s ease;
    width: 100%;
    margin-top: 1rem;
    -webkit-tap-highlight-color: transparent;
}

.btn-filter:active {
    background: var(--bsu-red-dark);
    transform: scale(0.98);
}

/* Calendar Header */
.calendar-header {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    margin-bottom: 1rem;
}

.calendar-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: #2c3e50;
    margin: 0;
    text-align: center;
}

.calendar-nav {
    display: flex;
    justify-content: center;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.calendar-nav a {
    color: #495057;
    text-decoration: none;
    padding: 0.75rem 1rem;
    border-radius: 12px;
    background: white;
    border: 1px solid #dee2e6;
    transition: all 0.2s ease;
    font-size: 0.9rem;
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    flex: 1;
    justify-content: center;
    min-width: 80px;
    -webkit-tap-highlight-color: transparent;
}

.calendar-nav a:active {
    background: var(--bsu-red);
    color: white;
    border-color: var(--bsu-red);
}

/* Calendar Grid */
.cal-grid {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 16px;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.cal-grid table {
    width: 100%;
    min-width: 700px;
    border-collapse: collapse;
    table-layout: fixed;
}

.cal-grid th {
    background: #f8f9fa;
    color: #666;
    font-size: 0.8rem;
    font-weight: 600;
    padding: 0.75rem 0.5rem;
    text-align: center;
    border: 1px solid #e9ecef;
}

.cal-grid td {
    border: 1px solid #e9ecef;
    vertical-align: top;
    padding: 0.5rem;
    min-height: 100px;
    height: 110px;
    font-size: 0.75rem;
    cursor: pointer;
    transition: background 0.2s ease;
}

.cal-grid td:hover {
    background: #f8f9fa;
}

.cal-grid td.other-month {
    background: #f8f9fa;
    color: #adb5bd;
}

.cal-grid td .day-num {
    font-weight: 600;
    margin-bottom: 0.25rem;
    color: #333;
}

.cal-grid td.today .day-num {
    color: var(--bsu-red);
    font-weight: 700;
}

/* Event Items with Venue Colors */
.event-item {
    display: flex;
    align-items: center;
    gap: 0.3rem;
    margin-bottom: 0.25rem;
    padding: 0.2rem 0.3rem;
    border-radius: 4px;
    background: #f8f9fa;
    transition: all 0.2s ease;
    -webkit-tap-highlight-color: transparent;
    pointer-events: none; /* Prevent clicking on individual events */
}

.event-color-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    flex-shrink: 0;
}

.event-title-preview {
    font-size: 0.7rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    color: #333;
    flex: 1;
}

.event-multi-venues {
    display: flex;
    gap: 0.15rem;
    margin-left: auto;
    padding-left: 0.2rem;
}

.multi-venue-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
}

.cal-more {
    color: var(--bsu-red);
    font-size: 0.7rem;
    font-weight: 600;
    padding: 0.25rem 0;
    text-align: center;
    background: #f8f9fa;
    border-radius: 4px;
    margin-top: 0.2rem;
    pointer-events: none;
}

/* Modal Styles */
.event-details-modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    padding: 1rem;
    backdrop-filter: blur(4px);
}

.event-details-modal.show {
    display: flex;
}

.modal-card {
    background: white;
    border-radius: 24px;
    width: 100%;
    max-width: 600px;
    max-height: 80vh;
    overflow-y: auto;
    box-shadow: 0 20px 40px rgba(0,0,0,0.2);
    animation: slideUp 0.3s ease;
}

@keyframes slideUp {
    from { transform: translateY(30px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

.modal-header {
    padding: 1.25rem 1.5rem;
    background: linear-gradient(135deg, var(--bsu-red), var(--bsu-red-dark));
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-radius: 24px 24px 0 0;
    position: sticky;
    top: 0;
    z-index: 10;
}

.modal-header h3 {
    margin: 0;
    font-size: 1.2rem;
    font-weight: 600;
}

.modal-header h3 i {
    margin-right: 0.5rem;
}

.modal-close {
    background: none;
    border: none;
    color: white;
    font-size: 1.5rem;
    cursor: pointer;
    opacity: 0.8;
    transition: opacity 0.2s;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    -webkit-tap-highlight-color: transparent;
}

.modal-close:hover,
.modal-close:active {
    opacity: 1;
    background: rgba(255,255,255,0.1);
}

.modal-body {
    padding: 1.5rem;
}

/* Day Events List */
/* Day Events List */
.day-events-list {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.day-event-card {
    border: 1px solid #e9ecef;
    border-radius: 16px;
    padding: 1.25rem;
    background: white;
    transition: all 0.2s ease;
}

.day-event-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}

.day-event-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 0.75rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #f0f0f0;
}

.day-event-time {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.95rem;
    color: var(--bsu-red);
    margin-bottom: 0.75rem;
    background: #fdeae8;
    padding: 0.5rem 0.75rem;
    border-radius: 8px;
}

.day-event-time i {
    font-size: 1rem;
}

.day-event-details {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    margin-bottom: 1rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px dashed #e9ecef;
}

.day-event-detail {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
    color: #555;
}

.day-event-detail i {
    width: 20px;
    color: var(--bsu-red);
    font-size: 0.95rem;
}

.day-event-venues {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    margin-top: 0.5rem;
}

.day-event-venue-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.4rem 0.75rem;
    background: #f8f9fa;
    border-radius: 8px;
    font-size: 0.9rem;
}

.venue-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    flex-shrink: 0;
}

.venue-name {
    font-weight: 500;
    color: #2c3e50;
}

.venue-floor {
    font-size: 0.8rem;
    color: #999;
    margin-left: 0.25rem;
}

.no-events-message {
    text-align: center;
    padding: 3rem 1rem;
    color: #999;
    font-style: italic;
}

.no-events-message i {
    font-size: 3rem;
    color: #ddd;
    margin-bottom: 1rem;
}

/* Page Info */
.page-info {
    font-size: 0.8rem;
    color: #999;
    text-align: center;
    margin-top: 1rem;
    padding: 0.5rem;
}

/* Tablet and Desktop */
@media (min-width: 768px) {
    .calendar-page {
        padding: 2rem;
    }
    
    .calendar-layout {
        flex-direction: row;
        align-items: flex-start;
    }
    
    .calendar-sidebar {
        width: 280px;
        flex-shrink: 0;
    }
    
    .calendar-header {
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
    }
    
    .calendar-title {
        text-align: left;
    }
    
    .calendar-nav a {
        flex: none;
        padding: 0.5rem 1rem;
    }
    
    .cal-grid {
        overflow-x: visible;
    }
    
    .cal-grid table {
        min-width: auto;
    }
}

/* Small phones */
@media (max-width: 480px) {
    .calendar-nav {
        flex-direction: column;
    }
    
    .calendar-nav a {
        width: 100%;
    }
    
    .modal-card {
        max-height: 90vh;
    }
    
    .day-event-details {
        font-size: 0.8rem;
    }
}
</style>

<main class="calendar-page">
    <div class="container-fluid px-0">
        <div class="calendar-layout">
            <!-- Sidebar -->
            <aside class="calendar-sidebar">
                <div class="card">
                    <h3 class="card-title">
                        <i class="bi bi-calendar3 me-2"></i><?= date('F Y', $month_start) ?>
                    </h3>
                    <div class="cal-mini">
                        <?php
                        $cur = strtotime('last Sunday of previous month', $month_start);
                        if (date('w', $cur) != 0) $cur = strtotime('last Sunday', $month_start);
                        ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>S</th><th>M</th><th>T</th><th>W</th><th>T</th><th>F</th><th>S</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php for ($row = 0; $row < 6; $row++): ?>
                                <tr>
                                    <?php for ($col = 0; $col < 7; $col++): ?>
                                    <?php
                                    $d = date('Y-m-d', $cur);
                                    $in_month = (date('n', $cur) == $month && date('Y', $cur) == $year);
                                    $is_today = ($d === $today);
                                    $link = $url_base . '?y=' . date('Y', $cur) . '&m=' . date('n', $cur);
                                    ?>
                                    <td class="<?= $in_month ? '' : 'other-month' ?>">
                                        <a href="<?= htmlspecialchars($link) ?>" class="<?= $is_today ? 'today' : '' ?>">
                                            <?= date('j', $cur) ?>
                                        </a>
                                    </td>
                                    <?php $cur = strtotime('+1 day', $cur); endfor; ?>
                                </tr>
                            <?php endfor; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="card">
                    <h3 class="card-title">
                        <i class="bi bi-funnel me-2"></i>Filter by Venue
                    </h3>
                    <form method="get" action="<?= $url_base ?>" id="filterForm">
                        <input type="hidden" name="y" value="<?= $year ?>">
                        <input type="hidden" name="m" value="<?= $month ?>">
                        
                        <div class="filter-list">
                            <label>
                                <input type="radio" name="venue" value="0" <?= $filter_venue == 0 ? 'checked' : '' ?>>
                                <span class="dot" style="background: #6c757d"></span>
                                <span>All Venues</span>
                            </label>
                            <?php foreach ($rooms_filter_list as $r): 
                                $rid = (int)$r['id'];
                                $checked = $filter_venue == $rid ? 'checked' : '';
                                $color = $venues_colors[$rid] ?? '#b71c1c';
                            ?>
                            <label>
                                <input type="radio" name="venue" value="<?= $rid ?>" <?= $checked ?>>
                                <span class="dot" style="background:<?= $color ?>"></span>
                                <span><?= htmlspecialchars($r['name']) ?></span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                        
                        <button type="submit" class="btn-filter">
                            <i class="bi bi-check-circle me-2"></i>Apply Filter
                        </button>
                    </form>
                </div>
                
                <div class="page-info">
                    <i class="bi bi-info-circle me-1"></i> Click on any date to see all events
                </div>
            </aside>

            <!-- Main Calendar -->
            <div class="calendar-main">
                <div class="calendar-header">
                    <h1 class="calendar-title"><?= $month_name ?></h1>
                    <div class="calendar-nav">
                        <a href="<?= $url_base ?>?y=<?= $prev_year ?>&m=<?= $prev_month ?><?= $filter_venue ? '&venue='.$filter_venue : '' ?>">
                            <i class="bi bi-chevron-left"></i> Prev
                        </a>
                        <a href="<?= $url_base ?>?y=<?= date('Y') ?>&m=<?= date('n') ?><?= $filter_venue ? '&venue='.$filter_venue : '' ?>">
                            <i class="bi bi-calendar3"></i> Today
                        </a>
                        <a href="<?= $url_base ?>?y=<?= $next_year ?>&m=<?= $next_month ?><?= $filter_venue ? '&venue='.$filter_venue : '' ?>">
                            Next <i class="bi bi-chevron-right"></i>
                        </a>
                    </div>
                </div>

                <div class="cal-grid">
                    <table>
                        <thead>
                            <tr>
                                <th>Sun</th><th>Mon</th><th>Tue</th><th>Wed</th><th>Thu</th><th>Fri</th><th>Sat</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $day = 1;
                        $total_cells = ceil(($first_dow + $days_in_month) / 7) * 7;
                        $prev_month_days = date('t', strtotime("$year-" . ($month - 1) . "-01"));
                        ?>
                        <tr>
                        <?php for ($i = 0; $i < $total_cells; $i++): ?>
                            <?php
                            if ($i < $first_dow) {
                                $d = $prev_month_days - $first_dow + $i + 1;
                                $cell_date = sprintf('%04d-%02d-%02d', $month == 1 ? $year - 1 : $year, $month == 1 ? 12 : $month - 1, $d);
                                $in_current = false;
                            } elseif ($day <= $days_in_month) {
                                $d = $day;
                                $cell_date = sprintf('%04d-%02d-%02d', $year, $month, $d);
                                $in_current = true;
                                $day++;
                            } else {
                                $d = $day - $days_in_month;
                                $cell_date = sprintf('%04d-%02d-%02d', $month == 12 ? $year + 1 : $year, $month == 12 ? 1 : $month + 1, $d);
                                $in_current = false;
                            }
                            $events = $events_by_date[$cell_date] ?? [];
                            $is_today = ($cell_date === $today);
                            $max_show = 3;
                            $show_events = array_slice($events, 0, $max_show);
                            $more_count = count($events) - $max_show;
                            ?>
                            <td class="<?= $in_current ? '' : 'other-month' ?> <?= $is_today ? 'today' : '' ?>" 
                                onclick="showDayEvents('<?= $cell_date ?>', '<?= date('F j, Y', strtotime($cell_date)) ?>')">
                                <div class="day-num"><?= (int)$d ?></div>
                                <?php foreach ($show_events as $ev): 
                                    $preview_text = strlen($ev['activity_name']) > 20 ? substr($ev['activity_name'], 0, 18) . '...' : $ev['activity_name'];
                                ?>
                                <div class="event-item">
                                    <?php if (count($ev['venues']) == 1): ?>
                                        <span class="event-color-dot" style="background: <?= $ev['venues'][0]['color'] ?>"></span>
                                    <?php else: ?>
                                        <span class="event-color-dot" style="background: <?= $ev['venues'][0]['color'] ?>"></span>
                                        <span class="event-multi-venues">
                                            <?php foreach (array_slice($ev['venues'], 1, 3) as $venue): ?>
                                                <span class="multi-venue-dot" style="background: <?= $venue['color'] ?>"></span>
                                            <?php endforeach; ?>
                                            <?php if (count($ev['venues']) > 4): ?>
                                                <span style="font-size: 0.6rem; color: #666; margin-left: 2px;">+<?= count($ev['venues']) - 4 ?></span>
                                            <?php endif; ?>
                                        </span>
                                    <?php endif; ?>
                                    <span class="event-title-preview"><?= htmlspecialchars($preview_text) ?></span>
                                </div>
                                <?php endforeach; ?>
                                <?php if ($more_count > 0): ?>
                                <div class="cal-more">
                                    +<?= $more_count ?> more
                                </div>
                                <?php endif; ?>
                            </td>
                            <?php if (($i + 1) % 7 === 0 && ($i + 1) < $total_cells): ?></tr><tr><?php endif; ?>
                        <?php endfor; ?>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Day Events Modal -->
<div class="event-details-modal" id="dayEventsModal">
    <div class="modal-card">
        <div class="modal-header">
            <h3><i class="bi bi-calendar-day"></i> Events for <span id="modalDate"></span></h3>
            <button class="modal-close" onclick="closeDayEventsModal()">&times;</button>
        </div>
        <div class="modal-body" id="dayEventsModalBody">
            <!-- Events will be loaded here -->
        </div>
    </div>
</div>

<script>
// Store events data for modal display
var eventsData = <?= json_encode($events_by_date) ?>;

function showDayEvents(date, formattedDate) {
    document.getElementById('modalDate').textContent = formattedDate;
    
    let events = eventsData[date] || [];
    let modalBody = document.getElementById('dayEventsModalBody');
    
    if (events.length === 0) {
        modalBody.innerHTML = '<div class="no-events-message"><i class="bi bi-calendar-x" style="font-size: 3rem; display: block; margin-bottom: 1rem;"></i>No events scheduled for this day.</div>';
    } else {
        // Sort events by start time
        events.sort((a, b) => new Date(a.start_datetime) - new Date(b.start_datetime));
        
        let html = '<div class="day-events-list">';
        
        events.forEach(event => {
            let startTime = new Date(event.start_datetime).toLocaleTimeString('en-US', { 
                hour: 'numeric', 
                minute: '2-digit', 
                hour12: true 
            });
            let endTime = new Date(event.end_datetime).toLocaleTimeString('en-US', { 
                hour: 'numeric', 
                minute: '2-digit', 
                hour12: true 
            });
            
            let officeInfo = event.office_type_name || 'N/A';
            if (event.office_name) {
                officeInfo += ' - ' + event.office_name;
            } else if (event.external_office_name) {
                officeInfo += ' - ' + event.external_office_name;
            }
            
            html += `
                <div class="day-event-card">
                    <div class="day-event-title">${escapeHtml(event.activity_name)}</div>
                    <div class="day-event-time">
                        <i class="bi bi-clock-fill"></i> ${startTime} – ${endTime}
                    </div>
                    <div class="day-event-details">
                        <div class="day-event-detail">
                            <i class="bi bi-tag"></i>
                            <span>${escapeHtml(event.event_type_name || 'N/A')}</span>
                        </div>
                        <div class="day-event-detail">
                            <i class="bi bi-briefcase"></i>
                            <span>${escapeHtml(officeInfo)}</span>
                        </div>
                    </div>
                    <div class="day-event-venues">
            `;
            
            // Display all venues in a clean list
            if (event.venues && event.venues.length > 0) {
                event.venues.forEach((venue, index) => {
                    html += `
                        <div class="day-event-venue-item">
                            <span class="venue-dot" style="background: ${venue.color}"></span>
                            <span class="venue-name">${escapeHtml(venue.name)}</span>
                            <span class="venue-floor">${escapeHtml(venue.floor || '')}</span>
                        </div>
                    `;
                });
            } else {
                // Fallback if no venues array
                html += `
                    <div class="day-event-venue-item">
                        <span class="venue-dot" style="background: #b71c1c"></span>
                        <span class="venue-name">${escapeHtml(event.venue_name)}</span>
                        <span class="venue-floor">${escapeHtml(event.floor || '')}</span>
                    </div>
                `;
            }
            
            html += `
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
        modalBody.innerHTML = html;
    }
    
    document.getElementById('dayEventsModal').classList.add('show');
}

function closeDayEventsModal() {
    document.getElementById('dayEventsModal').classList.remove('show');
}

function escapeHtml(text) {
    if (!text) return '';
    let div = document.createElement('div');
    div.appendChild(document.createTextNode(text));
    return div.innerHTML;
}

// Close modal when clicking outside
document.getElementById('dayEventsModal').addEventListener('click', function(e) {
    if (e.target === this) closeDayEventsModal();
});

// Close on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDayEventsModal();
    }
});

// Prevent body scroll when modal is open
document.addEventListener('touchmove', function(e) {
    if (document.getElementById('dayEventsModal').classList.contains('show')) {
        e.preventDefault();
    }
}, { passive: false });
</script>

<?php require_once __DIR__ . '/inc/footer.php'; ?>