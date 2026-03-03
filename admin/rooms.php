<?php
$pageTitle = 'Room Management';
require_once __DIR__ . '/inc/header.php';

// Handle actions
$message = '';

// Handle room deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    
    if ($action === 'delete_room') {
        $room_id = (int)($_POST['room_id'] ?? 0);
        
        // Start transaction to ensure all related data is deleted
        $conn->begin_transaction();
        
        try {
            // First, delete related records in room_facilities
            $stmt = $conn->prepare("DELETE FROM room_facilities WHERE room_id = ?");
            $stmt->bind_param("i", $room_id);
            $stmt->execute();
            $stmt->close();
            
            // REMOVED: Delete related records in room_features (table doesn't exist)
            // $stmt = $conn->prepare("DELETE FROM room_features WHERE room_id = ?");
            // $stmt->bind_param("i", $room_id);
            // $stmt->execute();
            // $stmt->close();
            
            // Delete room images
            $images = $conn->query("SELECT image_path FROM room_images WHERE room_id = $room_id");
            if ($images && $images->num_rows > 0) {
                while ($img = $images->fetch_assoc()) {
                    $file_path = $_SERVER['DOCUMENT_ROOT'] . '/BSU_HRS/assets/images/rooms/' . $img['image_path'];
                    if (file_exists($file_path)) {
                        unlink($file_path);
                    }
                }
            }
            
            // Delete from room_images
            $stmt = $conn->prepare("DELETE FROM room_images WHERE room_id = ?");
            $stmt->bind_param("i", $room_id);
            $stmt->execute();
            $stmt->close();
            
            // Finally, delete the room itself
            $stmt = $conn->prepare("DELETE FROM rooms WHERE id = ?");
            $stmt->bind_param("i", $room_id);
            $stmt->execute();
            
            if ($stmt->affected_rows > 0) {
                $conn->commit();
                $message = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                            Room deleted successfully!
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>';
            } else {
                throw new Exception("Room not found");
            }
            $stmt->close();
            
        } catch (Exception $e) {
            $conn->rollback();
            $message = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                        Error deleting room: ' . $e->getMessage() . '
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>';
        }
    }
    
    // Handle image upload for room
    elseif ($action === 'add_image' || $action === 'update_image') {
        $room_id = (int)($_POST['room_id'] ?? 0);
        $is_primary = isset($_POST['is_primary']) ? 1 : 0;
        
        // Handle image upload
        if (isset($_FILES['room_image']) && $_FILES['room_image']['error'] === UPLOAD_ERR_OK) {
            $target_dir = $_SERVER['DOCUMENT_ROOT'] . '/BSU_HRS/assets/images/rooms/';
            
            // Create directory if it doesn't exist
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            
            $file_extension = strtolower(pathinfo($_FILES['room_image']['name'], PATHINFO_EXTENSION));
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            
            if (!in_array($file_extension, $allowed_extensions)) {
                $message = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                            Only JPG, JPEG, PNG, GIF, and WEBP files are allowed.
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>';
            } else {
                // Generate unique filename
                $new_filename = 'room_' . $room_id . '_' . time() . '_' . rand(100, 999) . '.' . $file_extension;
                $target_file = $target_dir . $new_filename;
                
                if (move_uploaded_file($_FILES['room_image']['tmp_name'], $target_file)) {
                    // If this is set as primary, remove primary status from other images for this room
                    if ($is_primary) {
                        $conn->query("UPDATE room_images SET is_primary = 0 WHERE room_id = $room_id");
                    }
                    
                    // Insert into room_images table
                    $stmt = $conn->prepare("INSERT INTO room_images (room_id, image_path, is_primary) VALUES (?, ?, ?)");
                    $stmt->bind_param("isi", $room_id, $new_filename, $is_primary);
                    
                    if ($stmt->execute()) {
                        $message = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                                    Image uploaded successfully!
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>';
                    } else {
                        $message = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    Database error: ' . $conn->error . '
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>';
                    }
                    $stmt->close();
                } else {
                    $message = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                Failed to upload image.
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>';
                }
            }
        }
    } elseif ($action === 'delete_image') {
        $image_id = (int)($_POST['image_id'] ?? 0);
        
        // Get image info to delete file
        $img_query = $conn->query("SELECT image_path, room_id, is_primary FROM room_images WHERE id = $image_id");
        if ($img_query && $img_query->num_rows > 0) {
            $img = $img_query->fetch_assoc();
            $file_path = $_SERVER['DOCUMENT_ROOT'] . '/BSU_HRS/assets/images/rooms/' . $img['image_path'];
            
            // Delete the file
            if (file_exists($file_path)) {
                unlink($file_path);
            }
            
            // Delete from database
            $conn->query("DELETE FROM room_images WHERE id = $image_id");
            
            // If this was primary, set another image as primary
            if ($img['is_primary'] == 1) {
                $conn->query("UPDATE room_images SET is_primary = 1 WHERE room_id = {$img['room_id']} LIMIT 1");
            }
            
            $message = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        Image deleted successfully!
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>';
        }
    } elseif ($action === 'set_primary') {
        $image_id = (int)($_POST['image_id'] ?? 0);
        
        // Get room_id from image
        $img_query = $conn->query("SELECT room_id FROM room_images WHERE id = $image_id");
        if ($img_query && $img_query->num_rows > 0) {
            $img = $img_query->fetch_assoc();
            $room_id = $img['room_id'];
            
            // Remove primary from all images of this room
            $conn->query("UPDATE room_images SET is_primary = 0 WHERE room_id = $room_id");
            
            // Set this image as primary
            $conn->query("UPDATE room_images SET is_primary = 1 WHERE id = $image_id");
            
            $message = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        Primary image updated!
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>';
        }
    } elseif ($action === 'edit_room') {
        $room_id = (int)($_POST['room_id'] ?? 0);
        $name = clean($_POST['name'] ?? '');
        $type_id = (int)($_POST['type_id'] ?? 0);
        $capacity = (int)($_POST['capacity'] ?? 0);
        $description = clean($_POST['description'] ?? '');
        
        if ($room_id && $name) {
            $stmt = $conn->prepare("UPDATE rooms SET name = ?, type_id = ?, capacity = ?, description = ? WHERE id = ?");
            $stmt->bind_param("siisi", $name, $type_id, $capacity, $description, $room_id);
            if ($stmt->execute()) {
                $message = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                            Room updated successfully!
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>';
            }
            $stmt->close();
        }
    }
}

