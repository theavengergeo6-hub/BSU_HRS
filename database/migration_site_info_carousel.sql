-- Migration: School/site info for footer, carousel slides, editable images
-- Run once on existing hostelweb database (after hostelweb.sql)

USE hostelweb;

-- contact_details: add columns for footer/school (ignore duplicate column errors if re-run)
ALTER TABLE contact_details ADD COLUMN facebook_url VARCHAR(255) DEFAULT NULL;
ALTER TABLE contact_details ADD COLUMN phone2 VARCHAR(50) DEFAULT NULL;
ALTER TABLE contact_details ADD COLUMN email2 VARCHAR(150) DEFAULT NULL;
ALTER TABLE contact_details ADD COLUMN iframe TEXT DEFAULT NULL;

-- settings: tagline and title for footer
ALTER TABLE settings ADD COLUMN site_tagline TEXT DEFAULT NULL;
ALTER TABLE settings ADD COLUMN site_title VARCHAR(200) DEFAULT NULL;

-- types_room: image path for homepage room cards
ALTER TABLE types_room ADD COLUMN image VARCHAR(255) DEFAULT NULL;

-- Carousel slides (hero): admin-editable
CREATE TABLE IF NOT EXISTS carousel_slides (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    subtitle VARCHAR(500) DEFAULT NULL,
    button_text VARCHAR(100) DEFAULT 'View Rooms',
    button_url VARCHAR(255) DEFAULT 'rooms.php',
    image_path VARCHAR(255) NOT NULL,
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Default carousel slides (run once; skip if you already have slides)
INSERT IGNORE INTO carousel_slides (id, title, subtitle, button_text, button_url, image_path, sort_order) VALUES
(1, 'Welcome to BSU Hostel', 'The perfect venue for your events. Spacious function rooms and comfortable guest rooms for meetings, celebrations, and group stays. Reserve your space today.', 'View Rooms', 'rooms.php', 'hostel/hostel2.png', 1),
(2, 'Book Your Function or Guest Room', 'Check availability and reserve your stay in minutes. Ideal for seminars, events, and overnight stays.', 'Check Availability', 'rooms.php', 'rooms/IMG_19689.jfif', 2),
(3, 'Your Comfort, Our Priority', 'Modern amenities and a welcoming environment for every guest.', 'See Facilities', 'facilities.php', 'rooms/IMG_85146.png', 3),
(4, 'Stay With Us', 'Ideal for students, groups, and travelers visiting BSU.', 'Get in Touch', 'contact.php', 'hostel/hostel2.png', 4);

UPDATE contact_details SET
  address = COALESCE(address, 'BatStateU ARASOF Nasugbu, Batangas'),
  phone = COALESCE(phone, '+63 43 723 1234'),
  email = COALESCE(email, 'hostel@batstateu.edu.ph'),
  facebook_url = COALESCE(facebook_url, 'https://www.facebook.com/batstateu')
WHERE id = 1 LIMIT 1;

UPDATE settings SET
  site_title = COALESCE(site_title, 'BSU Hostel'),
  site_tagline = COALESCE(site_tagline, 'The BSU Hostel Reservation System simplifies booking for function rooms and guest rooms.')
WHERE id = 1 LIMIT 1;
