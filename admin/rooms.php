<?php
$pageTitle = 'Room Management';
require_once __DIR__ . '/inc/header.php';

$message = '';

// ══════════════════════════════════════════════════════════════
//  VENUE PHOTO MANAGER  — images for the public showcase
//  (venues table + venue_images table, same data rooms_showcase.php reads)
// ══════════════════════════════════════════════════════════════

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['venue_action'])) {
    $venue_action = $_POST['venue_action'];

    // ── Upload a new image to a venue ──────────────────────────────
    if ($venue_action === 'upload_venue_image') {
        $venue_id   = (int)($_POST['venue_id']  ?? 0);
        $is_primary = isset($_POST['is_primary']) ? 1 : 0;

        if ($venue_id && isset($_FILES['venue_image']) && $_FILES['venue_image']['error'] === UPLOAD_ERR_OK) {
            $target_dir = $_SERVER['DOCUMENT_ROOT'] . '/BSU_HRS/assets/images/rooms/';
            if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
            $ext = strtolower(pathinfo($_FILES['venue_image']['name'], PATHINFO_EXTENSION));

            if (!in_array($ext, ['jpg','jpeg','png','gif','webp'])) {
                $message = '<div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-exclamation-circle me-2"></i>Only JPG, PNG, GIF, WEBP allowed. <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
            } elseif ($_FILES['venue_image']['size'] > 5 * 1024 * 1024) {
                $message = '<div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-exclamation-circle me-2"></i>Image must be under 5 MB. <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
            } else {
                $fn = 'venue_' . $venue_id . '_' . time() . '_' . rand(100,999) . '.' . $ext;
                if (move_uploaded_file($_FILES['venue_image']['tmp_name'], $target_dir . $fn)) {
                    if ($is_primary) $conn->query("UPDATE venue_images SET is_primary=0 WHERE venue_id=$venue_id");
                    $stmt = $conn->prepare("INSERT INTO venue_images (venue_id, image_path, is_primary) VALUES(?,?,?)");
                    $stmt->bind_param("isi", $venue_id, $fn, $is_primary);
                    $stmt->execute()
                        ? $message = '<div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i>Photo uploaded! The showcase page now shows it. <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>'
                        : $message = '<div class="alert alert-danger alert-dismissible fade show">Database error: ' . $conn->error . ' <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
                    $stmt->close();
                } else {
                    $message = '<div class="alert alert-danger alert-dismissible fade show">Upload failed — check folder permissions. <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
                }
            }
        }
    }

    // ── Replace the primary showcase image (delete old, add new) ──
    elseif ($venue_action === 'replace_primary') {
        $venue_id   = (int)($_POST['venue_id']   ?? 0);
        $old_img_id = (int)($_POST['old_image_id'] ?? 0);

        if ($venue_id && isset($_FILES['venue_image']) && $_FILES['venue_image']['error'] === UPLOAD_ERR_OK) {
            $target_dir = $_SERVER['DOCUMENT_ROOT'] . '/BSU_HRS/assets/images/rooms/';
            if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
            $ext = strtolower(pathinfo($_FILES['venue_image']['name'], PATHINFO_EXTENSION));

            if (!in_array($ext, ['jpg','jpeg','png','gif','webp'])) {
                $message = '<div class="alert alert-danger alert-dismissible fade show">Only JPG, PNG, GIF, WEBP allowed. <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
            } elseif ($_FILES['venue_image']['size'] > 5 * 1024 * 1024) {
                $message = '<div class="alert alert-danger alert-dismissible fade show">Image must be under 5 MB. <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
            } else {
                $fn = 'venue_' . $venue_id . '_' . time() . '_' . rand(100,999) . '.' . $ext;
                if (move_uploaded_file($_FILES['venue_image']['tmp_name'], $target_dir . $fn)) {
                    // Delete old primary record + file
                    if ($old_img_id) {
                        $r = $conn->query("SELECT image_path FROM venue_images WHERE id=$old_img_id");
                        if ($r && $row = $r->fetch_assoc()) {
                            $fp = $target_dir . $row['image_path'];
                            if (file_exists($fp)) unlink($fp);
                        }
                        $conn->query("DELETE FROM venue_images WHERE id=$old_img_id");
                    }
                    $conn->query("UPDATE venue_images SET is_primary=0 WHERE venue_id=$venue_id");
                    $stmt = $conn->prepare("INSERT INTO venue_images (venue_id, image_path, is_primary) VALUES(?,?,1)");
                    $stmt->bind_param("is", $venue_id, $fn);
                    $stmt->execute()
                        ? $message = '<div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i>Showcase photo replaced! Visitors will see the new photo immediately. <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>'
                        : $message = '<div class="alert alert-danger alert-dismissible fade show">Database error. <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
                    $stmt->close();
                } else {
                    $message = '<div class="alert alert-danger alert-dismissible fade show">Upload failed. <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
                }
            }
        }
    }

    // ── Delete one venue image ─────────────────────────────────────
    elseif ($venue_action === 'delete_venue_image') {
        $img_id = (int)($_POST['image_id'] ?? 0);
        $r = $conn->query("SELECT image_path, venue_id, is_primary FROM venue_images WHERE id=$img_id");
        if ($r && $row = $r->fetch_assoc()) {
            $fp = $_SERVER['DOCUMENT_ROOT'] . '/BSU_HRS/assets/images/rooms/' . $row['image_path'];
            if (file_exists($fp)) unlink($fp);
            $conn->query("DELETE FROM venue_images WHERE id=$img_id");
            if ($row['is_primary']) $conn->query("UPDATE venue_images SET is_primary=1 WHERE venue_id={$row['venue_id']} LIMIT 1");
            $message = '<div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i>Photo deleted. <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
        }
    }

    // ── Set any image as the primary showcase photo ────────────────
    elseif ($venue_action === 'set_venue_primary') {
        $img_id = (int)($_POST['image_id'] ?? 0);
        $r = $conn->query("SELECT venue_id FROM venue_images WHERE id=$img_id");
        if ($r && $row = $r->fetch_assoc()) {
            $conn->query("UPDATE venue_images SET is_primary=0 WHERE venue_id={$row['venue_id']}");
            $conn->query("UPDATE venue_images SET is_primary=1 WHERE id=$img_id");
            $message = '<div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i>Showcase photo updated! <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
        }
    }
}

