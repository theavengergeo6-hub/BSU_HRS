<?php
/**
 * Essential Helper Functions
 * BSU Hostel Management System
 */

define('BASE_URL', 'http://localhost/BSU_HRS/');
define('UPLOAD_PATH', __DIR__ . '/../images/uploads/');
define('ROOM_IMAGES_PATH', UPLOAD_PATH . 'rooms/');
define('PROFILE_PATH', UPLOAD_PATH . 'profiles/');

/**
 * Sanitize input to prevent XSS
 */
function clean($data) {
    return htmlspecialchars(trim($data ?? ''), ENT_QUOTES, 'UTF-8');
}

/**
 * Generate booking number: GM RES-YYYYMMDD-XXX
 */
function generateBookingNo() {
    $prefix = 'GM RES-' . date('Ymd') . '-';
    return $prefix . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
}

/**
 * Redirect helper
 */
function redirect($url, $delay = 0) {
    if ($delay > 0) {
        header("Refresh: $delay; url=$url");
    } else {
        header("Location: $url");
    }
    exit;
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Check if admin is logged in
 */
function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']);
}

function display_messages() {
    if (isset($_SESSION['success_message'])) {
        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                ' . htmlspecialchars($_SESSION['success_message']) . '
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
        unset($_SESSION['success_message']);
    }

    if (isset($_SESSION['error_message'])) {
        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                ' . htmlspecialchars($_SESSION['error_message']) . '
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
        unset($_SESSION['error_message']);
    }
}


/**
 * Get current user ID
 */
function getUserId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Format currency
 */
function formatPrice($amount) {
    return '₱' . number_format($amount, 2);
}

/**
 * Validate date range
 */
function isValidDateRange($checkIn, $checkOut) {
    $in = strtotime($checkIn);
    $out = strtotime($checkOut);
    return $in && $out && $out > $in;
}

/**
 * Get carousel slides from DB (admin-editable)
 */
function getCarouselSlides($conn) {
    static $slides = null;
    if ($slides !== null) return $slides;
    try {
        $res = $conn->query("SELECT * FROM carousel_slides WHERE is_active = 1 ORDER BY sort_order ASC");
        $slides = ($res && $res->num_rows > 0) ? $res->fetch_all(MYSQLI_ASSOC) : [];
    } catch (mysqli_sql_exception $e) {
        $slides = [];
    }
    return $slides;
}

/**
 * Get site/contact info for footer
 */
function getSiteInfo($conn) {
    $contact = $settings = null;
    try {
        $r1 = $conn->query("SELECT * FROM contact_details LIMIT 1");
        if ($r1 && $r1->num_rows) $contact = $r1->fetch_assoc();
        $r2 = $conn->query("SELECT * FROM settings LIMIT 1");
        if ($r2 && $r2->num_rows) $settings = $r2->fetch_assoc();
    } catch (mysqli_sql_exception $e) {
        $contact = $settings = null;
    }
    return [
        'contact' => $contact ?: ['address' => '', 'phone' => '', 'email' => '', 'facebook_url' => '', 'phone2' => '', 'email2' => '', 'iframe' => ''],
        'settings' => $settings ?: ['site_name' => 'BSU Hostel', 'site_title' => 'BSU Hostel', 'site_tagline' => '']
    ];
}

/**
 * Get room types for homepage cards (with image path)
 */
function getRoomTypesForHome($conn) {
    try {
        $res = $conn->query("SELECT * FROM types_room ORDER BY id ASC LIMIT 6");
        return ($res && $res->num_rows > 0) ? $res->fetch_all(MYSQLI_ASSOC) : [];
    } catch (mysqli_sql_exception $e) {
        return [];
    }
}

/**
 * Featured booking room types: Function Room & Guest Room only, with image from room_images
 */
