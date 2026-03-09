<?php
$pageTitle = 'Dashboard';
require_once __DIR__ . '/inc/header.php';

// Get statistics
$today = date('Y-m-d');
$next_week = date('Y-m-d', strtotime('+5 days'));

// Today's reservations
$today_count = $conn->query("
    SELECT COUNT(*) as count 
    FROM facility_reservations 
    WHERE DATE(start_datetime) = '$today' 
    AND status IN ('approved', 'pencil_booked')
")->fetch_assoc()['count'];

// Pending approvals
$pending_count = $conn->query("
    SELECT COUNT(*) as count 
    FROM facility_reservations 
    WHERE status = 'pending'
")->fetch_assoc()['count'];

// Pencil booked count
$pencil_count = $conn->query("
    SELECT COUNT(*) as count 
    FROM facility_reservations 
    WHERE status = 'pencil_booked'
")->fetch_assoc()['count'];

// Available rooms today
$occupied_rooms = $conn->query("
    SELECT COUNT(DISTINCT venue_id) as count 
    FROM facility_reservations 
    WHERE DATE(start_datetime) = '$today' 
    AND status = 'approved'
")->fetch_assoc()['count'];

$total_rooms = $conn->query("SELECT COUNT(*) as count FROM venues WHERE is_active = 1")->fetch_assoc()['count'];
$available_rooms = $total_rooms - $occupied_rooms;

// Upcoming events (next 5 days)
$upcoming = $conn->query("
    SELECT r.*, v.name as venue_name,
           CONCAT(r.last_name, ', ', r.first_name) as requester
    FROM facility_reservations r
    JOIN venues v ON r.venue_id = v.id
    WHERE DATE(r.start_datetime) BETWEEN '$today' AND '$next_week'
    AND r.status IN ('approved', 'pencil_booked')
    ORDER BY r.start_datetime ASC
    LIMIT 5
");

$upcoming_count = $upcoming->num_rows;

// Get pending reservations for quick view
$pending_preview = $conn->query("
    SELECT r.*, v.name as venue_name
    FROM facility_reservations r
    JOIN venues v ON r.venue_id = v.id
    WHERE r.status = 'pending'
    ORDER BY r.created_at ASC
    LIMIT 3
");

// Get pencil booked reservations for quick view
// Get pencil booked reservations for quick view
$pencil_preview = $conn->query("
    SELECT r.*, v.name as venue_name
    FROM facility_reservations r
    JOIN venues v ON r.venue_id = v.id
    WHERE r.status = 'pencil_booked'
    ORDER BY r.start_datetime ASC
    LIMIT 3
");

// Get guest reservation counts from new guest_room_reservations table
$guest_pending = $conn->query("SELECT COUNT(*) as count FROM guest_room_reservations WHERE status = 'pending' AND deleted = 0")->fetch_assoc()['count'] ?? 0;
$guest_confirmed = $conn->query("SELECT COUNT(*) as count FROM guest_room_reservations WHERE status = 'confirmed' AND deleted = 0")->fetch_assoc()['count'] ?? 0;
$guest_cancelled = $conn->query("SELECT COUNT(*) as count FROM guest_room_reservations WHERE status = 'cancelled' AND deleted = 0")->fetch_assoc()['count'] ?? 0;

// Guests currently staying (checked in)
$guest_today = $conn->query("
    SELECT COUNT(*) as count
    FROM guest_room_reservations
    WHERE status IN ('confirmed', 'checked_in')
    AND check_in_date <= CURDATE()
    AND check_out_date > CURDATE()
    AND deleted = 0
")->fetch_assoc()['count'] ?? 0;

// Check-ins today
$guest_checkin_today = $conn->query("
    SELECT COUNT(*) as count 
    FROM guest_room_reservations 
    WHERE status IN ('confirmed', 'pending') 
    AND check_in_date = CURDATE()
    AND deleted = 0
")->fetch_assoc()['count'] ?? 0;

// Check-outs today
$guest_checkout_today = $conn->query("
    SELECT COUNT(*) as count 
    FROM guest_room_reservations 
    WHERE status IN ('confirmed', 'checked_in') 
    AND check_out_date = CURDATE()
    AND deleted = 0
")->fetch_assoc()['count'] ?? 0;

// Recent guest reservations for preview
$guest_preview = $conn->query("
    SELECT 
        gr.*, 
        g.room_name,
        gr.check_in_date AS arrival_date,
        gr.check_out_date AS departure_date
    FROM guest_room_reservations gr
    JOIN guest_rooms g ON gr.guest_room_id = g.id
    WHERE gr.status IN ('pending', 'confirmed', 'checked_in')
    AND gr.deleted = 0
    ORDER BY gr.created_at DESC
    LIMIT 4
");

?>
<style>
/* Dashboard Redesign - Fixed Layout */
:root {
    --bsu-red: #b71c1c;
    --bsu-red-dark: #8b0000;
    --card-shadow: 0 4px 12px rgba(0,0,0,0.05);
    --card-hover-shadow: 0 8px 20px rgba(183,28,28,0.1);
}

.content-area {
    padding: 1.5rem;
}

/* Stats Cards Grid - 4 columns */
.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.25rem;
    margin-bottom: 2rem;
}

.nav-card {
    background: white;
    border-radius: 16px;
    padding: 1.25rem 1rem;
    box-shadow: var(--card-shadow);
    border: 1px solid #f0f0f0;
    transition: all 0.2s ease;
    cursor: pointer;
    text-decoration: none;
    color: inherit;
    display: flex;
    align-items: center;
    gap: 1rem;
    position: relative;
    overflow: hidden;
}

.nav-card:hover {
    transform: translateY(-3px);
    border-color: var(--bsu-red);
    box-shadow: var(--card-hover-shadow);
}

.nav-card-icon {
    width: 48px;
    height: 48px;
    background: #fdeae8;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--bsu-red);
    font-size: 1.5rem;
    transition: all 0.2s ease;
}

.nav-card:hover .nav-card-icon {
    background: var(--bsu-red);
    color: white;
}

.nav-card.pencil-card .nav-card-icon {
    background: #e2d5f1;
    color: #5e3c8b;
}

.nav-card.pencil-card:hover .nav-card-icon {
    background: #5e3c8b;
    color: white;
}

.nav-card-content {
    flex: 1;
}

.nav-card-title {
    font-size: 0.8rem;
    color: #666;
    margin-bottom: 0.25rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 600;
}

.nav-card-value {
    font-size: 1.8rem;
    font-weight: 700;
    color: #333;
    line-height: 1.2;
}

.nav-card-badge {
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
    background: var(--bsu-red);
    color: white;
    font-size: 0.7rem;
    padding: 0.15rem 0.4rem;
    border-radius: 20px;
    min-width: 20px;
    text-align: center;
}

/* Two Column Layout */
.dashboard-two-col {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

.dashboard-card {
    background: white;
    border-radius: 16px;
    padding: 1.25rem;
    box-shadow: var(--card-shadow);
    border: 1px solid #f0f0f0;
    height: fit-content;
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    padding-bottom: 0.75rem;
    border-bottom: 2px solid #f0f0f0;
}

.card-header h3 {
    font-size: 1rem;
    font-weight: 600;
    color: #333;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.card-header a {
    font-size: 0.8rem;
    color: var(--bsu-red);
    text-decoration: none;
    font-weight: 500;
    transition: all 0.2s ease;
}

.card-header a:hover {
    color: var(--bsu-red-dark);
    transform: translateX(3px);
}

/* Pending Items List */
.pending-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.pending-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 0;
    border-bottom: 1px solid #f0f0f0;
    transition: all 0.2s ease;
}

.pending-item:last-child {
    border-bottom: none;
}

.pending-item:hover {
    background: #f8f9fa;
    padding-left: 0.5rem;
    margin-left: -0.5rem;
    border-radius: 8px;
}

.pending-item-icon {
    width: 32px;
    height: 32px;
    background: #fff3cd;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #856404;
    font-size: 0.9rem;
    flex-shrink: 0;
}

.pending-item-content {
    flex: 1;
    min-width: 0;
}

.pending-item-title {
    font-size: 0.9rem;
    font-weight: 500;
    color: #333;
    margin-bottom: 0.15rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.pending-item-meta {
    font-size: 0.7rem;
    color: #999;
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.pending-item-meta i {
    margin-right: 0.2rem;
}

.pending-item-action {
    opacity: 0;
    transition: opacity 0.2s ease;
    flex-shrink: 0;
}

.pending-item:hover .pending-item-action {
    opacity: 1;
}

.pending-item-action a {
    color: var(--bsu-red);
    font-size: 0.9rem;
    text-decoration: none;
}

/* Status badges small */
.status-badge-small {
    font-size: 0.6rem;
    padding: 0.2rem 0.5rem;
    border-radius: 50px;
    font-weight: 600;
    display: inline-block;
}

.status-pending-small {
    background: #fff3cd;
    color: #856404;
}

.status-pencil-small {
    background: #e2d5f1;
    color: #5e3c8b;
}

.status-approved-small {
    background: #d4edda;
    color: #155724;
}

/* Upcoming Events List */
.upcoming-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.upcoming-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 0;
    border-bottom: 1px solid #f0f0f0;
}

.upcoming-item:last-child {
    border-bottom: none;
}

.upcoming-date {
    min-width: 45px;
    text-align: center;
    background: #f8f9fa;
    border-radius: 8px;
    padding: 0.25rem 0;
    flex-shrink: 0;
}

.upcoming-date .day {
    font-size: 1.2rem;
    font-weight: 700;
    color: var(--bsu-red);
    line-height: 1;
}

.upcoming-date .month {
    font-size: 0.65rem;
    color: #666;
    text-transform: uppercase;
}

.upcoming-content {
    flex: 1;
    min-width: 0;
}

.upcoming-title {
    font-size: 0.9rem;
    font-weight: 500;
    color: #333;
    margin-bottom: 0.15rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.upcoming-venue {
    font-size: 0.7rem;
    color: #999;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.upcoming-time {
    font-size: 0.7rem;
    color: #666;
    background: #f8f9fa;
    padding: 0.2rem 0.5rem;
    border-radius: 20px;
    white-space: nowrap;
    flex-shrink: 0;
}

/* Quick Actions Grid */
.quick-actions-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 0.75rem;
}

.quick-action-btn {
    text-decoration: none;
    padding: 0.75rem 0.5rem;
    border-radius: 12px;
    text-align: center;
    transition: all 0.2s ease;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.25rem;
}

.quick-action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.quick-action-btn i {
    font-size: 1.2rem;
}

.quick-action-btn div {
    font-size: 0.75rem;
    font-weight: 600;
}

/* Empty States */
.empty-state {
    text-align: center;
    padding: 2rem 1rem;
    color: #999;
    font-size: 0.9rem;
}

.empty-state i {
    font-size: 2rem;
    color: #ddd;
    margin-bottom: 0.5rem;
}

/* Responsive */
@media (max-width: 1024px) {
    .dashboard-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .dashboard-two-col {
        grid-template-columns: 1fr;
    }
    
    .quick-actions-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 480px) {
    .dashboard-grid {
        grid-template-columns: 1fr;
    }
    
    .quick-actions-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .upcoming-item {
        flex-wrap: wrap;
    }
    
    .upcoming-time {
        margin-left: auto;
    }
}
</style>

<div class="content-area">
    <!-- Stats Cards Row - 4 cards -->
    <div class="dashboard-grid">
        <!-- Today's Reservations Card -->
        <a href="reservations.php?date=today" class="nav-card">
            <div class="nav-card-icon">
                <i class="bi bi-calendar-day"></i>
            </div>
            <div class="nav-card-content">
                <div class="nav-card-title">Today's Reservations</div>
                <div class="nav-card-value"><?= $today_count ?></div>
            </div>
            <?php if ($today_count > 0): ?>
                <span class="nav-card-badge"><?= $today_count ?></span>
            <?php endif; ?>
        </a>

        <!-- Pending Approval Card -->
        <a href="reservations.php?status=pending" class="nav-card">
            <div class="nav-card-icon">
                <i class="bi bi-hourglass-split"></i>
            </div>
            <div class="nav-card-content">
                <div class="nav-card-title">Pending Approval</div>
                <div class="nav-card-value"><?= $pending_count ?></div>
            </div>
            <?php if ($pending_count > 0): ?>
                <span class="nav-card-badge"><?= $pending_count ?></span>
            <?php endif; ?>
        </a>

        <!-- Pencil Booked Card -->
        <a href="reservations.php?status=pencil_booked" class="nav-card pencil-card">
            <div class="nav-card-icon">
                <i class="bi bi-pencil"></i>
            </div>
            <div class="nav-card-content">
                <div class="nav-card-title">Pencil Booked</div>
                <div class="nav-card-value"><?= $pencil_count ?></div>
            </div>
            <?php if ($pencil_count > 0): ?>
                <span class="nav-card-badge"><?= $pencil_count ?></span>
            <?php endif; ?>
        </a>

        <!-- Available Rooms Card -->
        <a href="rooms.php" class="nav-card">
            <div class="nav-card-icon">
                <i class="bi bi-door-open"></i>
            </div>
            <div class="nav-card-content">
                <div class="nav-card-title">Available Rooms</div>
                <div class="nav-card-value"><?= $available_rooms ?>/<?= $total_rooms ?></div>
            </div>
        </a>
    </div>

    <!-- First Row - Pending and Pencil Previews -->
    <div class="dashboard-two-col">
        <!-- Pending Reservations Preview -->
        <div class="dashboard-card">
            <div class="card-header">
                <h3><i class="bi bi-hourglass-split" style="color: var(--bsu-red);"></i> Pending Reviews</h3>
                <?php if ($pending_count > 0): ?>
                    <a href="reservations.php?status=pending">View All <i class="bi bi-arrow-right"></i></a>
                <?php endif; ?>
            </div>
            
            <?php if ($pending_preview && $pending_preview->num_rows > 0): ?>
                <ul class="pending-list">
                    <?php while ($row = $pending_preview->fetch_assoc()): ?>
                    <li class="pending-item">
                        <div class="pending-item-icon">
                            <i class="bi bi-clock"></i>
                        </div>
                        <div class="pending-item-content">
                            <div class="pending-item-title">
                                <?= htmlspecialchars(substr($row['activity_name'], 0, 25)) ?>
                                <?= strlen($row['activity_name']) > 25 ? '...' : '' ?>
                                <span class="status-badge-small status-pending-small">PENDING</span>
                            </div>
                            <div class="pending-item-meta">
                                <span><i class="bi bi-person"></i> <?= htmlspecialchars(substr($row['last_name'], 0, 10)) ?></span>
                                <span><i class="bi bi-building"></i> <?= htmlspecialchars(substr($row['venue_name'], 0, 10)) ?></span>
                                <span><i class="bi bi-calendar"></i> <?= date('M d', strtotime($row['start_datetime'])) ?></span>
                            </div>
                        </div>
                        <div class="pending-item-action">
                            <a href="reservation_details.php?id=<?= $row['id'] ?>" title="Review">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </div>
                    </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <div class="empty-state">
                    <i class="bi bi-check-circle"></i>
                    <p>No pending reservations</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Pencil Booked Preview -->
        <div class="dashboard-card">
            <div class="card-header">
                <h3><i class="bi bi-pencil" style="color: #5e3c8b;"></i> Pencil Booked</h3>
                <?php if ($pencil_count > 0): ?>
                    <a href="reservations.php?status=pencil_booked">View All <i class="bi bi-arrow-right"></i></a>
                <?php endif; ?>
            </div>
            
            <?php if ($pencil_preview && $pencil_preview->num_rows > 0): ?>
                <ul class="pending-list">
                    <?php while ($row = $pencil_preview->fetch_assoc()): ?>
                    <li class="pending-item">
                        <div class="pending-item-icon" style="background: #e2d5f1; color: #5e3c8b;">
                            <i class="bi bi-pencil"></i>
                        </div>
                        <div class="pending-item-content">
                            <div class="pending-item-title">
                                <?= htmlspecialchars(substr($row['activity_name'], 0, 25)) ?>
                                <?= strlen($row['activity_name']) > 25 ? '...' : '' ?>
                                <span class="status-badge-small status-pencil-small">PENCIL</span>
                            </div>
                            <div class="pending-item-meta">
                                <span><i class="bi bi-person"></i> <?= htmlspecialchars(substr($row['last_name'], 0, 10)) ?></span>
                                <span><i class="bi bi-building"></i> <?= htmlspecialchars(substr($row['venue_name'], 0, 10)) ?></span>
                                <span><i class="bi bi-calendar"></i> <?= date('M d', strtotime($row['start_datetime'])) ?></span>
                            </div>
                        </div>
                        <div class="pending-item-action">
                            <a href="reservation_details.php?id=<?= $row['id'] ?>" title="View">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </div>
                    </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <div class="empty-state">
                    <i class="bi bi-pencil"></i>
                    <p>No pencil booked reservations</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Second Row - Upcoming Events and Quick Actions -->
    <div class="dashboard-two-col">
        <!-- Upcoming Events Preview -->
        <div class="dashboard-card">
            <div class="card-header">
                <h3><i class="bi bi-calendar-check" style="color: var(--bsu-red);"></i> Next 5 Days</h3>
                <a href="calendar.php">View Calendar <i class="bi bi-arrow-right"></i></a>
            </div>
            
            <?php if ($upcoming && $upcoming->num_rows > 0): ?>
                <ul class="upcoming-list">
                    <?php while ($row = $upcoming->fetch_assoc()): 
                        $date = strtotime($row['start_datetime']);
                        $status_class = $row['status'] == 'pencil_booked' ? 'status-pencil-small' : 'status-approved-small';
                        $status_text = $row['status'] == 'pencil_booked' ? 'PENCIL' : 'APPROVED';
                    ?>
                    <li class="upcoming-item">
                        <div class="upcoming-date">
                            <div class="day"><?= date('d', $date) ?></div>
                            <div class="month"><?= date('M', $date) ?></div>
                        </div>
                        <div class="upcoming-content">
                            <div class="upcoming-title">
                                <?= htmlspecialchars(substr($row['activity_name'], 0, 20)) ?>
                                <?= strlen($row['activity_name']) > 20 ? '...' : '' ?>
                                <span class="status-badge-small <?= $status_class ?>"><?= $status_text ?></span>
                            </div>
                            <div class="upcoming-venue">
                                <i class="bi bi-building"></i> <?= htmlspecialchars(substr($row['venue_name'], 0, 15)) ?>
                            </div>
                        </div>
                        <div class="upcoming-time">
                            <?= date('g:i A', $date) ?>
                        </div>
                    </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <div class="empty-state">
                    <i class="bi bi-calendar-x"></i>
                    <p>No upcoming events</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Quick Actions -->
        <div class="dashboard-card">
            <div class="card-header">
                <h3><i class="bi bi-lightning-charge" style="color: var(--bsu-red);"></i> Quick Actions</h3>
            </div>
            <div class="quick-actions-grid">
                <a href="reservations.php?view=function&status=pending" class="quick-action-btn" style="background: #fff3cd; color: #856404;">
                    <i class="bi bi-hourglass-split"></i>
                    <div>Pending</div>
                    <small><?= $pending_count ?></small>
                </a>
                <a href="reservations.php?view=function&status=pencil_booked" class="quick-action-btn" style="background: #e2d5f1; color: #5e3c8b;">
                    <i class="bi bi-pencil"></i>
                    <div>Pencil</div>
                    <small><?= $pencil_count ?></small>
                </a>
                <a href="reservations.php?view=function&status=approved" class="quick-action-btn" style="background: #d4edda; color: #155724;">
                    <i class="bi bi-check-circle"></i>
                    <div>Approved</div>
                </a>
                <a href="reservations.php?view=guest" class="quick-action-btn" style="background: #e2f0fb; color: #004085;">
                    <i class="bi bi-door-open"></i>
                    <div>Guest Rooms</div>
                    <small><?= $guest_pending ?></small>
                </a>
                <a href="reservations.php?view=function" class="quick-action-btn" style="background: #e9ecef; color: #495057;">
                    <i class="bi bi-calendar3"></i>
                    <div>Calendar</div>
                </a>
                <a href="guest_reservations.php" class="quick-action-btn" style="background: #cce5ff; color: #004085;">
                    <i class="bi bi-list-ul"></i>
                    <div>Guest List</div>
                </a>
            </div>
            <div style="margin-top: 1rem; font-size: 0.7rem; color: #999; text-align: right; border-top: 1px solid #f0f0f0; padding-top: 0.75rem;">
                <i class="bi bi-clock"></i> Last updated: <?= date('M d, Y h:i A') ?>
            </div>
        </div>
    </div>

    <!-- Guest Room Stats Row -->
    <div class="dashboard-two-col" style="margin-bottom:1.5rem;">
        <!-- Guest Room Summary Card -->
        <div class="dashboard-card">
            <div class="card-header">
                <h3><i class="bi bi-door-open" style="color: var(--bsu-red);"></i> Guest Rooms Today</h3>
                <a href="reservations.php?view=guest">View Calendar <i class="bi bi-arrow-right"></i></a>
            </div>
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:.75rem;margin-bottom:1rem;">
                <div style="text-align:center;padding:.75rem;background:#f8f9fa;border-radius:10px;">
                    <div style="font-size:1.5rem;font-weight:700;color:#004085;"><?= $guest_today ?></div>
                    <div style="font-size:0.72rem;color:#6c757d;font-weight:600;text-transform:uppercase;">Currently Staying</div>
                </div>
                <div style="text-align:center;padding:.75rem;background:#d4edda;border-radius:10px;">
                    <div style="font-size:1.5rem;font-weight:700;color:#155724;"><?= $guest_checkin_today ?></div>
                    <div style="font-size:0.72rem;color:#155724;font-weight:600;text-transform:uppercase;">Check-ins Today</div>
                </div>
                <div style="text-align:center;padding:.75rem;background:#f8d7da;border-radius:10px;">
                    <div style="font-size:1.5rem;font-weight:700;color:#721c24;"><?= $guest_checkout_today ?></div>
                    <div style="font-size:0.72rem;color:#721c24;font-weight:600;text-transform:uppercase;">Check-outs Today</div>
                </div>
            </div>
            <div style="display:flex;gap:.5rem;flex-wrap:wrap;">
                <a href="guest_reservations.php?status=pending" style="flex:1;min-width:120px;text-align:center;padding:.5rem;background:#fff3cd;color:#856404;border-radius:8px;text-decoration:none;font-size:.82rem;font-weight:600;">
                    <i class="bi bi-hourglass-split d-block mb-1"></i>
                    <?= $guest_pending ?> Pending
                </a>
                <a href="guest_reservations.php?status=confirmed" style="flex:1;min-width:120px;text-align:center;padding:.5rem;background:#d4edda;color:#155724;border-radius:8px;text-decoration:none;font-size:.82rem;font-weight:600;">
                    <i class="bi bi-check-circle d-block mb-1"></i>
                    <?= $guest_confirmed ?> Confirmed
                </a>
                <a href="guest_reservations.php?status=cancelled" style="flex:1;min-width:120px;text-align:center;padding:.5rem;background:#f8d7da;color:#721c24;border-radius:8px;text-decoration:none;font-size:.82rem;font-weight:600;">
                    <i class="bi bi-x-circle d-block mb-1"></i>
                    <?= $guest_cancelled ?> Cancelled
                </a>
            </div>
        </div>

        <!-- Recent Guest Reservations -->
        <div class="dashboard-card">
            <div class="card-header">
                <h3><i class="bi bi-person-badge" style="color: var(--bsu-red);"></i> Recent Guest Bookings</h3>
                <a href="guest_reservations.php">View All <i class="bi bi-arrow-right"></i></a>
            </div>
            <?php if ($guest_preview && $guest_preview->num_rows > 0): ?>
                <ul class="pending-list">
                    <?php while ($grow = $guest_preview->fetch_assoc()):
                        $gStatusClass = $grow['status'] === 'confirmed' ? 'status-approved-small' : 'status-pending-small';
                        $gStatusText  = strtoupper($grow['status']);
                        $gNights = (strtotime($grow['departure_date']) - strtotime($grow['arrival_date'])) / 86400;
                    ?>
                    <li class="pending-item">
                        <div class="pending-item-icon" style="background:#e2f0fb;color:#004085;">
                            <i class="bi bi-door-open"></i>
                        </div>
                        <div class="pending-item-content">
                            <div class="pending-item-title">
                                <?= htmlspecialchars(substr($grow['guest_name'], 0, 22)) ?><?= strlen($grow['guest_name']) > 22 ? '…' : '' ?>
                                <span class="status-badge-small <?= $gStatusClass ?>"><?= $gStatusText ?></span>
                            </div>
                            <div class="pending-item-meta">
                                <span><i class="bi bi-door-open"></i> <?= htmlspecialchars(substr($grow['room_name'], 0, 14)) ?></span>
                                <span><i class="bi bi-calendar-check"></i> <?= date('M d', strtotime($grow['arrival_date'])) ?></span>
                                <span><i class="bi bi-moon-stars"></i> <?= (int)$gNights ?> night<?= $gNights!=1?'s':'' ?></span>
                            </div>
                        </div>
                        <div class="pending-item-action">
                            <a href="guest_reservation_details.php?id=<?= $grow['id'] ?>" title="View">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </div>
                    </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <div class="empty-state">
                    <i class="bi bi-door-open"></i>
                    <p>No recent guest bookings</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

<script>
// Add ripple effect for cards
document.querySelectorAll('.nav-card').forEach(card => {
    card.addEventListener('click', function(e) {
        const ripple = document.createElement('span');
        ripple.classList.add('ripple-effect');
        ripple.style.position = 'absolute';
        ripple.style.background = 'rgba(255,255,255,0.3)';
        ripple.style.borderRadius = '50%';
        ripple.style.transform = 'scale(0)';
        ripple.style.animation = 'ripple 0.6s linear';
        ripple.style.pointerEvents = 'none';
        this.style.position = 'relative';
        this.style.overflow = 'hidden';
        this.appendChild(ripple);
        
        const x = e.clientX - this.offsetLeft;
        const y = e.clientY - this.offsetTop;
        ripple.style.left = x + 'px';
        ripple.style.top = y + 'px';
        ripple.style.width = '100px';
        ripple.style.height = '100px';
        ripple.style.marginLeft = '-50px';
        ripple.style.marginTop = '-50px';
        
        setTimeout(() => ripple.remove(), 600);
    });
});

// Highlight card if coming from a filtered link
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const statusParam = urlParams.get('status');
    const dateParam = urlParams.get('date');
    
    if (statusParam) {
        document.querySelectorAll('.nav-card').forEach(card => {
            if (card.href.includes('status=' + statusParam)) {
                card.style.border = '2px solid var(--bsu-red)';
                setTimeout(() => {
                    card.style.border = '1px solid #f0f0f0';
                }, 2000);
            }
        });
    }
    
    if (dateParam === 'today') {
        document.querySelectorAll('.nav-card').forEach(card => {
            if (card.href.includes('date=today')) {
                card.style.border = '2px solid var(--bsu-red)';
                setTimeout(() => {
                    card.style.border = '1px solid #f0f0f0';
                }, 2000);
            }
        });
    }
});
</script>

<style>
@keyframes ripple {
    to {
        transform: scale(4);
        opacity: 0;
    }
}
</style>

<?php require_once __DIR__ . '/inc/footer.php'; ?>