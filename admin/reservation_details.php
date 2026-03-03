<?php
require_once __DIR__ . '/inc/db_config.php';
require_once __DIR__ . '/inc/essentials.php';
require_once __DIR__ . '/inc/auth.php';

if (!isAdminLoggedIn()) {
    redirect('index.php');
}

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    redirect('reservations.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_status = $_POST['status'] ?? '';
    $admin_remarks = clean($_POST['admin_remarks'] ?? '');
    $reservation_id = (int)($_POST['reservation_id'] ?? 0);

    // Updated allowed statuses - all possible statuses
    if ($reservation_id === $id && in_array($new_status, ['pending', 'pencil_booked', 'approved', 'cancelled'])) {
        $remark_entry = "\n--- " . date("Y-m-d H:i:s") . " (" . ucfirst(str_replace('_', ' ', $new_status)) . ") ---\n" . $admin_remarks;
        $update_stmt = $conn->prepare("UPDATE facility_reservations SET status = ?, admin_remarks = CONCAT(IFNULL(admin_remarks, ''), ?) WHERE id = ?");
        $update_stmt->bind_param("ssi", $new_status, $remark_entry, $id);
        if ($update_stmt->execute()) {
            $_SESSION['success_message'] = "Reservation status updated successfully!";
        } else {
            $_SESSION['error_message'] = "Error updating status: " . $conn->error;
        }
        redirect("reservation_details.php?id=$id");
        exit;
    }
}

$query = "
    SELECT r.*, 
           ot.name as office_type_name, 
           o.name as office_name, 
           et.name as event_type_name, 
           vs.name as venue_setup_name, 
           b.name as banquet_style_name, 
           b.image as banquet_image 
    FROM facility_reservations r 
    LEFT JOIN office_types ot ON r.office_type_id = ot.id 
    LEFT JOIN offices o ON r.office_id = o.id 
    LEFT JOIN event_types et ON r.event_type_id = et.id 
    LEFT JOIN venue_setups vs ON r.venue_setup_id = vs.id 
    LEFT JOIN banquet b ON r.banquet_style_id = b.id 
    WHERE r.id = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error_message'] = "Reservation not found.";
    redirect('reservations.php');
}

$reservation = $result->fetch_assoc();
$stmt->close();

// ---------------------------------------------------------------
// Get all venues from reservation_venues pivot table.
// FALLBACK: old records (pre-pivot-table fix) have nothing there,
// so fall back to the main venue_id + start/end on the reservation.
// ---------------------------------------------------------------
$all_venues = [];

