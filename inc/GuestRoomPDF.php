<?php
/**
 * Guest room reservation PDF generator using TCPDF.
 * Requires: composer require tecnickcom/tcpdf
 */
class GuestRoomPDF {
    private array $data;
    private string $templatePage1;
    private string $templatePage2;
    private string $templatePage3;

    public function __construct(array $reservationData, string $templatePage1 = '', string $templatePage2 = '', string $templatePage3 = '') {
        $this->data = $reservationData;
        $this->templatePage1 = $templatePage1;
        $this->templatePage2 = $templatePage2;
        $this->templatePage3 = $templatePage3;
    }

    public function generate(): string {
        $autoload = dirname(__DIR__) . '/vendor/autoload.php';
        if (!file_exists($autoload)) {
            throw new RuntimeException('Composer autoload not found. Run composer install.');
        }
        require_once $autoload;

        $pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator('BSU HRS');
        $pdf->SetAuthor('BatStateU Hostel - Nasugbu');
        $pdf->SetTitle('Hostel Room Registration Form - ' . ($this->data['booking_no'] ?? ''));
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(0, 0, 0);
        $pdf->SetAutoPageBreak(false, 0);

        $p1 = $this->templatePage1;
        $p2 = $this->templatePage2;
        $p3 = $this->templatePage3;
        if ($p1 === '' || $p2 === '' || $p3 === '' || !file_exists($p1) || !file_exists($p2) || !file_exists($p3)) {
            throw new RuntimeException(
                "Guest registration template images not found.\n" .
                "Please export the DOCX into 3 image files and place them here:\n" .
                "- " . dirname(__DIR__) . "/documents/guest_form_page1.jpg (or .png)\n" .
                "- " . dirname(__DIR__) . "/documents/guest_form_page2.jpg (or .png)\n" .
                "- " . dirname(__DIR__) . "/documents/guest_form_page3.jpg (or .png)\n"
            );
        }

        $this->renderPage1($pdf, $p1);
        $this->renderPage2($pdf, $p2);
        $this->renderPage3($pdf, $p3);

        return $pdf->Output('', 'S');
    }

    /**
     * Draw background image without distortion (preserve aspect ratio),
     * centered on the A4 page. Returns [offsetX, offsetY] in mm.
     */
    private function drawBackground(\TCPDF $pdf, string $bgPath): array {
        $pageW = (float)$pdf->getPageWidth();
        $pageH = (float)$pdf->getPageHeight();

        $info = @getimagesize($bgPath);
        if (!$info || empty($info[0]) || empty($info[1])) {
            // Fallback: stretch if image metadata isn't available
            $pdf->Image($bgPath, 0, 0, $pageW, $pageH, '', '', '', false, 300, '', false, false, 0, false, false, false);
            return [0.0, 0.0];
        }

        $imgW = (float)$info[0];
        $imgH = (float)$info[1];
        $imgRatio = $imgW / $imgH;
        $pageRatio = $pageW / $pageH;

        if ($imgRatio >= $pageRatio) {
            // Fit to page width
            $w = $pageW;
            $h = $pageW / $imgRatio;
        } else {
            // Fit to page height
            $h = $pageH;
            $w = $pageH * $imgRatio;
        }

        $x = ($pageW - $w) / 2.0;
        $y = ($pageH - $h) / 2.0;

        $pdf->Image($bgPath, $x, $y, $w, $h, '', '', '', false, 300, '', false, false, 0, false, false, false);
        return [$x, $y];
    }

    private function renderPage1(\TCPDF $pdf, string $bgPath): void {
        $pdf->AddPage();
        [$ox, $oy] = $this->drawBackground($pdf, $bgPath);

        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 10);

        // ---- Calibration notes (page 1)
        // Coordinates below are tuned to match the exported form background.
        // If the DOCX export changes margins/scale, adjust these values.

        $bookingNo = (string)($this->data['booking_no'] ?? '');
        if ($bookingNo !== '') {
            $this->text($pdf, 22 + $ox, 28 + $oy, $bookingNo);
        }

