# BSU Hostel Management System

A complete hostel management solution with public and admin interfaces, booking system, and user management.

## Requirements

- PHP 7.x/8.x
- MariaDB/MySQL
- Apache (XAMPP recommended)

## Setup

1. Place the project in your web root (e.g. `C:\xampp\htdocs\BSU_HRS`).

2. **Run setup** (first time only):
   - Open `http://localhost/BSU_HRS/setup.php` in your browser
   - This creates the database, tables, sample data, and default admin
   - **Delete `setup.php` after setup** for security

3. **Default admin credentials:**
   - Username: `admin`
   - Password: `admin123`

## Configuration

- **Database:** Edit `inc/db_config.php` if your MySQL credentials differ
- **Base URL:** Edit `BASE_URL` in `inc/essentials.php` if your URL differs from `http://localhost/BSU_HRS/`

## Structure

```
BSU_HRS/
├── index.php          # Homepage
├── rooms.php          # Room listing
├── room-details.php   # Room detail + booking
├── booking.php        # Complete booking
├── login.php
├── register.php
├── facilities.php
├── contact.php
├── admin/             # Admin panel
│   ├── index.php      # Admin login
│   ├── dashboard.php
│   ├── rooms.php
│   ├── users.php
│   └── bookings.php
├── user/
│   └── dashboard.php  # User bookings
├── inc/               # Shared includes
├── ajax/              # AJAX handlers
├── css/
├── script/
├── images/uploads/
└── database/
    └── hostelweb.sql  # Schema
```

## Features

- **Public:** Browse rooms, check availability, book online, user registration, reviews
- **Admin:** Dashboard, room management, user management, booking management
- **Security:** Password hashing, prepared statements, XSS sanitization
# BSU_HRS
# BSU_HRS
