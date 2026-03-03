-- Facility Reservation tables for BSU Hostel Management System
-- Run on bsu_hrs_schema (or your app database)

-- Office types for dropdown
CREATE TABLE IF NOT EXISTS `office_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `sort_order` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `office_types` (`name`, `sort_order`) VALUES
('College', 1), ('Office', 2), ('Student Organization', 3), ('External', 4);

-- Offices/Colleges/Orgs (linked to office_type_id; for External, user enters name in form)
CREATE TABLE IF NOT EXISTS `offices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `office_type_id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `office_type_id` (`office_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `offices` (`office_type_id`, `name`) VALUES
(1, 'College of Engineering'), (1, 'College of Arts and Sciences'), (1, 'College of Business'),
(2, 'Registrar Office'), (2, 'Student Affairs'), (2, 'Administration'),
(3, 'Student Council'), (3, 'Academic Club'), (3, 'Cultural Organization');

-- Venues (function rooms, etc.)
CREATE TABLE IF NOT EXISTS `venues` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `floor` varchar(50) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `venues` (`name`, `floor`, `is_active`) VALUES
('Function Room 1', '1F', 1), ('Function Room 2', '1F', 1), ('Function Room 3', '2F', 1),
('Guest Room A', '2F', 1), ('Guest Room B', '2F', 1);

-- Event types (5 options)
CREATE TABLE IF NOT EXISTS `event_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `sort_order` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO `event_types` (`name`, `sort_order`) VALUES
('Meeting and Conference', 1), ('Seminar and Lecture', 2), ('Buffet and Celebrations', 3),
('Orientation and Presentation', 4), ('Programs and Special Events', 5);

-- Venue setup types
CREATE TABLE IF NOT EXISTS `venue_setups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `venue_setups` (`name`) VALUES
('Theater Style'), ('Classroom Style'), ('Banquet/Round Tables'), ('U-Shape'), ('Boardroom');

-- Miscellaneous items (checkboxes with quantity)
CREATE TABLE IF NOT EXISTS `miscellaneous_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `has_speaker_spec` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `miscellaneous_items` (`name`, `has_speaker_spec`, `sort_order`) VALUES
('Basic Sound System', 1, 1), ('Projector', 0, 2), ('Whiteboard', 0, 3), ('Tables', 0, 4), ('Chairs', 0, 5);

-- Banquet styles
CREATE TABLE IF NOT EXISTS `banquet` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `image` varchar(255) DEFAULT NULL,
  `name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO `banquet` (`image`, `name`, `description`, `sort_order`) VALUES
('banquet1.jpg', 'Classic Theater', 'Rows of chairs facing the stage', 1),
('banquet2.jpg', 'Banquet Round', 'Round tables for group dining', 2),
('banquet3.jpg', 'U-Shape', 'Tables arranged in U-shape for meetings', 3);

-- Facility reservations (multi-room, multi-schedule)
CREATE TABLE IF NOT EXISTS `facility_reservations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `booking_no` varchar(50) NOT NULL,
  `reservation_type` enum('pencil','full') DEFAULT 'pencil',
  `last_name` varchar(100) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_initial` varchar(5) DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `contact_number` varchar(50) NOT NULL,
  `office_type_id` int(11) DEFAULT NULL,
  `office_id` int(11) DEFAULT NULL,
  `office_external_name` varchar(200) DEFAULT NULL,
  `event_type` varchar(100) DEFAULT NULL,
  `activity_name` varchar(200) NOT NULL,
  `participants` int(11) DEFAULT NULL,
  `facilities_schedules_json` text DEFAULT NULL,
  `venue_setup_id` int(11) DEFAULT NULL,
  `miscellaneous_items` text DEFAULT NULL,
  `banquet_style_id` int(11) DEFAULT NULL,
  `additional_instruction` text DEFAULT NULL,
  `status` enum('pending','confirmed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Terms and conditions (admin-editable)
CREATE TABLE IF NOT EXISTS `terms_conditions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) DEFAULT 'Facility Reservation Terms',
  `content` text NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `terms_conditions` (`title`, `content`) VALUES
('Facility Reservation Terms and Conditions',
'By submitting this form, you agree to comply with BSU Hostel policies. Reservations are subject to approval. Please ensure accurate information. You will receive a confirmation via email once approved. Cancellations must be made at least 24 hours in advance. This event/activity will be officially reserved upon approval.');
