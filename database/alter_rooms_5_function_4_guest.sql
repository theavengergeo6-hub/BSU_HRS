-- Add room detail columns and ensure 5 Function Rooms + 4 Guest Rooms
-- Database: use the one your app uses (bsu_hrs or bsu_hrs_schema). No USE statement so it runs on current connection.

-- 1) Add columns for admin-editable details (run once; ignore "Duplicate column" if re-running)
ALTER TABLE rooms ADD COLUMN seats_capacity INT DEFAULT NULL COMMENT 'Max seats (function rooms)';
ALTER TABLE rooms ADD COLUMN tables_count INT DEFAULT NULL COMMENT 'Number of tables (function rooms)';
ALTER TABLE rooms ADD COLUMN details_extra TEXT DEFAULT NULL COMMENT 'Additional details';

-- If you get "Duplicate column name", the columns already exist; skip to step 2.

-- 2) Optional: prevent duplicate room names per type (uncomment if you want idempotent inserts)
-- ALTER TABLE rooms ADD UNIQUE KEY unique_room_name_type (name, type_id);

-- 3) Insert 5 Function Rooms (type_id = 1) and 4 Guest Rooms (type_id = 2). Run once.
-- If you already have rooms, adjust or skip. Names can be changed later by admin.
INSERT INTO rooms (name, type_id, price, area, adult_capacity, children_capacity, description, status, seats_capacity, tables_count) VALUES
('Function Room 1', 1, 1500.00, '50 sqm', 30, 0, 'Spacious function room for meetings and events.', 'available', 30, 5),
('Function Room 2', 1, 1800.00, '60 sqm', 40, 0, 'Ideal for seminars and workshops.', 'available', 40, 8),
('Function Room 3', 1, 2000.00, '70 sqm', 50, 0, 'Largest function room with AV equipment.', 'available', 50, 10),
('Function Room 4', 1, 1200.00, '40 sqm', 20, 0, 'Small function room for intimate events.', 'available', 20, 4),
('Function Room 5', 1, 1600.00, '55 sqm', 35, 0, 'Versatile space for training and events.', 'available', 35, 6),
('Guest Room 1', 2, 800.00, '25 sqm', 2, 1, 'Comfortable guest room with queen bed.', 'available', NULL, NULL),
('Guest Room 2', 2, 850.00, '28 sqm', 2, 1, 'Guest room with city view.', 'available', NULL, NULL),
('Guest Room 3', 2, 900.00, '30 sqm', 3, 1, 'Spacious guest room for small families.', 'available', NULL, NULL),
('Guest Room 4', 2, 750.00, '22 sqm', 2, 0, 'Cozy room for couples or solo travelers.', 'available', NULL, NULL);

-- If your table already has rows and you get duplicate key errors, delete the INSERT block above and add rooms manually via admin.
