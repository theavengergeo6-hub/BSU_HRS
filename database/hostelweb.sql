-- Hostel Management System Database Schema
-- Database: hostelweb

CREATE DATABASE IF NOT EXISTS hostelweb;
USE hostelweb;

-- Admin authentication
CREATE TABLE IF NOT EXISTS admin_cred (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_name VARCHAR(100) NOT NULL UNIQUE,
    admin_pass VARCHAR(255) NOT NULL,
    profile VARCHAR(255) DEFAULT NULL,
    user_email VARCHAR(150) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- User registration
CREATE TABLE IF NOT EXISTS user_reg (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    phone VARCHAR(20) DEFAULT NULL,
    address TEXT DEFAULT NULL,
    password VARCHAR(255) NOT NULL,
    profile_pic VARCHAR(255) DEFAULT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Room types/categories
CREATE TABLE IF NOT EXISTS types_room (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT DEFAULT NULL
);

-- Rooms
CREATE TABLE IF NOT EXISTS rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    type_id INT DEFAULT NULL,
    price DECIMAL(10,2) NOT NULL DEFAULT 0,
    area VARCHAR(50) DEFAULT NULL,
    adult_capacity INT DEFAULT 1,
    children_capacity INT DEFAULT 0,
    description TEXT DEFAULT NULL,
    status ENUM('available', 'occupied', 'maintenance') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (type_id) REFERENCES types_room(id) ON DELETE SET NULL
);

-- Room images
CREATE TABLE IF NOT EXISTS room_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    is_primary TINYINT(1) DEFAULT 0,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE
);

-- Room reservations/bookings
CREATE TABLE IF NOT EXISTS room_reservation (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_no VARCHAR(50) NOT NULL UNIQUE,
    user_id INT NOT NULL,
    room_id INT NOT NULL,
    check_in DATE NOT NULL,
    check_out DATE NOT NULL,
    adults INT DEFAULT 1,
    children INT DEFAULT 0,
    total_price DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
    special_requests TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES user_reg(id),
    FOREIGN KEY (room_id) REFERENCES rooms(id)
);

-- Function hall reservations
CREATE TABLE IF NOT EXISTS reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_no VARCHAR(50) NOT NULL UNIQUE,
    user_id INT NOT NULL,
    event_name VARCHAR(200) NOT NULL,
    event_date DATE NOT NULL,
    event_time TIME NOT NULL,
    duration_hours INT DEFAULT 1,
    total_price DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
    special_requests TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES user_reg(id)
);

-- Room reviews
CREATE TABLE IF NOT EXISTS room_reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_id INT NOT NULL,
    user_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    review_text TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES user_reg(id)
);

-- Function room reviews
CREATE TABLE IF NOT EXISTS function_room_reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    review_text TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES user_reg(id)
);

-- Facilities/amenities
CREATE TABLE IF NOT EXISTS facilities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    description TEXT DEFAULT NULL,
    icon VARCHAR(100) DEFAULT NULL,
    image VARCHAR(255) DEFAULT NULL
);

-- Room features
CREATE TABLE IF NOT EXISTS features (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    icon VARCHAR(100) DEFAULT NULL
);

-- Room-Feature mapping
CREATE TABLE IF NOT EXISTS room_features (
    room_id INT NOT NULL,
    feature_id INT NOT NULL,
    PRIMARY KEY (room_id, feature_id),
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE,
    FOREIGN KEY (feature_id) REFERENCES features(id) ON DELETE CASCADE
);

-- Contact details
CREATE TABLE IF NOT EXISTS contact_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    address TEXT DEFAULT NULL,
    phone VARCHAR(50) DEFAULT NULL,
    email VARCHAR(150) DEFAULT NULL,
    map_embed TEXT DEFAULT NULL
);

-- Settings
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    site_name VARCHAR(150) DEFAULT 'Hostel Management',
    site_logo VARCHAR(255) DEFAULT NULL,
    timezone VARCHAR(50) DEFAULT 'Asia/Manila',
    currency VARCHAR(10) DEFAULT 'PHP'
);

-- Notifications (user-facing)
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES user_reg(id)
);

-- Admin notifications
CREATE TABLE IF NOT EXISTS admin_notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Chat messages
CREATE TABLE IF NOT EXISTS chat_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    admin_id INT DEFAULT NULL,
    message TEXT NOT NULL,
    sender ENUM('user', 'admin') NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES user_reg(id),
    FOREIGN KEY (admin_id) REFERENCES admin_cred(id)
);

-- Liabilities
CREATE TABLE IF NOT EXISTS liabilities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    agreed TINYINT(1) DEFAULT 0,
    agreed_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES user_reg(id)
);

-- Banquet/Event services
CREATE TABLE IF NOT EXISTS banguet (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    description TEXT DEFAULT NULL,
    price DECIMAL(10,2) DEFAULT 0,
    image VARCHAR(255) DEFAULT NULL
);

-- Testimonials (optional)
CREATE TABLE IF NOT EXISTS testimonials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    name VARCHAR(100) NOT NULL,
    text TEXT NOT NULL,
    rating INT DEFAULT 5,
    is_approved TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Default admin created via setup.php (username: admin, password: admin123)

-- Sample room types
INSERT INTO types_room (name, description) VALUES 
('Standard', 'Basic accommodation'),
('Deluxe', 'Enhanced comfort with amenities'),
('Suite', 'Premium room with extra space');

-- Sample rooms
INSERT INTO rooms (name, type_id, price, area, adult_capacity, children_capacity, description, status) VALUES 
('Room 101', 1, 500.00, '20', 2, 1, 'Comfortable standard room', 'available'),
('Room 102', 1, 500.00, '20', 2, 1, 'Comfortable standard room', 'available'),
('Room 201', 2, 800.00, '30', 3, 2, 'Spacious deluxe room', 'available'),
('Room 202', 2, 800.00, '30', 3, 2, 'Spacious deluxe room', 'available'),
('Suite 301', 3, 1200.00, '45', 4, 2, 'Premium suite with living area', 'available');

-- Sample facilities
INSERT INTO facilities (name, description) VALUES 
('WiFi', 'Free high-speed internet access'),
('Parking', 'Secure parking area'),
('Common Room', 'Shared lounge for guests');

-- Insert default settings
INSERT INTO contact_details (address, phone, email) VALUES 
('Sample Address, City', '+63 123 456 7890', 'contact@hostel.local');

INSERT INTO settings (site_name, timezone, currency) VALUES 
('BSU Hostel Management', 'Asia/Manila', 'PHP');
