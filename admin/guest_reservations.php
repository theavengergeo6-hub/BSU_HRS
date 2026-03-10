<?php
$pageTitle = 'Guest Reservations';
require_once __DIR__ . '/inc/header.php';

// Get filter parameters
$status_filter_raw = isset($_GET['status']) ? $_GET['status'] : '';
$status_filter = ($status_filter_raw === 'completed') ? 'checked_out' : $status_filter_raw;
$date_filter = isset($_GET['date']) ? $_GET['date'] : '';
$search = isset($_GET['search']) ? clean($_GET['search']) : '';

// Build query with filters
$query = "SELECT 
              gr.*,
              g.room_name,
              g.floor,
              gr.check_in_date AS arrival_date,
              gr.check_out_date AS departure_date
          FROM guest_room_reservations gr
          JOIN guest_rooms g ON gr.guest_room_id = g.id
          WHERE gr.deleted = 0";

$params = [];
$types = "";

if ($status_filter) {
    $query .= " AND gr.status = ?";
    $params[] = $status_filter;
    $types .= "s";
}

if ($date_filter === 'today') {
    $today = date('Y-m-d');
    $query .= " AND (gr.check_in_date = ? OR gr.check_out_date = ?)";
    $params[] = $today;
    $params[] = $today;
    $types .= "ss";
} elseif ($date_filter === 'week') {
    $start_week = date('Y-m-d', strtotime('monday this week'));
    $end_week = date('Y-m-d', strtotime('sunday this week'));
    $query .= " AND (gr.check_in_date BETWEEN ? AND ? OR gr.check_out_date BETWEEN ? AND ?)";
    $params[] = $start_week;
    $params[] = $end_week;
    $params[] = $start_week;
    $params[] = $end_week;
    $types .= "ssss";
} elseif ($date_filter === 'month') {
    $start_month = date('Y-m-01');
    $end_month = date('Y-m-t');
    $query .= " AND (gr.check_in_date BETWEEN ? AND ? OR gr.check_out_date BETWEEN ? AND ?)";
    $params[] = $start_month;
    $params[] = $end_month;
    $params[] = $start_month;
    $params[] = $end_month;
    $types .= "ssss";
}

if ($search) {
    $query .= " AND (gr.booking_no LIKE ? OR gr.guest_name LIKE ? OR gr.guest_email LIKE ?)";
    $search_term = "%$search%";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $types .= "sss";
}

$query .= " ORDER BY gr.created_at DESC";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$reservations = $stmt->get_result();

// Get counts for each status
$status_counts = [
    'pending' => $conn->query("SELECT COUNT(*) as count FROM guest_room_reservations WHERE deleted=0 AND status = 'pending'")->fetch_assoc()['count'],
    'confirmed' => $conn->query("SELECT COUNT(*) as count FROM guest_room_reservations WHERE deleted=0 AND status = 'confirmed'")->fetch_assoc()['count'],
    'cancelled' => $conn->query("SELECT COUNT(*) as count FROM guest_room_reservations WHERE deleted=0 AND status = 'cancelled'")->fetch_assoc()['count'],
    'completed' => $conn->query("SELECT COUNT(*) as count FROM guest_room_reservations WHERE deleted=0 AND status IN ('checked_out')")->fetch_assoc()['count']
];

function getStatusBadgeClass($status) {
    switch (strtolower($status)) {
        case 'pending': return 'status-pending';
        case 'confirmed': return 'status-approved';
        case 'cancelled': return 'status-cancelled';
        case 'checked_out': return 'status-completed';
        default: return 'status-default';
    }
}
?>

<style>
/* Guest Reservations Page Styles */
.guest-reservations-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.guest-reservations-header h1 {
    font-size: 1.5rem;
    font-weight: 600;
    color: #2c3e50;
    margin: 0;
}

/* Filter Section */
.filter-section {
    background: white;
    border-radius: 16px;
    padding: 1.25rem;
    margin-bottom: 1.5rem;
    border: 1px solid #e9ecef;
    box-shadow: 0 2px 8px rgba(0,0,0,0.02);
}

