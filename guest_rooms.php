<?php
$pageTitle = 'Guest Rooms';
require_once __DIR__ . '/inc/header.php';

// Get all guest rooms (venues that are guest rooms)
// Assuming guest rooms have "Guest" in their name or you have a type column
$guest_rooms = $conn->query("
    SELECT v.*, 
           (SELECT COUNT(*) FROM facility_reservations r 
            WHERE r.venue_id = v.id 
            AND r.status = 'approved' 
            AND r.start_datetime >= NOW()) as upcoming_bookings
    FROM venues v
    WHERE v.is_active = 1 
    AND v.name LIKE '%Guest%'  -- Adjust this condition based on your naming convention
    ORDER BY v.name
");

// Get all images for each room
$images_by_room = [];
$images_query = "SELECT * FROM room_images ORDER BY room_id, is_primary DESC, id DESC";
$images_result = $conn->query($images_query);
if ($images_result && $images_result->num_rows > 0) {
    while ($img = $images_result->fetch_assoc()) {
        $images_by_room[$img['room_id']][] = $img;
    }
}

// Get upcoming approved reservations for availability checking
$today = date('Y-m-d');
$bookings_query = $conn->query("
    SELECT venue_id, start_datetime, end_datetime 
    FROM facility_reservations 
    WHERE status = 'approved' 
    AND start_datetime >= '$today'
    ORDER BY start_datetime ASC
");

$bookings_by_venue = [];
while ($booking = $bookings_query->fetch_assoc()) {
    $bookings_by_venue[$booking['venue_id']][] = $booking;
}
?>

<style>
:root {
    --bsu-red: #b71c1c;
    --bsu-red-dark: #8b0000;
    --bsu-gold: #c4a747;
}

.guest-rooms-page {
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    min-height: 100vh;
    padding: 2rem 0;
}

/* Hero Section */
.rooms-hero {
    background: linear-gradient(135deg, var(--bsu-red), var(--bsu-red-dark));
    border-radius: 30px;
    padding: 4rem 2rem;
    margin-bottom: 3rem;
    text-align: center;
    color: white;
    position: relative;
    overflow: hidden;
}

.rooms-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" opacity="0.1"><path d="M0 0 L100 100 M100 0 L0 100" stroke="white" stroke-width="2"/></svg>');
    opacity: 0.1;
}

.hero-content {
    position: relative;
    z-index: 2;
}

.rooms-hero h1 {
    font-size: 3.5rem;
    font-weight: 800;
    margin-bottom: 1rem;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
}

.rooms-hero p {
    font-size: 1.3rem;
    max-width: 700px;
    margin: 0 auto;
    opacity: 0.95;
}

/* Filter Bar */
.filter-bar {
    background: white;
    border-radius: 50px;
    padding: 0.5rem;
    margin-bottom: 2.5rem;
    box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    justify-content: center;
}

.filter-btn {
    padding: 0.8rem 1.8rem;
    border: none;
    border-radius: 50px;
    background: transparent;
    color: #666;
    font-weight: 600;
    font-size: 0.95rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.filter-btn:hover {
    color: var(--bsu-red);
    background: #fdeae8;
}

.filter-btn.active {
    background: var(--bsu-red);
    color: white;
    box-shadow: 0 5px 15px rgba(183,28,28,0.3);
}

/* Room Grid */
.rooms-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 2rem;
    margin-top: 2rem;
}

.room-card {
    background: white;
    border-radius: 24px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    animation: fadeInUp 0.6s ease;
    animation-fill-mode: both;
}

.room-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(183,28,28,0.15);
}

/* Room Image Gallery */
.room-image-container {
    position: relative;
    height: 250px;
    overflow: hidden;
    background: #f0f0f0;
}

.room-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.room-card:hover .room-image {
    transform: scale(1.1);
}

.no-image-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    color: #adb5bd;
    font-size: 3rem;
}

.image-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(to top, rgba(0,0,0,0.5), transparent);
}

.image-count {
    position: absolute;
    bottom: 1rem;
    right: 1rem;
    background: rgba(0,0,0,0.7);
    color: white;
    padding: 0.3rem 0.8rem;
    border-radius: 50px;
    font-size: 0.75rem;
    backdrop-filter: blur(5px);
    z-index: 2;
}

/* Room Details */
.room-details {
    padding: 1.5rem;
}

.room-name {
    font-size: 1.4rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 0.5rem;
}

.room-location {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #666;
    font-size: 0.9rem;
    margin-bottom: 1rem;
}

.room-location i {
    color: var(--bsu-red);
}