function getFeaturedBookingRoomTypes($conn) {
    try {
        // First, get the room types
        $sql = "SELECT DISTINCT t.id AS type_id, t.name AS type_name, t.description AS type_description,
                (SELECT ri.image_path FROM rooms r
                 LEFT JOIN room_images ri ON ri.room_id = r.id
                 WHERE r.type_id = t.id AND r.status = 'available'
                 ORDER BY ri.is_primary DESC, ri.id ASC LIMIT 1) AS image_path
                FROM types_room t
                WHERE t.name IN ('Function Room', 'Guest Room')
                ORDER BY FIELD(t.name, 'Function Room', 'Guest Room')
                LIMIT 2";
        
        $res = $conn->query($sql);
        $results = [];
        
        if ($res && $res->num_rows > 0) {
            while ($row = $res->fetch_assoc()) {
                $results[] = $row;
            }
        }
        
        // Always ensure we have both Function Room and Guest Room
        $found_function = false;
        $found_guest = false;
        
        foreach ($results as $result) {
            if (strpos($result['type_name'], 'Function') !== false) $found_function = true;
            if (strpos($result['type_name'], 'Guest') !== false) $found_guest = true;
        }
        
        // Add Function Room default if not found
        if (!$found_function) {
            $results[] = [
                'type_id' => null,
                'type_name' => 'Function Room',
                'type_description' => 'Our function room is a flexible space for meetings, seminars, parties, and other events. It has a modern design, audio visual equipment, adjustable seating, and fast internet.',
                'image_path' => 'rooms/IMG_19689.jpg'
            ];
        }
        
        // Add Guest Room default if not found
        if (!$found_guest) {
            $results[] = [
                'type_id' => null,
                'type_name' => 'Guest Room',
                'type_description' => 'Our Guest Room is built for comfort and relaxation. It is ideal for individuals, couples, or small families who want a private and cozy space.',
                'image_path' => 'rooms/IMG_85146.png'
            ];
        }
        
        return $results;
        
    } catch (mysqli_sql_exception $e) {
        error_log("Error in getFeaturedBookingRoomTypes: " . $e->getMessage());
        return [
            [
                'type_id' => null,
                'type_name' => 'Function Room',
                'type_description' => 'Our function room is a flexible space for meetings, seminars, parties, and other events. It has a modern design, audio visual equipment, adjustable seating, and fast internet.',
                'image_path' => 'rooms/IMG_19689.jpg'
            ],
            [
                'type_id' => null,
                'type_name' => 'Guest Room',
                'type_description' => 'Our Guest Room is built for comfort and relaxation. It is ideal for individuals, couples, or small families who want a private and cozy space.',
                'image_path' => 'rooms/IMG_85146.png'
            ]
        ];
    }
}

/**
 * Get rooms by type for rooms.php (with primary/first image from room_images).
 * Includes seats_capacity, tables_count, details_extra if columns exist (run alter_rooms_5_function_4_guest.sql).
 */
function getRoomsWithImagesByType($conn) {
    try {
        $cols = 'r.id, r.name, r.type_id, r.price, r.area, r.adult_capacity, r.children_capacity, r.description';
        try {
            $chk = $conn->query("SHOW COLUMNS FROM rooms LIKE 'seats_capacity'");
            if ($chk && $chk->num_rows > 0) {
                $cols .= ', r.seats_capacity, r.tables_count, r.details_extra';
            }
        } catch (Exception $e) { /* ignore */ }
        $sql = "SELECT $cols, t.name AS type_name,
                (SELECT ri.image_path FROM room_images ri WHERE ri.room_id = r.id ORDER BY ri.is_primary DESC, ri.id ASC LIMIT 1) AS image_path
                FROM rooms r
                LEFT JOIN types_room t ON t.id = r.type_id
                WHERE r.status = 'available'
                AND (t.name LIKE '%Function%' OR t.name LIKE '%Guest%' OR t.name IN ('Function Room', 'Guest Room', 'Function Rooms', 'Guest Rooms'))
                ORDER BY t.name, r.name";
        $res = $conn->query($sql);
        return ($res && $res->num_rows > 0) ? $res->fetch_all(MYSQLI_ASSOC) : [];
    } catch (mysqli_sql_exception $e) {
        return [];
    }
}

/**
 * Get all image paths for a room (for modal gallery).
 */