// Get all rooms with their types
$rooms_query = "
    SELECT r.*, t.name as type_name 
    FROM rooms r 
    LEFT JOIN types_room t ON r.type_id = t.id 
    ORDER BY t.name, r.name
";
$rooms = $conn->query($rooms_query);

// Get all images for each room
$images_by_room = [];
$images_query = "SELECT * FROM room_images ORDER BY room_id, is_primary DESC, id DESC";
$images_result = $conn->query($images_query);
if ($images_result && $images_result->num_rows > 0) {
    while ($img = $images_result->fetch_assoc()) {
        $images_by_room[$img['room_id']][] = $img;
    }
}

// Get room types for dropdown
$types = $conn->query("SELECT id, name FROM types_room ORDER BY name");
?>

<style>
    :root {
        --bsu-red: #b71c1c;
        --bsu-dark-red: #8b0000;
    }
    
    .room-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 1.5rem;
        margin-top: 1.5rem;
    }
    
    .room-card {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
        border: 1px solid #e9ecef;
        position: relative;
    }
    
    .room-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(183,28,28,0.1);
        border-color: var(--bsu-red);
    }
    
    .room-header {
        background: linear-gradient(135deg, var(--bsu-red), var(--bsu-dark-red));
        color: white;
        padding: 1rem 1.25rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .room-header h3 {
        margin: 0;
        font-size: 1.2rem;
        font-weight: 600;
    }
    
    .room-header small {
        opacity: 0.9;
        font-size: 0.85rem;
    }
    
    .room-actions {
        display: flex;
        gap: 0.5rem;
    }
    
    .room-action-btn {
        background: rgba(255,255,255,0.2);
        border: none;
        color: white;
        width: 32px;
        height: 32px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .room-action-btn:hover {
        background: rgba(255,255,255,0.3);
        transform: scale(1.1);
    }
    
    .room-action-btn.delete:hover {
        background: #dc3545;
    }
    
    .room-body {
        padding: 1.25rem;
    }
    
    .image-gallery {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.75rem;
        margin-bottom: 1rem;
    }
    
    .image-item {
        position: relative;
        border-radius: 8px;
        overflow: hidden;
        aspect-ratio: 1 / 1;
        border: 2px solid transparent;
        transition: all 0.2s ease;
    }
    
    .image-item.primary {
        border-color: var(--bsu-red);
        box-shadow: 0 0 0 2px rgba(183,28,28,0.3);
    }
    
    .image-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .image-badge {
        position: absolute;
        top: 5px;
        left: 5px;
        background: var(--bsu-red);
        color: white;
        font-size: 0.7rem;
        padding: 0.15rem 0.4rem;
        border-radius: 4px;
        z-index: 2;
    }
    
    .image-actions {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(0,0,0,0.7);
        padding: 0.35rem;
        display: flex;
        justify-content: center;
        gap: 0.5rem;
        opacity: 0;
        transition: opacity 0.2s ease;
        z-index: 3;
    }
    
    .image-item:hover .image-actions {
        opacity: 1;
    }
    
    .image-action-btn {
        background: none;
        border: none;
        color: white;
        font-size: 1rem;
        cursor: pointer;
        padding: 0.2rem 0.4rem;
        border-radius: 4px;
        transition: all 0.2s ease;
    }
    
    .image-action-btn:hover {
        background: var(--bsu-red);
        transform: scale(1.1);
    }
    
    .image-action-btn.delete:hover {
        background: #dc3545;
    }
    
    .no-images {
        grid-column: span 3;
        text-align: center;
        padding: 2rem;
        background: #f8f9fa;
        border-radius: 8px;
        color: #6c757d;
        font-size: 0.9rem;
    }
    
    .upload-area {
        margin-top: 1rem;
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 8px;
        border: 2px dashed #dee2e6;
        transition: all 0.2s ease;
    }
    
    .upload-area:hover {
        border-color: var(--bsu-red);
        background: #fff8f8;
    }
    
    .upload-btn {
        background: var(--bsu-red);
        color: white;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .upload-btn:hover {
        background: var(--bsu-dark-red);
        transform: translateY(-2px);
    }
    
    .primary-checkbox {
        margin: 0.5rem 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.9rem;
    }
    
    /* Modal Styles */
    .modal-content {
        border-radius: 16px;
        border: none;
    }
    
    .modal-header {
        border-radius: 16px 16px 0 0;
        padding: 1.25rem;
    }
    
    .modal-header.bg-danger {
        background: linear-gradient(135deg, #dc3545, #a71d2a) !important;
    }
    
    /* Alert styles */
    .alert {
        border-radius: 12px;
        border: none;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        margin-bottom: 1.5rem;
    }
    
    .alert-success {
        background: linear-gradient(135deg, #d4edda, #c3e6cb);
        color: #155724;
    }
    
    .alert-danger {
        background: linear-gradient(135deg, #f8d7da, #f5c6cb);
        color: #721c24;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .room-grid {
            grid-template-columns: 1fr;
        }
        
        .image-gallery {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>

<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Room Management</h2>
        <button class="btn btn-primary" onclick="showAddRoomModal()">
            <i class="bi bi-plus-circle"></i> Add New Room
        </button>
    </div>
    
    <?= $message ?>
    
    <div class="room-grid">
        <?php 
        if ($rooms && $rooms->num_rows > 0):
            while ($room = $rooms->fetch_assoc()): 
                $room_images = $images_by_room[$room['id']] ?? [];
                $primary_image = null;
                foreach ($room_images as $img) {
                    if ($img['is_primary'] == 1) {
                        $primary_image = $img;
                        break;
                    }
                }
        ?>
        <div class="room-card" data-room-id="<?= $room['id'] ?>">
            <div class="room-header">
                <div>
                    <h3><?= htmlspecialchars($room['name']) ?></h3>
                    <small><?= htmlspecialchars($room['type_name'] ?? 'Uncategorized') ?></small>
                </div>
                <div class="room-actions">
                    <button class="room-action-btn" onclick="editRoom(<?= $room['id'] ?>)" title="Edit">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button type="button" class="room-action-btn delete" title="Delete" 
                            onclick="confirmDelete(<?= $room['id'] ?>, '<?= htmlspecialchars($room['name'], ENT_QUOTES) ?>')">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
            <div class="room-body">
                <!-- Image Gallery -->
                <div class="image-gallery">
                    <?php if (!empty($room_images)): ?>
                        <?php foreach (array_slice($room_images, 0, 6) as $img): ?>
                            <div class="image-item <?= $img['is_primary'] ? 'primary' : '' ?>">
                                <?php if ($img['is_primary']): ?>
                                    <span class="image-badge">Primary</span>
                                <?php endif; ?>
                                <img src="../assets/images/rooms/<?= htmlspecialchars($img['image_path']) ?>" 
                                     alt="Room Image"
                                     onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'no-image\' style=\'background:#f0f0f0;height:100%;display:flex;align-items:center;justify-content:center;\'><i class=\'bi bi-image\' style=\'font-size:1.5rem;color:#999;\'></i></div>'">
                                <div class="image-actions">
                                    <?php if (!$img['is_primary']): ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="set_primary">
                                        <input type="hidden" name="image_id" value="<?= $img['id'] ?>">
                                        <button type="submit" class="image-action-btn" title="Set as primary">
                                            <i class="bi bi-star"></i>
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                    <button type="button" class="image-action-btn delete" title="Delete" 
                                            onclick="confirmImageDelete(<?= $img['id'] ?>)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-images">
                            <i class="bi bi-images mb-2 d-block" style="font-size: 2rem;"></i>
                            <p>No images yet</p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Upload Form -->
                <div class="upload-area">
                    <form method="POST" enctype="multipart/form-data" class="upload-form">
                        <input type="hidden" name="action" value="add_image">
                        <input type="hidden" name="room_id" value="<?= $room['id'] ?>">
                        
                        <div class="mb-2">
                            <label class="form-label fw-bold">Upload New Image</label>
                            <input type="file" name="room_image" class="form-control form-control-sm" accept="image/*" required>
                        </div>
                        
                        <div class="primary-checkbox">
                            <input type="checkbox" name="is_primary" id="primary_<?= $room['id'] ?>" value="1" <?= empty($room_images) ? 'checked' : '' ?>>
                            <label for="primary_<?= $room['id'] ?>">Set as primary image</label>
                        </div>
                        
                        <button type="submit" class="upload-btn w-100">
                            <i class="bi bi-cloud-upload"></i> Upload Image
                        </button>
                    </form>
                </div>
                
                <!-- Room Info -->
                <div class="mt-3 small text-muted">
                    <i class="bi bi-info-circle"></i>
                    Capacity: <?= $room['capacity'] ?? 'N/A' ?> • 
                    <?= count($room_images) ?> image(s)
                </div>
            </div>
        </div>
        <?php 
            endwhile;
        else:
        ?>
        <div class="col-12 text-center py-5">
            <i class="bi bi-building" style="font-size: 4rem; color: #ccc;"></i>
            <h4 class="mt-3 text-muted">No rooms found</h4>
            <p>Click the "Add New Room" button to create your first room.</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Add/Edit Room Modal -->
<div class="modal fade" id="roomModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, var(--bsu-red), var(--bsu-dark-red)); color: white;">
                <h5 class="modal-title" id="roomModalTitle">Add New Room</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" id="roomForm">
                <div class="modal-body">
                    <input type="hidden" name="action" value="edit_room">
                    <input type="hidden" name="room_id" id="room_id" value="0">
                    
                    <div class="mb-3">
                        <label class="form-label">Room Name *</label>
                        <input type="text" class="form-control" name="name" id="room_name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Room Type</label>
                        <select class="form-select" name="type_id" id="room_type">
                            <option value="">Select Type</option>
                            <?php 
                            if ($types && $types->num_rows > 0) {
                                $types->data_seek(0);
                                while ($type = $types->fetch_assoc()): 
                            ?>
                            <option value="<?= $type['id'] ?>"><?= htmlspecialchars($type['name']) ?></option>
                            <?php 
                                endwhile;
                            } 
                            ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Capacity</label>
                        <input type="number" class="form-control" name="capacity" id="room_capacity" min="1">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" id="room_description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Room</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Custom Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, var(--bsu-red), var(--bsu-dark-red)); color: white;">
                <h5 class="modal-title">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    Confirm Deletion
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="bi bi-building" style="font-size: 3rem; color: var(--bsu-red); opacity: 0.5;"></i>
                </div>
                <h4 class="text-center mb-3" id="deleteRoomName" style="font-weight: 600; color: #2c3e50;"></h4>
                <p class="text-center mb-0">
                    Are you sure you want to delete this room? This action cannot be undone and will also remove:
                </p>
                <ul class="mt-3">
                    <li><i class="bi bi-image text-danger me-2"></i>All associated images</li>
                    <li><i class="bi bi-gear text-danger me-2"></i>Facility relationships</li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-2"></i>Cancel
                </button>
                <form method="POST" id="deleteRoomForm" style="display: inline;">
                    <input type="hidden" name="action" value="delete_room">
                    <input type="hidden" name="room_id" id="deleteRoomId" value="">
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-2"></i>Yes, Delete Room
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Image Delete Confirmation Modal -->
<div class="modal fade" id="imageDeleteConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #dc3545, #a71d2a); color: white;">
                <h5 class="modal-title">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    Delete Image
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <i class="bi bi-image" style="font-size: 3rem; color: #dc3545; opacity: 0.5;"></i>
                <p class="mt-3 mb-0">Are you sure you want to delete this image?</p>
                <p class="small text-muted">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" id="deleteImageForm" style="display: inline;">
                    <input type="hidden" name="action" value="delete_image">
                    <input type="hidden" name="image_id" id="deleteImageId" value="">
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-2"></i>Delete Image
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Initialize all modals when document is ready
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Bootstrap modals
    var deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
    var imageModal = new bootstrap.Modal(document.getElementById('imageDeleteConfirmModal'));
    var roomModal = new bootstrap.Modal(document.getElementById('roomModal'));
    
    // Make modals globally accessible
    window.deleteModal = deleteModal;
    window.imageModal = imageModal;
    window.roomModal = roomModal;
    
    // Preview image before upload
    document.querySelectorAll('input[type="file"]').forEach(input => {
        input.addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                const fileName = this.files[0].name;
                const fileSize = (this.files[0].size / 1024).toFixed(2);
                const parent = this.closest('.upload-area');
                
                // Remove existing file info if any
                const existingInfo = parent.querySelector('.file-info');
                if (existingInfo) {
                    existingInfo.remove();
                }
                
                const info = document.createElement('div');
                info.className = 'file-info small text-muted mt-1';
                info.innerHTML = `<i class="bi bi-file-image"></i> ${fileName} (${fileSize} KB)`;
                parent.appendChild(info);
            }
        });
    });
});

// Custom confirmation for room deletion
function confirmDelete(roomId, roomName) {
    document.getElementById('deleteRoomId').value = roomId;
    document.getElementById('deleteRoomName').textContent = '"' + roomName + '"';
    if (window.deleteModal) {
        window.deleteModal.show();
    }
    return false;
}

// Handle image deletion
function confirmImageDelete(imageId) {
    document.getElementById('deleteImageId').value = imageId;
    if (window.imageModal) {
        window.imageModal.show();
    }
    return false;
}

function showAddRoomModal() {
    document.getElementById('roomModalTitle').innerText = 'Add New Room';
    document.getElementById('room_id').value = '0';
    document.getElementById('room_name').value = '';
    document.getElementById('room_type').value = '';
    document.getElementById('room_capacity').value = '';
    document.getElementById('room_description').value = '';
    
    if (window.roomModal) {
        window.roomModal.show();
    }
}

function editRoom(roomId) {
    // You can implement AJAX to load room data here
    alert('Edit functionality for room ID: ' + roomId + ' - Implement AJAX to load room data');
}

// Prevent event bubbling for delete buttons
document.querySelectorAll('.room-action-btn.delete').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.stopPropagation();
    });
});
</script>

<?php require_once __DIR__ . '/inc/footer.php'; ?>