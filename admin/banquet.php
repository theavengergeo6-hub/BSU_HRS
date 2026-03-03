<?php
$pageTitle = 'Banquet Styles';
require_once __DIR__ . '/inc/header.php';

// Handle actions
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        
        if ($action === 'add' || $action === 'edit') {
            $id = $_POST['id'] ?? 0;
            $name = clean($_POST['name'] ?? '');
            $description = clean($_POST['description'] ?? '');
            
            // Handle image upload
            $image = '';
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $target_dir = $_SERVER['DOCUMENT_ROOT'] . '/BSU_HRS/assets/images/banquet/';
                if (!file_exists($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }
                
                $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $image = 'IMG_' . time() . '_' . rand(100, 999) . '.' . $extension;
                $target_file = $target_dir . $image;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                    // Success
                }
            }
            
            if ($action === 'add') {
                $stmt = $conn->prepare("INSERT INTO banquet (name, description, image) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $name, $description, $image);
                if ($stmt->execute()) {
                    $message = '<div class="alert alert-success">Banquet style added successfully.</div>';
                } else {
                    $message = '<div class="alert alert-danger">Error: ' . $conn->error . '</div>';
                }
            } else {
                if ($image) {
                    $stmt = $conn->prepare("UPDATE banquet SET name=?, description=?, image=? WHERE id=?");
                    $stmt->bind_param("sssi", $name, $description, $image, $id);
                } else {
                    $stmt = $conn->prepare("UPDATE banquet SET name=?, description=? WHERE id=?");
                    $stmt->bind_param("ssi", $name, $description, $id);
                }
                if ($stmt->execute()) {
                    $message = '<div class="alert alert-success">Banquet style updated successfully.</div>';
                } else {
                    $message = '<div class="alert alert-danger">Error: ' . $conn->error . '</div>';
                }
            }
        } elseif ($action === 'delete') {
            $id = (int)$_POST['id'];
            
            // Get image to delete file
            $img = $conn->query("SELECT image FROM banquet WHERE id = $id")->fetch_assoc();
            if ($img && $img['image']) {
                $file = $_SERVER['DOCUMENT_ROOT'] . '/BSU_HRS/assets/images/banquet/' . $img['image'];
                if (file_exists($file)) unlink($file);
            }
            
            $stmt = $conn->prepare("DELETE FROM banquet WHERE id = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $message = '<div class="alert alert-success">Banquet style deleted.</div>';
            }
        }
    }
}

// Get all banquet styles
$banquet = $conn->query("SELECT * FROM banquet ORDER BY id DESC");
?>
<?= $message ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Banquet Styles</h2>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
        <i class="bi bi-plus-circle"></i> Add New Style
    </button>
</div>

<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Name</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $banquet->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td>
                    <?php if (!empty($row['image'])): ?>
                        <img src="<?= BASE_URL ?>/assets/images/banquet/<?= $row['image'] ?>" alt="" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                    <?php else: ?>
                        <span class="text-muted">No image</span>
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars(substr($row['description'] ?? '', 0, 100)) ?>...</td>
                <td>
                    <button class="btn btn-sm btn-outline" onclick="editBanquet(<?= $row['id'] ?>)">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-sm btn-outline" onclick="deleteBanquet(<?= $row['id'] ?>)">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add">
                <div class="modal-header">
                    <h5 class="modal-title">Add Banquet Style</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Name *</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="4"></textarea>
                    </div>
                    <div class="mb-3">
                        <label>Image</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editBanquet(id) {
    // You'll need to implement AJAX to load data
    alert('Edit function - ID: ' + id);
}

function deleteBanquet(id) {
    if (confirm('Are you sure you want to delete this item?')) {
        var form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = '<input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="' + id + '">';
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<?php require_once __DIR__ . '/inc/footer.php'; ?>