function getRoomImages($conn, $room_id) {
    $room_id = (int) $room_id;
    if ($room_id <= 0) return [];
    try {
        $stmt = $conn->prepare("SELECT image_path FROM room_images WHERE room_id = ? ORDER BY is_primary DESC, id ASC");
        $stmt->bind_param('i', $room_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $out = [];
        while ($row = $res->fetch_assoc()) $out[] = $row['image_path'];
        return $out;
    } catch (Exception $e) {
        return [];
    }
}

/**
 * Get bookings for calendar view (month). Returns events keyed by date Y-m-d.
 * Each event: id, booking_no, room_id, room_name, type_name, check_in, check_out, guest_name.
 */
function getBookingsForCalendar($conn, $year, $month) {
    $year = (int) $year;
    $month = (int) $month;
    $first = sprintf('%04d-%02d-01', $year, $month);
    $last = date('Y-m-t', strtotime($first));
    try {
        $stmt = $conn->prepare("
            SELECT rr.id, rr.booking_no, rr.room_id, rr.check_in, rr.check_out,
                   r.name AS room_name, t.name AS type_name, u.name AS guest_name
            FROM room_reservation rr
            JOIN rooms r ON r.id = rr.room_id
            LEFT JOIN types_room t ON t.id = r.type_id
            LEFT JOIN user_reg u ON u.id = rr.user_id
            WHERE rr.check_in <= ? AND rr.check_out >= ?
            AND rr.status IN ('pending', 'confirmed')
            ORDER BY rr.check_in, r.name
        ");
        $stmt->bind_param('ss', $last, $first);
        $stmt->execute();
        $res = $stmt->get_result();
        $events_by_date = [];
        while ($row = $res->fetch_assoc()) {
            $start = strtotime($row['check_in']);
            $end = strtotime($row['check_out']);
            for ($d = $start; $d <= $end; $d = strtotime('+1 day', $d)) {
                $dateKey = date('Y-m-d', $d);
                if ($dateKey < $first || $dateKey > $last) continue;
                if (!isset($events_by_date[$dateKey])) $events_by_date[$dateKey] = [];
                $events_by_date[$dateKey][] = $row;
            }
        }
        return $events_by_date;
    } catch (Exception $e) {
        return [];
    }
}

/**
 * Get list of rooms (id, name, type_name) for calendar filter
 */
function getRoomsForCalendarFilter($conn) {
    try {
        $res = $conn->query("
            SELECT r.id, r.name, t.name AS type_name
            FROM rooms r
            LEFT JOIN types_room t ON t.id = r.type_id
            WHERE t.name LIKE '%Function%' OR t.name LIKE '%Guest%'
            ORDER BY t.name, r.name
        ");
        return ($res && $res->num_rows > 0) ? $res->fetch_all(MYSQLI_ASSOC) : [];
    } catch (Exception $e) {
        return [];
    }
}

/**
 * Get event types for reservation dropdown (5 options)
 */
function getEventTypes($conn) {
    try {
        $res = $conn->query("SELECT id, name FROM event_types ORDER BY sort_order, name");
        if ($res && $res->num_rows > 0) {
            return $res->fetch_all(MYSQLI_ASSOC);
        }
    } catch (Exception $e) { /* */ }
    return [
        ['id' => 1, 'name' => 'Meeting and Conference'],
        ['id' => 2, 'name' => 'Seminar and Lecture'],
        ['id' => 3, 'name' => 'Buffet and Celebrations'],
        ['id' => 4, 'name' => 'Orientation and Presentation'],
        ['id' => 5, 'name' => 'Programs and Special Events'],
    ];
}

/**
 * Get banquet styles for modal selection
 */
function getBanquetStyles($conn) {
    try {
        $res = $conn->query("SELECT id, image, name, description FROM banquet ORDER BY sort_order, name");
        return ($res && $res->num_rows > 0) ? $res->fetch_all(MYSQLI_ASSOC) : [];
    } catch (Exception $e) { /* */ }
    return [];
}

/**
 * Get venue setups for dropdown
 */
function getVenueSetups($conn) {
    try {
        $res = $conn->query("SELECT id, name FROM venue_setups ORDER BY name");
        return ($res && $res->num_rows > 0) ? $res->fetch_all(MYSQLI_ASSOC) : [];
    } catch (Exception $e) { /* */ }
    return [];
}

/**
 * Get terms and conditions for reservation (admin-editable)
 */
function getTermsConditions($conn) {
    try {
        $res = $conn->query("SELECT title, content FROM terms_conditions ORDER BY id DESC LIMIT 1");
        if ($res && $res->num_rows > 0) {
            return $res->fetch_assoc();
        }
    } catch (Exception $e) { /* */ }
    return ['title' => 'Terms and Conditions', 'content' => 'By submitting this form, you agree to comply with BSU Hostel policies. Reservations are subject to approval.'];
}

/**
 * Get FAQ items for index FAQ section (accordion)
 */
function getFaqItems($conn) {
    try {
        $res = $conn->query("SELECT id, question, answer, sort_order FROM faq ORDER BY sort_order ASC, id ASC");
        return ($res && $res->num_rows > 0) ? $res->fetch_all(MYSQLI_ASSOC) : [];
    } catch (mysqli_sql_exception $e) {
        return [];
    }
}

/**
 * Get amenities (facilities) for homepage
 */
function getAmenities($conn, $limit = 8) {
    try {
        $res = $conn->query("SELECT * FROM facilities ORDER BY id ASC LIMIT " . (int)$limit);
        return ($res && $res->num_rows > 0) ? $res->fetch_all(MYSQLI_ASSOC) : [];
    } catch (mysqli_sql_exception $e) {
        return [];
    }
}