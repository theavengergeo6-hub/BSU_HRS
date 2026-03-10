<?php
require_once __DIR__ . '/inc/auth.php';
requireAdminLogin();

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    redirect('guest_reservations.php');
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $new_status = $_POST['status'] ?? '';
    $admin_remarks = clean($_POST['admin_remarks'] ?? '');
    
    if (in_array($new_status, ['pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled', 'no_show'], true)) {
        $remark_entry = "\n--- " . date("Y-m-d H:i:s") . " (" . ucfirst($new_status) . ") ---\n" . $admin_remarks;

        $update_stmt = $conn->prepare("UPDATE guest_room_reservations SET status = ?, admin_remarks = CONCAT(IFNULL(admin_remarks, ''), ?) WHERE id = ? AND deleted = 0");
        $update_stmt->bind_param("ssi", $new_status, $remark_entry, $id);
        if ($update_stmt->execute()) {
            $_SESSION['success_message'] = "Reservation status updated successfully!";
        } else {
            $_SESSION['error_message'] = "Error updating status: " . $conn->error;
        }
        redirect("guest_reservation_details.php?id=$id");
        exit;
    }
}

// Get reservation details (must happen before header output so we can redirect safely)
$query = "SELECT 
              gr.*,
              g.room_name,
              g.floor,
              g.room_type,
              g.max_guests AS capacity,
              gr.check_in_date AS arrival_date,
              gr.check_out_date AS departure_date,
              gr.total_amount AS total_price
          FROM guest_room_reservations gr
          JOIN guest_rooms g ON gr.guest_room_id = g.id
          WHERE gr.id = ? AND gr.deleted = 0";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error_message'] = "Reservation not found.";
    redirect('guest_reservations.php');
}

$reservation = $result->fetch_assoc();
$stmt->close();

// Decode other guests
$other_guests = json_decode($reservation['other_guests'], true);

// Only include the admin header AFTER all redirects/updates/lookups (prevents headers-already-sent).
$pageTitle = 'Guest Reservation Details';
require_once __DIR__ . '/inc/header.php';

function getStatusBadgeClass($status) {
    switch (strtolower($status)) {
        case 'pending': return 'status-pending';
        case 'confirmed': return 'status-approved';
        case 'cancelled': return 'status-cancelled';
        case 'checked_out': return 'status-completed';
        default: return 'status-default';
    }
}

$total_nights = (strtotime($reservation['departure_date']) - strtotime($reservation['arrival_date'])) / (60 * 60 * 24);
?>

<style>
:root {
    --bsu-red: #b71c1c;
    --bsu-red-dark: #8b0000;
    --card-border: #e9ecef;
}

.content-area {
    background: #f8f9fa;
    padding: 1.5rem;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.page-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: #2c3e50;
    margin: 0;
}

.btn-back {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.6rem 1.2rem;
    background: white;
    color: #495057;
    border: 1px solid #dee2e6;
    border-radius: 50px;
    font-weight: 500;
    font-size: 0.9rem;
    text-decoration: none;
    transition: all 0.2s ease;
}

.btn-back:hover {
    background: #f8f9fa;
    border-color: var(--bsu-red);
    color: var(--bsu-red);
    transform: translateX(-3px);
}

.details-header {
    background: linear-gradient(135deg, var(--bsu-red), var(--bsu-red-dark));
    color: white;
    padding: 1.25rem 1.5rem;
    border-radius: 12px;
    margin-bottom: 2rem;
    box-shadow: 0 4px 12px rgba(183,28,28,0.15);
}

.status-badge {
    padding: 0.35rem 0.8rem;
    border-radius: 50px;
    font-weight: 600;
    font-size: 0.8rem;
    display: inline-block;
    letter-spacing: 0.3px;
}

