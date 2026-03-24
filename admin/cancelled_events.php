<?php
$pageTitle = 'Cancelled Events';
require_once __DIR__ . '/inc/header.php';

// Fetch cancelled facility reservations
$cancelled_fac = [];
$res_fac = $conn->query("
    SELECT fr.id, fr.booking_no, CONCAT(fr.last_name, ', ', fr.first_name) AS requester,
           fr.activity_name, fr.start_datetime, fr.end_datetime, fr.status,
           v.name AS venue_name
    FROM facility_reservations fr
    LEFT JOIN venues v ON fr.venue_id = v.id
    WHERE fr.status = 'cancelled' OR fr.status = 'denied'
    ORDER BY fr.start_datetime DESC
");
if ($res_fac) {
    while ($row = $res_fac->fetch_assoc()) {
        $cancelled_fac[] = $row;
    }
}

// Fetch cancelled guest room reservations
$cancelled_guest = [];
$res_guest = $conn->query("
    SELECT gr.id, gr.booking_no, gr.guest_name,
           gr.check_in_date, gr.check_out_date, gr.status,
           g.room_name
    FROM guest_room_reservations gr
    LEFT JOIN guest_rooms g ON gr.guest_room_id = g.id
    WHERE (gr.status = 'cancelled' OR gr.status = 'denied') AND gr.deleted = 0
    ORDER BY gr.check_in_date DESC
");
if ($res_guest) {
    while ($row = $res_guest->fetch_assoc()) {
        $cancelled_guest[] = $row;
    }
}
?>

<div class="content-area">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title mb-0">
            <i class="bi bi-x-circle text-danger me-2"></i>Cancelled Events & Reservations
        </h1>
    </div>

    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; overflow: hidden;">
        <div class="card-header bg-white py-3 border-bottom">
            <h5 class="mb-0 text-danger" style="font-weight: 600;"><i class="bi bi-building me-2"></i>Function Room Cancellations</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="min-width: 800px;">
                    <thead class="bg-light text-muted" style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">
                        <tr>
                            <th class="py-3 px-4 fw-semibold border-0">Booking No.</th>
                            <th class="py-3 px-4 fw-semibold border-0">Requester</th>
                            <th class="py-3 px-4 fw-semibold border-0">Activity / Venue</th>
                            <th class="py-3 px-4 fw-semibold border-0">Date & Time</th>
                            <th class="py-3 px-4 fw-semibold border-0">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($cancelled_fac)): ?>
                            <tr>
                                <td colspan="5" class="py-4 text-center text-muted">No cancelled function room reservations found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($cancelled_fac as $row): ?>
                                <tr>
                                    <td class="py-3 px-4 fw-medium text-dark"><?= htmlspecialchars($row['booking_no']) ?></td>
                                    <td class="py-3 px-4"><?= htmlspecialchars($row['requester']) ?></td>
                                    <td class="py-3 px-4">
                                        <div class="fw-medium text-dark"><?= htmlspecialchars($row['activity_name']) ?></div>
                                        <div class="text-muted" style="font-size: 0.85rem;"><?= htmlspecialchars($row['venue_name'] ?? 'Multiple Venues') ?></div>
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="text-dark"><?= date('M d, Y', strtotime($row['start_datetime'])) ?></div>
                                        <div class="text-muted" style="font-size: 0.85rem;">
                                            <?= date('h:i A', strtotime($row['start_datetime'])) ?> - <?= date('h:i A', strtotime($row['end_datetime'])) ?>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-3 py-1">
                                            <?= ucfirst($row['status']) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
        <div class="card-header bg-white py-3 border-bottom">
            <h5 class="mb-0 text-danger" style="font-weight: 600;"><i class="bi bi-door-open me-2"></i>Guest Room Cancellations</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="min-width: 800px;">
                    <thead class="bg-light text-muted" style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">
                        <tr>
                            <th class="py-3 px-4 fw-semibold border-0">Booking No.</th>
                            <th class="py-3 px-4 fw-semibold border-0">Guest Name</th>
                            <th class="py-3 px-4 fw-semibold border-0">Room</th>
                            <th class="py-3 px-4 fw-semibold border-0">Dates (Arrival - Departure)</th>
                            <th class="py-3 px-4 fw-semibold border-0">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($cancelled_guest)): ?>
                            <tr>
                                <td colspan="5" class="py-4 text-center text-muted">No cancelled guest room reservations found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($cancelled_guest as $row): ?>
                                <tr>
                                    <td class="py-3 px-4 fw-medium text-dark"><?= htmlspecialchars($row['booking_no']) ?></td>
                                    <td class="py-3 px-4"><?= htmlspecialchars($row['guest_name']) ?></td>
                                    <td class="py-3 px-4 fw-medium text-dark"><?= htmlspecialchars($row['room_name'] ?? 'Unknown Room') ?></td>
                                    <td class="py-3 px-4">
                                        <div class="text-dark"><?= date('M d, Y', strtotime($row['check_in_date'])) ?> to <?= date('M d, Y', strtotime($row['check_out_date'])) ?></div>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-3 py-1">
                                            <?= ucfirst($row['status']) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<?php require_once __DIR__ . '/inc/footer.php'; ?>