/* Room Features */
.room-features {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 0.8rem;
    margin: 1rem 0;
    padding: 1rem 0;
    border-top: 1px solid #f0f0f0;
    border-bottom: 1px solid #f0f0f0;
}

.feature-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #495057;
    font-size: 0.9rem;
}

.feature-item i {
    color: var(--bsu-red);
    font-size: 1rem;
}

/* Availability Badge */
.availability-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.3rem 1rem;
    border-radius: 50px;
    font-size: 0.8rem;
    font-weight: 600;
    margin-bottom: 1rem;
}

.availability-badge.available {
    background: #d4edda;
    color: #155724;
}

.availability-badge.limited {
    background: #fff3cd;
    color: #856404;
}

.availability-badge.booked {
    background: #f8d7da;
    color: #721c24;
}

/* Price & Action */
.room-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 1rem;
}

.room-price {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--bsu-red);
}

.room-price small {
    font-size: 0.8rem;
    font-weight: normal;
    color: #999;
}

.btn-book {
    background: var(--bsu-red);
    color: white;
    border: none;
    padding: 0.6rem 1.5rem;
    border-radius: 50px;
    font-weight: 600;
    font-size: 0.9rem;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
}

.btn-book:hover {
    background: var(--bsu-red-dark);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(183,28,28,0.3);
    color: white;
}

/* Quick Stats */
.stats-section {
    background: white;
    border-radius: 30px;
    padding: 3rem 2rem;
    margin-top: 4rem;
    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 2rem;
    text-align: center;
}

.stat-item {
    padding: 1.5rem;
    border-radius: 20px;
    background: #f8f9fa;
    transition: all 0.3s ease;
}

.stat-item:hover {
    transform: translateY(-5px);
    background: white;
    box-shadow: 0 10px 25px rgba(183,28,28,0.1);
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 800;
    color: var(--bsu-red);
    line-height: 1.2;
    margin-bottom: 0.3rem;
}

