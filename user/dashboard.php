<?php
$pageTitle = 'My Dashboard';
require_once __DIR__ . '/../inc/link.php';

if (!isLoggedIn()) {
    redirect(BASE_URL . 'login.php');
}

$user_id = getUserId();
$bookings = $conn->prepare("SELECT br.*, r.name as room_name FROM room_reservation br JOIN rooms r ON br.room_id = r.id WHERE br.user_id = ? ORDER BY br.created_at DESC LIMIT 10");
$bookings->bind_param("i", $user_id);
$bookings->execute();
$bookings = $bookings->get_result();
?>
<?php require_once __DIR__ . '/../inc/header.php'; ?>

<div class="container py-4">
    <h1 class="mb-4">Welcome, <?= clean($_SESSION['user_name']) ?>!</h1>
    <?php if (isset($_GET['booked'])): ?>
    <div class="alert alert-success">Your booking has been submitted. We will confirm shortly.</div>
    <?php endif; ?>
    
    <h3 class="mb-3">My Bookings</h3>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Booking No</th>
                    <th>Room</th>
                    <th>Check-in</th>
                    <th>Check-out</th>
                    <th>Total</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $bookings->fetch_assoc()): ?>
                <tr>
                    <td><?= clean($row['booking_no']) ?></td>
                    <td><?= clean($row['room_name']) ?></td>
                    <td><?= $row['check_in'] ?></td>
                    <td><?= $row['check_out'] ?></td>
                    <td><?= formatPrice($row['total_price']) ?></td>
                    <td><span class="badge bg-<?= $row['status']==='confirmed'?'success':($row['status']==='pending'?'warning':'secondary') ?>"><?= $row['status'] ?></span></td>
                </tr>
                <?php endwhile; ?>
                <?php if ($bookings->num_rows == 0): ?>
                <tr><td colspan="6" class="text-muted">No bookings yet. <a href="<?= BASE_URL ?>rooms.php">Browse rooms</a></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../inc/footer.php'; ?>
