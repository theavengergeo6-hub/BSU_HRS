<?php
/**
 * Rooms: Featured Function Rooms & Guest Rooms (bookable)
 * Horizontal scrollable cards, full-screen modal with details
 */
$pageTitle = 'Rooms';
require_once __DIR__ . '/inc/link.php';

$base = rtrim(BASE_URL, '/');
$assets_base = $base . '/assets/images/';

$rooms_with_images = getRoomsWithImagesByType($conn);
$function_rooms = [];
$guest_rooms = [];
foreach ($rooms_with_images as $r) {
    $t = $r['type_name'] ?? '';
    if (stripos($t, 'Function') !== false) {
        $function_rooms[] = $r;
    } else {
        $guest_rooms[] = $r;
    }
}

// Build full room data for modal (all images + details)
$rooms_for_modal = [];
foreach (array_merge($function_rooms, $guest_rooms) as $room) {
    $room_id = (int)($room['id'] ?? 0);
    $images = getRoomImages($conn, $room_id);
    $rooms_for_modal[$room_id] = [
        'id' => $room_id,
        'name' => $room['name'] ?? '',
        'description' => $room['description'] ?? '',
        'price' => $room['price'] ?? 0,
        'area' => $room['area'] ?? '',
        'seats_capacity' => $room['seats_capacity'] ?? null,
        'tables_count' => $room['tables_count'] ?? null,
        'adult_capacity' => $room['adult_capacity'] ?? 0,
        'children_capacity' => $room['children_capacity'] ?? 0,
        'type_name' => $room['type_name'] ?? '',
        'images' => $images,
        'details_extra' => $room['details_extra'] ?? '',
    ];
}

$has_any = !empty($function_rooms) || !empty($guest_rooms);

