# Copilot Workspace Instructions for BSU Hostel Management System

## Overview
This workspace is a PHP-based hostel/facility reservation system with both public and admin interfaces. It uses MySQL/MariaDB for data storage and is designed to run on Apache (XAMPP recommended). The codebase includes booking, user management, and admin features.

## Build & Run
- **No build step required.**
- To run locally: Place the project in your web root (e.g., `C:/xampp/htdocs/BSU_HRS`).
- Start Apache and MySQL via XAMPP.
- Access via `http://localhost/BSU_HRS/` in your browser.

## Setup
- First-time setup: Open `http://localhost/BSU_HRS/setup.php` to initialize the database and create sample data.
- **Delete `setup.php` after setup for security.**
- Default admin credentials: `admin` / `admin123`.
- Database config: Edit `inc/db_config.php` if needed.
- Base URL: Edit `BASE_URL` in `inc/essentials.php` if not using the default.

## Project Structure
- `admin/` — Admin panel (room, user, booking management)
- `user/` — User dashboard
- `inc/` — Shared PHP includes (config, helpers)
- `ajax/` — AJAX handlers for dynamic UI
- `css/`, `script/` — Static assets
- `database/` — SQL schema and migration scripts

## Conventions
- Use prepared statements for all DB queries (see `inc/essentials.php` and `inc/db_config.php`).
- Sanitize all user input and output (XSS protection).
- Use the provided structure for new features (e.g., add AJAX handlers to `ajax/`, shared logic to `inc/`).
- For new admin features, follow the pattern in `admin/` (separate PHP files per feature, use includes for header/footer).

## Testing
- No automated tests included. Manual testing via browser is standard.
- For database changes, use migration scripts in `database/`.

## Common Pitfalls
- Not deleting `setup.php` after setup (security risk).
- Incorrect DB credentials in `inc/db_config.php`.
- File/folder permissions issues on Windows.
- Not starting Apache/MySQL in XAMPP before accessing the site.

## Example Prompts
- "Add a new admin page for managing facility types."
- "Create an AJAX endpoint to check room availability."
- "Refactor user registration to use prepared statements."
- "Update the reservation summary to include guest remarks."

---
For more details, see `README.md` or ask for specific file conventions.