.filter-form {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    align-items: flex-end;
}

.filter-group {
    flex: 1;
    min-width: 200px;
}

.filter-group label {
    display: block;
    font-size: 0.8rem;
    font-weight: 600;
    color: #666;
    margin-bottom: 0.3rem;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.filter-group select,
.filter-group input {
    width: 100%;
    padding: 0.6rem 1rem;
    border: 2px solid #e9ecef;
    border-radius: 10px;
    font-size: 0.9rem;
    transition: all 0.2s ease;
    background: #f8f9fa;
}

.filter-group select:focus,
.filter-group input:focus {
    border-color: #b71c1c;
    background: white;
    outline: none;
}

.checkbox-group {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    background: #f8f9fa;
    padding: 0.6rem 1rem;
    border-radius: 10px;
    border: 2px solid #e9ecef;
}

.checkbox-label {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    font-size: 0.9rem;
    color: #495057;
    cursor: pointer;
    padding: 0.2rem 0.5rem;
    border-radius: 20px;
    transition: all 0.2s ease;
}

.checkbox-label:hover {
    background: white;
}

.checkbox-label input[type="checkbox"] {
    width: 16px;
    height: 16px;
    cursor: pointer;
    accent-color: #b71c1c;
}

.checkbox-label .count {
    color: #999;
    font-size: 0.8rem;
    margin-left: 0.2rem;
}

.filter-actions {
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

.btn-filter {
    background: #b71c1c;
    color: white;
    border: none;
    padding: 0.6rem 1.5rem;
    border-radius: 10px;
    font-weight: 500;
    font-size: 0.9rem;
    cursor: pointer;
    transition: all 0.2s ease;
    height: 42px;
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
}

.btn-filter:hover {
    background: #8b0000;
    transform: translateY(-2px);
}

.btn-clear {
    background: #e9ecef;
    color: #495057;
    border: none;
    padding: 0.6rem 1.2rem;
    border-radius: 10px;
    font-weight: 500;
    font-size: 0.9rem;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    height: 42px;
    transition: all 0.2s ease;
}

.btn-clear:hover {
    background: #dee2e6;
    color: #212529;
}

/* Status Badges */
.status-badge {
    padding: 0.35rem 0.8rem;
    border-radius: 50px;
    font-weight: 600;
    font-size: 0.75rem;
    display: inline-block;
    letter-spacing: 0.3px;
    text-align: center;
    min-width: 100px;
}

.status-pending {
    background: #fff3cd;
    color: #856404;
}

.status-approved {
    background: #d4edda;
    color: #155724;
}

.status-cancelled {
    background: #f8d7da;
    color: #721c24;
}

.status-completed {
    background: #cce5ff;
    color: #004085;
}

/* Table Styles */
.table-responsive {
    background: white;
    border-radius: 16px;
    padding: 0;
    box-shadow: 0 4px 12px rgba(0,0,0,0.03);
    border: 1px solid #e9ecef;
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
    min-width: 1200px;
}

th {
    text-align: left;
    padding: 1rem 1.25rem;
    background: #f8f9fa;
    color: #495057;
    font-weight: 600;
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    border-bottom: 2px solid #e9ecef;
}

td {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid #f0f0f0;
    color: #212529;
    font-size: 0.9rem;
}

tr:hover {
    background: #f8f9fa;
}

.booking-no {
    font-weight: 600;
    color: #b71c1c;
    font-size: 0.85rem;
}

.guest-name {
    font-weight: 500;
    color: #2c3e50;
}

.room-info {
    color: #666;
    font-size: 0.85rem;
}

.date-info {
    font-size: 0.85rem;
    color: #495057;
}

.date-info i {
    margin-right: 0.3rem;
    color: #999;
    font-size: 0.8rem;
}

.action-buttons {
    display: flex;
    gap: 0.5rem;
}

.btn-view {
    background: none;
    border: 1px solid #dee2e6;
    color: #495057;
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    transition: all 0.2s ease;
}

.btn-view:hover {
    background: #b71c1c;
    border-color: #b71c1c;
    color: white;
    transform: translateY(-2px);
}

/* Tabs */
.reservation-tabs {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 1.5rem;
    border-bottom: 2px solid #f0f0f0;
    padding-bottom: 0.5rem;
}

.tab-link {
    padding: 0.6rem 1.5rem;
    border-radius: 50px;
    font-weight: 600;
    font-size: 0.9rem;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
    color: #666;
}

.tab-link:hover {
    background: #fdeae8;
    color: #b71c1c;
}

.tab-link.active {
    background: #b71c1c;
    color: white;
}

.tab-link i {
    margin-right: 0.5rem;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 3rem 1rem;
    color: #999;
}

.empty-state i {
    font-size: 3rem;
    color: #ddd;
    margin-bottom: 1rem;
}

/* Stats Cards */
.stats-cards {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 1rem;
    border-left: 4px solid #b71c1c;
    box-shadow: 0 2px 8px rgba(0,0,0,0.03);
}

.stat-number {
    font-size: 1.8rem;
    font-weight: 700;
    color: #333;
    line-height: 1.2;
}

.stat-label {
    color: #666;
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Responsive */
@media (max-width: 768px) {
    .filter-form {
        flex-direction: column;
    }
    
    .filter-group {
        width: 100%;
    }
    
    .stats-cards {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 480px) {
    .stats-cards {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="content-area">
    <div class="guest-reservations-header">
        <h1><i class="bi bi-door-open me-2" style="color: #b71c1c;"></i>Guest Reservations</h1>
        <a href="guest_reservation_details.php" class="btn-back" style="display: none;">Back</a>
    </div>

    <!-- Tabs -->
    <div class="reservation-tabs">
        <a href="reservations.php" class="tab-link">
            <i class="bi bi-calendar-event"></i> Function Rooms
        </a>
        <a href="guest_reservations.php" class="tab-link active">
            <i class="bi bi-door-open"></i> Guest Rooms
        </a>
        <a href="calendar.php" class="tab-link">
            <i class="bi bi-calendar3"></i> Calendar
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="stats-cards">
        <div class="stat-card">
            <div class="stat-number"><?= $status_counts['pending'] ?></div>
            <div class="stat-label">Pending</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= $status_counts['confirmed'] ?></div>
            <div class="stat-label">Confirmed</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= $status_counts['completed'] ?></div>
            <div class="stat-label">Completed</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= $status_counts['cancelled'] ?></div>
            <div class="stat-label">Cancelled</div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-section">
        <form method="GET" class="filter-form" id="filterForm">
            <div class="filter-group">
                <label><i class="bi bi-tags me-1"></i>Status</label>
                <div class="checkbox-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="status" value="pending" 
                            <?= $status_filter === 'pending' ? 'checked' : '' ?>>
                        Pending <span class="count">(<?= $status_counts['pending'] ?>)</span>
                    </label>
                    <label class="checkbox-label">
                        <input type="checkbox" name="status" value="confirmed" 
                            <?= $status_filter === 'confirmed' ? 'checked' : '' ?>>
                        Confirmed <span class="count">(<?= $status_counts['confirmed'] ?>)</span>
                    </label>
                    <label class="checkbox-label">
                        <input type="checkbox" name="status" value="completed" 
                            <?= $status_filter_raw === 'completed' ? 'checked' : '' ?>>
                        Completed <span class="count">(<?= $status_counts['completed'] ?>)</span>
                    </label>
                    <label class="checkbox-label">
                        <input type="checkbox" name="status" value="cancelled" 
                            <?= $status_filter === 'cancelled' ? 'checked' : '' ?>>
                        Cancelled <span class="count">(<?= $status_counts['cancelled'] ?>)</span>
                    </label>
                </div>
            </div>

            <div class="filter-group">
                <label><i class="bi bi-calendar-range me-1"></i>Date Range</label>
                <select name="date">
                    <option value="">All Dates</option>
                    <option value="today" <?= $date_filter === 'today' ? 'selected' : '' ?>>Today</option>
                    <option value="week" <?= $date_filter === 'week' ? 'selected' : '' ?>>This Week</option>
                    <option value="month" <?= $date_filter === 'month' ? 'selected' : '' ?>>This Month</option>
                </select>
            </div>

            <div class="filter-group">
                <label><i class="bi bi-search me-1"></i>Search</label>
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" 
                       placeholder="Booking #, Guest Name, Email...">
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn-filter">
                    <i class="bi bi-funnel"></i> Apply Filters
                </button>
                <a href="guest_reservations.php" class="btn-clear">
                    <i class="bi bi-x-circle"></i> Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Results Count -->
    <div style="margin-bottom: 1rem; font-size: 0.9rem; color: #666;">
        <i class="bi bi-list-ul me-1"></i> Showing <?= $reservations->num_rows ?> reservation(s)
    </div>

    <!-- Reservations Table -->
    <div class="table-responsive">
        <?php if ($reservations && $reservations->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Booking #</th>
                    <th>Guest Name</th>
                    <th>Room</th>
                    <th>Arrival</th>
                    <th>Departure</th>
                    <th>Guests</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $reservations->fetch_assoc()): 
                    $other_guests = json_decode($row['other_guests'], true);
                    $total_guests = (int)($row['total_guests'] ?? 0);
                    if (!empty($other_guests)) {
                        foreach ($other_guests as $guest) {
                            if (!empty($guest['name'])) $total_guests++;
                        }
                    }
                ?>
                <tr>
                    <td>
                        <div class="booking-no"><?= htmlspecialchars($row['booking_no']) ?></div>
                        <small style="color: #999;"><?= date('M d, Y', strtotime($row['created_at'])) ?></small>
                    </td>
                    <td>
                        <div class="guest-name"><?= htmlspecialchars($row['guest_name']) ?></div>
                        <small style="color: #999;"><?= htmlspecialchars($row['guest_email']) ?></small>
                    </td>
                    <td>
                        <div class="room-info">
                            <i class="bi bi-building"></i> <?= htmlspecialchars($row['room_name']) ?>
                        </div>
                        <small style="color: #999;"><?= htmlspecialchars($row['floor']) ?></small>
                    </td>
                    <td>
                        <div class="date-info">
                            <i class="bi bi-calendar"></i> <?= date('M d, Y', strtotime($row['arrival_date'])) ?>
                        </div>
                        <div class="date-info">
                            <i class="bi bi-clock"></i> <?= date('h:i A', strtotime($row['check_in_time'])) ?>
                        </div>
                    </td>
                    <td>
                        <div class="date-info">
                            <i class="bi bi-calendar"></i> <?= date('M d, Y', strtotime($row['departure_date'])) ?>
                        </div>
                        <div class="date-info">
                            <i class="bi bi-clock"></i> <?= date('h:i A', strtotime($row['check_out_time'])) ?>
                        </div>
                    </td>
                    <td>
                        <div class="date-info">
                            <i class="bi bi-people"></i> <?= (int)$total_guests ?>
                        </div>
                    </td>
                    <td>
                        <span class="status-badge <?= getStatusBadgeClass($row['status']) ?>">
                            <?= strtoupper($row['status']) ?>
                        </span>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <a href="guest_reservation_details.php?id=<?= $row['id'] ?>" class="btn-view" title="View Details">
                                <i class="bi bi-eye"></i>
                            </a>
                            <?php if ($row['status'] === 'pending'): ?>
                            <a href="guest_reservation_details.php?id=<?= $row['id'] ?>#admin-actions" class="btn-view" title="Review" style="background: #fff3cd; border-color: #856404; color: #856404;">
                                <i class="bi bi-hourglass-split"></i>
                            </a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="empty-state">
            <i class="bi bi-door-open"></i>
            <p>No guest reservations found</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Preserve checkbox states on page load
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const statusValues = urlParams.getAll('status');
    
    document.querySelectorAll('input[name="status"]').forEach(checkbox => {
        if (statusValues.includes(checkbox.value)) {
            checkbox.checked = true;
        }
    });
});
</script>

<?php require_once __DIR__ . '/inc/footer.php'; ?>