// ══════════════════════════════════════════════════════════════
//  ORIGINAL ROOM ACTIONS (rooms table)
// ══════════════════════════════════════════════════════════════

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'delete_room') {
        $room_id = (int)($_POST['room_id'] ?? 0);
        $conn->begin_transaction();
        try {
            $stmt = $conn->prepare("DELETE FROM room_facilities WHERE room_id=?");
            $stmt->bind_param("i",$room_id); $stmt->execute(); $stmt->close();
            $imgs = $conn->query("SELECT image_path FROM room_images WHERE room_id=$room_id");
            if ($imgs) while ($img=$imgs->fetch_assoc()) {
                $fp=$_SERVER['DOCUMENT_ROOT'].'/BSU_HRS/assets/images/rooms/'.$img['image_path'];
                if(file_exists($fp)) unlink($fp);
            }
            $stmt=$conn->prepare("DELETE FROM room_images WHERE room_id=?");
            $stmt->bind_param("i",$room_id); $stmt->execute(); $stmt->close();
            $stmt=$conn->prepare("DELETE FROM rooms WHERE id=?");
            $stmt->bind_param("i",$room_id); $stmt->execute();
            if($stmt->affected_rows>0){$conn->commit();$message='<div class="alert alert-success alert-dismissible fade show">Room deleted! <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';}
            else throw new Exception("Room not found");
            $stmt->close();
        } catch(Exception $e){$conn->rollback();$message='<div class="alert alert-danger alert-dismissible fade show">Error: '.$e->getMessage().' <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';}
    } elseif ($action==='add_image'||$action==='update_image') {
        $room_id=(int)($_POST['room_id']??0);$is_primary=isset($_POST['is_primary'])?1:0;
        if(isset($_FILES['room_image'])&&$_FILES['room_image']['error']===UPLOAD_ERR_OK){
            $td=$_SERVER['DOCUMENT_ROOT'].'/BSU_HRS/assets/images/rooms/';
            if(!file_exists($td))mkdir($td,0777,true);
            $ext=strtolower(pathinfo($_FILES['room_image']['name'],PATHINFO_EXTENSION));
            if(!in_array($ext,['jpg','jpeg','png','gif','webp'])){$message='<div class="alert alert-danger alert-dismissible fade show">Invalid type. <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';}
            else{$fn='room_'.$room_id.'_'.time().'_'.rand(100,999).'.'.$ext;
                if(move_uploaded_file($_FILES['room_image']['tmp_name'],$td.$fn)){
                    if($is_primary)$conn->query("UPDATE room_images SET is_primary=0 WHERE room_id=$room_id");
                    $stmt=$conn->prepare("INSERT INTO room_images (room_id,image_path,is_primary) VALUES(?,?,?)");
                    $stmt->bind_param("isi",$room_id,$fn,$is_primary);
                    $stmt->execute()?$message='<div class="alert alert-success alert-dismissible fade show">Image uploaded! <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>':$message='<div class="alert alert-danger alert-dismissible fade show">DB error. <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
                    $stmt->close();}}
        }
    } elseif($action==='delete_image'){
        $image_id=(int)($_POST['image_id']??0);
        $r=$conn->query("SELECT image_path,room_id,is_primary FROM room_images WHERE id=$image_id");
        if($r&&$img=$r->fetch_assoc()){$fp=$_SERVER['DOCUMENT_ROOT'].'/BSU_HRS/assets/images/rooms/'.$img['image_path'];if(file_exists($fp))unlink($fp);$conn->query("DELETE FROM room_images WHERE id=$image_id");if($img['is_primary'])$conn->query("UPDATE room_images SET is_primary=1 WHERE room_id={$img['room_id']} LIMIT 1");$message='<div class="alert alert-success alert-dismissible fade show">Image deleted! <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';}
    } elseif($action==='set_primary'){
        $image_id=(int)($_POST['image_id']??0);
        $r=$conn->query("SELECT room_id FROM room_images WHERE id=$image_id");
        if($r&&$img=$r->fetch_assoc()){$conn->query("UPDATE room_images SET is_primary=0 WHERE room_id={$img['room_id']}");$conn->query("UPDATE room_images SET is_primary=1 WHERE id=$image_id");$message='<div class="alert alert-success alert-dismissible fade show">Primary updated! <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';}
    } elseif($action==='edit_room'){
        $room_id=(int)($_POST['room_id']??0);$name=clean($_POST['name']??'');$type_id=(int)($_POST['type_id']??0);$capacity=(int)($_POST['capacity']??0);$description=clean($_POST['description']??'');
        if($room_id&&$name){$stmt=$conn->prepare("UPDATE rooms SET name=?,type_id=?,capacity=?,description=? WHERE id=?");$stmt->bind_param("siisi",$name,$type_id,$capacity,$description,$room_id);$stmt->execute()?$message='<div class="alert alert-success alert-dismissible fade show">Room updated! <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>':$message='<div class="alert alert-danger alert-dismissible fade show">Update failed. <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';$stmt->close();}
    }
}

// ══════════════════════════════════════════════════════════════
//  DATA FETCH
// ══════════════════════════════════════════════════════════════

