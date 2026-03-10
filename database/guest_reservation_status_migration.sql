-- Guest Reservation Status Migration
-- Adds pencil_booked status; only approved/checked_in block room availability.

-- 1. Add pencil_booked to status enum
ALTER TABLE guest_room_reservations
MODIFY COLUMN status ENUM(
  'pending',
  'pencil_booked',
  'confirmed',
  'checked_in',
  'checked_out',
  'cancelled',
  'no_show'
) NOT NULL DEFAULT 'pending';

-- 2. Drop the INSERT trigger (it blocks room on every insert; we only block when approved)
DROP TRIGGER IF EXISTS trg_guest_reservation_after_insert;
