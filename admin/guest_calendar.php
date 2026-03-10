<?php
// Guest Reservations Calendar
$month = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('m');
$year  = isset($_GET['year'])  ? (int)$_GET['year']  : (int)date('Y');

$prev_month = $month == 1  ? 12 : $month - 1;
$prev_year  = $month == 1  ? $year - 1 : $year;
$next_month = $month == 12 ? 1  : $month + 1;
$next_year  = $month == 12 ? $year + 1 : $year;

$start_date = "$year-$month-01";
$end_date   = date('Y-m-t', strtotime($start_date));

// Get guest reservation counts for stats
$total_month = $conn->query("
    SELECT COUNT(*) as c 
    FROM guest_room_reservations
    WHERE deleted = 0
      AND MONTH(check_in_date)=$month AND YEAR(check_in_date)=$year
")->fetch_assoc()['c'];

$confirmed_month = $conn->query("
    SELECT COUNT(*) as c 
    FROM guest_room_reservations
    WHERE deleted = 0
      AND status='confirmed' 
      AND MONTH(check_in_date)=$month AND YEAR(check_in_date)=$year
")->fetch_assoc()['c'];

$pending_month = $conn->query("
    SELECT COUNT(*) as c 
    FROM guest_room_reservations
    WHERE deleted = 0
      AND status='pending' 
      AND MONTH(check_in_date)=$month AND YEAR(check_in_date)=$year
")->fetch_assoc()['c'];

$completed_month = $conn->query("
    SELECT COUNT(*) as c 
    FROM guest_room_reservations
    WHERE deleted = 0
      AND status='checked_out'
      AND MONTH(check_in_date)=$month AND YEAR(check_in_date)=$year
")->fetch_assoc()['c'];

// Get all guest reservations for the month
$guest_reservations = $conn->query("
    SELECT 
        gr.*,
        g.room_name as venue_name,
        g.floor,
        DATE(gr.check_in_date)  as start_date,
        DATE(gr.check_out_date) as end_date,
        gr.guest_name as guest_name,
        gr.check_in_date  as arrival_date,
        gr.check_out_date as departure_date
    FROM guest_room_reservations gr
    JOIN guest_rooms g ON gr.guest_room_id = g.id
    WHERE gr.deleted = 0
      AND (MONTH(gr.check_in_date) = $month OR MONTH(gr.check_out_date) = $month)
      AND (YEAR(gr.check_in_date) = $year OR YEAR(gr.check_out_date) = $year)
    AND gr.status IN ('confirmed', 'checked_in', 'pending')
    ORDER BY gr.check_in_date ASC
");

// Group reservations by date
$events_by_date = [];
while ($row = $guest_reservations->fetch_assoc()) {
    // Add to arrival date
    $arrival_date = $row['arrival_date'];
    if (!isset($events_by_date[$arrival_date])) {
        $events_by_date[$arrival_date] = [];
    }
    $events_by_date[$arrival_date][] = array_merge($row, ['event_type' => 'arrival']);
    
    // Add to departure date
    $departure_date = $row['departure_date'];
    if ($departure_date !== $arrival_date) {
        if (!isset($events_by_date[$departure_date])) {
            $events_by_date[$departure_date] = [];
        }
        $events_by_date[$departure_date][] = array_merge($row, ['event_type' => 'departure']);
    }
    
    // Add all days in between
    $current = strtotime($arrival_date . ' +1 day');
    $end = strtotime($departure_date);
    while ($current < $end) {
        $mid_date = date('Y-m-d', $current);
        if (!isset($events_by_date[$mid_date])) {
            $events_by_date[$mid_date] = [];
        }
        $events_by_date[$mid_date][] = array_merge($row, ['event_type' => 'stay']);
        $current = strtotime('+1 day', $current);
    }
}

$month_name = date('F Y', strtotime("$year-$month-01"));
$first_day = mktime(0,0,0,$month,1,$year);
$days_in_month = date('t', $first_day);
$day_of_week = date('w', $first_day);
$today = date('Y-m-d');

// Encode event data for JS
$guest_events_json = json_encode($events_by_date);
?>

<!-- Stats Cards for Guest Reservations -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-info"><div class="stat-title">Total This Month</div><div class="stat-number"><?= $total_month ?></div></div>
        <i class="bi bi-calendar-event stat-icon"></i>
    </div>
    <div class="stat-card">
        <div class="stat-info"><div class="stat-title">Confirmed</div><div class="stat-number"><?= $confirmed_month ?></div></div>
        <i class="bi bi-check-circle stat-icon"></i>
    </div>
    <div class="stat-card">
        <div class="stat-info"><div class="stat-title">Pending</div><div class="stat-number"><?= $pending_month ?></div></div>
        <i class="bi bi-hourglass-split stat-icon"></i>
    </div>
    <div class="stat-card">
        <div class="stat-info"><div class="stat-title">Completed</div><div class="stat-number"><?= $completed_month ?></div></div>
        <i class="bi bi-check2-all stat-icon"></i>
    </div>
</div>

<!-- Calendar Wrapper -->
<div class="calendar-wrapper">
    <div class="calendar-header">
        <div class="calendar-title"><h2><?= $month_name ?></h2></div>
        <div class="calendar-nav">
            <a href="?view=guest&month=<?= $prev_month ?>&year=<?= $prev_year ?>" class="calendar-nav-btn"><i class="bi bi-chevron-left"></i></a>
            <a href="?view=guest&month=<?= date('m') ?>&year=<?= date('Y') ?>" class="calendar-nav-btn"><i class="bi bi-calendar3"></i></a>
            <a href="?view=guest&month=<?= $next_month ?>&year=<?= $next_year ?>" class="calendar-nav-btn"><i class="bi bi-chevron-right"></i></a>
        </div>
    </div>

    <!-- Filter bar for guest reservations -->
    <div class="calendar-filters">
        <span class="filter-label">Show:</span>
        <label class="filter-checkbox-wrap">
            <input type="checkbox" id="filterConfirmed" checked onchange="renderGuestCalendar()">
            <span class="filter-dot" style="background: #28a745;"></span>
            <span class="filter-text">Confirmed</span>
        </label>
        <label class="filter-checkbox-wrap">
            <input type="checkbox" id="filterGuestPending" checked onchange="renderGuestCalendar()">
            <span class="filter-dot" style="background: #ffc107;"></span>
            <span class="filter-text">Pending</span>
        </label>
    </div>

    <!-- Weekday Headers -->
    <div class="calendar-grid">
        <?php foreach (['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $d): ?>
        <div class="calendar-weekday"><?= $d ?></div>
        <?php endforeach; ?>
    </div>

    <!-- Calendar Days -->
    <div class="calendar-grid" id="guestCalendarGrid">
        <?php
        for ($i = 0; $i < $day_of_week; $i++) {
            echo '<div class="calendar-day other-month"></div>';
        }
        for ($day = 1; $day <= $days_in_month; $day++) {
            $date = sprintf("%04d-%02d-%02d", $year, $month, $day);
            $day_class = $date == $today ? 'today' : '';
            echo '<div class="calendar-day ' . $day_class . '" data-date="' . $date . '" onclick="showGuestEvents(\'' . $date . '\')">';
            echo '<div class="day-number">' . $day;
            echo '<span class="event-badges">';
            echo '<span class="event-count-badge confirmed-badge" id="gb-' . $date . '"></span>';
            echo '<span class="event-count-badge guest-pending-badge" id="gpb-' . $date . '"></span>';
            echo '</span>';
            echo '</div>';
            echo '<div class="event-indicator" id="guest-dots-' . $date . '"></div>';
            echo '</div>';
        }
        $remaining = 42 - ($day_of_week + $days_in_month);
        for ($i = 0; $i < $remaining; $i++) {
            echo '<div class="calendar-day other-month"></div>';
        }
        ?>
    </div>
</div>

<!-- Guest Events Modal -->
<div class="event-details-modal" id="guestDateModal">
    <div class="modal-card">
        <div class="modal-header">
            <h3><i class="bi bi-calendar-date me-2"></i>Guest Stays for <span id="guestSelectedDate"></span></h3>
            <button class="modal-close" onclick="closeGuestDateModal()">&times;</button>
        </div>
        <div class="modal-body" id="guestDateModalBody"></div>
    </div>
</div>

<script>
var guestEventsData = <?= $guest_events_json ?>;

function renderGuestCalendar() {
    var showConfirmed = document.getElementById('filterConfirmed').checked;
    var showPending = document.getElementById('filterGuestPending').checked;
    
    var allDates = Object.keys(guestEventsData);
    
    allDates.forEach(function(date) {
        var dotsEl = document.getElementById('guest-dots-' + date);
        var confirmedBadge = document.getElementById('gb-' + date);
        var pendingBadge = document.getElementById('gpb-' + date);
        
        if (!dotsEl) return;
        
        dotsEl.innerHTML = '';
        var events = guestEventsData[date] || [];
        var confirmedCount = events.filter(e => e.status === 'confirmed').length;
        var pendingCount = events.filter(e => e.status === 'pending').length;
        
        // Confirmed badge + dots
        if (showConfirmed && confirmedCount > 0) {
            confirmedBadge.textContent = confirmedCount + ' confirmed';
            confirmedBadge.style.display = 'inline-block';
            for (var i = 0; i < Math.min(confirmedCount, 5); i++) {
                var dot = document.createElement('span');
                dot.className = 'event-dot';
                dot.style.background = '#28a745';
                dot.title = (events[i] || {}).guest_name || '';
                dotsEl.appendChild(dot);
            }
        } else {
            confirmedBadge.style.display = 'none';
        }
        
        // Pending badge + dots
        if (showPending && pendingCount > 0) {
            pendingBadge.textContent = pendingCount + ' pending';
            pendingBadge.style.display = 'inline-block';
            for (var j = 0; j < Math.min(pendingCount, 5); j++) {
                var dot2 = document.createElement('span');
                dot2.className = 'event-dot';
                dot2.style.background = '#ffc107';
                dot2.title = (events[j] || {}).guest_name || '';
                dotsEl.appendChild(dot2);
            }
        } else {
            pendingBadge.style.display = 'none';
        }
    });
}

function showGuestEvents(date) {
    var showConfirmed = document.getElementById('filterConfirmed').checked;
    var showPending = document.getElementById('filterGuestPending').checked;
    
    var options = { month:'long', day:'numeric', year:'numeric' };
    document.getElementById('guestSelectedDate').textContent =
        new Date(date + 'T00:00:00').toLocaleDateString('en-US', options);
    
    var events = guestEventsData[date] || [];
    var filteredEvents = events.filter(e => 
        (showConfirmed && e.status === 'confirmed') || 
        (showPending && e.status === 'pending')
    );
    
    if (filteredEvents.length === 0) {
        document.getElementById('guestDateModalBody').innerHTML = 
            '<div class="text-center py-5"><i class="bi bi-calendar-x fs-1 text-muted"></i><p class="mt-2">No guest stays on this day.</p></div>';
        document.getElementById('guestDateModal').classList.add('show');
        return;
    }
    
    var html = '';
    filteredEvents.sort((a, b) => a.arrival_date.localeCompare(b.arrival_date));
    
    filteredEvents.forEach(function(event) {
        var statusClass = event.status === 'confirmed' ? 'approved' : 'pending';
        var pillClass = event.status === 'confirmed' ? 'pill-approved' : 'pill-pending';
        var eventTypeText = event.event_type === 'arrival' ? '🟢 Arrival' : 
                           (event.event_type === 'departure' ? '🔴 Departure' : '🟡 Stay');
        
        html += `
            <div class="event-card ${statusClass}">
                <div class="event-header">
                    <span class="event-title">${escapeHtml(event.guest_name)}</span>
                    <span class="event-status-pill ${pillClass}">${event.status.toUpperCase()}</span>
                </div>
                <div class="event-time">
                    <i class="bi bi-info-circle"></i> ${eventTypeText}
                </div>
                <div class="event-details">
                    <div class="event-detail-item"><i class="bi bi-building"></i><span>${escapeHtml(event.venue_name)} (${escapeHtml(event.floor)})</span></div>
                    <div class="event-detail-item"><i class="bi bi-calendar"></i><span>Arrival: ${formatDate(event.arrival_date)}</span></div>
                    <div class="event-detail-item"><i class="bi bi-calendar-check"></i><span>Departure: ${formatDate(event.departure_date)}</span></div>
                    <div class="event-detail-item"><i class="bi bi-people"></i><span>${event.adults_count} Adult(s), ${event.children_count} Kid(s)</span></div>
                </div>
                <div class="view-link"><a href="guest_reservation_details.php?id=${event.id}">View Details <i class="bi bi-arrow-right"></i></a></div>
            </div>
        `;
    });
    
    document.getElementById('guestDateModalBody').innerHTML = html;
    document.getElementById('guestDateModal').classList.add('show');
}

function formatDate(dateStr) {
    var d = new Date(dateStr + 'T00:00:00');
    return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
}

function escapeHtml(text) {
    if (!text) return '';
    var div = document.createElement('div');
    div.appendChild(document.createTextNode(text));
    return div.innerHTML;
}

function closeGuestDateModal() {
    document.getElementById('guestDateModal').classList.remove('show');
}

// Initialize
renderGuestCalendar();

document.addEventListener('keydown', function(e) { 
    if (e.key === 'Escape') closeGuestDateModal(); 
});

document.getElementById('guestDateModal').addEventListener('click', function(e) { 
    if (e.target === this) closeGuestDateModal(); 
});
</script>

<style>
/* Additional styles for guest calendar */
.event-count-badge.confirmed-badge {
    background: #d4edda;
    color: #155724;
    display: none;
}

.event-count-badge.guest-pending-badge {
    background: #fff3cd;
    color: #856404;
    display: none;
}
</style>