<?php
$pageTitle = 'Room Details';
require_once __DIR__ . '/inc/link.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) { header('Location: rooms.php'); exit; }

$stmt = $conn->prepare("SELECT r.*, t.name AS type_name FROM rooms r LEFT JOIN types_room t ON r.type_id = t.id WHERE r.id = ? AND r.status = 'available'");
$stmt->bind_param("i", $id);
$stmt->execute();
$room = $stmt->get_result()->fetch_assoc();
if (!$room) { header('Location: rooms.php'); exit; }

$reviews = $conn->prepare("SELECT rr.*, u.name FROM room_reviews rr JOIN user_reg u ON rr.user_id = u.id WHERE rr.room_id = ? ORDER BY rr.created_at DESC LIMIT 5");
$reviews->bind_param("i", $id);
$reviews->execute();
$reviews = $reviews->get_result();
?>
<?php require_once __DIR__ . '/inc/header.php'; ?>

<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="rooms.php">Rooms</a></li>
            <li class="breadcrumb-item active"><?= clean($room['name']) ?></li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-body bg-light" style="min-height: 300px;"></div>
                <div class="card-body">
                    <h1><?= clean($room['name']) ?></h1>
                    <p class="text-muted"><?= clean($room['type_name'] ?? 'Standard') ?> · <?= $room['area'] ?? 'N/A' ?> sqm</p>
                    <p><?= nl2br(clean($room['description'] ?? 'No description.')) ?></p>
                    <p><strong>Capacity:</strong> <?= $room['adult_capacity'] ?> adults, <?= $room['children_capacity'] ?> children</p>
                </div>
            </div>

            <?php if ($reviews->num_rows > 0): ?>
            <h4>Reviews</h4>
            <?php while ($r = $reviews->fetch_assoc()): ?>
            <div class="card mb-2">
                <div class="card-body">
                    <div class="mb-1"><?= str_repeat('★', $r['rating']) ?><?= str_repeat('☆', 5 - $r['rating']) ?> <?= clean($r['name']) ?></div>
                    <p class="mb-0"><?= clean($r['review_text'] ?? '-') ?></p>
                </div>
            </div>
            <?php endwhile; ?>
            <?php endif; ?>
        </div>

        <div class="col-md-4">
            <div class="card sticky-top">
                <div class="card-body">
                    <h4><?= formatPrice($room['price']) ?> <small>/ night</small></h4>
                    <?php if (isLoggedIn()): ?>
                    <form action="booking.php" method="GET">
                        <input type="hidden" name="room_id" value="<?= $room['id'] ?>">
                        <div class="mb-2">
                            <label class="form-label">Check-in</label>
                            <input type="date" name="check_in" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Check-out</label>
                            <input type="date" name="check_out" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Book Now</button>
                    </form>
                    <?php else: ?>
                    <p class="text-muted">Please <a href="login.php">login</a> to book this room.</p>
                    <a href="login.php?redirect=<?= urlencode('room-details.php?id='.$room['id']) ?>" class="btn btn-primary w-100">Login to Book</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/inc/footer.php'; ?>
