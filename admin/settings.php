<?php
$pageTitle = 'Settings';
require_once __DIR__ . '/inc/header.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_pass = $_POST['current_password'] ?? '';
    $new_pass = $_POST['new_password'] ?? '';
    $confirm_pass = $_POST['confirm_password'] ?? '';

    // Verify current password
    $stmt = $conn->prepare("SELECT password FROM admin_users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['admin_id']);
    $stmt->execute();
    $admin = $stmt->get_result()->fetch_assoc();

    if ($admin && password_verify($current_pass, $admin['password'])) {
        if ($new_pass === $confirm_pass) {
            if (strlen($new_pass) >= 8) {
                $hash = password_hash($new_pass, PASSWORD_DEFAULT);
                $update = $conn->prepare("UPDATE admin_users SET password = ? WHERE id = ?");
                $update->bind_param("si", $hash, $_SESSION['admin_id']);
                if ($update->execute()) {
                    $success = 'Password updated successfully.';
                    logAdminAction($conn, 'change_password', 'Updated own password');
                } else {
                    $error = 'Failed to update password.';
                }
            } else {
                $error = 'New password must be at least 8 characters long.';
            }
        } else {
            $error = 'New passwords do not match.';
        }
    } else {
        $error = 'Incorrect current password.';
    }
}
?>

<main class="admin-main">
    <div class="dashboard-header">
        <h1><i class="bi bi-gear me-2"></i>Admin Settings</h1>
        <p>Manage your account and system preferences</p>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="dashboard-card">
                <div class="card-header">
                    <h3><i class="bi bi-shield-lock me-2"></i>Change Password</h3>
                </div>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                <?php endif; ?>

                <form method="POST" class="mt-3">
                    <input type="hidden" name="change_password" value="1">
                    <div class="mb-3">
                        <label class="form-label">Current Password</label>
                        <input type="password" name="current_password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">New Password</label>
                        <input type="password" name="new_password" class="form-control" required minlength="8">
                        <small class="text-muted">Minimum 8 characters</small>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Confirm New Password</label>
                        <input type="password" name="confirm_password" class="form-control" required minlength="8">
                    </div>
                    <button type="submit" class="btn btn-primary bg-danger border-0">
                        <i class="bi bi-check-circle me-1"></i> Update Password
                    </button>
                </form>
            </div>
        </div>

        <div class="col-md-6">
            <div class="dashboard-card shadow-sm border-0">
                <div class="card-header">
                    <h3><i class="bi bi-info-circle me-2"></i>Security Information</h3>
                </div>
                <div class="p-3">
                    <div class="alert alert-info">
                        <i class="bi bi-info-diamond me-2"></i>
                        Dashboard access is restricted to authenticated administrators only.
                    </div>
                    <ul class="list-group list-group-flush mb-0">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Role <span><?= htmlspecialchars($admin['role'] ?? 'Administrator') ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Last Login <span><?= $admin['last_login'] ? date('M d, Y h:i A', strtotime($admin['last_login'])) : 'N/A' ?></span>
                        </li>
                        <li class="list-group-item">
                            <small class="text-muted">For maximum security, ensure your password contains both numbers and special characters.</small>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/inc/footer.php'; ?>
