-- BSU HRS Data Migration Script
-- Migrates all data from hostelweb to bsu_hrs_schema
-- Run this after creating the new database structure

-- First, create the new database structure (run bsu_hrs_schema.sql first)
-- Then run this migration script

USE bsu_hrs_schema;

-- Migrate admin credentials
INSERT IGNORE INTO admin_cred (id, admin_name, admin_pass, profile, user_email, created_at)
SELECT id, admin_name, admin_pass, profile, user_email, created_at 
FROM hostelweb.admin_cred;

-- Migrate users
INSERT IGNORE INTO user_reg (id, name, email, phone, address, password, profile_pic, status, created_at)
SELECT id, name, email, phone, address, password, profile_pic, status, created_at 
FROM hostelweb.user_reg;

-- Migrate room types
INSERT IGNORE INTO types_room (id, name, description)
SELECT id, name, description 
FROM hostelweb.types_room;

-- Migrate rooms
INSERT IGNORE INTO rooms (id, name, type_id, price, area, adult_capacity, children_capacity, description, status, created_at)
SELECT id, name, type_id, price, area, adult_capacity, children_capacity, description, status, created_at 
FROM hostelweb.rooms;

-- Migrate room images
INSERT IGNORE INTO room_images (id, room_id, image_path, is_primary)
SELECT id, room_id, image_path, is_primary 
FROM hostelweb.room_images;

-- Migrate room reservations
INSERT IGNORE INTO room_reservation (id, booking_no, user_id, room_id, check_in, check_out, adults, children, total_price, status, special_requests, created_at)
SELECT id, booking_no, user_id, room_id, check_in, check_out, adults, children, total_price, status, special_requests, created_at 
FROM hostelweb.room_reservation;

-- Migrate function hall reservations
INSERT IGNORE INTO reservations (id, booking_no, user_id, event_name, event_date, event_time, duration_hours, total_price, status, special_requests, created_at)
SELECT id, booking_no, user_id, event_name, event_date, event_time, duration_hours, total_price, status, special_requests, created_at 
FROM hostelweb.reservations;

-- Migrate facilities
INSERT IGNORE INTO facilities (id, name, description, icon, image)
SELECT id, name, description, icon, image 
FROM hostelweb.facilities;

-- Migrate features
INSERT IGNORE INTO features (id, name, icon)
SELECT id, name, icon 
FROM hostelweb.features;

-- Migrate room-feature relationships
INSERT IGNORE INTO room_features (room_id, feature_id)
SELECT room_id, feature_id 
FROM hostelweb.room_features;

-- Migrate contact details
INSERT IGNORE INTO contact_details (id, address, phone, phone2, email, email2, facebook_url, iframe)
SELECT id, address, phone, phone2, email, email2, facebook_url, iframe 
FROM hostelweb.contact_details;

-- Migrate settings
INSERT IGNORE INTO settings (id, site_name, site_title, site_tagline, site_logo, timezone, currency)
SELECT id, site_name, site_title, site_tagline, site_logo, timezone, currency 
FROM hostelweb.settings;

-- Migrate carousel slides
INSERT IGNORE INTO carousel_slides (id, title, subtitle, button_text, button_url, image_path, sort_order, is_active, created_at)
SELECT id, title, subtitle, button_text, button_url, image_path, sort_order, is_active, created_at 
FROM hostelweb.carousel_slides;

-- Migrate notifications
INSERT IGNORE INTO notifications (id, user_id, title, message, is_read, created_at)
SELECT id, user_id, title, message, is_read, created_at 
FROM hostelweb.notifications;

-- Migrate admin notifications
INSERT IGNORE INTO admin_notifications (id, title, message, is_read, created_at)
SELECT id, title, message, is_read, created_at 
FROM hostelweb.admin_notifications;

-- Migrate chat messages
INSERT IGNORE INTO chat_messages (id, user_id, admin_id, message, sender, is_read, created_at)
SELECT id, user_id, admin_id, message, sender, is_read, created_at 
FROM hostelweb.chat_messages;

-- Migrate testimonials
INSERT IGNORE INTO testimonials (id, user_id, name, text, rating, is_approved, created_at)
SELECT id, user_id, name, text, rating, is_approved, created_at 
FROM hostelweb.testimonials;

-- Migrate banquet/services (if table exists)
INSERT IGNORE INTO banguet (id, name, description, price, image)
SELECT id, name, description, price, image 
FROM hostelweb.banguet;

-- Migrate liabilities (if table exists)
INSERT IGNORE INTO liabilities (id, user_id, title, content, agreed, agreed_at)
SELECT id, user_id, title, content, agreed, agreed_at 
FROM hostelweb.liabilities;

-- Migrate room reviews
INSERT IGNORE INTO room_reviews (id, room_id, user_id, rating, review_text, created_at)
SELECT id, room_id, user_id, rating, review_text, created_at 
FROM hostelweb.room_reviews;

-- Migrate function room reviews
INSERT IGNORE INTO function_room_reviews (id, user_id, rating, review_text, created_at)
SELECT id, user_id, rating, review_text, created_at 
FROM hostelweb.function_room_reviews;

-- Verify migration results
SELECT 'Migration Complete' as Status;

-- Show counts from new database
SELECT 'types_room' as Table, COUNT(*) as Count FROM types_room
UNION ALL
SELECT 'rooms' as Table, COUNT(*) as Count FROM rooms
UNION ALL
SELECT 'room_images' as Table, COUNT(*) as Count FROM room_images
UNION ALL
SELECT 'user_reg' as Table, COUNT(*) as Count FROM user_reg
UNION ALL
SELECT 'carousel_slides' as Table, COUNT(*) as Count FROM carousel_slides
UNION ALL
SELECT 'facilities' as Table, COUNT(*) as Count FROM facilities;
