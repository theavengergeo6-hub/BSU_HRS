# PDF & Email Setup (Function Room Reservations)

## 1. Install Composer (if not installed)

- **Windows:** Download [Composer-Setup.exe](https://getcomposer.org/Composer-Setup.exe) and run it. Restart the terminal after installation.
- **Or:** Download `composer.phar` from [getcomposer.org](https://getcomposer.org/download/) and run `php composer.phar install` from the project root.

## 2. Install PHP dependencies

From the project root (`C:\xampp\htdocs\BSU_HRS`):

```bash
composer install
```

This installs **PHPMailer** and **TCPDF** into the `vendor/` folder.

## 3. Gmail SMTP configuration

1. Copy `inc/email_config.local.php.example` to `inc/email_config.local.php`.
2. In Gmail, enable 2-Step Verification, then create an **App Password**:  
   [Google App Passwords](https://myaccount.google.com/apppasswords)
3. Edit `inc/email_config.local.php` and set:
   - `smtp_username`: your Gmail address (e.g. the hostel Gmail)
   - `smtp_password`: the 16-character App Password (no spaces when pasted is fine)

## 4. Optional: logos on the PDF

Place your logos so the paths exist:

- `assets/images/hostel.jpg` – hostel image
- `assets/images/bsu-logo.jpg` – BSU logo

If these files are missing, the PDF is still generated without images.

## 5. Behaviour

- When a **function room** reservation is successfully submitted:
  1. Data is saved to the database.
  2. A PDF is generated with reservation details (requestor, office, event, venue, time, misc items, instructions).
  3. An email is sent to the customer’s address with the PDF attached and BCC to `hostel.nasugbu@g.batstate-u.edu.ph`.
- If PDF generation or email fails, the reservation **still succeeds**; the error is written to `debug.log`.
