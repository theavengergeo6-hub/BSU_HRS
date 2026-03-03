# BSU HRS Database Migration Guide

## Overview
This guide helps you migrate from the old `hostelweb` database to a new clean `bsu_hrs_schema` database specifically designed for BSU Hostel Reservation System.

## Files Created
1. **bsu_hrs_schema.sql** - Clean database structure
2. **migrate_to_bsu_hrs.sql** - Data migration script
3. **Updated db_config.php** - Points to new database

## Step-by-Step Migration

### Step 1: Create New Database Structure
1. Open phpMyAdmin
2. Click on "Import" tab
3. Select file: `database/bsu_hrs_schema.sql`
4. Click "Go" to execute
5. This creates the clean `bsu_hrs_schema` database with all tables

### Step 2: Migrate Existing Data
1. In phpMyAdmin, select the new `bsu_hrs_schema` database
2. Click on "SQL" tab
3. Copy contents of `database/migrate_to_bsu_hrs.sql`
4. Paste into SQL textarea
5. Click "Go" to execute migration

### Step 3: Verify Migration
After migration, run this query to verify data was transferred:

```sql
SELECT 
    'types_room' as Table, COUNT(*) as Count FROM types_room
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
```

### Step 4: Test Website
1. Open your BSU HRS website
2. Check homepage - should show 1 Function Room + 1 Guest Room card
3. Check rooms.php - should display all rooms with images
4. Test login with admin account (admin/admin123)

## What Gets Migrated

✅ **All User Data** - Accounts, profiles, bookings
✅ **Room Types** - Function Room, Guest Room, and others
✅ **Room Details** - Names, prices, capacities, descriptions
✅ **Room Images** - All image paths preserved
✅ **Bookings** - Both room and function hall reservations
✅ **Facilities** - Amenities with icons and descriptions
✅ **Settings** - Site configuration, contact info
✅ **Carousel Slides** - Homepage images
✅ **Admin Data** - Admin accounts and notifications

## Important Notes

🔒 **INSERT IGNORE** used to prevent duplicate data
🔒 **No data loss** - Original `hostelweb` database remains intact
🔒 **Images unchanged** - All image paths remain in `/assets/images/`
🔒 **Rollback possible** - Keep `hostelweb` until you verify new database works

## Troubleshooting

### If images don't load:
- Check that `/assets/images/` directory exists
- Verify image paths in `room_images` table
- Ensure web server has read permissions

### If login fails:
- Verify admin credentials: admin / admin123
- Check `admin_cred` table was migrated
- Ensure password hashing is compatible

### If room cards show duplicates:
- Check `types_room` table for duplicate entries
- Run: `SELECT name, COUNT(*) FROM types_room GROUP BY name HAVING COUNT(*) > 1;`

## Completion

After successful migration:
- ✅ Your website uses clean `bsu_hrs_schema` database
- ✅ All existing data preserved
- ✅ No more duplicate room card issues
- ✅ Clean foundation for future development

## Optional: Clean Up (After Verification)

Once you confirm everything works, you can optionally:
- Backup `hostelweb` database
- Delete `hostelweb` database
- Remove old migration files

---

**Created for BSU Hostel Reservation System**
**Database Migration Complete Guide**