.stat-label {
    color: #666;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.room-card:nth-child(1) { animation-delay: 0.1s; }
.room-card:nth-child(2) { animation-delay: 0.2s; }
.room-card:nth-child(3) { animation-delay: 0.3s; }
.room-card:nth-child(4) { animation-delay: 0.4s; }
.room-card:nth-child(5) { animation-delay: 0.5s; }
.room-card:nth-child(6) { animation-delay: 0.6s; }

/* Responsive */
@media (max-width: 992px) {
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .rooms-hero h1 {
        font-size: 2.5rem;
    }
}

@media (max-width: 768px) {
    .rooms-hero {
        padding: 3rem 1rem;
    }
    
    .rooms-hero h1 {
        font-size: 2rem;
    }
    
    .rooms-hero p {
        font-size: 1rem;
    }
    
    .filter-bar {
        border-radius: 20px;
    }
    
    .rooms-grid {
        grid-template-columns: 1fr;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<main class="guest-rooms-page">
    <div class="container">
        <!-- Hero Section -->
        <div class="rooms-hero">
            <div class="hero-content">
                <h1>Experience Comfort at BSU Hostel</h1>
                <p>Discover our premium guest rooms designed for your utmost comfort and relaxation</p>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="filter-bar">
            <button class="filter-btn active" onclick="filterRooms('all')">All Rooms</button>
            <button class="filter-btn" onclick="filterRooms('available')">Available Now</button>
            <button class="filter-btn" onclick="filterRooms('popular')">Most Popular</button>
        </div>

        <!-- Rooms Grid -->
        <div class="rooms-grid" id="roomsGrid">
            <?php if ($guest_rooms && $guest_rooms->num_rows > 0): ?>
                <?php while ($room = $guest_rooms->fetch_assoc()): 
                    $room_images = $images_by_room[$room['id']] ?? [];
                    $primary_image = null;
                    foreach ($room_images as $img) {
                        if ($img['is_primary'] == 1) {
                            $primary_image = $img;
                            break;
                        }
                    }
                    if (!$primary_image && !empty($room_images)) {
                        $primary_image = $room_images[0];
                    }
                    
                    // Check availability
                    $has_upcoming = isset($bookings_by_venue[$room['id']]) && !empty($bookings_by_venue[$room['id']]);
                    $availability_class = $has_upcoming ? 'limited' : 'available';
                    $availability_text = $has_upcoming ? 'Limited Availability' : 'Available Now';
                ?>
                <div class="room-card" data-availability="<?= $has_upcoming ? 'limited' : 'available' ?>">
                    <div class="room-image-container">
                        <?php if ($primary_image): ?>
                            <img src="../assets/images/rooms/<?= htmlspecialchars($primary_image['image_path']) ?>" 
                                 alt="<?= htmlspecialchars($room['name']) ?>" 
                                 class="room-image"
                                 onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'no-image-placeholder\'><i class=\'bi bi-image\'></i></div>'">
                        <?php else: ?>
                            <div class="no-image-placeholder">
                                <i class="bi bi-image"></i>
                            </div>
                        <?php endif; ?>
                        <div class="image-overlay"></div>
                        <?php if (count($room_images) > 0): ?>
                            <span class="image-count">
                                <i class="bi bi-images"></i> <?= count($room_images) ?> photo<?= count($room_images) > 1 ? 's' : '' ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="room-details">
                        <h3 class="room-name"><?= htmlspecialchars($room['name']) ?></h3>
                        
                        <div class="room-location">
                            <i class="bi bi-geo-alt-fill"></i>
                            <span><?= htmlspecialchars($room['floor'] ?? 'Ground Floor') ?></span>
                        </div>
                        
                        <div class="availability-badge <?= $availability_class ?>">
                            <i class="bi bi-<?= $has_upcoming ? 'exclamation-circle' : 'check-circle' ?>"></i>
                            <?= $availability_text ?>
                        </div>
                        
                        <p class="text-muted" style="font-size: 0.9rem; line-height: 1.6; min-height: 60px;">
                            <?= htmlspecialchars($room['description'] ?? 'Experience comfort and convenience in our well-appointed guest room.') ?>
                        </p>
                        
                        <div class="room-features">
                            <div class="feature-item">
                                <i class="bi bi-people-fill"></i>
                                <span>Up to <?= $room['capacity'] ?? '2' ?> guests</span>
                            </div>
                            <div class="feature-item">
                                <i class="bi bi-wifi"></i>
                                <span>Free WiFi</span>
                            </div>
                            <div class="feature-item">
                                <i class="bi bi-snow"></i>
                                <span>Air Conditioning</span>
                            </div>
                            <div class="feature-item">
                                <i class="bi bi-cup-hot-fill"></i>
                                <span>Coffee/Tea</span>
                            </div>
                        </div>
                        
                        <div class="room-footer">
                            <div class="room-price">
                                ₱2,500 <small>/ night</small>
                            </div>
                            <a href="reservation.php?room=<?= $room['id'] ?>" class="btn-book">
                                Book Now <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <i class="bi bi-building" style="font-size: 4rem; color: #ccc;"></i>
                    <h4 class="mt-3 text-muted">No guest rooms available</h4>
                    <p>Please check back later for room availability.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Stats Section -->
        <div class="stats-section">
            <div class="stats-grid">
                <?php
                $total_guest_rooms = $conn->query("SELECT COUNT(*) as count FROM venues WHERE is_active = 1 AND name LIKE '%Guest%'")->fetch_assoc()['count'];
                $available_now = $conn->query("
                    SELECT COUNT(DISTINCT v.id) as count 
                    FROM venues v
                    LEFT JOIN facility_reservations r ON v.id = r.venue_id 
                        AND r.status = 'approved' 
                        AND DATE(r.start_datetime) = CURDATE()
                    WHERE v.is_active = 1 
                    AND v.name LIKE '%Guest%'
                    AND r.id IS NULL
                ")->fetch_assoc()['count'];
                ?>
                <div class="stat-item">
                    <div class="stat-number"><?= $total_guest_rooms ?></div>
                    <div class="stat-label">Guest Rooms</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">24/7</div>
                    <div class="stat-label">Front Desk</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number"><?= $available_now ?></div>
                    <div class="stat-label">Available Today</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">500+</div>
                    <div class="stat-label">Happy Guests</div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
function filterRooms(filter) {
    const buttons = document.querySelectorAll('.filter-btn');
    buttons.forEach(btn => btn.classList.remove('active'));
    
    const activeBtn = Array.from(buttons).find(btn => btn.textContent.toLowerCase().includes(filter));
    if (activeBtn) activeBtn.classList.add('active');
    
    const rooms = document.querySelectorAll('.room-card');
    
    rooms.forEach(room => {
        switch(filter) {
            case 'all':
                room.style.display = 'block';
                break;
            case 'available':
                if (room.dataset.availability === 'available') {
                    room.style.display = 'block';
                } else {
                    room.style.display = 'none';
                }
                break;
            case 'popular':
                // You can implement popularity logic here
                room.style.display = 'block';
                break;
        }
    });
}

// Animate stats on scroll
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
        }
    });
});

document.querySelectorAll('.stat-item').forEach(item => {
    item.style.opacity = '0';
    item.style.transform = 'translateY(20px)';
    item.style.transition = 'all 0.6s ease';
    observer.observe(item);
});
</script>

<?php require_once __DIR__ . '/inc/footer.php'; ?>