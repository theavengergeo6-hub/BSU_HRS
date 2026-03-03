-- Add Function Room & Guest Room types and sample rooms with images (for booking)
-- Run once. Ensures homepage and rooms.php show images from room_images.

USE hostelweb;

-- Add types if not present
INSERT INTO types_room (name, description)
SELECT * FROM (SELECT 'Function Room' AS name, 'Versatile space for meetings, seminars, parties and events. Modern design, AV equipment, fast internet.' AS description) AS t
WHERE NOT EXISTS (SELECT 1 FROM types_room WHERE name = 'Function Room' LIMIT 1);

INSERT INTO types_room (name, description)
SELECT * FROM (SELECT 'Guest Room' AS name, 'Comfortable accommodation for individuals, couples or small families. Private and cozy.' AS description) AS t
WHERE NOT EXISTS (SELECT 1 FROM types_room WHERE name = 'Guest Room' LIMIT 1);

-- Add one room per type if none exist
INSERT INTO rooms (name, type_id, price, area, adult_capacity, children_capacity, description, status)
SELECT 'Function Room 1', t.id, 1500.00, '50', 30, 0, 'Main function room for events and seminars.', 'available'
FROM types_room t WHERE t.name = 'Function Room' LIMIT 1
AND NOT EXISTS (SELECT 1 FROM rooms r WHERE r.type_id = t.id LIMIT 1);

INSERT INTO rooms (name, type_id, price, area, adult_capacity, children_capacity, description, status)
SELECT 'Guest Room 1', t.id, 800.00, '25', 2, 1, 'Comfortable guest room with modern amenities.', 'available'
FROM types_room t WHERE t.name = 'Guest Room' LIMIT 1
AND NOT EXISTS (SELECT 1 FROM rooms r WHERE r.type_id = t.id LIMIT 1);

-- Link images from room_images (path relative to assets/images/)
INSERT INTO room_images (room_id, image_path, is_primary)
SELECT r.id, 'rooms/IMG_19689.jfif', 1 FROM rooms r
INNER JOIN types_room t ON r.type_id = t.id WHERE t.name = 'Function Room'
AND NOT EXISTS (SELECT 1 FROM room_images ri WHERE ri.room_id = r.id LIMIT 1)
LIMIT 1;

INSERT INTO room_images (room_id, image_path, is_primary)
SELECT r.id, 'rooms/IMG_85146.png', 1 FROM rooms r
INNER JOIN types_room t ON r.type_id = t.id WHERE t.name = 'Guest Room'
AND NOT EXISTS (SELECT 1 FROM room_images ri WHERE ri.room_id = r.id LIMIT 1)
LIMIT 1;