.status-pending { background: #fff3cd; color: #856404; }
.status-approved { background: #d4edda; color: #155724; }
.status-cancelled { background: #f8d7da; color: #721c24; }
.status-completed { background: #cce5ff; color: #004085; }

.detail-card {
    background: white;
    border: 1px solid var(--card-border);
    border-radius: 12px;
    margin-bottom: 1.25rem;
    box-shadow: 0 2px 6px rgba(0,0,0,0.02);
    overflow: hidden;
}

.card-header-custom {
    background-color: var(--bsu-red);
    color: white;
    padding: 0.75rem 1.25rem;
    font-weight: 600;
    font-size: 0.95rem;
    border-bottom: 1px solid var(--bsu-red-dark);
}

.card-header-custom i {
    margin-right: 0.5rem;
}

.card-body {
    padding: 1.25rem;
}

.detail-row {
    display: flex;
    align-items: flex-start;
    padding: 0.5rem 0;
    border-bottom: 1px solid #f0f0f0;
}

.detail-row:last-child {
    border-bottom: none;
}

.detail-icon {
    width: 28px;
    color: var(--bsu-red);
    font-size: 0.95rem;
    flex-shrink: 0;
}

.detail-content {
    flex: 1;
}

.detail-label {
    font-size: 0.7rem;
    font-weight: 600;
    color: #666;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    margin-bottom: 0.15rem;
}

.detail-value {
    color: #2c3e50;
    font-size: 0.95rem;
    font-weight: 500;
}

.guest-card {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 1rem;
    margin-bottom: 1rem;
    border: 1px solid #e9ecef;
}

.guest-name {
    font-weight: 600;
    color: var(--bsu-red);
    margin-bottom: 0.5rem;
}

.guest-details {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 0.5rem;
    font-size: 0.85rem;
    color: #666;
}

.data-privacy-box {
    background: #f8f9fa;
    border-left: 4px solid var(--bsu-red);
    padding: 1rem;
    border-radius: 8px;
    font-size: 0.9rem;
    color: #495057;
    line-height: 1.6;
}

.signature-box {
    background: #f8f9fa;
    border: 1px dashed #dee2e6;
    padding: 1rem;
    border-radius: 8px;
    font-family: 'Dancing Script', cursive;
    font-size: 1.1rem;
    color: var(--bsu-red);
}

.form-group {
    margin-bottom: 1rem;
}

.form-group label {
    font-weight: 600;
    color: #495057;
    font-size: 0.85rem;
    margin-bottom: 0.3rem;
    display: block;
}

.form-select, .form-control {
    width: 100%;
    padding: 0.6rem 1rem;
    border: 2px solid #e9ecef;
    border-radius: 10px;
    font-size: 0.9rem;
}

.btn-update {
    background: var(--bsu-red);
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.95rem;
    cursor: pointer;
    transition: all 0.2s ease;
    width: 100%;
}

.btn-update:hover {
    background: var(--bsu-red-dark);
    transform: translateY(-2px);
}

.remarks-history {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 1rem;
    max-height: 200px;
    overflow-y: auto;
    font-size: 0.85rem;
    white-space: pre-wrap;
}

@media (max-width: 768px) {
    .page-header {
        flex-direction: column;
        align-items: flex-start;
    }
}
</style>

<div class="content-area">
    <div class="page-header">
        <h1 class="page-title">
            <i class="bi bi-door-open me-2" style="color: var(--bsu-red);"></i>
            Guest Reservation Details
        </h1>
        <a href="guest_reservations.php" class="btn-back">
            <i class="bi bi-arrow-left"></i> Back to Guest Reservations
        </a>
    </div>

    <?php display_messages(); ?>

    <!-- Status Header -->
    <div class="details-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h2><?= htmlspecialchars($reservation['booking_no']) ?></h2>
                <small>Reservation ID: <?= (int)$reservation['id'] ?></small>
            </div>
            <div class="text-end">
                <span class="status-badge <?= getStatusBadgeClass($reservation['status']) ?>">
                    <?= htmlspecialchars(strtoupper($reservation['status'])) ?>
                </span>
                <div class="mt-1">
                    <small>Booked: <?= date("M d, Y", strtotime($reservation['created_at'])) ?></small>
                </div>
            </div>
        </div>
    </div>

    <!-- Principal Guest Information -->
    <div class="detail-card">
        <div class="card-header-custom">
            <i class="bi bi-person-circle"></i> Principal Guest Information
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="detail-row">
                        <div class="detail-icon"><i class="bi bi-person-badge"></i></div>
                        <div class="detail-content">
                            <div class="detail-label">Full Name</div>
                            <div class="detail-value">
                                <?= htmlspecialchars($reservation['guest_name']) ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="detail-row">
                        <div class="detail-icon"><i class="bi bi-calendar"></i></div>
                        <div class="detail-content">
                            <div class="detail-label">Date of Birth</div>
                            <div class="detail-value">
                                <?php
                                    $legacy = null;
                                    $legacyRaw = $reservation['special_requests'] ?? '';
                                    if (is_string($legacyRaw) && $legacyRaw !== '' && $legacyRaw[0] === '{') {
                                        $tmp = json_decode($legacyRaw, true);
                                        if (is_array($tmp)) $legacy = $tmp;
                                    }
                                    $dob = $reservation['guest_dob'] ?? '';
                                    if (!$dob && $legacy && !empty($legacy['dob'])) $dob = $legacy['dob'];
                                    echo $dob ? date('F d, Y', strtotime($dob)) : '—';
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="detail-row">
                        <div class="detail-icon"><i class="bi bi-geo-alt"></i></div>
                        <div class="detail-content">
                            <div class="detail-label">Address</div>
                            <div class="detail-value">
                                <?php
                                    $addr = $reservation['guest_address'] ?? '';
                                    if (!$addr && $legacy && !empty($legacy['address'])) $addr = $legacy['address'];
                                    echo htmlspecialchars($addr ?: '—');
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="detail-row">
                        <div class="detail-icon"><i class="bi bi-envelope"></i></div>
                        <div class="detail-content">
                            <div class="detail-label">Email</div>
                            <div class="detail-value"><?= htmlspecialchars($reservation['guest_email'] ?? '') ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="detail-row">
                        <div class="detail-icon"><i class="bi bi-telephone"></i></div>
                        <div class="detail-content">
                            <div class="detail-label">Contact Number</div>
                            <div class="detail-value"><?= htmlspecialchars($reservation['guest_contact'] ?? '') ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Other Guests -->
    <?php if (!empty($other_guests)): ?>
    <div class="detail-card">
        <div class="card-header-custom">
            <i class="bi bi-people-fill"></i> Other Guests (<?= count(array_filter($other_guests, function($g) { return !empty($g['name']); })) ?>)
        </div>
        <div class="card-body">
            <div class="row">
                <?php foreach ($other_guests as $i => $guest): ?>
                    <?php if (!empty($guest['name'])): ?>
                    <div class="col-md-6">
                        <div class="guest-card">
                            <div class="guest-name">Guest <?= $i ?></div>
                            <div class="guest-details">
                                <span><strong>Name:</strong> <?= htmlspecialchars($guest['name']) ?></span>
                                <span><strong>Age:</strong> <?= htmlspecialchars($guest['age'] ?? 'N/A') ?></span>
                                <span><strong>DOB:</strong> <?= !empty($guest['dob']) ? date('M d, Y', strtotime($guest['dob'])) : 'N/A' ?></span>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Stay Details -->
    <div class="detail-card">
        <div class="card-header-custom">
            <i class="bi bi-calendar-check"></i> Stay Details
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="detail-row">
                        <div class="detail-icon"><i class="bi bi-calendar"></i></div>
                        <div class="detail-content">
                            <div class="detail-label">Arrival</div>
                            <div class="detail-value"><?= date('F d, Y', strtotime($reservation['arrival_date'])) ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="detail-row">
                        <div class="detail-icon"><i class="bi bi-clock"></i></div>
                        <div class="detail-content">
                            <div class="detail-label">Check-in Time</div>
                            <div class="detail-value"><?= date('h:i A', strtotime($reservation['check_in_time'])) ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="detail-row">
                        <div class="detail-icon"><i class="bi bi-calendar"></i></div>
                        <div class="detail-content">
                            <div class="detail-label">Departure</div>
                            <div class="detail-value"><?= date('F d, Y', strtotime($reservation['departure_date'])) ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="detail-row">
                        <div class="detail-icon"><i class="bi bi-clock"></i></div>
                        <div class="detail-content">
                            <div class="detail-label">Check-out Time</div>
                            <div class="detail-value"><?= date('h:i A', strtotime($reservation['check_out_time'])) ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="detail-row">
                        <div class="detail-icon"><i class="bi bi-people"></i></div>
                        <div class="detail-content">
                            <div class="detail-label">Guests</div>
                            <div class="detail-value">
                                <?= (int)$reservation['adults_count'] ?> Adult(s), <?= (int)$reservation['children_count'] ?> Kid(s)
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="detail-row">
                        <div class="detail-icon"><i class="bi bi-building"></i></div>
                        <div class="detail-content">
                            <div class="detail-label">Room</div>
                            <div class="detail-value">
                                <?= htmlspecialchars($reservation['room_name']) ?> (<?= htmlspecialchars($reservation['floor']) ?>)
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="detail-row">
                        <div class="detail-icon"><i class="bi bi-door-open"></i></div>
                        <div class="detail-content">
                            <div class="detail-label">Room Type</div>
                            <div class="detail-value"><?= htmlspecialchars($reservation['room_type']) ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="detail-row">
                        <div class="detail-icon"><i class="bi bi-moon"></i></div>
                        <div class="detail-content">
                            <div class="detail-label">Nights</div>
                            <div class="detail-value"><?= $total_nights ?> night(s)</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php
                $displayRemarks = $reservation['special_requests'] ?? '';
                if (isset($legacy) && is_array($legacy) && isset($legacy['remarks'])) {
                    $displayRemarks = (string)$legacy['remarks'];
                }
            ?>
            <?php if (!empty($displayRemarks)): ?>
            <div class="mt-3 pt-3 border-top">
                <div class="detail-label mb-2">Remarks / Special Arrangements</div>
                <div class="detail-value"><?= nl2br(htmlspecialchars($displayRemarks)) ?></div>
            </div>
            <?php endif; ?>
            
            <div class="mt-3 pt-3 border-top">
                <div class="detail-label mb-2">Registered By</div>
                <div class="detail-value">
                    <?php
                        $registeredBy = $reservation['terms_accepted_by'] ?? '';
                        if (!$registeredBy && isset($legacy) && is_array($legacy) && !empty($legacy['registered_by'])) {
                            $registeredBy = (string)$legacy['registered_by'];
                        }
                        if (!$registeredBy && !empty($reservation['created_by'])) $registeredBy = 'User ID #' . (int)$reservation['created_by'];
                        echo htmlspecialchars($registeredBy ?: '—');
                    ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Privacy & Signature -->
    <div class="detail-card">
        <div class="card-header-custom">
            <i class="bi bi-shield-check"></i> Data Privacy & Consent
        </div>
        <div class="card-body">
            <div class="data-privacy-box mb-3">
                <p class="mb-2"><strong>Data Privacy and Protection</strong></p>
                <p class="small">During your stay, information will be collected about you and your preferences in order to provide you with the best possible service. The information will be retained to facilitate future stays at BatStateU ARASOF Hostel.</p>
                <p class="mb-0 small">By signing, you are expressly giving your consent to the collection and storage of your personal data as provided herein.</p>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="detail-label">Consent Given</div>
                    <div class="detail-value">
                        <?php if ($reservation['data_privacy_consent']): ?>
                            <span class="text-success"><i class="bi bi-check-circle-fill"></i> Yes, consent provided</span>
                        <?php else: ?>
                            <span class="text-danger"><i class="bi bi-x-circle-fill"></i> No consent</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="detail-label">Digital Signature</div>
                    <div class="signature-box">
                        <?php if (!empty($reservation['digital_signature']) && str_starts_with($reservation['digital_signature'], 'data:image')): ?>
                            <img src="<?= htmlspecialchars($reservation['digital_signature']) ?>" alt="Digital signature" style="max-width: 100%; height: auto; display:block;">
                        <?php elseif (!empty($reservation['digital_signature'])): ?>
                            <i class="bi bi-pen"></i> <?= htmlspecialchars($reservation['digital_signature']) ?>
                        <?php else: ?>
                            <span class="text-muted">—</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Admin Actions -->
    <div class="detail-card">
        <div class="card-header-custom">
            <i class="bi bi-pencil-square"></i> Admin Actions
        </div>
        <div class="card-body">
            <form method="POST">
                <input type="hidden" name="action" value="update_status">
                <div class="form-group">
                    <label><i class="bi bi-tag me-1"></i> Update Status</label>
                    <select class="form-select" name="status">
                        <option value="pending" <?= $reservation['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="confirmed" <?= $reservation['status'] === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                        <option value="checked_in" <?= $reservation['status'] === 'checked_in' ? 'selected' : '' ?>>Checked In</option>
                        <option value="checked_out" <?= $reservation['status'] === 'checked_out' ? 'selected' : '' ?>>Completed (Checked Out)</option>
                        <option value="cancelled" <?= $reservation['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                        <option value="no_show" <?= $reservation['status'] === 'no_show' ? 'selected' : '' ?>>No Show</option>
                    </select>
                    <small class="text-muted">Current: <strong><?= ucfirst($reservation['status']) ?></strong></small>
                </div>
                <div class="form-group">
                    <label><i class="bi bi-chat me-1"></i> Admin Remarks</label>
                    <textarea class="form-control" name="admin_remarks" rows="3" placeholder="Add remarks..." required></textarea>
                </div>
                <button type="submit" class="btn-update">
                    <i class="bi bi-check-circle me-1"></i> Update Status
                </button>
            </form>
        </div>
    </div>

    <!-- Remarks History -->
    <div class="detail-card">
        <div class="card-header-custom">
            <i class="bi bi-clock-history"></i> Remarks History
        </div>
        <div class="card-body">
            <?php if (!empty($reservation['admin_remarks'])): ?>
                <div class="remarks-history">
                    <?= nl2br(htmlspecialchars(trim($reservation['admin_remarks']))) ?>
                </div>
            <?php else: ?>
                <p class="text-muted mb-0">No remarks have been added yet.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const status = document.querySelector('select[name="status"]').value;
            const remarks = document.querySelector('textarea[name="admin_remarks"]').value;
            
            if (!remarks.trim()) {
                e.preventDefault();
                alert('Please add admin remarks.');
                return;
            }
            
            if (!confirm('Are you sure you want to mark this reservation as ' + status.toUpperCase() + '?')) {
                e.preventDefault();
            }
        });
    }
});
</script>

<?php require_once __DIR__ . '/inc/footer.php'; ?>