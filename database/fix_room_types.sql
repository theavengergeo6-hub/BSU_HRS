-- Fix Room Types - Remove duplicates and ensure proper entries
-- Run this in phpMyAdmin to fix the duplicate Function Room issue

USE hostelweb;

-- First, let's see what we currently have (you can run this to check)
SELECT id, name, description FROM types_room ORDER BY id;

-- Remove only duplicate entries, keeping the first one (safer approach)
DELETE t1 FROM types_room t1
INNER JOIN types_room t2 
WHERE t1.id > t2.id 
AND t1.name IN ('Function Room', 'Guest Room')
AND t2.name IN ('Function Room', 'Guest Room');

-- Add Guest Room if it doesn't exist
INSERT INTO types_room (name, description)
SELECT 'Guest Room', 'Our Guest Room is built for comfort and relaxation. It is ideal for individuals, couples, or small families who want a private and cozy space.'
WHERE NOT EXISTS (SELECT 1 FROM types_room WHERE name = 'Guest Room' LIMIT 1);

-- Verify the results (you can run this to check)
SELECT id, name, description FROM types_room WHERE name IN ('Function Room', 'Guest Room') ORDER BY FIELD(name, 'Function Room', 'Guest Room');

-- Optional: If you want to clean up any orphaned rooms and images
-- Uncomment these lines if needed
-- DELETE ri FROM room_images ri 
-- LEFT JOIN rooms r ON ri.room_id = r.id 
-- WHERE r.id IS NULL;

-- DELETE r FROM rooms r 
-- LEFT JOIN types_room t ON r.type_id = t.id 
-- WHERE t.id IS NULL;
