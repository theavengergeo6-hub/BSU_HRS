<?php
$pageTitle = 'Contact';
require_once __DIR__ . '/inc/link.php';

$contact = $conn->query("SELECT * FROM contact_details LIMIT 1")->fetch_assoc();
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isLoggedIn()) {
    $msg = clean($_POST['message'] ?? '');
    if ($msg && getUserId()) {
        $user_id = getUserId();
        $stmt = $conn->prepare("INSERT INTO admin_notifications (title, message) VALUES (?, ?)");
        $title = "Contact from user #" . $user_id;
        $stmt->bind_param("ss", $title, $msg);
        if ($stmt->execute()) {
            $success = 'Message sent. We will get back to you soon.';
        }
    }
}
?>
<?php require_once __DIR__ . '/inc/header.php'; ?>

<main>
<div class="container py-4">
    <h1 class="mb-4">Contact Us</h1>
    <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
    <div class="row">
        <div class="col-md-6">
            <?php if ($contact): ?>
            <p><strong>Address:</strong> <?= clean($contact['address'] ?? '') ?></p>
            <p><strong>Phone:</strong> <?= clean($contact['phone'] ?? '') ?></p>
            <p><strong>Email:</strong> <?= clean($contact['email'] ?? '') ?></p>
            <?php endif; ?>
        </div>
        <div class="col-md-6">
            <?php if (isLoggedIn()): ?>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Message</label>
                    <textarea name="message" class="form-control" rows="4" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Send Message</button>
            </form>
            <?php else: ?>
            <p class="text-muted">Please <a href="<?= rtrim(BASE_URL, '/') ?>/login.php">login</a> to send us a message.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
</main>

<?php require_once __DIR__ . '/inc/footer.php'; ?>
