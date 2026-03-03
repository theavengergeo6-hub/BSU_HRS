<?php
$pageTitle = 'Amenities';
require_once __DIR__ . '/inc/link.php';

$facilities = $conn->query("SELECT * FROM facilities ORDER BY id");
$base = rtrim(BASE_URL, '/');
?>
<?php require_once __DIR__ . '/inc/header.php'; ?>

<div class="container py-4">
    <h1 class="mb-4">Our Amenities</h1>
    <div class="row g-4">
        <?php if ($facilities && $facilities->num_rows > 0): ?>
        <?php while ($f = $facilities->fetch_assoc()): ?>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title"><?= clean($f['name']) ?></h5>
                    <p class="card-text"><?= clean($f['description'] ?? '') ?></p>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
        <?php else: ?>
        <p class="text-muted">Amenities information coming soon.</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/inc/footer.php'; ?>
