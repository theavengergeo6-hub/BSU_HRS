# BSU Hostel Reservation System (BSU_HRS)

A high-performance, real-time management solution for the BatStateU ARASOF-Nasugbu Hostel. This system enables users to reserve function rooms and guest rooms through a seamless, automated interface while providing admins with a powerful, live-updating dashboard for operations.

---

## 🚩 Latest System Status
The system is currently in **Production-Ready** status. The latest updates include:
- **Real-Time Admin Hub:** The dashboard and sidebar now update automatically without page refreshes when new reservations are made.
- **Live Notifications:** Audio alerts and visual toast notifications for incoming bookings.
- **Enhanced Calendar:** Dynamic dot indicators for both Function and Guest Room availability.
- **PDF Report Engine:** Official PDF generation for all approved reservations.

---

## 💻 Tech Stack
- **Backend:** PHP 8.1+
- **Database:** MariaDB / MySQL
- **Frontend:** Vanilla CSS, Bootstrap 5.3, Bootstrap Icons
- **Real-time:** AJAX/Fetch API Polling
- **PDF Engine:** Custom PHP PDF Generator

---

## 🚀 Quick Setup

1. **Place Project:** Copy the project folder to your web root (e.g., `C:\xampp\htdocs\BSU_HRS`).
2. **First-Time Initialize:**
   - Open your browser to: `http://localhost/BSU_HRS/setup.php`
   - This will automatically create the database (`bsu_hrs_db`), establish tables, and seed default data.
   - **Important:** Remove or rename `setup.php` after successful initialization.
3. **Default Credentials:**
   - **Username:** `admin`
   - **Password:** `hostel123`

---

## 📂 Project Architecture

```bash
BSU_HRS/
├── admin/                  # Admin Management Suite
│   ├── reservations.php    # Live Calendar Hub
│   ├── rooms.php           # Inventory Management
│   ├── guest_reservations.php # Guest Stay listing
│   └── index.php           # Admin Dashboard
├── ajax/                   # Backend API endpoints (JSON/Fetch)
├── assets/                 # Brand assets and images
├── inc/                    # Core configuration and global functions
├── guest_rooms.php         # Guest room catalog
├── rooms_showcase.php      # Public room listing
├── calendar.php            # Public availability check
├── reservation.php         # Multi-step booking engine
└── bsu_hrs_schema.sql      # Database blueprint
```

---

## ✨ Core Features

### For the Public
- **Real-time Availability:** Browse a dynamic calendar to see room occupancy.
- **Automated Booking:** Integrated terms of service and multi-step validation.
- **Dynamic Showcase:** Modern UI for viewing hostel rooms and venues.

### For Admins
- **Live Dashboard:** Real-time stats on pending, approved, and pencil-booked events.
- **Real-time Engine:** The system polls for new reservations silently to update sidebar badges and markers.
- **PDF Reporting:** Generate and download official reservation forms with signatures.
- **Calendar Filters:** Filter views by Approved, Pending, or Pencil Booked status.

---

## 🛠️ Configuration
- **Database:** Update `inc/db_config.php` for your local MySQL credentials.
- **Base URL:** If NOT using `localhost/BSU_HRS`, update `BASE_URL` in `inc/essentials.php`.

---
*Developed for BatStateU ARASOF-Nasugbu*