$venue_stmt = $conn->prepare("
    SELECT v.id, v.name, v.floor, rv.start_datetime, rv.end_datetime
    FROM reservation_venues rv
    JOIN venues v ON rv.venue_id = v.id
    WHERE rv.reservation_id = ?
    ORDER BY rv.start_datetime ASC
");
$venue_stmt->bind_param("i", $id);
$venue_stmt->execute();
$venues_result = $venue_stmt->get_result();
while ($vrow = $venues_result->fetch_assoc()) {
    $all_venues[] = $vrow;
}
$venue_stmt->close();

// Fallback for old records not yet in reservation_venues
if (empty($all_venues)) {
    $fb_stmt = $conn->prepare("
        SELECT v.id, v.name, v.floor,
               r.start_datetime, r.end_datetime
        FROM facility_reservations r
        JOIN venues v ON r.venue_id = v.id
        WHERE r.id = ?
    ");
    $fb_stmt->bind_param("i", $id);
    $fb_stmt->execute();
    $fb_result = $fb_stmt->get_result();
    while ($vrow = $fb_result->fetch_assoc()) {
        $all_venues[] = $vrow;
    }
    $fb_stmt->close();
}

function getStatusBadgeClass($status) {
    switch (strtolower($status)) {
        case 'pending':       return 'status-pending';
        case 'pencil_booked': return 'status-pencil';
        case 'approved':      return 'status-approved';
        case 'cancelled':     return 'status-cancelled';
        case 'denied':        return 'status-denied';
        case 'completed':     return 'status-completed';
        default:              return 'status-default';
    }
}

$start = new DateTime($reservation['start_datetime']);
$end   = new DateTime($reservation['end_datetime']);
$interval = $start->diff($end);
$duration_hours = $interval->h + ($interval->days * 24);
if ($interval->i > 0) $duration_hours += $interval->i / 60;
$duration_text = round($duration_hours, 1) . " hours";

$page_title = "Reservation Details - " . htmlspecialchars($reservation['booking_no']);
include 'inc/header.php';
?>

<style>
    :root {
        --bsu-red: #b71c1c;
        --bsu-dark-red: #8b0000;
        --bsu-light-bg: #f8f9fa;
        --card-border: #e9ecef;
    }
    body { background-color: #f8f9fa; }
    .content-area { background-color: #f8f9fa; padding: 1.25rem; }
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
    .page-title { font-size: 1.5rem; font-weight: 600; color: #2c3e50; margin: 0; }
    .btn-back {
        display: inline-flex; align-items: center; gap: 0.5rem;
        padding: 0.6rem 1.2rem; background-color: white; color: #495057;
        border: 1px solid #dee2e6; border-radius: 50px; font-weight: 500;
        font-size: 0.9rem; text-decoration: none; transition: all 0.2s ease;
    }
    .btn-back:hover { background-color: #f8f9fa; border-color: var(--bsu-red); color: var(--bsu-red); transform: translateX(-3px); }
    .details-header {
        background: linear-gradient(135deg, var(--bsu-red), var(--bsu-dark-red));
        color: white; padding: 1.25rem 1.5rem; border-radius: 12px;
        margin-bottom: 1.5rem; box-shadow: 0 4px 12px rgba(183,28,28,0.15);
    }
    .details-header h2 { margin: 0; font-size: 1.5rem; font-weight: 600; }
    .details-header small { opacity: 0.9; font-size: 0.85rem; }
    .status-badge { padding: 0.35rem 0.8rem; border-radius: 50px; font-weight: 600; font-size: 0.8rem; display: inline-block; letter-spacing: 0.3px; }
    .status-pending   { background: #fff3cd; color: #856404; }
    .status-pencil    { background: #e2d5f1; color: #5e3c8b; }
    .status-approved  { background: #d4edda; color: #155724; }
    .status-denied, .status-cancelled { background: #f8d7da; color: #721c24; }
    .status-completed { background: #cce5ff; color: #004085; }
    .status-default   { background: #e9ecef; color: #495057; }
    .detail-card {
        background: white; border: 1px solid var(--card-border); border-radius: 12px;
        margin-bottom: 1.25rem; box-shadow: 0 2px 6px rgba(0,0,0,0.02);
        transition: all 0.2s ease; overflow: hidden;
    }
    .detail-card:hover { border-color: var(--bsu-red); box-shadow: 0 4px 12px rgba(183,28,28,0.08); }
    .card-header-custom {
        background-color: var(--bsu-red); color: white;
        padding: 0.75rem 1.25rem; font-weight: 600; font-size: 0.95rem;
        border-bottom: 1px solid var(--bsu-dark-red);
    }
    .card-header-custom i { margin-right: 0.5rem; color: white; font-size: 1rem; }
    .card-body { background: white; padding: 1.25rem; }
    .detail-item { display: flex; align-items: flex-start; padding: 0.5rem 0; border-bottom: 1px solid #f0f0f0; }
    .detail-item:last-child { border-bottom: none; }
    .detail-item i { font-size: 1rem; color: var(--bsu-red); margin-right: 0.75rem; width: 20px; text-align: center; }
    .detail-item-content { flex: 1; }
    .detail-item-content .label { font-weight: 600; color: #495057; margin-bottom: 0.1rem; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.3px; }
    .detail-item-content .value { color: #212529; font-size: 0.9rem; line-height: 1.4; }
    .venue-row { padding: 0.5rem 0; border-bottom: 1px dashed #eee; }
    .venue-row:last-child { border-bottom: none; }
    .venue-name { font-weight: 600; color: #2c3e50; font-size: 0.95rem; margin-bottom: 0.15rem; }
    .venue-name i { font-size: 0.9rem; margin-right: 0.4rem; color: var(--bsu-red); }
    .venue-schedule { font-size: 0.85rem; color: #6c757d; padding-left: 1.6rem; }
    .venue-schedule i { font-size: 0.8rem; margin-right: 0.3rem; color: #6c757d; }
    .banquet-thumbnail { width: 60px; height: 60px; object-fit: cover; border-radius: 6px; border: 1px solid var(--card-border); transition: all 0.2s ease; }
    .banquet-thumbnail:hover { border-color: var(--bsu-red); }
    #adminRemarksHistory { background-color: #f8f9fa; border-radius: 6px; padding: 0.75rem; max-height: 150px; overflow-y: auto; white-space: pre-wrap; font-size: 0.85rem; color: #495057; border: 1px solid var(--card-border); }
    .misc-list { list-style: none; padding: 0; margin: 0; }
    .misc-list li { display: flex; align-items: center; padding: 0.35rem 0; border-bottom: 1px dashed #eee; font-size: 0.9rem; }
    .misc-list li:last-child { border-bottom: none; }
    .misc-list i { color: var(--bsu-red); margin-right: 0.5rem; font-size: 0.9rem; }
    .form-select, .form-control { font-size: 0.9rem; padding: 0.4rem 0.75rem; border-radius: 6px; }
    .btn-update { background-color: var(--bsu-red); color: white; border: none; padding: 0.6rem 1rem; border-radius: 6px; font-weight: 500; font-size: 0.9rem; transition: all 0.2s ease; }
    .btn-update:hover { background-color: var(--bsu-dark-red); transform: translateY(-1px); }
    .row { margin-left: -0.75rem; margin-right: -0.75rem; }
    .col-lg-7, .col-lg-5 { padding-left: 0.75rem; padding-right: 0.75rem; }
    @media (max-width: 768px) {
        .page-header { flex-direction: column; gap: 0.75rem; align-items: flex-start; }
    }
</style>

<div class="content-area">
    <div class="page-header">
        <h1 class="page-title">Reservation Details</h1>
        <a href="reservations.php" class="btn-back"><i class="bi bi-arrow-left"></i> Back to Reservations</a>
    </div>

    <?php display_messages(); ?>

    <div class="details-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h2><?= htmlspecialchars($reservation['booking_no']) ?></h2>
                <small>Reservation No: <?= htmlspecialchars($reservation['reservation_no']) ?></small>
            </div>
            <div class="text-end">
                <span class="status-badge <?= getStatusBadgeClass($reservation['status']) ?>">
                    <?= htmlspecialchars(strtoupper(str_replace('_', ' ', $reservation['status']))) ?>
                </span>
                <div class="mt-1"><small>Submitted: <?= date("M d, Y", strtotime($reservation['created_at'])) ?></small></div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Left Column -->
        <div class="col-lg-7">
            <!-- Requester Info -->
            <div class="detail-card">
                <div class="card-header-custom"><i class="bi bi-person-circle"></i> Requester Information</div>
                <div class="card-body">
                    <div class="detail-item">
                        <i class="bi bi-person-badge"></i>
                        <div class="detail-item-content">
                            <div class="label">Full Name</div>
                            <div class="value"><?= htmlspecialchars($reservation['last_name'] . ', ' . $reservation['first_name'] . ' ' . $reservation['middle_initial']) ?></div>
                        </div>
                    </div>
                    <div class="detail-item">
                        <i class="bi bi-envelope"></i>
                        <div class="detail-item-content">
                            <div class="label">Email</div>
                            <div class="value"><?= htmlspecialchars($reservation['email']) ?></div>
                        </div>
                    </div>
                    <div class="detail-item">
                        <i class="bi bi-telephone"></i>
                        <div class="detail-item-content">
                            <div class="label">Contact</div>
                            <div class="value"><?= htmlspecialchars($reservation['contact_number']) ?></div>
                        </div>
                    </div>
                    <div class="detail-item">
                        <i class="bi bi-building"></i>
                        <div class="detail-item-content">
                            <div class="label">Office</div>
                            <div class="value">
                                <?php
                                echo htmlspecialchars($reservation['office_type_name'] ?? 'N/A');
                                if (($reservation['office_type_name'] ?? '') === 'External') {
                                    echo ' - ' . htmlspecialchars($reservation['external_office_name'] ?? '');
                                } else {
                                    echo ' - ' . htmlspecialchars($reservation['office_name'] ?? '');
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Event Details -->
            <div class="detail-card">
                <div class="card-header-custom"><i class="bi bi-calendar-event"></i> Event Details</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="detail-item">
                                <i class="bi bi-flag"></i>
                                <div class="detail-item-content">
                                    <div class="label">Activity</div>
                                    <div class="value"><?= htmlspecialchars($reservation['activity_name']) ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-item">
                                <i class="bi bi-tag"></i>
                                <div class="detail-item-content">
                                    <div class="label">Event Type</div>
                                    <div class="value"><?= htmlspecialchars($reservation['event_type_name'] ?? 'N/A') ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="detail-item">
                                <i class="bi bi-people"></i>
                                <div class="detail-item-content">
                                    <div class="label">Participants</div>
                                    <div class="value"><?= htmlspecialchars($reservation['participants_count']) ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-item">
                                <i class="bi bi-clock"></i>
                                <div class="detail-item-content">
                                    <div class="label">Duration (First Venue)</div>
                                    <div class="value"><?= $duration_text ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-lg-5">

            <!-- Venues Card -->
            <div class="detail-card">
                <div class="card-header-custom">
                    <i class="bi bi-pin-map"></i> Venues (<?= count($all_venues) ?>)
                </div>
                <div class="card-body">
                    <?php if (!empty($all_venues)): ?>
                        <?php
                        $venues_by_date = [];
                        foreach ($all_venues as $venue) {
                            $date_key = date("M j, Y", strtotime($venue['start_datetime']));
                            $venues_by_date[$date_key][] = $venue;
                        }
                        ?>
                        <?php foreach ($venues_by_date as $date => $date_venues): ?>
                            <div class="mb-3">
                                <div style="color:var(--bsu-red);font-weight:600;font-size:0.9rem;margin-bottom:0.5rem;">
                                    <i class="bi bi-calendar3 me-1"></i><?= $date ?>
                                </div>
                                <?php foreach ($date_venues as $venue): ?>
                                    <div class="venue-row">
                                        <div class="venue-name">
                                            <i class="bi bi-building"></i><?= htmlspecialchars($venue['name'] . ' (' . $venue['floor'] . ')') ?>
                                        </div>
                                        <div class="venue-schedule">
                                            <i class="bi bi-clock"></i>
                                            <?= date("g:i A", strtotime($venue['start_datetime'])) ?> —
                                            <?= date("g:i A", strtotime($venue['end_datetime'])) ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                        <div class="mt-2 pt-2 border-top small text-muted">
                            <i class="bi bi-hourglass-split me-1"></i>
                            Total of <?= count($all_venues) ?> venue<?= count($all_venues) > 1 ? 's' : '' ?> across <?= count($venues_by_date) ?> day<?= count($venues_by_date) > 1 ? 's' : '' ?>
                        </div>
                    <?php else: ?>
                        <div class="text-muted small p-2">
                            <i class="bi bi-exclamation-circle"></i> No venue information available.
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Setup & Style -->
            <div class="detail-card">
                <div class="card-header-custom"><i class="bi bi-gear"></i> Setup & Style</div>
                <div class="card-body">
                    <div class="detail-item">
                        <i class="bi bi-easel2"></i>
                        <div class="detail-item-content">
                            <div class="label">Venue Setup</div>
                            <div class="value">
                                <?php
                                $setup_name = $reservation['venue_setup_name'] ?? null;
                                echo !empty($setup_name)
                                    ? htmlspecialchars($setup_name)
                                    : '<span class="text-muted fst-italic">Not specified</span>';
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="detail-item">
                        <i class="bi bi-grid-1x2"></i>
                        <div class="detail-item-content">
                            <div class="label">Banquet Style</div>
                            <div class="d-flex align-items-center flex-wrap gap-2">
                                <?php if (!empty($reservation['banquet_image'])): ?>
                                    <img src="../assets/images/banquet/<?= htmlspecialchars($reservation['banquet_image']) ?>"
                                         alt="<?= htmlspecialchars($reservation['banquet_style_name'] ?? 'Banquet') ?>"
                                         class="banquet-thumbnail me-2"
                                         onerror="this.style.display='none'">
                                <?php endif; ?>
                                <span class="value">
                                    <?= !empty($reservation['banquet_style_name'])
                                        ? htmlspecialchars($reservation['banquet_style_name'])
                                        : '<span class="text-muted fst-italic">No banquet style selected</span>' ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Miscellaneous Items -->
            <div class="detail-card">
                <div class="card-header-custom"><i class="bi bi-box-seam"></i> Miscellaneous</div>
                <div class="card-body">
                    <?php
                    $misc_items = json_decode($reservation['miscellaneous_items'], true);
                    if (json_last_error() === JSON_ERROR_NONE && !empty($misc_items)):
                    ?>
                        <ul class="misc-list">
                        <?php foreach ($misc_items as $key => $item): ?>
                            <li>
                                <i class="bi bi-dot"></i>
                                <?php if (is_array($item)):
                                    $label = ucwords(str_replace('_', ' ', $key));
                                    echo '<strong>' . htmlspecialchars($label) . ':</strong> ';
                                    $parts = [];
                                    foreach ($item as $sub_key => $sub_value) {
                                        $parts[] = htmlspecialchars(ucfirst($sub_key)) . ': ' . htmlspecialchars($sub_value);
                                    }
                                    echo implode(', ', $parts);
                                else:
                                    echo '<strong>' . htmlspecialchars(ucwords(str_replace('_', ' ', $key))) . ':</strong> ' . htmlspecialchars($item) . ' pcs';
                                endif; ?>
                            </li>
                        <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="text-muted mb-0 small">No miscellaneous items requested.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Additional Instructions -->
            <div class="detail-card">
                <div class="card-header-custom"><i class="bi bi-chat-text"></i> Instructions</div>
                <div class="card-body">
                    <p class="mb-0 small">
                        <?= $reservation['additional_instruction']
                            ? nl2br(htmlspecialchars($reservation['additional_instruction']))
                            : '<span class="text-muted">No additional instructions provided.</span>' ?>
                    </p>
                </div>
            </div>

            <!-- Admin Actions - FIXED: Now allows updates from any status -->
            <div class="detail-card">
                <div class="card-header-custom"><i class="bi bi-pencil-square"></i> Admin Actions</div>
                <div class="card-body">
                    <form id="statusUpdateForm" method="POST">
                        <input type="hidden" name="reservation_id" value="<?= $id ?>">
                        <div class="mb-2">
                            <label for="status" class="form-label small fw-bold mb-1">Update Status</label>
                            <select class="form-select form-select-sm" id="status" name="status">
                                <option value="pending" <?= ($reservation['status'] === 'pending') ? 'selected' : '' ?>>Pending</option>
                                <option value="pencil_booked" <?= ($reservation['status'] === 'pencil_booked') ? 'selected' : '' ?>>Pencil Booked</option>
                                <option value="approved" <?= ($reservation['status'] === 'approved') ? 'selected' : '' ?>>Approved</option>
                                <option value="cancelled" <?= ($reservation['status'] === 'cancelled') ? 'selected' : '' ?>>Cancelled</option>
                            </select>
                            <small class="text-muted">Current status: <strong><?= str_replace('_', ' ', ucwords($reservation['status'])) ?></strong></small>
                        </div>
                        <div class="mb-3">
                            <label for="admin_remarks" class="form-label small fw-bold mb-1">Admin Remarks</label>
                            <textarea class="form-control form-control-sm" id="admin_remarks" name="admin_remarks"
                                rows="3" placeholder="Add remarks..." required></textarea>
                        </div>
                        <button type="submit" class="btn-update w-100">
                            <i class="bi bi-check-circle me-1"></i> Update Status
                        </button>
                    </form>
                </div>
            </div>

            <!-- Remarks History -->
            <div class="detail-card">
                <div class="card-header-custom"><i class="bi bi-clock-history"></i> Remarks History</div>
                <div class="card-body">
                    <?php if (!empty($reservation['admin_remarks'])): ?>
                        <div id="adminRemarksHistory"><?= nl2br(htmlspecialchars(trim($reservation['admin_remarks']))) ?></div>
                    <?php else: ?>
                        <p class="text-muted mb-0 small">No remarks have been added yet.</p>
                    <?php endif; ?>
                </div>
            </div>

        </div><!-- /col-lg-5 -->
    </div><!-- /row -->
</div><!-- /content-area -->

<?php include 'inc/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('statusUpdateForm');
    if (!form) return;
    
    form.addEventListener('submit', function(e) {
        const statusSelect = document.getElementById('status');
        const remarksText  = document.getElementById('admin_remarks');
        const currentStatus = '<?= $reservation['status'] ?>';

        // Check if they actually changed the status
        if (statusSelect.value === currentStatus) {
            e.preventDefault();
            alert('Please select a different status than the current one.');
            return;
        }
        
        if (remarksText.value.trim() === '') {
            e.preventDefault();
            alert('Admin remarks are required when updating reservation status.');
            return;
        }
        
        let statusDisplay = statusSelect.value.replace('_', ' ').toUpperCase();
        if (!confirm('Are you sure you want to change this reservation to ' + statusDisplay + '?')) {
            e.preventDefault();
        }
    });
});
</script>