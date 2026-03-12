<?php
/**
 * Email configuration for BSU HRS (PHPMailer / SMTP).
 * Copy this file to email_config.local.php and set your Gmail app password there.
 * Do not commit email_config.local.php to version control.
 */

$email_config = [
    // Gmail SMTP
    'smtp_host'     => 'smtp.gmail.com',
    'smtp_port'     => 587,
    'smtp_secure'   => 'tls',
    'smtp_username' => '', // e.g. your.gmail@gmail.com
    'smtp_password' => '', // Gmail App Password (not regular password)
    'from_email'    => 'hostel.nasugbu@g.batstate-u.edu.ph',
    'from_name'     => 'BatStateU Hostel - Nasugbu',
    // Optional BCC for confirmation emails (leave blank to disable)
    'bcc_email'     => '',
];

// Override with local config if present
$local_config = __DIR__ . '/email_config.local.php';
if (file_exists($local_config)) {
    $email_config = array_merge($email_config, (array) require $local_config);
}

return $email_config;
