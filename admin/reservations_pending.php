<?php
$pageTitle = 'Pending & Pencil Booked Reservations';
require_once __DIR__ . '/inc/header.php';

// Handle approval/pencil/denial actions
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $id = (int)$_POST['id'];
    $action = $_POST['action'];
    
    if (in_array($action, ['approve', 'pencil', 'deny'])) {
        if ($action === 'approve') {
            $status = 'approved';
        } elseif ($action === 'pencil') {
            $status = 'pencil_booked';
        } else {
            $status = 'denied';
        }
        
        // Update reservation status
        $stmt = $conn->prepare("UPDATE facility_reservations SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $id);
        
        if ($stmt->execute()) {
            $action_text = $action === 'approve' ? 'approved' : ($action === 'pencil' ? 'marked as pencil booked' : 'denied');
            $message = '<div class="alert alert-success">Reservation ' . $action_text . ' successfully!</div>';
            
            // Log the action
            logAdminAction($conn, "Reservation $action", "Reservation ID: $id");
        } else {
            $message = '<div class="alert alert-danger">Error updating reservation.</div>';
        }
    }
}

// Get all pending and pencil booked reservations
$reservations = $conn->query("
    SELECT r.*, 
           v.name as venue_name, 
           v.floor,
           CONCAT(r.last_name, ', ', r.first_name) as requester_name
    FROM facility_reservations r
    JOIN venues v ON r.venue_id = v.id
    WHERE r.status IN ('pending', 'pencil_booked')
    ORDER BY 
        CASE r.status 
            WHEN 'pending' THEN 1 
            WHEN 'pencil_booked' THEN 2 
        END,
        r.start_datetime ASC
");

// Get separate counts
$pending_count = $conn->query("SELECT COUNT(*) as count FROM facility_reservations WHERE status = 'pending'")->fetch_assoc()['count'];
$pencil_count = $conn->query("SELECT COUNT(*) as count FROM facility_reservations WHERE status = 'pencil_booked'")->fetch_assoc()['count'];
$total_count = $pending_count + $pencil_count;
?>

<style>
/* Pending Reservations Specific Styles */
.pending-container {
    padding: 1rem 0;
}

/* Status Tabs */
.status-tabs {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 1.5rem;
    border-bottom: 2px solid #f0f0f0;
    padding-bottom: 0.5rem;
}

.status-tab {
    padding: 0.6rem 1.5rem;
    border-radius: 50px;
    font-weight: 600;
    font-size: 0.9rem;
    cursor: pointer;
    transition: all 0.2s ease;
    border: 2px solid transparent;
}

.status-tab {
    background: rgb(255, 183, 183);
    color:rgba(128, 85, 85, 0.94);
}

.pending-tab{
    background: #fff3cd;
    color: #856404;
}

.status-tab.pencil-tab {
    background: #e2d5f1;
    color: #5e3c8b;
}

.status-tab.active {
    border-color: var(--bsu-red);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.status-tab .count {
    background: white;
    padding: 0.1rem 0.5rem;
    border-radius: 20px;
    margin-left: 0.5rem;
    font-size: 0.8rem;
}

.stats-mini {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}

.stat-mini-card {
    background: white;
    border-radius: 12px;
    padding: 1rem;
    border-left: 4px solid var(--bsu-red);
    box-shadow: 0 4px 10px rgba(0,0,0,0.03);
    transition: all 0.3s ease;
}

.stat-mini-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(183,28,28,0.1);
}

.stat-mini-number {
    font-size: 1.8rem;
    font-weight: 700;
    color: #333;
    line-height: 1.2;
}

.stat-mini-label {
    color: #666;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Reservation Cards */
.reservation-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 1.5rem;
    margin-top: 1.5rem;
}

.reservation-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid #f0f0f0;
    animation: fadeInUp 0.5s ease;
    animation-fill-mode: both;
}

.reservation-card.pending-card {
    border-left: 4px solid #ffc107;
}