        // Principal guest name split: LAST | FIRST | MI
        [$last, $first, $mi] = $this->splitPrincipalName((string)($this->data['guest_name'] ?? ''));
        $nameY = 72.0;
        $this->text($pdf, 90 + $ox, 79 + $oy, $last);
        $this->text($pdf, 130 + $ox, 79 + $oy, $first);               
        $this->text($pdf, 166 + $ox, 79 + $oy, $mi);     

        $this->text($pdf, 115 + $ox, 93 + $oy, $this->fmtDateShort($this->data['guest_dob'] ?? ''));
        $this->text($pdf, 100 + $ox, 100 + $oy, (string)($this->data['guest_address'] ?? ''));
        $this->text($pdf, 110 + $ox, 107.0 + $oy, (string)($this->data['guest_email'] ?? ''));
        $this->text($pdf, 116 + $ox, 114 + $oy, (string)($this->data['guest_contact'] ?? ''));

        // Other guests (page 1 has 4 slots)
        $others = $this->decodeOtherGuests($this->data['other_guests'] ?? '[]');
        $rows = [
            1 => 134.5,
            2 => 141,
            3 => 148.0,
            4 => 154.5,
        ];
        for ($i = 0; $i < 4; $i++) {
            $g = $others[$i] ?? null;
            $name = $g ? trim((string)($g['name'] ?? '')) : '';
            $age  = $g ? trim((string)($g['age'] ?? '')) : '';
            $dob  = $g ? $this->fmtDateShort($g['dob'] ?? '') : '';
            $y = $rows[$i + 1];
            if ($name !== '') {
                $this->text($pdf, 30 + $ox, $y + $oy, $name);
                $this->text($pdf, 105 + $ox, $y + $oy, $age);
                $this->text($pdf, 145 + $ox, $y + $oy, $dob);
            }
        }

        // Stay details block
        $this->text($pdf, 65 + $ox, 168.0 + $oy, $this->fmtDateShort($this->data['check_in_date'] ?? ''));
        $this->text($pdf, 68 + $ox, 174 + $oy, $this->fmtTimeShort($this->data['check_in_time'] ?? ''));
        $this->text($pdf, 73 + $ox, 181.0 + $oy, (string)((int)($this->data['adults_count'] ?? 0)));
        $this->text($pdf, 64 + $ox, 188 + $oy, (string)($this->data['room_name'] ?? ''));

        $this->text($pdf, 148 + $ox, 168.0 + $oy, $this->fmtDateShort($this->data['check_out_date'] ?? ''));
        $this->text($pdf, 150 + $ox, 174 + $oy, $this->fmtTimeShort($this->data['check_out_time'] ?? ''));
        $this->text($pdf, 155 + $ox, 181 + $oy, (string)((int)($this->data['children_count'] ?? 0)));
        $this->text($pdf, 150 + $ox, 188 + $oy, (string)($this->data['room_type'] ?? ''));

        // Remarks / special arrangements box (multi-line)
        $remarks = trim((string)($this->data['special_requests'] ?? ''));
        if ($remarks !== '') {
            $pdf->SetFont('helvetica', '', 9);
            $pdf->SetXY(32 + $ox, 216.0 + $oy);
            $pdf->MultiCell(160, 18, $remarks, 0, 'L', false, 1);
            $pdf->SetFont('helvetica', '', 10);
        }

