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
            $smtpUser = trim((string)($this->config['smtp_username'] ?? ''));
            $smtpPass = trim((string)($this->config['smtp_password'] ?? ''));
            if ($smtpUser === '' || $smtpPass === '') {
                $this->lastError = 'SMTP credentials missing. Set smtp_username and smtp_password in inc/email_config.local.php';
                return false;
            }
            $mail->Username   = $smtpUser;
            $mail->Password   = $smtpPass;
            $mail->SMTPSecure = $this->config['smtp_secure'] ?? 'tls';
            $mail->Port       = (int)($this->config['smtp_port'] ?? 587);
            $mail->CharSet    = 'UTF-8';
            $mail->Encoding   = 'base64';

            // Gmail SMTP usually requires the From address to match the authenticated account.
            // Use the SMTP username as the sender, and set the hostel email as Reply-To if provided.
            $fromName = $this->config['from_name'] ?? 'BSU Hostel';
            $replyTo = trim((string)($this->config['from_email'] ?? ''));
            $mail->setFrom($smtpUser, $fromName);
            if ($replyTo !== '' && strcasecmp($replyTo, $smtpUser) !== 0) {
                $mail->addReplyTo($replyTo, $fromName);
            }
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

    /**
     * Send status update notification for function room reservation.
     * @param string $toEmail Customer email
     * @param string $customerName
     * @param string $bookingNo
     * @param string $activityName
     * @param string $status New status
     * @param string $adminRemarks Optional admin remarks
     * @return bool
     */
    public function sendFunctionRoomStatusUpdate($toEmail, $customerName, $bookingNo, $activityName, $status, $adminRemarks = '') {
        $statusLabel = ucfirst(str_replace('_', ' ', $status));
        $subject = "Reservation Status Update: $statusLabel - $bookingNo";
        $htmlBody = $this->buildStatusUpdateEmailBody($customerName, $bookingNo, $activityName, $status, $adminRemarks, 'Function Room');
        return $this->sendWithAttachment($toEmail, $subject, $htmlBody);
    }

    /**
     * Send status update notification for guest room reservation.
     */
    public function sendGuestRoomStatusUpdate($toEmail, $customerName, $bookingNo, $status, $adminRemarks = '') {
        $statusLabel = ucfirst(str_replace('_', ' ', $status));
        $subject = "Room Registration Status Update: $statusLabel - $bookingNo";
        $htmlBody = $this->buildStatusUpdateEmailBody($customerName, $bookingNo, 'Guest Room Reservation', $status, $adminRemarks, 'Guest Room');
        return $this->sendWithAttachment($toEmail, $subject, $htmlBody);
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

    private function buildStatusUpdateEmailBody($customerName, $bookingNo, $activityName, $status, $adminRemarks, $typeLabel = 'Function Room') {
        $red = '#b71c1c';
        $statusLabel = strtoupper(str_replace('_', ' ', $status));
        $statusColor = '#555';
        
        // Colors from Admin UI for consistency
        if ($status === 'approved' || $status === 'confirmed') $statusColor = '#28a745';
        elseif ($status === 'cancelled' || $status === 'denied') $statusColor = '#dc3545';
        elseif ($status === 'pencil_booked') $statusColor = '#5e3c8b';
        elseif ($status === 'checked_in') $statusColor = '#004085';
        
        $statusMsg = "The status of your $typeLabel reservation has been updated to <strong>$statusLabel</strong>.";
        if ($status === 'approved' || $status === 'confirmed') {
            $statusMsg = "We are pleased to inform you that your $typeLabel reservation has been <strong>APPROVED</strong>.";
        } elseif ($status === 'denied' || $status === 'cancelled') {
            $statusMsg = "We regret to inform you that your $typeLabel reservation request has been <strong>" . ($status === 'denied' ? 'DENIED' : 'CANCELLED') . "</strong>.";
        }

        $html = '<!DOCTYPE html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"></head><body style="margin:0; font-family: Arial, sans-serif; background:#f5f5f5; padding: 20px;">';
        $html .= '<div style="max-width: 600px; margin: 0 auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">';
        $html .= '<div style="background: ' . $red . '; color: #fff; padding: 24px 20px; text-align: center;">';
        $html .= '<h1 style="margin: 0; font-size: 22px;">BATANGAS STATE UNIVERSITY</h1>';
        $html .= '<p style="margin: 8px 0 0; font-size: 14px; opacity: 0.95;">BatStateU Hostel – Reservation Update</p>';
        $html .= '</div>';
        $html .= '<div style="padding: 24px 20px;">';
        $html .= '<p style="margin: 0 0 16px; color: #333;">Dear ' . htmlspecialchars($customerName) . ',</p>';
        $html .= '<p style="margin: 0 0 16px; color: #555; line-height: 1.6;">' . $statusMsg . '</p>';
        $html .= '<table style="width: 100%; border-collapse: collapse; margin: 20px 0;">';
        $html .= '<tr><td style="padding: 10px 12px; background: #f9f9f9; border: 1px solid #eee; font-weight: bold; width: 140px;">Booking Reference</td><td style="padding: 10px 12px; border: 1px solid #eee;">' . htmlspecialchars($bookingNo) . '</td></tr>';
        $html .= '<tr><td style="padding: 10px 12px; background: #f9f9f9; border: 1px solid #eee; font-weight: bold;">Reservation Type</td><td style="padding: 10px 12px; border: 1px solid #eee;">' . htmlspecialchars($typeLabel) . '</td></tr>';
        if ($typeLabel === 'Function Room') {
            $html .= '<tr><td style="padding: 10px 12px; background: #f9f9f9; border: 1px solid #eee; font-weight: bold;">Activity / Event</td><td style="padding: 10px 12px; border: 1px solid #eee;">' . htmlspecialchars($activityName) . '</td></tr>';
        }
        $html .= '<tr><td style="padding: 10px 12px; background: #f9f9f9; border: 1px solid #eee; font-weight: bold;">New Status</td><td style="padding: 10px 12px; border: 1px solid #eee;"><span style="color: ' . $statusColor . '; font-weight: bold;">' . $statusLabel . '</span></td></tr>';
        $html .= '</table>';
        
        if (!empty($adminRemarks)) {
            $html .= '<div style="margin: 20px 0; padding: 15px; background: #fff8f8; border-left: 4px solid ' . $red . '; font-style: italic; color: #555;">';
            $html .= '<strong>Admin Remarks:</strong><br>' . nl2br(htmlspecialchars($adminRemarks));
            $html .= '</div>';
        }

        $html .= '<p style="margin:0; color: #555; line-height: 1.6;">For inquiries, contact us at <strong>hostel.nasugbu@g.batstate-u.edu.ph</strong>.</p>';
        $html .= '</div>';
        $html .= '<div style="padding: 16px 20px; background: #f9f9f9; border-top: 1px solid #eee; text-align: center; font-size: 12px; color: #777;">';
        $html .= 'This is an automated message from BatStateU Hostel Reservation System.';
        $html .= '</div></div></body></html>';
        return $html;
    }


}
