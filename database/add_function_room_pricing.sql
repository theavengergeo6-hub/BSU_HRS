-- ============================================================
--  BSU HRS — Function Room Pricing Migration
--  Safe to run multiple times (idempotent via IF NOT EXISTS)
-- ============================================================

-- 1. Add half_day_rate column
ALTER TABLE venues
    ADD COLUMN IF NOT EXISTS half_day_rate DECIMAL(10,2) NOT NULL DEFAULT 2000.00
    COMMENT 'External rate for up to 4 hours';

-- 2. Add whole_day_rate column
ALTER TABLE venues
    ADD COLUMN IF NOT EXISTS whole_day_rate DECIMAL(10,2) NOT NULL DEFAULT 3000.00
    COMMENT 'External rate for up to 8 hours';

-- 3. Add extension_rate column
ALTER TABLE venues
    ADD COLUMN IF NOT EXISTS extension_rate DECIMAL(10,2) NOT NULL DEFAULT 400.00
    COMMENT 'External rate per hour beyond the rented period';

-- 4. Add sound_system_fee column
ALTER TABLE venues
    ADD COLUMN IF NOT EXISTS sound_system_fee DECIMAL(10,2) NOT NULL DEFAULT 1500.00
    COMMENT 'Optional fee for basic sound system (2 wireless mics + speakers)';

-- 5. Populate existing function rooms with default rates
UPDATE venues
SET
    half_day_rate   = 2000.00,
    whole_day_rate  = 3000.00,
    extension_rate  = 400.00,
    sound_system_fee = 1500.00
WHERE
    name LIKE '%Function%'
    AND is_active = 1
    AND (half_day_rate = 0 OR half_day_rate IS NULL);

-- 6. (Optional) Add index on name for faster room-type queries
-- Already exists if you have one; skip if duplicate key error
ALTER TABLE venues
    ADD INDEX IF NOT EXISTS idx_venues_name (name);

SELECT
    id,
    name,
    half_day_rate,
    whole_day_rate,
    extension_rate,
    sound_system_fee
FROM venues
WHERE name LIKE '%Function%'
ORDER BY name;