.reservation-card.pencil-card {
    border-left: 4px solid #5e3c8b;
}

.reservation-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(183,28,28,0.15);
    border-color: var(--bsu-red);
}

.reservation-header {
    background: linear-gradient(135deg, #f8f9fa, #fff);
    padding: 1.25rem;
    border-bottom: 2px solid #f0f0f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.reservation-header h3 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--bsu-red);
}

.reservation-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 50px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.reservation-badge.pending {
    background: #fff3cd;
    color: #856404;
}

.reservation-badge.pencil {
    background: #e2d5f1;
    color: #5e3c8b;
}

.reservation-body {
    padding: 1.25rem;
}

.reservation-detail {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 0.75rem;
    padding: 0.5rem;
    border-radius: 8px;
    transition: background 0.2s ease;
}

.reservation-detail:hover {
    background: #f8f9fa;
}

.detail-icon {
    width: 35px;
    height: 35px;
    background: #fdeae8;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--bsu-red);
    font-size: 1rem;
}

.detail-content {
    flex: 1;
}

.detail-label {
    font-size: 0.7rem;
    color: #999;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.detail-value {
    font-weight: 500;
    color: #333;
    font-size: 0.9rem;
}

.reservation-footer {
    padding: 1rem 1.25rem;
    background: #f8f9fa;
    border-top: 1px solid #f0f0f0;
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.btn-action {
    flex: 1;
    padding: 0.6rem;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.9rem;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    min-width: 80px;
}

.btn-approve {
    background: #d4edda;
    color: #155724;
}

.btn-approve:hover {
    background: #28a745;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
}

.btn-pencil {
    background: #e2d5f1;
    color: #5e3c8b;
}

.btn-pencil:hover {
    background: #5e3c8b;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(94, 60, 139, 0.3);
}

.btn-deny {
    background: #f8d7da;
    color: #721c24;
}

.btn-deny:hover {
    background: #dc3545;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3);
}

.btn-view {
    background: white;
    color: var(--bsu-red);
    border: 2px solid var(--bsu-red);
    min-width: 50px;
    flex: 0.5;
}

.btn-view:hover {
    background: var(--bsu-red);
    color: white;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: white;
    border-radius: 16px;
    border: 2px dashed #f0f0f0;
}

.empty-state i {
    font-size: 4rem;
    color: #ddd;
    margin-bottom: 1rem;
}

.empty-state h3 {
    color: #333;
    margin-bottom: 0.5rem;
}

.empty-state p {
    color: #999;
}

/* Animation delays */
.reservation-card:nth-child(1) { animation-delay: 0.1s; }
.reservation-card:nth-child(2) { animation-delay: 0.2s; }
.reservation-card:nth-child(3) { animation-delay: 0.3s; }
.reservation-card:nth-child(4) { animation-delay: 0.4s; }
.reservation-card:nth-child(5) { animation-delay: 0.5s; }
.reservation-card:nth-child(6) { animation-delay: 0.6s; }
.reservation-card:nth-child(7) { animation-delay: 0.7s; }
.reservation-card:nth-child(8) { animation-delay: 0.8s; }

/* Quick Stats */
.quick-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
    margin-bottom: 2rem;
}

.quick-stat-card {
    background: white;
    border-radius: 12px;
    padding: 1rem;
    text-align: center;
    box-shadow: 0 2px 8px rgba(0,0,0,0.03);
    border: 1px solid #f0f0f0;
    transition: all 0.3s ease;
}

.quick-stat-card:hover {
    transform: translateY(-3px);
    border-color: var(--bsu-red);
    box-shadow: 0 8px 20px rgba(183,28,28,0.1);
}

.quick-stat-number {
    font-size: 1.8rem;
    font-weight: 700;
    color: var(--bsu-red);
    line-height: 1.2;
}

.quick-stat-label {
    font-size: 0.8rem;
    color: #666;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Filter controls */
.filter-controls {
    display: flex;
    gap: 1rem;
    margin-bottom: 1.5rem;
    align-items: center;
}

.filter-select {
    padding: 0.5rem 1rem;
    border: 2px solid #e9ecef;
    border-radius: 10px;
    font-size: 0.9rem;
    background: white;
    cursor: pointer;
}

.filter-select:focus {
    border-color: var(--bsu-red);
    outline: none;
}

/* Responsive */
@media (max-width: 768px) {
    .reservation-grid {
        grid-template-columns: 1fr;
    }
    
    .quick-stats {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .reservation-footer {
        flex-wrap: wrap;
    }
    
    .btn-action {
        flex: 1 1 calc(50% - 0.5rem);
    }
}

@media (max-width: 480px) {
    .reservation-footer {
        flex-direction: column;
    }
    
    .btn-action {
        width: 100%;
    }
    
    .quick-stats {
        grid-template-columns: 1fr;
    }
}

/* =============================================
   CUSTOM CONFIRMATION MODAL
   ============================================= */
.confirm-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 10000;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(2px);
    animation: fadeIn 0.2s ease;
}

.confirm-overlay.active {
    display: flex;
}

.confirm-modal {
    background: white;
    border-radius: 20px;
    width: 100%;
    max-width: 420px;
    margin: 1rem;
    box-shadow: 0 25px 60px rgba(0, 0, 0, 0.25);
    overflow: hidden;
    animation: slideUp 0.25s ease;
}

.confirm-modal-header {
    padding: 1.5rem 1.5rem 0;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.confirm-modal-icon {
    width: 52px;
    height: 52px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    flex-shrink: 0;
}

.confirm-modal-icon.approve {
    background: #d4edda;
    color: #28a745;
}

.confirm-modal-icon.pencil {
    background: #e2d5f1;
    color: #5e3c8b;
}

.confirm-modal-icon.deny {
    background: #f8d7da;
    color: #dc3545;
}

.confirm-modal-title {
    font-size: 1.2rem;
    font-weight: 700;
    color: #2c3e50;
    margin: 0;
}

.confirm-modal-body {
    padding: 1rem 1.5rem 1.5rem;
}

.confirm-modal-message {
    color: #555;
    font-size: 0.95rem;
    line-height: 1.6;
    margin: 0;
}

.confirm-modal-activity {
    margin-top: 0.75rem;
    padding: 0.75rem 1rem;
    background: #f8f9fa;
    border-radius: 10px;
    border-left: 3px solid var(--bsu-red);
    font-weight: 600;
    color: #2c3e50;
    font-size: 0.9rem;
}

.confirm-modal-footer {
    padding: 1rem 1.5rem 1.5rem;
    display: flex;
    gap: 0.75rem;
    justify-content: flex-end;
}

.confirm-btn {
    padding: 0.6rem 1.5rem;
    border-radius: 10px;
    border: none;
    font-weight: 600;
    font-size: 0.95rem;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 0.4rem;
}

.confirm-btn-cancel {
    background: #f0f0f0;
    color: #555;
}

.confirm-btn-cancel:hover {
    background: #e0e0e0;
}

.confirm-btn-approve {
    background: #28a745;
    color: white;
}

.confirm-btn-approve:hover {
    background: #218838;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(40, 167, 69, 0.35);
}

.confirm-btn-pencil {
    background: #5e3c8b;
    color: white;
}

.confirm-btn-pencil:hover {
    background: #4a2e6f;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(94, 60, 139, 0.35);
}

.confirm-btn-deny {
    background: #dc3545;
    color: white;
}

.confirm-btn-deny:hover {
    background: #c82333;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(220, 53, 69, 0.35);
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideUp {
    from { transform: translateY(30px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}
</style>

<div class="content-area pending-container">
    <!-- Message Display -->
    <div id="messageContainer">
        <?= $message ?>
    </div>

    <!-- Status Tabs -->
    <div class="status-tabs">
        <div class="status-tab active" onclick="filterReservations('all')">
            All <span class="count"><?= $total_count ?></span>
        </div>
        <div class="status-tab pending-tab" onclick="filterReservations('pending')">
            Pending <span class="count"><?= $pending_count ?></span>
        </div>
        <div class="status-tab pencil-tab" onclick="filterReservations('pencil')">
            Pencil Booked <span class="count"><?= $pencil_count ?></span>
        </div>
    </div>

    <!-- Quick Stats -->
    <?php
    $oldest_pending = $conn->query("SELECT MIN(start_datetime) as oldest FROM facility_reservations WHERE status = 'pending'")->fetch_assoc()['oldest'];
    $oldest_pencil = $conn->query("SELECT MIN(start_datetime) as oldest FROM facility_reservations WHERE status = 'pencil_booked'")->fetch_assoc()['oldest'];
    $most_requested = $conn->query("
        SELECT v.name, COUNT(*) as count 
        FROM facility_reservations r 
        JOIN venues v ON r.venue_id = v.id 
        WHERE r.status IN ('pending', 'pencil_booked')
        GROUP BY v.name 
        ORDER BY count DESC 
        LIMIT 1
    ")->fetch_assoc();
    ?>
    
    <div class="quick-stats">
        <div class="quick-stat-card">
            <div class="quick-stat-number"><?= $total_count ?></div>
            <div class="quick-stat-label">Total to Review</div>
        </div>
        <div class="quick-stat-card">
            <div class="quick-stat-number"><?= $pending_count ?></div>
            <div class="quick-stat-label">Pending</div>
        </div>
        <div class="quick-stat-card">
            <div class="quick-stat-number"><?= $pencil_count ?></div>
            <div class="quick-stat-label">Pencil Booked</div>
        </div>
        <div class="quick-stat-card">
            <div class="quick-stat-number"><?= $most_requested ? substr($most_requested['name'], 0, 10) . '...' : 'N/A' ?></div>
            <div class="quick-stat-label">Top Venue</div>
        </div>
    </div>

    <!-- Reservations Grid -->
    <?php if ($total_count > 0): ?>
        <div class="reservation-grid" id="reservationGrid">
            <?php 
            $reservations->data_seek(0); // Reset pointer
            while ($row = $reservations->fetch_assoc()): 
                $is_pending = $row['status'] === 'pending';
                $card_class = $is_pending ? 'pending-card' : 'pencil-card';
                $badge_class = $is_pending ? 'pending' : 'pencil';
                $badge_text = $is_pending ? 'PENDING' : 'PENCIL BOOKED';
            ?>
            <div class="reservation-card <?= $card_class ?>" data-status="<?= $row['status'] ?>" id="reservation-<?= $row['id'] ?>">
                <div class="reservation-header">
                    <h3><?= htmlspecialchars($row['activity_name']) ?></h3>
                    <span class="reservation-badge <?= $badge_class ?>"><?= $badge_text ?></span>
                </div>
                <div class="reservation-body">
                    <div class="reservation-detail">
                        <div class="detail-icon"><i class="bi bi-person"></i></div>
                        <div class="detail-content">
                            <div class="detail-label">Requester</div>
                            <div class="detail-value"><?= htmlspecialchars($row['requester_name']) ?></div>
                        </div>
                    </div>
                    
                    <div class="reservation-detail">
                        <div class="detail-icon"><i class="bi bi-calendar"></i></div>
                        <div class="detail-content">
                            <div class="detail-label">Date & Time</div>
                            <div class="detail-value">
                                <?= date('M d, Y', strtotime($row['start_datetime'])) ?><br>
                                <small><?= date('h:i A', strtotime($row['start_datetime'])) ?> - <?= date('h:i A', strtotime($row['end_datetime'])) ?></small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="reservation-detail">
                        <div class="detail-icon"><i class="bi bi-building"></i></div>
                        <div class="detail-content">
                            <div class="detail-label">Venue</div>
                            <div class="detail-value"><?= htmlspecialchars($row['venue_name'] . ' (' . $row['floor'] . ')') ?></div>
                        </div>
                    </div>
                    
                    <div class="reservation-detail">
                        <div class="detail-icon"><i class="bi bi-people"></i></div>
                        <div class="detail-content">
                            <div class="detail-label">Participants</div>
                            <div class="detail-value"><?= $row['participants_count'] ?></div>
                        </div>
                    </div>
                    
                    <?php if (!empty($row['additional_instruction'])): ?>
                    <div class="reservation-detail">
                        <div class="detail-icon"><i class="bi bi-chat"></i></div>
                        <div class="detail-content">
                            <div class="detail-label">Instructions</div>
                            <div class="detail-value"><?= htmlspecialchars(substr($row['additional_instruction'], 0, 50)) ?>...</div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="reservation-footer">
                    <?php if ($is_pending): ?>
                        <button class="btn-action btn-approve" 
                            onclick="handleAction(<?= $row['id'] ?>, 'approve', '<?= htmlspecialchars(addslashes($row['activity_name'])) ?>')">
                            <i class="bi bi-check-lg"></i> Approve
                        </button>
                        <button class="btn-action btn-pencil" 
                            onclick="handleAction(<?= $row['id'] ?>, 'pencil', '<?= htmlspecialchars(addslashes($row['activity_name'])) ?>')">
                            <i class="bi bi-pencil"></i> Pencil
                        </button>
                    <?php else: ?>
                        <button class="btn-action btn-approve" 
                            onclick="handleAction(<?= $row['id'] ?>, 'approve', '<?= htmlspecialchars(addslashes($row['activity_name'])) ?>')">
                            <i class="bi bi-check-lg"></i> Approve
                        </button>
                    <?php endif; ?>
                    <button class="btn-action btn-deny" 
                        onclick="handleAction(<?= $row['id'] ?>, 'deny', '<?= htmlspecialchars(addslashes($row['activity_name'])) ?>')">
                        <i class="bi bi-x-lg"></i> Deny
                    </button>
                    <button class="btn-action btn-view" onclick="viewDetails(<?= $row['id'] ?>)">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <i class="bi bi-check-circle"></i>
            <h3>No Reservations to Review</h3>
            <p>All caught up! There are no pending or pencil booked reservations.</p>
        </div>
    <?php endif; ?>
</div>

<!-- Custom Confirmation Modal -->
<div class="confirm-overlay" id="confirmOverlay">
    <div class="confirm-modal">
        <div class="confirm-modal-header">
            <div class="confirm-modal-icon" id="confirmIcon">
                <i id="confirmIconInner" class="bi bi-check-lg"></i>
            </div>
            <h5 class="confirm-modal-title" id="confirmTitle">Confirm Action</h5>
        </div>
        <div class="confirm-modal-body">
            <p class="confirm-modal-message" id="confirmMessage"></p>
            <div class="confirm-modal-activity" id="confirmActivity"></div>
        </div>
        <div class="confirm-modal-footer">
            <button class="confirm-btn confirm-btn-cancel" onclick="closeConfirmModal()">
                <i class="bi bi-x"></i> Cancel
            </button>
            <button class="confirm-btn" id="confirmActionBtn" onclick="submitAction()">
                <i class="bi bi-check-lg"></i> Confirm
            </button>
        </div>
    </div>
</div>

<!-- Hidden form for actions -->
<form id="actionForm" method="POST" style="display: none;">
    <input type="hidden" name="id" id="actionId">
    <input type="hidden" name="action" id="actionType">
</form>

<script>
// Store pending action details
let pendingActionId = null;
let pendingActionType = null;

function handleAction(id, action, activityName) {
    pendingActionId = id;
    pendingActionType = action;

    const isApprove = action === 'approve';
    const isPencil = action === 'pencil';
    const isDeny = action === 'deny';
    
    const overlay = document.getElementById('confirmOverlay');
    const icon = document.getElementById('confirmIcon');
    const iconInner = document.getElementById('confirmIconInner');
    const title = document.getElementById('confirmTitle');
    const message = document.getElementById('confirmMessage');
    const activity = document.getElementById('confirmActivity');
    const actionBtn = document.getElementById('confirmActionBtn');

    // Set icon, colors, and text based on action
    if (isApprove) {
        icon.className = 'confirm-modal-icon approve';
        iconInner.className = 'bi bi-check-lg';
        title.textContent = 'Approve Reservation';
        message.textContent = 'Are you sure you want to approve this reservation? The requester will be notified.';
        actionBtn.className = 'confirm-btn confirm-btn-approve';
        actionBtn.innerHTML = '<i class="bi bi-check-lg"></i> Yes, Approve';
    } else if (isPencil) {
        icon.className = 'confirm-modal-icon pencil';
        iconInner.className = 'bi bi-pencil';
        title.textContent = 'Mark as Pencil Booked';
        message.textContent = 'Are you sure you want to mark this as pencil booked? This indicates the reservation is tentatively scheduled pending requirements.';
        actionBtn.className = 'confirm-btn confirm-btn-pencil';
        actionBtn.innerHTML = '<i class="bi bi-pencil"></i> Yes, Pencil Book';
    } else {
        icon.className = 'confirm-modal-icon deny';
        iconInner.className = 'bi bi-x-lg';
        title.textContent = 'Deny Reservation';
        message.textContent = 'Are you sure you want to deny this reservation? This action cannot be undone.';
        actionBtn.className = 'confirm-btn confirm-btn-deny';
        actionBtn.innerHTML = '<i class="bi bi-x-lg"></i> Yes, Deny';
    }

    activity.textContent = activityName;

    // Show modal
    overlay.classList.add('active');
}

function closeConfirmModal() {
    document.getElementById('confirmOverlay').classList.remove('active');
    pendingActionId = null;
    pendingActionType = null;
}

function submitAction() {
    if (!pendingActionId || !pendingActionType) return;

    document.getElementById('actionId').value = pendingActionId;
    document.getElementById('actionType').value = pendingActionType;
    document.getElementById('actionForm').submit();
}

// Filter reservations by status
function filterReservations(status) {
    const cards = document.querySelectorAll('.reservation-card');
    const tabs = document.querySelectorAll('.status-tab');
    
    // Update active tab
    tabs.forEach(tab => tab.classList.remove('active'));
    if (status === 'all') {
        tabs[0].classList.add('active');
    } else if (status === 'pending') {
        tabs[1].classList.add('active');
    } else {
        tabs[2].classList.add('active');
    }
    
    // Filter cards
    cards.forEach(card => {
        if (status === 'all') {
            card.style.display = 'block';
        } else if (status === 'pending') {
            card.style.display = card.dataset.status === 'pending' ? 'block' : 'none';
        } else {
            card.style.display = card.dataset.status === 'pencil_booked' ? 'block' : 'none';
        }
    });
}

// Close modal when clicking the backdrop
document.getElementById('confirmOverlay').addEventListener('click', function(e) {
    if (e.target === this) {
        closeConfirmModal();
    }
});

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeConfirmModal();
    }
});

function viewDetails(id) {
    window.location.href = 'reservation_details.php?id=' + id;
}

// Real-time updates — reload every 30 seconds
function checkForUpdates() {
    setTimeout(function() {
        location.reload();
    }, 30000);
}
checkForUpdates();

// Auto-hide messages after 3 seconds
setTimeout(function() {
    var messageContainer = document.getElementById('messageContainer');
    if (messageContainer && messageContainer.children.length > 0) {
        messageContainer.innerHTML = '';
    }
}, 3000);
</script>

<?php require_once __DIR__ . '/inc/footer.php'; ?>