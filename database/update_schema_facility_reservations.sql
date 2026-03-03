-- Update BSU HRS Schema - Add facility_reservations table for banquet functionality
-- Run this after creating the main schema

USE bsu_hrs_schema;

-- Create banquet table if it doesn't exist
CREATE TABLE IF NOT EXISTS banquet (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    description TEXT DEFAULT NULL,
    image VARCHAR(255) DEFAULT NULL,
    price DECIMAL(10,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create venues table if it doesn't exist
CREATE TABLE IF NOT EXISTS venues (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    capacity INT DEFAULT 0,
    location VARCHAR(255) DEFAULT NULL,
    description TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create facility_reservations table for banquet and venue bookings
CREATE TABLE IF NOT EXISTS facility_reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    venue_id INT DEFAULT NULL,
    banquet_style_id INT DEFAULT NULL,
    event_name VARCHAR(200) NOT NULL,
    start_datetime DATETIME NOT NULL,
    end_datetime DATETIME NOT NULL,
    participants INT DEFAULT 1,
    total_price DECIMAL(10,2) NOT NULL DEFAULT 0,
    status ENUM('pending', 'approved', 'denied', 'cancelled', 'completed') DEFAULT 'pending',
    special_requests TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES user_reg(id),
    FOREIGN KEY (venue_id) REFERENCES venues(id),
    FOREIGN KEY (banquet_style_id) REFERENCES banquet(id)
);

-- Insert sample banquet styles
INSERT IGNORE INTO banquet (name, description, image) VALUES 
('Classic Setup', 'Traditional banquet setup with round tables and standard decorations', 'classic_setup.jpg'),
('Modern Setup', 'Contemporary style with sleek rectangular tables and modern lighting', 'modern_setup.jpg'),
('Garden Setup', 'Outdoor or garden-style setup with natural ambiance', 'garden_setup.jpg'),
('Formal Setup', 'Elegant formal arrangement with premium table settings', 'formal_setup.jpg'),
('Casual Setup', 'Relaxed informal setup suitable for casual gatherings', 'casual_setup.jpg');

-- Insert sample venues
INSERT IGNORE INTO venues (name, capacity, location, description) VALUES 
('Main Hall', 200, 'Ground Floor', 'Main function hall with stage and audio system'),
('Conference Room A', 50, 'Second Floor', 'Conference room with presentation equipment'),
('Conference Room B', 30, 'Second Floor', 'Smaller conference room for intimate meetings'),
('Garden Pavilion', 100, 'Outdoor Area', 'Open-air pavilion for outdoor events');

-- Add indexes for better performance
CREATE INDEX IF NOT EXISTS idx_facility_reservations_venue ON facility_reservations(venue_id, status, start_datetime, end_datetime);
CREATE INDEX IF NOT EXISTS idx_facility_reservations_user ON facility_reservations(user_id);
CREATE INDEX IF NOT EXISTS idx_facility_reservations_banquet ON facility_reservations(banquet_style_id);
