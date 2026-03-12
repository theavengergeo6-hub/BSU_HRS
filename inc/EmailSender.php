<?php
/**
 * Email sender wrapper using PHPMailer (SMTP).
 * Requires: composer require phpmailer/phpmailer
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailSender {

    /** @var array */
    private $config;
    /** @var string|null */
    private $lastError;

    public function __construct(array $emailConfig = null) {
        $this->config = $emailConfig ?? (require __DIR__ . '/email_config.php');
        $this->lastError = null;
    }

    /**
     * Get last error message.
     * @return string|null
     */
    public function getLastError() {
        return $this->lastError;
    }

    /**
     * Send function room reservation confirmation with PDF attachment.
     * @param string $toEmail Customer email
     * @param string $customerName Display name (e.g. "Last, First MI")
     * @param string $bookingNo Booking reference
     * @param string $activityName Activity/event name
     * @param string $pdfContent Raw PDF binary content
     * @param string $pdfFilename Attachment filename (e.g. "Reservation-FAC-20260309-186.pdf")
     * @return bool True if sent successfully
     */
    public function sendFunctionRoomConfirmation($toEmail, $customerName, $bookingNo, $activityName, $pdfContent, $pdfFilename = '') {
        $subject = 'Function Room Reservation Confirmation - ' . $activityName;
        if ($pdfFilename === '') {
            $pdfFilename = 'Reservation-' . $bookingNo . '.pdf';
        }
        $htmlBody = $this->buildFunctionRoomEmailBody($customerName, $bookingNo, $activityName);
        return $this->sendWithAttachment($toEmail, $subject, $htmlBody, $pdfContent, $pdfFilename);
    }

    /**
     * Send generic email with optional attachment.
     * @param string $toEmail
     * @param string $subject
     * @param string $htmlBody
     * @param string|null $attachmentContent Raw file content
     * @param string|null $attachmentFilename
     * @return bool
     */
    public function sendWithAttachment($toEmail, $subject, $htmlBody, $attachmentContent = null, $attachmentFilename = null) {
        $this->lastError = null;
        $autoload = dirname(__DIR__) . '/vendor/autoload.php';
        if (!file_exists($autoload)) {
            $this->lastError = 'Composer autoload not found. Run: composer install';
            return false;
        }
        require_once $autoload;

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = $this->config['smtp_host'] ?? 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = $this->config['smtp_username'] ?? '';
            $mail->Password   = $this->config['smtp_password'] ?? '';
            $mail->SMTPSecure = $this->config['smtp_secure'] ?? 'tls';
            $mail->Port       = (int)($this->config['smtp_port'] ?? 587);
            $mail->CharSet    = 'UTF-8';
            $mail->Encoding   = 'base64';

            $mail->setFrom($this->config['from_email'] ?? '', $this->config['from_name'] ?? 'BSU Hostel');
            $mail->addAddress($toEmail);
            if (!empty($this->config['bcc_email'])) {
                $mail->addBCC($this->config['bcc_email']);
            }

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $htmlBody;
            $mail->AltBody = strip_tags(preg_replace('/<br\s*\/?>/i', "\n", $htmlBody));

            if ($attachmentContent !== null && $attachmentFilename !== null) {
                $mail->addStringAttachment($attachmentContent, $attachmentFilename, 'base64', 'application/pdf');
            }

            $mail->send();
            return true;
        } catch (Exception $e) {
            $this->lastError = $mail->ErrorInfo ?: $e->getMessage();
            return false;
        }
    }

    private function buildFunctionRoomEmailBody($customerName, $bookingNo, $activityName) {
        $red = '#b71c1c';
        $html = '<!DOCTYPE html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"></head><body style="margin:0; font-family: Arial, sans-serif; background:#f5f5f5; padding: 20px;">';
        $html .= '<div style="max-width: 600px; margin: 0 auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">';
        $html .= '<div style="background: ' . $red . '; color: #fff; padding: 24px 20px; text-align: center;">';
        $html .= '<h1 style="margin: 0; font-size: 22px;">BATANGAS STATE UNIVERSITY</h1>';
        $html .= '<p style="margin: 8px 0 0; font-size: 14px; opacity: 0.95;">BatStateU Hostel – Function Room Reservation</p>';
        $html .= '</div>';
        $html .= '<div style="padding: 24px 20px;">';
        $html .= '<p style="margin: 0 0 16px; color: #333;">Dear ' . htmlspecialchars($customerName) . ',</p>';
        $html .= '<p style="margin: 0 0 16px; color: #555; line-height: 1.6;">Thank you for submitting your function room reservation. Your request has been received and is pending approval.</p>';
        $html .= '<table style="width: 100%; border-collapse: collapse; margin: 20px 0;">';
        $html .= '<tr><td style="padding: 10px 12px; background: #f9f9f9; border: 1px solid #eee; font-weight: bold; width: 140px;">Booking Reference</td><td style="padding: 10px 12px; border: 1px solid #eee;">' . htmlspecialchars($bookingNo) . '</td></tr>';
        $html .= '<tr><td style="padding: 10px 12px; background: #f9f9f9; border: 1px solid #eee; font-weight: bold;">Activity / Event</td><td style="padding: 10px 12px; border: 1px solid #eee;">' . htmlspecialchars($activityName) . '</td></tr>';
        $html .= '<tr><td style="padding: 10px 12px; background: #f9f9f9; border: 1px solid #eee; font-weight: bold;">Status</td><td style="padding: 10px 12px; border: 1px solid #eee;"><span style="color: #e65100; font-weight: bold;">Pending Approval</span></td></tr>';
        $html .= '</table>';
        $html .= '<p style="margin: 0 0 16px; color: #555; line-height: 1.6;">Please find your reservation details in the attached PDF. You will be notified once your reservation has been reviewed.</p>';
        $html .= '<p style="margin: 0; color: #555; line-height: 1.6;">For inquiries, contact us at <strong>hostel.nasugbu@g.batstate-u.edu.ph</strong>.</p>';
        $html .= '</div>';
        $html .= '<div style="padding: 16px 20px; background: #f9f9f9; border-top: 1px solid #eee; text-align: center; font-size: 12px; color: #777;">';
        $html .= 'This is an automated message from BatStateU Hostel Reservation System.';
        $html .= '</div></div></body></html>';
        return $html;
    }
}
