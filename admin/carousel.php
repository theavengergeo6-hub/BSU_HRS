<?php
$pageTitle = 'Carousel Manager';
require_once __DIR__ . '/inc/header.php';

$message = '';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        
        if ($action === 'add' || $action === 'edit') {
            $id = (int)($_POST['id'] ?? 0);
            $title = clean($_POST['title'] ?? '');
            $subtitle = clean($_POST['subtitle'] ?? '');
            $button_text = clean($_POST['button_text'] ?? 'View Rooms');
            $button_url = clean($_POST['button_url'] ?? 'rooms.php');
            $sort_order = (int)($_POST['sort_order'] ?? 0);
            
            // Handle image upload
            $image_path = $_POST['existing_image'] ?? '';
            
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $target_dir = $_SERVER['DOCUMENT_ROOT'] . '/BSU_HRS/assets/images/carousel/';
                if (!file_exists($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }
                
                $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $image_path = 'carousel_' . time() . '_' . rand(100, 999) . '.' . $extension;
                $target_file = $target_dir . $image_path;
                
                move_uploaded_file($_FILES['image']['tmp_name'], $target_file);
            }
            
            if ($action === 'add') {
                $stmt = $conn->prepare("INSERT INTO carousel_slides (title, subtitle, button_text, button_url, image_path, sort_order) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sssssi", $title, $subtitle, $button_text, $button_url, $image_path, $sort_order);
                if ($stmt->execute()) {
                    $message = '<div class="alert alert-success">Slide added.</div>';
                }
            } else {
                if ($image_path && $image_path !== $_POST['existing_image']) {
                    $stmt = $conn->prepare("UPDATE carousel_slides SET title=?, subtitle=?, button_text=?, button_url=?, image_path=?, sort_order=? WHERE id=?");
                    $stmt->bind_param("sssssii", $title, $subtitle, $button_text, $button_url, $image_path, $sort_order, $id);
                } else {
                    $stmt = $conn->prepare("UPDATE carousel_slides SET title=?, subtitle=?, button_text=?, button_url=?, sort_order=? WHERE id=?");
                    $stmt->bind_param("ssssii", $title, $subtitle, $button_text, $button_url, $sort_order, $id);
                }
                if ($stmt->execute()) {
                    $message = '<div class="alert alert-success">Slide updated.</div>';
                }
            }
        } elseif ($action === 'delete') {
            $id = (int)$_POST['id'];
            
            // Delete image file
            $img = $conn->query("SELECT image_path FROM carousel_slides WHERE id = $id")->fetch_assoc();
            if ($img && $img['image_path']) {
                $file = $_SERVER['DOCUMENT_ROOT'] . '/BSU_HRS/assets/images/carousel/' . $img['image_path'];
                if (file_exists($file)) unlink($file);
            }
            
            $stmt = $conn->prepare("DELETE FROM carousel_slides WHERE id = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $message = '<div class="alert alert-success">Slide deleted.</div>';
            }
        } elseif ($action === 'toggle') {
            $id = (int)$_POST['id'];
            $conn->query("UPDATE carousel_slides SET is_active = NOT is_active WHERE id = $id");
            $message = '<div class="alert alert-success">Status toggled.</div>';
        }
    }
}

$slides = $conn->query("SELECT * FROM carousel_slides ORDER BY sort_order ASC");
?>
<?= $message ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Carousel Slides</h2>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
        <i class="bi bi-plus-circle"></i> Add New Slide
    </button>
</div>

<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>Order</th>
                <th>Preview</th>
                <th>Title</th>
                <th>Button Text</th>
                <th>Active</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $slides->fetch_assoc()): ?>
            <tr>
                <td><?= $row['sort_order'] ?></td>
                <td>
                    <?php if (!empty($row['image_path'])): ?>
                        <img src="<?= BASE_URL ?>/assets/images/carousel/<?= $row['image_path'] ?>" alt="" style="width: 100px; height: 60px; object-fit: cover; border-radius: 4px;">
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($row['title']) ?></td>
                <td><?= htmlspecialchars($row['button_text']) ?></td>
                <td>
                    <span class="badge badge-<?= $row['is_active'] ? 'approved' : 'pending' ?>">
                        <?= $row['is_active'] ? 'Active' : 'Inactive' ?>
                    </span>
                </td>
                <td>
                    <button class="btn btn-sm btn-outline" onclick="editSlide(<?= $row['id'] ?>)">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="toggle">
                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                        <button type="submit" class="btn btn-sm btn-outline">
                            <i class="bi bi-toggle-<?= $row['is_active'] ? 'on' : 'off' ?>"></i>
                        </button>
                    </form>
                    <button class="btn btn-sm btn-outline" onclick="deleteSlide(<?= $row['id'] ?>)">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script>
function deleteSlide(id) {
    if (confirm('Delete this slide?')) {
        var form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = '<input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="' + id + '">';
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<?php require_once __DIR__ . '/inc/footer.php'; ?>