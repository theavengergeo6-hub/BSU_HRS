<?php
require_once __DIR__ . '/../../inc/db_config.php';
require_once __DIR__ . '/../inc/auth.php';

if (!isAdminLoggedIn()) {
    http_response_code(401);
    exit;
}

// Get all pending and pencil booked reservations (logic copied from reservations_pending.php)
$reservations = $conn->query("
    SELECT * FROM (
        SELECT 
            'function' AS res_type,
            r.id, 
            r.activity_name,
            r.status,
            r.start_datetime,
            r.end_datetime,
            r.participants_count,
            r.additional_instruction,
            v.name as venue_name, 
            v.floor,
            CONCAT(r.last_name, ', ', r.first_name) as requester_name
        FROM facility_reservations r
        JOIN venues v ON r.venue_id = v.id
        WHERE r.status IN ('pending', 'pencil_booked')
        
        UNION ALL
        
        SELECT
            'guest' AS res_type,
            gr.id,
            CONCAT('Guest Room - ', g.room_name) AS activity_name,
            gr.status,
            CONCAT(gr.check_in_date, ' 14:00:00') AS start_datetime,
            CONCAT(gr.check_out_date, ' 12:00:00') AS end_datetime,
            gr.total_guests AS participants_count,
            gr.special_requests AS additional_instruction,
            g.room_name AS venue_name,
            g.floor AS floor,
            gr.guest_name AS requester_name
        FROM guest_room_reservations gr
        JOIN guest_rooms g ON gr.guest_room_id = g.id
        WHERE gr.status IN ('pending', 'pencil_booked') AND gr.deleted = 0
    ) AS combined
    ORDER BY 
        CASE status 
            WHEN 'pending' THEN 1 
            WHEN 'pencil_booked' THEN 2 
        END,
        start_datetime ASC
");

// Get separate counts
$pending_count = $conn->query("
    SELECT (
        (SELECT COUNT(*) FROM facility_reservations WHERE status = 'pending') +
        (SELECT COUNT(*) FROM guest_room_reservations WHERE status = 'pending' AND deleted = 0)
    ) as count
")->fetch_assoc()['count'];

$pencil_count = $conn->query("
    SELECT (
        (SELECT COUNT(*) FROM facility_reservations WHERE status = 'pencil_booked') +
        (SELECT COUNT(*) FROM guest_room_reservations WHERE status = 'pencil_booked' AND deleted = 0)
    ) as count
")->fetch_assoc()['count'];

$total_count = $pending_count + $pencil_count;

// Get most requested for the stat card
$most_requested = $conn->query("
    SELECT venue_name AS name, COUNT(*) as count FROM (
        SELECT v.name as venue_name 
        FROM facility_reservations r 
        JOIN venues v ON r.venue_id = v.id 
        WHERE r.status IN ('pending', 'pencil_booked')
        UNION ALL
        SELECT g.room_name as venue_name
        FROM guest_room_reservations gr
        JOIN guest_rooms g ON gr.guest_room_id = g.id
        WHERE gr.status IN ('pending', 'pencil_booked') AND gr.deleted = 0
    ) as combined
    GROUP BY venue_name 
    ORDER BY count DESC 
    LIMIT 1
")->fetch_assoc();

ob_start();
?>
<?php if ($total_count > 0): ?>
    <?php while ($row = $reservations->fetch_assoc()): 
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
                    onclick="handleAction(<?= $row['id'] ?>, 'approve', '<?= htmlspecialchars(addslashes($row['activity_name'])) ?>', '<?= $row['res_type'] ?>')">
                    <i class="bi bi-check-lg"></i> Approve
                </button>
                <button class="btn-action btn-pencil" 
                    onclick="handleAction(<?= $row['id'] ?>, 'pencil', '<?= htmlspecialchars(addslashes($row['activity_name'])) ?>', '<?= $row['res_type'] ?>')">
                    <i class="bi bi-pencil"></i> Pencil
                </button>
            <?php else: ?>
                <button class="btn-action btn-approve" 
                    onclick="handleAction(<?= $row['id'] ?>, 'approve', '<?= htmlspecialchars(addslashes($row['activity_name'])) ?>', '<?= $row['res_type'] ?>')">
                    <i class="bi bi-check-lg"></i> Approve
                </button>
            <?php endif; ?>
            <button class="btn-action btn-deny" 
                onclick="handleAction(<?= $row['id'] ?>, 'deny', '<?= htmlspecialchars(addslashes($row['activity_name'])) ?>', '<?= $row['res_type'] ?>')">
                <i class="bi bi-x-lg"></i> Deny
            </button>
            <button class="btn-action btn-view" onclick="viewDetails(<?= $row['id'] ?>, '<?= $row['res_type'] ?>')">
                <i class="bi bi-eye"></i>
            </button>
        </div>
    </div>
    <?php endwhile; ?>
<?php else: ?>
    <div class="empty-state">
        <i class="bi bi-check-circle"></i>
        <h3>No Reservations to Review</h3>
        <p>All caught up! There are no pending or pencil booked reservations.</p>
    </div>
<?php endif; ?>
<?php
$html = ob_get_clean();

// Get max IDs for the next check
$fac_max = $conn->query("SELECT MAX(id) as max_id FROM facility_reservations")->fetch_assoc()['max_id'] ?? 0;
$guest_max = $conn->query("SELECT MAX(id) as max_id FROM guest_room_reservations")->fetch_assoc()['max_id'] ?? 0;

header('Content-Type: application/json');
echo json_encode([
    'html' => $html,
    'counts' => [
        'total' => (int)$total_count,
        'pending' => (int)$pending_count,
        'pencil' => (int)$pencil_count
    ],
    'top_venue' => $most_requested ? substr($most_requested['name'], 0, 10) . '...' : 'N/A',
    'max_ids' => [
        'facility' => (int)$fac_max,
        'guest' => (int)$guest_max
    ]
]);