// Venue showcase data
$function_venues = $conn->query("
    SELECT v.*,
           (SELECT id         FROM venue_images WHERE venue_id=v.id AND is_primary=1 LIMIT 1) as primary_img_id,
           (SELECT image_path FROM venue_images WHERE venue_id=v.id AND is_primary=1 LIMIT 1) as primary_image,
           (SELECT COUNT(*)   FROM venue_images WHERE venue_id=v.id) as image_count
    FROM venues v WHERE v.is_active=1 AND v.name LIKE '%Function%' ORDER BY v.name");

$guest_venues = $conn->query("
    SELECT v.*,
           (SELECT id         FROM venue_images WHERE venue_id=v.id AND is_primary=1 LIMIT 1) as primary_img_id,
           (SELECT image_path FROM venue_images WHERE venue_id=v.id AND is_primary=1 LIMIT 1) as primary_image,
           (SELECT COUNT(*)   FROM venue_images WHERE venue_id=v.id) as image_count
    FROM venues v WHERE v.is_active=1 AND v.name LIKE '%Guest%' ORDER BY v.name");

$venue_images_by_id = [];
$vi_res = $conn->query("SELECT vi.* FROM venue_images vi JOIN venues v ON vi.venue_id=v.id ORDER BY vi.venue_id, vi.is_primary DESC, vi.id DESC");
if ($vi_res) while ($vi=$vi_res->fetch_assoc()) $venue_images_by_id[$vi['venue_id']][]=$vi;

// Internal rooms
$rooms_result = $conn->query("SELECT r.*,t.name as type_name FROM rooms r LEFT JOIN types_room t ON r.type_id=t.id ORDER BY t.name,r.name");
$images_by_room=[];
$ir=$conn->query("SELECT * FROM room_images ORDER BY room_id, is_primary DESC, id DESC");
if($ir) while($img=$ir->fetch_assoc()) $images_by_room[$img['room_id']][]=$img;
$types=$conn->query("SELECT id,name FROM types_room ORDER BY name");
?>

<style>
:root{--red:#b71c1c;--red-dark:#8b0000;--red-pale:#fff5f5;--red-muted:rgba(183,28,28,0.08);--border:#e9ecef;--text:#1a1f2e;--sub:#6b7280;--bg:#f4f5f7;--surface:#ffffff;--radius:14px;--shadow:0 4px 20px rgba(0,0,0,0.06);}

/* ── Page wrap ── */
.rm-page{max-width:1380px;margin:0 auto;padding:0 1rem;}

/* ── Section banners ── */
.rm-banner{display:flex;align-items:center;gap:1rem;padding:1.1rem 1.5rem;border-radius:var(--radius);margin-bottom:1.5rem;color:white;background:linear-gradient(120deg,var(--red),var(--red-dark));box-shadow:0 4px 18px rgba(183,28,28,0.22);}
.rm-banner-dark{background:linear-gradient(120deg,#2c3e50,#1a252f);}
.rm-banner .b-icon{width:44px;height:44px;background:rgba(255,255,255,0.15);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;flex-shrink:0;}
.rm-banner .b-text h4{font-size:1rem;font-weight:700;margin:0;}
.rm-banner .b-text p{font-size:0.78rem;margin:0;opacity:.82;}

/* ── Venue cards grid ── */
.vpc-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(310px,1fr));gap:1.5rem;margin-bottom:2.5rem;}

/* ── Venue photo card ── */
.vpc{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;box-shadow:var(--shadow);transition:border-color .2s,box-shadow .2s;display:flex;flex-direction:column;}
.vpc:hover{border-color:var(--red);box-shadow:0 8px 30px rgba(183,28,28,0.11);}

.vpc-head{background:linear-gradient(120deg,var(--red),var(--red-dark));padding:.85rem 1.2rem;display:flex;align-items:center;gap:.75rem;}
.vpc-head-icon{width:36px;height:36px;background:rgba(255,255,255,.15);border-radius:8px;display:flex;align-items:center;justify-content:center;color:white;font-size:1rem;flex-shrink:0;}
.vpc-head-text{flex:1;}
.vpc-head-text h5{color:white;font-size:.95rem;font-weight:700;margin:0;line-height:1.2;}
.vpc-head-text small{color:rgba(255,255,255,.72);font-size:.72rem;}
.vpc-head-badge{background:rgba(255,255,255,.18);color:white;font-size:.68rem;font-weight:600;padding:.2rem .55rem;border-radius:50px;white-space:nowrap;}

/* ── Primary photo display ── */
.vpc-photo{position:relative;height:200px;overflow:hidden;background:#f0f0f0;cursor:zoom-in;}
.vpc-photo img{width:100%;height:100%;object-fit:cover;transition:transform .5s ease;}
.vpc:hover .vpc-photo img{transform:scale(1.04);}
.vpc-photo-overlay{position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,.55) 0%,transparent 55%);display:flex;align-items:flex-end;padding:.85rem 1rem;gap:.5rem;}
.tag-primary{display:inline-flex;align-items:center;gap:.3rem;background:var(--red);color:white;font-size:.68rem;font-weight:700;padding:.25rem .65rem;border-radius:50px;letter-spacing:.3px;}
.tag-count{background:rgba(0,0,0,.5);backdrop-filter:blur(6px);color:white;font-size:.7rem;padding:.25rem .6rem;border-radius:50px;display:flex;align-items:center;gap:.3rem;margin-left:auto;}

/* ── Replace button on primary photo ── */
.btn-replace{position:absolute;top:.75rem;right:.75rem;background:rgba(0,0,0,.5);backdrop-filter:blur(6px);color:white;border:1px solid rgba(255,255,255,.2);border-radius:8px;padding:.35rem .75rem;font-size:.72rem;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:.35rem;transition:all .2s;z-index:5;}
.btn-replace:hover{background:var(--red);border-color:var(--red);}

/* ── No photo state ── */
.vpc-no-photo{height:200px;background:#f8f9fa;border-bottom:1px solid var(--border);display:flex;flex-direction:column;align-items:center;justify-content:center;gap:.5rem;color:#aaa;}
.vpc-no-photo i{font-size:2.5rem;}
.vpc-no-photo p{font-size:.8rem;margin:0;}

/* ── Thumb strip ── */
.vpc-strip{padding:.85rem 1rem 0;display:flex;gap:.5rem;flex-wrap:wrap;}
.sthumb{position:relative;width:58px;height:58px;border-radius:8px;overflow:hidden;border:2px solid transparent;cursor:pointer;transition:border-color .2s;flex-shrink:0;}
.sthumb.active{border-color:var(--red);}
.sthumb img{width:100%;height:100%;object-fit:cover;}
.sthumb-ov{position:absolute;inset:0;background:rgba(0,0,0,.55);display:flex;align-items:center;justify-content:center;gap:.25rem;opacity:0;transition:opacity .2s;}
.sthumb:hover .sthumb-ov{opacity:1;}
.sth{background:none;border:none;color:white;font-size:.85rem;cursor:pointer;padding:.2rem .3rem;border-radius:4px;transition:background .15s;display:flex;align-items:center;}
.sth:hover{background:var(--red);}
.sth.del:hover{background:#dc3545;}

/* ── Upload zone ── */
.vpc-actions{padding:1rem;margin-top:auto;}
.uz{border:2px dashed #d1d5db;border-radius:10px;padding:1rem;background:#fafafa;transition:border-color .2s,background .2s;cursor:pointer;}
.uz:hover,.uz.dragover{border-color:var(--red);background:var(--red-pale);}
.uz-inner{display:flex;flex-direction:column;align-items:center;gap:.4rem;pointer-events:none;}
.uz-inner i{font-size:1.75rem;color:var(--red);}
.uz-inner .ut{font-size:.82rem;font-weight:600;color:var(--text);}
.uz-inner .us{font-size:.72rem;color:var(--sub);}
.uz-preview{display:none;align-items:center;gap:.75rem;padding:.5rem 0;pointer-events:none;}
.uz-preview.on{display:flex;}
.uz-thumb{width:52px;height:52px;border-radius:8px;object-fit:cover;border:2px solid var(--red);flex-shrink:0;}
.uz-fn{font-size:.8rem;font-weight:600;color:var(--text);}
.uz-fs{font-size:.72rem;color:var(--sub);}
.uz-opts{display:flex;align-items:center;justify-content:space-between;gap:.75rem;margin-top:.75rem;flex-wrap:wrap;}
.uz-opts label{font-size:.8rem;color:var(--sub);cursor:pointer;display:flex;align-items:center;gap:.35rem;}
.uz-opts input[type=checkbox]{accent-color:var(--red);width:15px;height:15px;}
.btn-up{background:var(--red);color:white;border:none;padding:.5rem 1.2rem;border-radius:8px;font-size:.82rem;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:.35rem;transition:background .2s,transform .15s,box-shadow .2s;white-space:nowrap;}
.btn-up:hover{background:var(--red-dark);transform:translateY(-1px);box-shadow:0 4px 12px rgba(183,28,28,.3);}
.btn-up:disabled{opacity:.55;cursor:not-allowed;transform:none;}

/* ── Live dot ── */
.live-dot{display:inline-flex;align-items:center;gap:.35rem;font-size:.7rem;color:#16a34a;font-weight:600;}
.live-dot::before{content:'';width:7px;height:7px;background:#16a34a;border-radius:50%;animation:pdot 2s infinite;}
@keyframes pdot{0%,100%{opacity:1;transform:scale(1);}50%{opacity:.4;transform:scale(.8);}}

/* ── Divider ── */
.rm-divider{display:flex;align-items:center;gap:1rem;margin:2.5rem 0 1.5rem;color:var(--sub);font-size:.78rem;font-weight:600;text-transform:uppercase;letter-spacing:1px;}
.rm-divider::before,.rm-divider::after{content:'';flex:1;height:1px;background:var(--border);}

/* ── Sub-section label ── */
.rm-sub-label{display:flex;align-items:center;gap:.5rem;margin-bottom:.85rem;font-size:.88rem;font-weight:700;color:var(--text);}
.rm-sub-label .count-pill{background:var(--red-muted);color:var(--red);font-size:.68rem;padding:.15rem .55rem;border-radius:50px;font-weight:600;}

/* ── Internal rooms (preserved) ── */
.room-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(350px,1fr));gap:1.5rem;margin-top:1.5rem;}
.room-card{background:white;border-radius:16px;overflow:hidden;box-shadow:0 5px 20px rgba(0,0,0,.05);transition:all .3s ease;border:1px solid #e9ecef;position:relative;}
.room-card:hover{transform:translateY(-5px);box-shadow:0 15px 30px rgba(183,28,28,.1);border-color:var(--red);}
.room-header{background:linear-gradient(135deg,var(--red),var(--red-dark));color:white;padding:1rem 1.25rem;display:flex;justify-content:space-between;align-items:center;}
.room-header h3{margin:0;font-size:1.2rem;font-weight:600;}
.room-header small{opacity:.9;font-size:.85rem;}
.room-actions{display:flex;gap:.5rem;}
.room-action-btn{background:rgba(255,255,255,.2);border:none;color:white;width:32px;height:32px;border-radius:6px;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all .2s ease;}
.room-action-btn:hover{background:rgba(255,255,255,.3);transform:scale(1.1);}
.room-action-btn.delete:hover{background:#dc3545;}
.room-body{padding:1.25rem;}
.image-gallery{display:grid;grid-template-columns:repeat(3,1fr);gap:.75rem;margin-bottom:1rem;}
.image-item{position:relative;border-radius:8px;overflow:hidden;aspect-ratio:1/1;border:2px solid transparent;transition:all .2s ease;}
.image-item.primary{border-color:var(--red);box-shadow:0 0 0 2px rgba(183,28,28,.3);}
.image-item img{width:100%;height:100%;object-fit:cover;}
.image-badge{position:absolute;top:5px;left:5px;background:var(--red);color:white;font-size:.7rem;padding:.15rem .4rem;border-radius:4px;z-index:2;}
.image-actions{position:absolute;bottom:0;left:0;right:0;background:rgba(0,0,0,.7);padding:.35rem;display:flex;justify-content:center;gap:.5rem;opacity:0;transition:opacity .2s ease;z-index:3;}
.image-item:hover .image-actions{opacity:1;}
.image-action-btn{background:none;border:none;color:white;font-size:1rem;cursor:pointer;padding:.2rem .4rem;border-radius:4px;transition:all .2s ease;}
.image-action-btn:hover{background:var(--red);transform:scale(1.1);}
.image-action-btn.delete:hover{background:#dc3545;}
.no-images{grid-column:span 3;text-align:center;padding:2rem;background:#f8f9fa;border-radius:8px;color:#6c757d;font-size:.9rem;}
.upload-area{margin-top:1rem;padding:1rem;background:#f8f9fa;border-radius:8px;border:2px dashed #dee2e6;transition:all .2s ease;}
.upload-area:hover{border-color:var(--red);background:#fff8f8;}
.upload-btn-sm{background:var(--red);color:white;border:none;padding:.5rem 1rem;border-radius:6px;font-weight:500;cursor:pointer;transition:all .2s ease;}
.upload-btn-sm:hover{background:var(--red-dark);transform:translateY(-2px);}
.primary-checkbox{margin:.5rem 0;display:flex;align-items:center;gap:.5rem;font-size:.9rem;}
.modal-content{border-radius:16px;border:none;}
.modal-header{border-radius:16px 16px 0 0;padding:1.25rem;}
.alert{border-radius:12px;border:none;box-shadow:0 5px 15px rgba(0,0,0,.05);margin-bottom:1.5rem;}
.alert-success{background:linear-gradient(135deg,#d4edda,#c3e6cb);color:#155724;}
.alert-danger{background:linear-gradient(135deg,#f8d7da,#f5c6cb);color:#721c24;}

/* ── Lightbox ── */
.rm-lb{display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.88);align-items:center;justify-content:center;}
.rm-lb.open{display:flex;}
.rm-lb img{max-width:90vw;max-height:85vh;border-radius:12px;box-shadow:0 20px 60px rgba(0,0,0,.5);object-fit:contain;}
.rm-lb-close{position:fixed;top:1.25rem;right:1.5rem;background:rgba(255,255,255,.12);backdrop-filter:blur(6px);border:1px solid rgba(255,255,255,.2);color:white;font-size:1.4rem;border-radius:50%;width:42px;height:42px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background .2s;}
.rm-lb-close:hover{background:var(--red);}

@media(max-width:768px){.vpc-grid,.room-grid{grid-template-columns:1fr;}.image-gallery{grid-template-columns:repeat(2,1fr);}.uz-opts{flex-direction:column;align-items:flex-start;}}
</style>

<!-- Lightbox -->
<div class="rm-lb" id="rmLb">
    <button class="rm-lb-close" onclick="closeLb()"><i class="bi bi-x"></i></button>
    <img src="" id="rmLbImg" alt="Preview">
</div>

<div class="rm-page">

    <?= $message ?>

    <!-- ══════════════════════════════════
         SECTION 1 — SHOWCASE PHOTO MANAGER
    ═══════════════════════════════════ -->
    <div class="rm-banner">
        <div class="b-icon"><i class="bi bi-camera-fill"></i></div>
        <div class="b-text">
            <h4>Showcase Photo Manager</h4>
            <p>Photos shown on the <strong>public rooms page</strong>. The <i class="bi bi-star-fill"></i> starred photo is what visitors see in the advertisement.</p>
        </div>
        <div class="ms-auto text-end"><div class="live-dot">Live on website</div></div>
    </div>

    <!-- Function Rooms -->
    <div class="rm-sub-label">
        <i class="bi bi-building text-danger"></i> Function Rooms
        <span class="count-pill"><?= $function_venues ? $function_venues->num_rows : 0 ?> venues</span>
    </div>

    <div class="vpc-grid">
        <?php if ($function_venues && $function_venues->num_rows > 0):
            while ($v = $function_venues->fetch_assoc()):
                $vimgs = $venue_images_by_id[$v['id']] ?? [];
        ?>
        <div class="vpc">
            <!-- Header -->
            <div class="vpc-head">
                <div class="vpc-head-icon"><i class="bi bi-door-open"></i></div>
                <div class="vpc-head-text">
                    <h5><?= htmlspecialchars($v['name']) ?></h5>
                    <small><?= htmlspecialchars($v['floor'] ?? '') ?> &middot; Capacity: <?= $v['capacity'] ?? 'N/A' ?></small>
                </div>
                <div class="vpc-head-badge"><i class="bi bi-images me-1"></i><?= $v['image_count'] ?> photo<?= $v['image_count']!=1?'s':'' ?></div>
            </div>

            <!-- Primary / showcase photo -->
            <?php if ($v['primary_image']): ?>
            <div class="vpc-photo" onclick="openLb('../assets/images/rooms/<?= htmlspecialchars($v['primary_image']) ?>')">
                <img src="../assets/images/rooms/<?= htmlspecialchars($v['primary_image']) ?>"
                     alt="<?= htmlspecialchars($v['name']) ?>"
                     onerror="this.closest('.vpc-photo').outerHTML='<div class=\'vpc-no-photo\'><i class=\'bi bi-image\'></i><p>File missing from server</p></div>'">
                <div class="vpc-photo-overlay">
                    <span class="tag-primary"><i class="bi bi-star-fill"></i> Showcase</span>
                    <?php if ($v['image_count'] > 1): ?>
                    <span class="tag-count"><i class="bi bi-images"></i> <?= $v['image_count'] ?></span>
                    <?php endif; ?>
                </div>
                <!-- One-click replace button -->
                <label class="btn-replace" title="Replace showcase photo"
                       onclick="event.stopPropagation();document.getElementById('qr_<?= $v['id'] ?>').click()">
                    <i class="bi bi-arrow-repeat"></i> Replace Photo
                </label>
                <input type="file" id="qr_<?= $v['id'] ?>" class="d-none" accept="image/*"
                       onchange="quickReplace(this,<?= $v['id'] ?>,<?= (int)$v['primary_img_id'] ?>)">
            </div>
            <?php else: ?>
            <div class="vpc-no-photo">
                <i class="bi bi-image-fill"></i>
                <p>No showcase photo yet</p>
                <small class="text-muted">Upload one below &darr;</small>
            </div>
            <?php endif; ?>

            <!-- All photo thumbnails -->
            <?php if (!empty($vimgs)): ?>
            <div class="vpc-strip">
                <?php foreach ($vimgs as $vi): ?>
                <div class="sthumb <?= $vi['is_primary']?'active':'' ?>" title="<?= $vi['is_primary']?'Current showcase photo':'Click ★ to make this the showcase photo' ?>">
                    <img src="../assets/images/rooms/<?= htmlspecialchars($vi['image_path']) ?>"
                         alt="" onclick="openLb('../assets/images/rooms/<?= htmlspecialchars($vi['image_path']) ?>')"
                         onerror="this.style.opacity=.3">
                    <div class="sthumb-ov">
                        <?php if (!$vi['is_primary']): ?>
                        <form method="POST" style="display:inline" onsubmit="return confirm('Set this as the showcase photo for <?= htmlspecialchars($v['name'],ENT_QUOTES) ?>?')">
                            <input type="hidden" name="venue_action" value="set_venue_primary">
                            <input type="hidden" name="image_id" value="<?= $vi['id'] ?>">
                            <button type="submit" class="sth" title="Set as showcase"><i class="bi bi-star"></i></button>
                        </form>
                        <?php else: ?>
                        <span style="color:#fbbf24;font-size:.9rem" title="Current showcase photo"><i class="bi bi-star-fill"></i></span>
                        <?php endif; ?>
                        <form method="POST" style="display:inline" onsubmit="return confirm('Permanently delete this photo?')">
                            <input type="hidden" name="venue_action" value="delete_venue_image">
                            <input type="hidden" name="image_id" value="<?= $vi['id'] ?>">
                            <button type="submit" class="sth del" title="Delete photo"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Upload zone -->
            <div class="vpc-actions">
                <form method="POST" enctype="multipart/form-data" id="vf_<?= $v['id'] ?>">
                    <input type="hidden" name="venue_action" value="upload_venue_image">
                    <input type="hidden" name="venue_id" value="<?= $v['id'] ?>">
                    <label class="uz" for="vfi_<?= $v['id'] ?>" id="vz_<?= $v['id'] ?>">
                        <div class="uz-preview" id="vp_<?= $v['id'] ?>">
                            <img class="uz-thumb" id="vpt_<?= $v['id'] ?>" src="" alt="">
                            <div><div class="uz-fn" id="vpn_<?= $v['id'] ?>"></div><div class="uz-fs" id="vps_<?= $v['id'] ?>"></div></div>
                        </div>
                        <div class="uz-inner" id="vzi_<?= $v['id'] ?>">
                            <i class="bi bi-cloud-upload"></i>
                            <span class="ut">Click or drag a photo here</span>
                            <span class="us">JPG &middot; PNG &middot; WEBP &middot; max 5 MB</span>
                        </div>
                    </label>
                    <input type="file" class="d-none" id="vfi_<?= $v['id'] ?>" name="venue_image" accept="image/*"
                           onchange="previewFile(this,<?= $v['id'] ?>)">
                    <div class="uz-opts">
                        <label>
                            <input type="checkbox" name="is_primary" value="1" <?= empty($vimgs)?'checked':'' ?>>
                            Make this the <strong>showcase photo</strong> <i class="bi bi-star-fill text-warning" style="font-size:.75rem"></i>
                        </label>
                        <button type="submit" class="btn-up" id="vub_<?= $v['id'] ?>" disabled>
                            <i class="bi bi-cloud-upload"></i> Upload
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <?php endwhile; else: ?>
        <div class="text-muted text-center py-4" style="grid-column:1/-1">
            <i class="bi bi-building" style="font-size:2rem;display:block;margin-bottom:.5rem"></i>
            No active function rooms found in the database.
        </div>
        <?php endif; ?>
    </div>

    <!-- Guest Rooms -->
    <div class="rm-sub-label">
        <i class="bi bi-house-heart text-danger"></i> Guest Rooms
        <span class="count-pill"><?= $guest_venues ? $guest_venues->num_rows : 0 ?> rooms</span>
    </div>

    <div class="vpc-grid">
        <?php if ($guest_venues && $guest_venues->num_rows > 0):
            while ($v = $guest_venues->fetch_assoc()):
                $vimgs = $venue_images_by_id[$v['id']] ?? [];
        ?>
        <div class="vpc">
            <div class="vpc-head">
                <div class="vpc-head-icon"><i class="bi bi-moon-stars-fill"></i></div>
                <div class="vpc-head-text">
                    <h5><?= htmlspecialchars($v['name']) ?></h5>
                    <small><?= htmlspecialchars($v['floor'] ?? '') ?> &middot; Capacity: <?= $v['capacity'] ?? 'N/A' ?></small>
                </div>
                <div class="vpc-head-badge"><i class="bi bi-images me-1"></i><?= $v['image_count'] ?> photo<?= $v['image_count']!=1?'s':'' ?></div>
            </div>

            <?php if ($v['primary_image']): ?>
            <div class="vpc-photo" onclick="openLb('../assets/images/rooms/<?= htmlspecialchars($v['primary_image']) ?>')">
                <img src="../assets/images/rooms/<?= htmlspecialchars($v['primary_image']) ?>"
                     alt="<?= htmlspecialchars($v['name']) ?>"
                     onerror="this.closest('.vpc-photo').outerHTML='<div class=\'vpc-no-photo\'><i class=\'bi bi-image\'></i><p>File missing</p></div>'">
                <div class="vpc-photo-overlay">
                    <span class="tag-primary"><i class="bi bi-star-fill"></i> Showcase</span>
                    <?php if ($v['image_count'] > 1): ?>
                    <span class="tag-count"><i class="bi bi-images"></i> <?= $v['image_count'] ?></span>
                    <?php endif; ?>
                </div>
                <label class="btn-replace"
                       onclick="event.stopPropagation();document.getElementById('qr_<?= $v['id'] ?>').click()">
                    <i class="bi bi-arrow-repeat"></i> Replace Photo
                </label>
                <input type="file" id="qr_<?= $v['id'] ?>" class="d-none" accept="image/*"
                       onchange="quickReplace(this,<?= $v['id'] ?>,<?= (int)$v['primary_img_id'] ?>)">
            </div>
            <?php else: ?>
            <div class="vpc-no-photo">
                <i class="bi bi-image-fill"></i>
                <p>No showcase photo yet</p>
                <small class="text-muted">Upload one below &darr;</small>
            </div>
            <?php endif; ?>

            <?php if (!empty($vimgs)): ?>
            <div class="vpc-strip">
                <?php foreach ($vimgs as $vi): ?>
                <div class="sthumb <?= $vi['is_primary']?'active':'' ?>">
                    <img src="../assets/images/rooms/<?= htmlspecialchars($vi['image_path']) ?>"
                         alt="" onclick="openLb('../assets/images/rooms/<?= htmlspecialchars($vi['image_path']) ?>')"
                         onerror="this.style.opacity=.3">
                    <div class="sthumb-ov">
                        <?php if (!$vi['is_primary']): ?>
                        <form method="POST" style="display:inline" onsubmit="return confirm('Set as showcase photo for <?= htmlspecialchars($v['name'],ENT_QUOTES) ?>?')">
                            <input type="hidden" name="venue_action" value="set_venue_primary">
                            <input type="hidden" name="image_id" value="<?= $vi['id'] ?>">
                            <button type="submit" class="sth"><i class="bi bi-star"></i></button>
                        </form>
                        <?php else: ?>
                        <span style="color:#fbbf24;font-size:.9rem"><i class="bi bi-star-fill"></i></span>
                        <?php endif; ?>
                        <form method="POST" style="display:inline" onsubmit="return confirm('Permanently delete this photo?')">
                            <input type="hidden" name="venue_action" value="delete_venue_image">
                            <input type="hidden" name="image_id" value="<?= $vi['id'] ?>">
                            <button type="submit" class="sth del"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <div class="vpc-actions">
                <form method="POST" enctype="multipart/form-data" id="vf_<?= $v['id'] ?>">
                    <input type="hidden" name="venue_action" value="upload_venue_image">
                    <input type="hidden" name="venue_id" value="<?= $v['id'] ?>">
                    <label class="uz" for="vfi_<?= $v['id'] ?>" id="vz_<?= $v['id'] ?>">
                        <div class="uz-preview" id="vp_<?= $v['id'] ?>">
                            <img class="uz-thumb" id="vpt_<?= $v['id'] ?>" src="" alt="">
                            <div><div class="uz-fn" id="vpn_<?= $v['id'] ?>"></div><div class="uz-fs" id="vps_<?= $v['id'] ?>"></div></div>
                        </div>
                        <div class="uz-inner" id="vzi_<?= $v['id'] ?>">
                            <i class="bi bi-cloud-upload"></i>
                            <span class="ut">Click or drag a photo here</span>
                            <span class="us">JPG &middot; PNG &middot; WEBP &middot; max 5 MB</span>
                        </div>
                    </label>
                    <input type="file" class="d-none" id="vfi_<?= $v['id'] ?>" name="venue_image" accept="image/*"
                           onchange="previewFile(this,<?= $v['id'] ?>)">
                    <div class="uz-opts">
                        <label>
                            <input type="checkbox" name="is_primary" value="1" <?= empty($vimgs)?'checked':'' ?>>
                            Make this the <strong>showcase photo</strong> <i class="bi bi-star-fill text-warning" style="font-size:.75rem"></i>
                        </label>
                        <button type="submit" class="btn-up" id="vub_<?= $v['id'] ?>" disabled>
                            <i class="bi bi-cloud-upload"></i> Upload
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <?php endwhile; else: ?>
        <div class="text-muted text-center py-4" style="grid-column:1/-1">
            <i class="bi bi-house" style="font-size:2rem;display:block;margin-bottom:.5rem"></i>
            No active guest rooms found.
        </div>
        <?php endif; ?>
    </div>

    <!-- ══════════════════════════════════
         SECTION 2 — INTERNAL ROOMS
    ═══════════════════════════════════ -->
    <div class="rm-divider">Internal Room Records</div>

    <div class="rm-banner rm-banner-dark" style="margin-bottom:1.25rem">
        <div class="b-icon"><i class="bi bi-database-fill"></i></div>
        <div class="b-text">
            <h4>Room Records</h4>
            <p>Manage internal room details, capacity info, and additional image sets.</p>
        </div>
        <button class="btn btn-light btn-sm ms-auto" onclick="showAddRoomModal()">
            <i class="bi bi-plus-circle me-1"></i> Add Room
        </button>
    </div>

    <div class="room-grid">
        <?php if ($rooms_result && $rooms_result->num_rows > 0):
            while ($room = $rooms_result->fetch_assoc()):
                $room_images = $images_by_room[$room['id']] ?? [];
        ?>
        <div class="room-card">
            <div class="room-header">
                <div>
                    <h3><?= htmlspecialchars($room['name']) ?></h3>
                    <small><?= htmlspecialchars($room['type_name'] ?? 'Uncategorized') ?></small>
                </div>
                <div class="room-actions">
                    <button class="room-action-btn" onclick="editRoom(<?= $room['id'] ?>)" title="Edit"><i class="bi bi-pencil"></i></button>
                    <button type="button" class="room-action-btn delete" title="Delete"
                            onclick="confirmDelete(<?= $room['id'] ?>,'<?= htmlspecialchars($room['name'],ENT_QUOTES) ?>')">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
            <div class="room-body">
                <div class="image-gallery">
                    <?php if (!empty($room_images)): ?>
                        <?php foreach (array_slice($room_images,0,6) as $img): ?>
                        <div class="image-item <?= $img['is_primary']?'primary':'' ?>">
                            <?php if($img['is_primary']): ?><span class="image-badge">Primary</span><?php endif; ?>
                            <img src="../assets/images/rooms/<?= htmlspecialchars($img['image_path']) ?>" alt="Room Image"
                                 onerror="this.parentElement.innerHTML='<div style=\'background:#f0f0f0;height:100%;display:flex;align-items:center;justify-content:center\'><i class=\'bi bi-image\' style=\'font-size:1.5rem;color:#999\'></i></div>'">
                            <div class="image-actions">
                                <?php if(!$img['is_primary']): ?>
                                <form method="POST" style="display:inline">
                                    <input type="hidden" name="action" value="set_primary">
                                    <input type="hidden" name="image_id" value="<?= $img['id'] ?>">
                                    <button type="submit" class="image-action-btn" title="Set primary"><i class="bi bi-star"></i></button>
                                </form>
                                <?php endif; ?>
                                <button type="button" class="image-action-btn delete" title="Delete" onclick="confirmImageDelete(<?= $img['id'] ?>)"><i class="bi bi-trash"></i></button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                    <div class="no-images"><i class="bi bi-images mb-2 d-block" style="font-size:2rem"></i><p>No images yet</p></div>
                    <?php endif; ?>
                </div>
                <div class="upload-area">
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="add_image">
                        <input type="hidden" name="room_id" value="<?= $room['id'] ?>">
                        <div class="mb-2">
                            <label class="form-label fw-bold">Upload New Image</label>
                            <input type="file" name="room_image" class="form-control form-control-sm" accept="image/*" required>
                        </div>
                        <div class="primary-checkbox">
                            <input type="checkbox" name="is_primary" id="p_<?= $room['id'] ?>" value="1" <?= empty($room_images)?'checked':'' ?>>
                            <label for="p_<?= $room['id'] ?>">Set as primary image</label>
                        </div>
                        <button type="submit" class="upload-btn-sm w-100"><i class="bi bi-cloud-upload"></i> Upload Image</button>
                    </form>
                </div>
                <div class="mt-3 small text-muted">
                    <i class="bi bi-info-circle"></i> Capacity: <?= $room['capacity']??'N/A' ?> &middot; <?= count($room_images) ?> image(s)
                </div>
            </div>
        </div>
        <?php endwhile; else: ?>
        <div class="col-12 text-center py-5">
            <i class="bi bi-building" style="font-size:4rem;color:#ccc"></i>
            <h4 class="mt-3 text-muted">No rooms found</h4>
            <p>Click "Add Room" to create your first room.</p>
        </div>
        <?php endif; ?>
    </div>

</div><!-- /rm-page -->

<!-- Add/Edit Room Modal -->
<div class="modal fade" id="roomModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background:linear-gradient(135deg,var(--red),var(--red-dark));color:white">
                <h5 class="modal-title" id="roomModalTitle">Add New Room</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="roomForm">
                <div class="modal-body">
                    <input type="hidden" name="action" value="edit_room">
                    <input type="hidden" name="room_id" id="room_id" value="0">
                    <div class="mb-3"><label class="form-label">Room Name *</label><input type="text" class="form-control" name="name" id="room_name" required></div>
                    <div class="mb-3"><label class="form-label">Room Type</label>
                        <select class="form-select" name="type_id" id="room_type">
                            <option value="">Select Type</option>
                            <?php if($types&&$types->num_rows>0){$types->data_seek(0);while($t=$types->fetch_assoc()):?><option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['name']) ?></option><?php endwhile;}?>
                        </select></div>
                    <div class="mb-3"><label class="form-label">Capacity</label><input type="number" class="form-control" name="capacity" id="room_capacity" min="1"></div>
                    <div class="mb-3"><label class="form-label">Description</label><textarea class="form-control" name="description" id="room_description" rows="3"></textarea></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Room</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Room Modal -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background:linear-gradient(135deg,var(--red),var(--red-dark));color:white">
                <h5 class="modal-title"><i class="bi bi-exclamation-triangle-fill me-2"></i>Confirm Deletion</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <i class="bi bi-building" style="font-size:3rem;color:var(--red);opacity:.5"></i>
                <h4 class="mt-3 mb-2" id="deleteRoomName" style="font-weight:600;color:#2c3e50"></h4>
                <p>Are you sure? All images and facility relationships will also be removed.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x-circle me-2"></i>Cancel</button>
                <form method="POST" id="deleteRoomForm" style="display:inline">
                    <input type="hidden" name="action" value="delete_room">
                    <input type="hidden" name="room_id" id="deleteRoomId" value="">
                    <button type="submit" class="btn btn-danger"><i class="bi bi-trash me-2"></i>Yes, Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Image Modal -->
<div class="modal fade" id="imageDeleteConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background:linear-gradient(135deg,#dc3545,#a71d2a);color:white">
                <h5 class="modal-title"><i class="bi bi-exclamation-triangle-fill me-2"></i>Delete Image</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <i class="bi bi-image" style="font-size:3rem;color:#dc3545;opacity:.5"></i>
                <p class="mt-3 mb-1">Delete this image permanently?</p>
                <p class="small text-muted">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" id="deleteImageForm" style="display:inline">
                    <input type="hidden" name="action" value="delete_image">
                    <input type="hidden" name="image_id" id="deleteImageId" value="">
                    <button type="submit" class="btn btn-danger"><i class="bi bi-trash me-2"></i>Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Hidden quick-replace form -->
<form method="POST" enctype="multipart/form-data" id="qrForm" style="display:none">
    <input type="hidden" name="venue_action" value="replace_primary">
    <input type="hidden" name="venue_id"     id="qr_vid">
    <input type="hidden" name="old_image_id" id="qr_oid">
    <input type="file"   name="venue_image"  id="qr_file">
</form>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    window.deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
    window.imageModal  = new bootstrap.Modal(document.getElementById('imageDeleteConfirmModal'));
    window.roomModal   = new bootstrap.Modal(document.getElementById('roomModal'));
});

/* ── File preview in upload zone ── */
function previewFile(input, id) {
    const file = input.files && input.files[0];
    const preview = document.getElementById('vp_'+id);
    const inner   = document.getElementById('vzi_'+id);
    const thumb   = document.getElementById('vpt_'+id);
    const nameEl  = document.getElementById('vpn_'+id);
    const sizeEl  = document.getElementById('vps_'+id);
    const btn     = document.getElementById('vub_'+id);
    const zone    = document.getElementById('vz_'+id);
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function(e) {
        thumb.src = e.target.result;
        nameEl.textContent = file.name;
        sizeEl.textContent = (file.size/1024).toFixed(1)+' KB';
        preview.classList.add('on');
        inner.style.display = 'none';
        btn.disabled = false;
        zone.style.borderColor = '#b71c1c';
        zone.style.background  = '#fff5f5';
    };
    reader.readAsDataURL(file);
}

/* ── Quick replace: click "Replace Photo" on primary ── */
function quickReplace(input, venueId, oldImgId) {
    const file = input.files && input.files[0];
    if (!file) return;
    const ext = file.name.split('.').pop().toLowerCase();
    if (!['jpg','jpeg','png','gif','webp'].includes(ext)) { alert('Only JPG, PNG, GIF, WEBP allowed.'); input.value=''; return; }
    if (file.size > 5*1024*1024) { alert('File must be under 5 MB.'); input.value=''; return; }
    if (!confirm('Replace the current showcase photo for this room?\nThe old photo will be permanently deleted.')) { input.value=''; return; }
    const dt = new DataTransfer();
    dt.items.add(file);
    document.getElementById('qr_file').files = dt.files;
    document.getElementById('qr_vid').value  = venueId;
    document.getElementById('qr_oid').value  = oldImgId;
    document.getElementById('qrForm').submit();
}

/* ── Drag & drop on upload zones ── */
document.querySelectorAll('.uz').forEach(function(zone) {
    zone.addEventListener('dragover',  function(e) { e.preventDefault(); zone.classList.add('dragover'); });
    zone.addEventListener('dragleave', function()  { zone.classList.remove('dragover'); });
    zone.addEventListener('drop', function(e) {
        e.preventDefault();
        zone.classList.remove('dragover');
        // The hidden input is the next sibling of the label
        const input = document.getElementById(zone.getAttribute('for'));
        if (!input || !e.dataTransfer.files.length) return;
        const dt = new DataTransfer();
        dt.items.add(e.dataTransfer.files[0]);
        input.files = dt.files;
        input.dispatchEvent(new Event('change'));
    });
});

/* ── Lightbox ── */
function openLb(src) {
    document.getElementById('rmLbImg').src = src;
    document.getElementById('rmLb').classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeLb() {
    document.getElementById('rmLb').classList.remove('open');
    document.body.style.overflow = '';
}
document.getElementById('rmLb').addEventListener('click', function(e) { if(e.target===this) closeLb(); });
document.addEventListener('keydown', function(e) { if(e.key==='Escape') closeLb(); });

/* ── Internal room modals ── */
function confirmDelete(id, name) {
    document.getElementById('deleteRoomId').value = id;
    document.getElementById('deleteRoomName').textContent = '"'+name+'"';
    window.deleteModal.show();
}
function confirmImageDelete(id) {
    document.getElementById('deleteImageId').value = id;
    window.imageModal.show();
}
function showAddRoomModal() {
    document.getElementById('roomModalTitle').innerText  = 'Add New Room';
    document.getElementById('room_id').value          = '0';
    document.getElementById('room_name').value        = '';
    document.getElementById('room_type').value        = '';
    document.getElementById('room_capacity').value    = '';
    document.getElementById('room_description').value = '';
    window.roomModal.show();
}
function editRoom(id) {
    alert('Edit room ID '+id+' — implement AJAX to pre-fill the form.');
}
</script>

<?php require_once __DIR__ . '/inc/footer.php'; ?>