$extraStyles = '<style>
    .rooms-dark { background: #2c2c2c; color: #f5f5f5; padding: 3rem 0; }
    .rooms-dark .section-title { font-family: Georgia, serif; font-size: 2rem; text-align: center; margin-bottom: 2rem; color: #f5f5f5; }
    .rooms-scroll-wrap { overflow-x: auto; overflow-y: hidden; padding-bottom: 1rem; -webkit-overflow-scrolling: touch; cursor: grab; user-select: none; scrollbar-width: none; -ms-overflow-style: none; }
    .rooms-scroll-wrap::-webkit-scrollbar { display: none; }
    .rooms-scroll-wrap.dragging { cursor: grabbing; scroll-behavior: auto; }
    .rooms-cards-row { display: flex; gap: 1.5rem; flex-wrap: nowrap; min-width: min-content; padding: 0 0.25rem; }
    .room-card-suite { flex: 0 0 320px; max-width: 320px; background: #3d3d3d; border-radius: 12px; overflow: hidden; box-shadow: 0 8px 24px rgba(0,0,0,0.3); display: flex; flex-direction: column; color: #e8e8e8; border: 1px solid #4a4a4a; }
    .room-card-suite .img-wrap { position: relative; height: 220px; background: #2a2a2a; overflow: hidden; }
    .room-card-suite .img-wrap img { width: 100%; height: 100%; object-fit: cover; pointer-events: none; }
    .room-card-suite .img-wrap .no-img { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #555, #333); color: #888; font-size: 2.5rem; }
    .room-card-suite .img-nav { position: absolute; top: 50%; transform: translateY(-50%); width: 36px; height: 36px; border-radius: 50%; background: rgba(0,0,0,0.5); color: #fff; border: none; display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 2; transition: background 0.2s; }
    .room-card-suite .img-nav:hover { background: rgba(183,28,28,0.9); }
    .room-card-suite .img-nav.prev { left: 8px; }
    .room-card-suite .img-nav.next { right: 8px; }
    .room-card-suite .card-body { padding: 1.25rem; flex: 1; display: flex; flex-direction: column; }
    .room-card-suite .card-title { font-family: Georgia, serif; font-size: 1.35rem; font-weight: 700; text-align: center; margin-bottom: 1rem; color: #f0f0f0; }
    .room-card-suite .room-features { list-style: none; padding: 0; margin: 0 0 1rem 0; font-size: 0.9rem; color: #d0d0d0; }
    .room-card-suite .room-features li { display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.4rem; }
    .room-card-suite .room-features li i { color: #c45c26; width: 1.1rem; }
    .room-card-suite .btn-learn { display: block; text-align: center; background: #c45c26; color: #fff; padding: 0.6rem 1rem; border-radius: 8px; font-weight: 600; text-decoration: none; border: none; cursor: pointer; transition: background 0.2s, transform 0.2s; }
    .room-card-suite .btn-learn:hover { background: #a04a1e; color: #fff; transform: translateY(-1px); }
    .empty-state { text-align: center; padding: 3rem; color: #aaa; }
    .room-modal-full { position: fixed; inset: 0; z-index: 9999; background: #1a1a1a; color: #f5f5f5; overflow-y: auto; display: none; }
    .room-modal-full.show { display: block; }
    .room-modal-full .modal-header-bar { display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.5rem; background: #252525; border-bottom: 1px solid #444; position: sticky; top: 0; z-index: 10; }
    .room-modal-full .modal-header-bar h2 { margin: 0; font-size: 1.5rem; font-weight: 600; }
    .room-modal-full .modal-close { background: transparent; border: none; color: #fff; font-size: 1.75rem; cursor: pointer; padding: 0.25rem; line-height: 1; }
    .room-modal-full .modal-close:hover { color: #c45c26; }
    .room-modal-full .modal-body { padding: 1.5rem; max-width: 900px; margin: 0 auto; }
    .room-modal-full .modal-gallery { position: relative; max-height: 400px; background: #333; border-radius: 12px; overflow: hidden; margin-bottom: 1.5rem; }
    .room-modal-full .modal-gallery img { width: 100%; height: 400px; object-fit: contain; background: #222; }
    .room-modal-full .modal-gallery .no-img { width: 100%; height: 400px; display: flex; align-items: center; justify-content: center; background: #333; color: #888; font-size: 3rem; }
    .room-modal-full .modal-gallery .img-nav { position: absolute; top: 50%; transform: translateY(-50%); width: 44px; height: 44px; border-radius: 50%; background: rgba(0,0,0,0.6); color: #fff; border: none; display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 2; font-size: 1.25rem; }
    .room-modal-full .modal-gallery .img-nav:hover { background: #b71c1c; }
    .room-modal-full .modal-gallery .img-nav.prev { left: 12px; }
    .room-modal-full .modal-gallery .img-nav.next { right: 12px; }
    .room-modal-full .modal-desc { margin-bottom: 1.5rem; line-height: 1.6; color: #ddd; }
    .room-modal-full .modal-details { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.5rem; }
    .room-modal-full .modal-details .detail-item { background: #252525; padding: 1rem; border-radius: 8px; }
    .room-modal-full .modal-details .detail-item strong { display: block; color: #c45c26; font-size: 0.85rem; margin-bottom: 0.25rem; }
    .room-modal-full .modal-details .detail-item span { color: #eee; }
    .room-modal-full .btn-book-modal { display: inline-block; background: #c45c26; color: #fff; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600; text-decoration: none; border: none; cursor: pointer; transition: background 0.2s; }
    .room-modal-full .btn-book-modal:hover { background: #a04a1e; color: #fff; }
</style>';
?>
<?php require_once __DIR__ . '/inc/header.php'; ?>

<div class="rooms-dark">
    <div class="container">
        <h1 class="section-title">Function Rooms & Guest Rooms</h1>
        <p class="text-center text-white-50 mb-4">Function rooms for events and meetings.</p>
        <p class="text-center text-white-50 mb-4">Guest rooms for your stay.</p>

        <?php if ($has_any): ?>

        <?php if (!empty($function_rooms)): ?>
        <h2 class="h5 text-white-50 mb-3">Function Rooms</h2>
        <div class="rooms-scroll-wrap">
            <div class="rooms-cards-row">
                <?php foreach ($function_rooms as $room):
                    $img_path = isset($room['image_path']) ? trim($room['image_path']) : '';
                    $img_url = $img_path ? ($assets_base . (strpos($img_path, '/') === 0 ? ltrim($img_path, '/') : $img_path)) : '';
                    $room_id = (int)$room['id'];
                    $all_imgs = getRoomImages($conn, $room_id);
                ?>
                <div class="room-card-suite" data-room-id="<?= $room_id ?>">
                    <div class="img-wrap" data-room-id="<?= $room_id ?>">
                        <?php if ($img_url): ?>
                        <img src="<?= htmlspecialchars($img_url) ?>" alt="<?= clean($room['name']) ?>" loading="lazy" data-room-id="<?= $room_id ?>">
                        <?php else: ?>
                        <div class="no-img" data-room-id="<?= $room_id ?>"><i class="bi bi-building"></i></div>
                        <?php endif; ?>
                        <?php if (count($all_imgs) > 1): ?>
                        <button type="button" class="img-nav prev" aria-label="Previous image">&lsaquo;</button>
                        <button type="button" class="img-nav next" aria-label="Next image">&rsaquo;</button>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <h3 class="card-title"><?= clean($room['name']) ?></h3>
                        <ul class="room-features">
                            <?php if (!empty($room['seats_capacity'])): ?>
                            <li><i class="bi bi-people-fill"></i> <?= (int)$room['seats_capacity'] ?> seats</li>
                            <?php endif; ?>
                            <?php if (!empty($room['area'])): ?>
                            <?php $area_display = trim($room['area']); if (preg_match('/^\d+$/', $area_display)) $area_display .= ' sqm'; ?>
                            <li><i class="bi bi-square-half" title="Area"></i> <?= clean($area_display) ?></li>
                            <?php endif; ?>
                            <?php if (!empty($room['tables_count'])): ?>
                            <li><i class="bi bi-table"></i> <?= (int)$room['tables_count'] ?> tables</li>
                            <?php endif; ?>
                            <?php if (empty($room['seats_capacity']) && empty($room['tables_count']) && !empty($room['adult_capacity'])): ?>
                            <li><i class="bi bi-person-bed"></i> Up to <?= (int)$room['adult_capacity'] ?> guests</li>
                            <?php endif; ?>
                        </ul>
                        <button type="button" class="btn-learn room-learn-more" data-room-id="<?= $room_id ?>">Learn More</button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($guest_rooms)): ?>
        <h2 class="h5 text-white-50 mb-3 mt-5">Guest Rooms</h2>
        <div class="rooms-scroll-wrap">
            <div class="rooms-cards-row">
                <?php foreach ($guest_rooms as $room):
                    $img_path = isset($room['image_path']) ? trim($room['image_path']) : '';
                    $img_url = $img_path ? ($assets_base . (strpos($img_path, '/') === 0 ? ltrim($img_path, '/') : $img_path)) : '';
                    $room_id = (int)$room['id'];
                    $all_imgs = getRoomImages($conn, $room_id);
                ?>
                <div class="room-card-suite" data-room-id="<?= $room_id ?>">
                    <div class="img-wrap" data-room-id="<?= $room_id ?>">
                        <?php if ($img_url): ?>
                        <img src="<?= htmlspecialchars($img_url) ?>" alt="<?= clean($room['name']) ?>" loading="lazy" data-room-id="<?= $room_id ?>">
                        <?php else: ?>
                        <div class="no-img" data-room-id="<?= $room_id ?>"><i class="bi bi-house-door"></i></div>
                        <?php endif; ?>
                        <?php if (count($all_imgs) > 1): ?>
                        <button type="button" class="img-nav prev" aria-label="Previous image">&lsaquo;</button>
                        <button type="button" class="img-nav next" aria-label="Next image">&rsaquo;</button>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <h3 class="card-title"><?= clean($room['name']) ?></h3>
                        <ul class="room-features">
                            <?php if (!empty($room['area'])): ?>
                            <?php $area_display = trim($room['area']); if (preg_match('/^\d+$/', $area_display)) $area_display .= ' sqm'; ?>
                            <li><i class="bi bi-square-half" title="Area"></i> <?= clean($area_display) ?></li>
                            <?php endif; ?>
                            <li><i class="bi bi-person-bed"></i> Up to <?= (int)($room['adult_capacity'] ?? 0) ?> guests</li>
                        </ul>
                        <button type="button" class="btn-learn room-learn-more" data-room-id="<?= $room_id ?>">Learn More</button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php else: ?>
        <div class="empty-state">
            <p>No function or guest rooms are set up yet.</p>
            <p class="small">Run <code>database/alter_rooms_5_function_4_guest.sql</code> or add rooms via Admin.</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Full-screen room detail modal -->
<div id="roomDetailModal" class="room-modal-full" aria-hidden="true">
    <div class="modal-header-bar">
        <h2 id="roomModalTitle">Room</h2>
        <button type="button" class="modal-close" id="roomModalClose" aria-label="Close">&times;</button>
    </div>
    <div class="modal-body">
        <div class="modal-gallery" id="roomModalGallery">
            <img id="roomModalImg" src="" alt="" style="display:none;">
            <div class="no-img" id="roomModalNoImg"><i class="bi bi-image"></i></div>
            <button type="button" class="img-nav prev" id="roomModalPrev" aria-label="Previous" style="display:none;">&lsaquo;</button>
            <button type="button" class="img-nav next" id="roomModalNext" aria-label="Next" style="display:none;">&rsaquo;</button>
        </div>
        <div class="modal-desc" id="roomModalDesc"></div>
        <div class="modal-details" id="roomModalDetails"></div>
        <?php if (!empty($rooms_for_modal)): ?>
        <div id="roomModalExtra" class="modal-desc"></div>
        <?php endif; ?>
        <a href="#" id="roomModalBookBtn" class="btn-book-modal">Book This Room</a>
    </div>
</div>

<script>
(function() {
    var base = <?= json_encode($assets_base) ?>;
    var roomDetails = <?= json_encode(array_values($rooms_for_modal)) ?>;
    var byId = {};
    roomDetails.forEach(function(r) {
        byId[r.id] = r;
        r.images = r.images || [];
        r.images.forEach(function(p, i) {
            r.images[i] = (p.indexOf('/') === 0 || p.indexOf('http') === 0) ? p : (base + p);
        });
    });

    var modal = document.getElementById('roomDetailModal');
    var modalTitle = document.getElementById('roomModalTitle');
    var modalImg = document.getElementById('roomModalImg');
    var modalNoImg = document.getElementById('roomModalNoImg');
    var modalDesc = document.getElementById('roomModalDesc');
    var modalDetails = document.getElementById('roomModalDetails');
    var modalExtra = document.getElementById('roomModalExtra');
    var modalBookBtn = document.getElementById('roomModalBookBtn');
    var modalPrev = document.getElementById('roomModalPrev');
    var modalNext = document.getElementById('roomModalNext');
    var currentRoomId = null;
    var currentImgIndex = 0;

    function openModal(roomId) {
        var r = byId[roomId];
        if (!r) return;
        currentRoomId = roomId;
        currentImgIndex = 0;
        modalTitle.textContent = r.name;
        modalDesc.textContent = r.description || 'No description.';
        modalBookBtn.href = '<?= rtrim(BASE_URL, '/') ?>/room-details.php?id=' + roomId;

        var html = '';
        if (r.area) html += '<div class="detail-item"><strong>Area</strong><span>' + escapeHtml(r.area) + '</span></div>';
        if (r.seats_capacity != null) html += '<div class="detail-item"><strong>Seats</strong><span>' + r.seats_capacity + '</span></div>';
        if (r.tables_count != null) html += '<div class="detail-item"><strong>Tables</strong><span>' + r.tables_count + '</span></div>';
        if (r.adult_capacity) html += '<div class="detail-item"><strong>Guests</strong><span>Up to ' + r.adult_capacity + '</span></div>';
        html += '<div class="detail-item"><strong>Price</strong><span>₱' + parseFloat(r.price).toLocaleString('en-PH', {minimumFractionDigits: 2}) + '</span></div>';
        modalDetails.innerHTML = html || '';

        if (r.details_extra) modalExtra.innerHTML = '<strong>More details</strong><br>' + escapeHtml(r.details_extra);
        else modalExtra.innerHTML = '';

        if (r.images.length) {
            modalImg.src = r.images[0];
            modalImg.style.display = 'block';
            modalNoImg.style.display = 'none';
            modalPrev.style.display = r.images.length > 1 ? 'flex' : 'none';
            modalNext.style.display = r.images.length > 1 ? 'flex' : 'none';
        } else {
            modalImg.style.display = 'none';
            modalNoImg.style.display = 'flex';
            modalPrev.style.display = 'none';
            modalNext.style.display = 'none';
        }
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function escapeHtml(s) {
        if (!s) return '';
        var d = document.createElement('div');
        d.textContent = s;
        return d.innerHTML;
    }

    function closeModal() {
        modal.classList.remove('show');
        document.body.style.overflow = '';
    }

    function setModalImage(index) {
        var r = byId[currentRoomId];
        if (!r || !r.images.length) return;
        currentImgIndex = (index + r.images.length) % r.images.length;
        modalImg.src = r.images[currentImgIndex];
    }

    modalPrev.addEventListener('click', function() { setModalImage(currentImgIndex - 1); });
    modalNext.addEventListener('click', function() { setModalImage(currentImgIndex + 1); });
    document.getElementById('roomModalClose').addEventListener('click', closeModal);
    modal.addEventListener('click', function(e) {
        if (e.target === modal) closeModal();
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeModal();
    });

    document.querySelectorAll('.room-learn-more').forEach(function(btn) {
        btn.addEventListener('click', function() {
            openModal(parseInt(this.getAttribute('data-room-id'), 10));
        });
    });

    // Card image carousel (per-card prev/next)
    document.querySelectorAll('.room-card-suite .img-wrap').forEach(function(wrap) {
        var rid = parseInt(wrap.getAttribute('data-room-id'), 10);
        var r = byId[rid];
        if (!r || r.images.length <= 1) return;
        var img = wrap.querySelector('img');
        var noImg = wrap.querySelector('.no-img');
        var idx = 0;
        function showCardImg(i) {
            idx = (i + r.images.length) % r.images.length;
            if (img) {
                img.src = r.images[idx];
                img.style.display = 'block';
            }
            if (noImg) noImg.style.display = 'none';
        }
        wrap.querySelectorAll('.img-nav.prev').forEach(function(btn) {
            btn.addEventListener('click', function(e) { e.preventDefault(); showCardImg(idx - 1); });
        });
        wrap.querySelectorAll('.img-nav.next').forEach(function(btn) {
            btn.addEventListener('click', function(e) { e.preventDefault(); showCardImg(idx + 1); });
        });
    });

    // Horizontal scroll by drag (mouse) or touch — scrollbar hidden
    document.querySelectorAll('.rooms-scroll-wrap').forEach(function(el) {
        var startX, startScroll, dragging = false, moved = false;
        el.addEventListener('mousedown', function(e) {
            if (e.target.closest('button') || e.target.closest('a')) return;
            dragging = true;
            moved = false;
            startX = e.pageX;
            startScroll = el.scrollLeft;
            el.classList.add('dragging');
        });
        document.addEventListener('mousemove', function(e) {
            if (!dragging) return;
            var dx = e.pageX - startX;
            if (Math.abs(dx) > 5) moved = true;
            el.scrollLeft = startScroll - dx;
        });
        document.addEventListener('mouseup', function() {
            if (dragging) el.classList.remove('dragging');
            dragging = false;
        });
        el.addEventListener('wheel', function(e) {
            if (el.scrollWidth <= el.clientWidth) return;
            e.preventDefault();
            el.scrollLeft += e.deltaY;
        }, { passive: false });
        // Touch scroll (mobile)
        el.addEventListener('touchstart', function(e) {
            if (e.touches.length !== 1) return;
            startX = e.touches[0].pageX;
            startScroll = el.scrollLeft;
        }, { passive: true });
        el.addEventListener('touchmove', function(e) {
            if (e.touches.length !== 1) return;
            var dx = e.touches[0].pageX - startX;
            el.scrollLeft = startScroll - dx;
        }, { passive: true });
    });
})();
</script>

<?php require_once __DIR__ . '/inc/footer.php'; ?>
