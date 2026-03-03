-- Migration: event_types, banquet, facility_reservations updates
-- Run on bsu_hrs_schema

-- Event types (5 options)
CREATE TABLE IF NOT EXISTS `event_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `sort_order` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO `event_types` (`name`, `sort_order`) VALUES
('Meeting and Conference', 1),
('Seminar and Lecture', 2),
('Buffet and Celebrations', 3),
('Orientation and Presentation', 4),
('Programs and Special Events', 5);

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

-- For existing facility_reservations: add new columns (run each ALTER separately in phpMyAdmin; ignore duplicate column errors)
-- ALTER TABLE facility_reservations ADD COLUMN miscellaneous_items text DEFAULT NULL;
-- ALTER TABLE facility_reservations ADD COLUMN banquet_style_id int(11) DEFAULT NULL;
-- ALTER TABLE facility_reservations ADD COLUMN additional_instruction text DEFAULT NULL;
-- ALTER TABLE facility_reservations ADD COLUMN venue_setup_id int(11) DEFAULT NULL;
-- If your table has venue_id, start_datetime, end_datetime instead of facilities_schedules_json, add:
-- ALTER TABLE facility_reservations ADD COLUMN facilities_schedules_json text DEFAULT NULL;