        // Registered by (stored in terms_accepted_by in current schema)
        $registeredBy = trim((string)($this->data['terms_accepted_by'] ?? ''));
        if ($registeredBy !== '') {
            $this->text($pdf, 80 + $ox, 215 + $oy, $registeredBy);
        }
    }

    private function renderPage2(\TCPDF $pdf, string $bgPath): void {
        $pdf->AddPage();
        [$ox, $oy] = $this->drawBackground($pdf, $bgPath);

        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 10);

        $others = $this->decodeOtherGuests($this->data['other_guests'] ?? '[]');
        // Page 2 shows other guests continuation (we'll continue from 5th guest)
        $startIndex = 4;
        // Y positions for 24 lines (approx)
        $y0 = 66.0;
        $dy = 8.0;
        for ($i = 0; $i < 24; $i++) {
            $g = $others[$startIndex + $i] ?? null;
            if (!$g) continue;
            $name = trim((string)($g['name'] ?? ''));
            if ($name === '') continue;
            $age  = trim((string)($g['age'] ?? ''));
            $dob  = $this->fmtDateShort($g['dob'] ?? '');
            $y = $y0 + ($i * $dy);
            $this->text($pdf, 38 + $ox, $y + $oy, $name);
            $this->text($pdf, 125 + $ox, $y + $oy, $age);
            $this->text($pdf, 155 + $ox, $y + $oy, $dob);
        }
    }

    private function renderPage3(\TCPDF $pdf, string $bgPath): void {
        $pdf->AddPage();
        [$ox, $oy] = $this->drawBackground($pdf, $bgPath);

        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 10);

        $others = $this->decodeOtherGuests($this->data['other_guests'] ?? '[]');
        // Page 3 continues after page 2's 24 lines: 4 (page1) + 24 (page2) = 28
        $startIndex = 28;
        $y0 = 66.0;
        $dy = 8.0;
        for ($i = 0; $i < 24; $i++) {
            $g = $others[$startIndex + $i] ?? null;
            if (!$g) continue;
            $name = trim((string)($g['name'] ?? ''));
            if ($name === '') continue;
            $age  = trim((string)($g['age'] ?? ''));
            $dob  = $this->fmtDateShort($g['dob'] ?? '');
            $y = $y0 + ($i * $dy);
            $this->text($pdf, 38 + $ox, $y + $oy, $name);
            $this->text($pdf, 125 + $ox, $y + $oy, $age);
            $this->text($pdf, 155 + $ox, $y + $oy, $dob);
        }
    }

    private function text(\TCPDF $pdf, float $x, float $y, string $text): void {
        $t = trim($text);
        if ($t === '') return;
        $pdf->SetXY($x, $y);
        $pdf->Cell(0, 0, $t, 0, 0, 'L', false, '', 0, false, 'T', 'M');
    }

    private function fmtDate($date): string {
        $date = (string)$date;
        if (trim($date) === '') return '—';
        $ts = strtotime($date);
        return $ts ? date('F d, Y', $ts) : '—';
    }

    private function fmtDateShort($date): string {
        $date = (string)$date;
        if (trim($date) === '') return '';
        $ts = strtotime($date);
        return $ts ? date('M d, Y', $ts) : '';
    }

    private function fmtTime($time): string {
        $time = (string)$time;
        if (trim($time) === '') return '—';
        $ts = strtotime('1970-01-01 ' . $time);
        return $ts ? date('h:i A', $ts) : '—';
    }

    private function fmtTimeShort($time): string {
        $time = (string)$time;
        if (trim($time) === '') return '';
        $ts = strtotime('1970-01-01 ' . $time);
        return $ts ? date('h:i A', $ts) : '';
    }

    private function calcNights($in, $out): int {
        $inTs = strtotime((string)$in);
        $outTs = strtotime((string)$out);
        if (!$inTs || !$outTs || $outTs <= $inTs) return 0;
        return (int)(($outTs - $inTs) / 86400);
    }

    private function decodeOtherGuests($raw): array {
        if (is_array($raw)) return $raw;
        $raw = (string)$raw;
        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : [];
    }

    private function splitPrincipalName(string $full): array {
        $full = trim($full);
        if ($full === '') return ['', '', ''];

        // Expected patterns:
        // 1) "Last, First MI"
        // 2) "First MI Last" (legacy)
        $last = $first = $mi = '';
        if (str_contains($full, ',')) {
            [$lastPart, $rest] = array_map('trim', explode(',', $full, 2));
            $last = $lastPart;
            $parts = preg_split('/\s+/', trim($rest)) ?: [];
            $first = $parts[0] ?? '';
            $miRaw = $parts[1] ?? '';
            $mi = rtrim($miRaw, '.');
        } else {
            $parts = preg_split('/\s+/', $full) ?: [];
            if (count($parts) >= 2) {
                $first = $parts[0];
                $last = $parts[count($parts) - 1];
                if (count($parts) >= 3) {
                    $mi = rtrim($parts[1], '.');
                }
            } else {
                $first = $full;
            }
        }
        return [$last, $first, $mi];
    }
}

