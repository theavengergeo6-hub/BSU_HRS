<?php
/**
 * GuestRoomPDF.php  — v3 (drawn from scratch, no background images)
 *
 * Generates a pixel-perfect 3-page PDF matching the official
 * "Hostel Room Guest's Registration Form" using TCPDF.
 * Every line, box, label, and field is drawn in code — no template images needed.
 *
 * Placement : BSU_HRS/admin/GuestRoomPDF.php
 * Requires  : composer require tecnickcom/tcpdf  (run in BSU_HRS/)
 *
 * Usage:
 *   $gen = new GuestRoomPDF($data);
 *   $gen->stream('registration.pdf');          // open in browser
 *   $gen->stream('registration.pdf', true);    // force download
 *   $raw = $gen->generate();                   // returns PDF bytes
 */
class GuestRoomPDF
{
    private array $d;   // reservation data shorthand

    // ── Page geometry (A4 = 210 × 297 mm) ────────────────────────────────────
    private const PW  = 210.0;   // page width  mm
    private const PH  = 297.0;   // page height mm
    private const ML  = 15.0;    // left margin
    private const MR  = 15.0;    // right margin
    private const CW  = 180.0;   // content width  (PW - ML - MR)

    public function __construct(array $reservationData)
    {
        $this->d = $reservationData;
    }

    // ── Public API ────────────────────────────────────────────────────────────

    public function generate(): string
    {
        $pdf = $this->boot();
        $this->page1($pdf);
        $this->page2($pdf);
        $this->page3($pdf);
        return $pdf->Output('', 'S');
    }

    public function stream(string $filename = 'guest_registration.pdf', bool $download = false): void
    {
        $pdf = $this->boot();
        $this->page1($pdf);
        $this->page2($pdf);
        $this->page3($pdf);
        $pdf->Output($filename, $download ? 'D' : 'I');
    }

    // ── Bootstrap ─────────────────────────────────────────────────────────────

    private function boot(): \TCPDF
    {
        $autoload = dirname(__DIR__) . '/vendor/autoload.php';
        if (!file_exists($autoload)) {
            throw new RuntimeException(
                "Composer autoload not found.\nRun: composer require tecnickcom/tcpdf"
            );
        }
        require_once $autoload;

        $pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator('BSU HRS');
        $pdf->SetAuthor('BatStateU ARASOF Hostel');
        $pdf->SetTitle('Hostel Room Registration – ' . ($this->d['booking_no'] ?? ''));
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(self::ML, 8, self::MR);
        $pdf->SetAutoPageBreak(false, 0);
        return $pdf;
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  PAGE 1  –  Guest information + stay details
    // ══════════════════════════════════════════════════════════════════════════
    private function page1(\TCPDF $pdf): void
    {
        $pdf->AddPage();
        $d  = $this->d;
        $ml = self::ML;
        $cw = self::CW;

        // ── Header ────────────────────────────────────────────────────────────
        $hy = $this->drawHeader($pdf);

        // ── GUEST'S INFORMATION outer box ────────────────────────────────────
        $boxY  = $hy + 2;
        $boxH1 = 64.0;   // guest info section height
        $pdf->SetLineWidth(0.5);
        $pdf->Rect($ml, $boxY, $cw, $boxH1, 'D');

        // Section label
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetXY($ml + 2, $boxY + 2);
        $pdf->Cell($cw - 4, 5, "GUEST'S INFORMATION", 0, 1, 'L');

        // PRINCIPAL GUEST NAME row
        $gy = $boxY + 9;
        $pdf->SetFont('helvetica', '', 8);
        $pdf->SetXY($ml + 2, $gy);
        $pdf->Cell(38, 4, 'PRINCIPAL GUEST NAME', 0, 0, 'L');

        // Three underlined columns: LAST NAME | FIRST NAME | M.I
        $cols = [
            ['label' => 'LAST NAME',  'x' => $ml + 42, 'w' => 52],
            ['label' => 'FIRST NAME', 'x' => $ml + 97, 'w' => 52],
            ['label' => 'M.I',        'x' => $ml + 152, 'w' => 26],
        ];
        [$last, $first, $mi] = $this->splitName((string)($d['guest_name'] ?? ''));
        $vals = [$last, $first, $mi];

        foreach ($cols as $ci => $col) {
            // underline
            $pdf->Line($col['x'], $gy + 4, $col['x'] + $col['w'], $gy + 4);
            // value above underline
            $pdf->SetFont('helvetica', '', 9);
            $pdf->SetXY($col['x'], $gy - 0.5);
            $pdf->Cell($col['w'], 4, $vals[$ci], 0, 0, 'C');
            // small label below underline
            $pdf->SetFont('helvetica', '', 6);
            $pdf->SetXY($col['x'], $gy + 4.5);
            $pdf->Cell($col['w'], 3, $col['label'], 0, 0, 'C');
        }

        // DATE OF BIRTH / ADDRESS / E-MAIL / CONTACT rows
        $fields = [
            ['label' => 'DATE OF BIRTH',  'key' => 'dob_formatted'],
            ['label' => 'ADDRESS',         'key' => 'guest_address'],
            ['label' => 'E-MAIL ADDRESS',  'key' => 'guest_email'],
            ['label' => 'CONTACT NO.',     'key' => 'guest_contact'],
        ];
        // Pre-format DOB
        $dob = (string)($d['guest_dob'] ?? '');
        $d['dob_formatted'] = $this->fmtDate($dob);

        $fy = $gy + 11;
        foreach ($fields as $f) {
            $pdf->SetFont('helvetica', '', 8);
            $pdf->SetXY($ml + 2, $fy);
            $pdf->Cell(34, 4, $f['label'], 0, 0, 'L');
            $val = trim((string)($d[$f['key']] ?? ''));
            // underline
            $pdf->Line($ml + 38, $fy + 4, $ml + $cw - 2, $fy + 4);
            // value
            $pdf->SetFont('helvetica', '', 9);
            $pdf->SetXY($ml + 38, $fy);
            $pdf->Cell($cw - 40, 4, $this->truncate($val, 65), 0, 0, 'L');
            $fy += 8;
        }

        // ── OTHER GUESTS NAME section (4 slots) ───────────────────────────────
        $ogBoxY = $boxY + $boxH1;
        $ogBoxH = 36.0;
        $pdf->SetLineWidth(0.5);
        $pdf->Rect($ml, $ogBoxY, $cw, $ogBoxH, 'D');

        // Column headers
        $pdf->SetFont('helvetica', '', 8);
        $nameX = $ml + 4;  $nameW = 82;
        $ageX  = $ml + 88; $ageW  = 24;
        $dobX  = $ml + 114; $dobW = $cw - 116;

        $pdf->SetXY($nameX, $ogBoxY + 2);
        $pdf->Cell($nameW, 4, 'OTHER GUESTS NAME', 0, 0, 'L');
        $pdf->SetXY($ageX, $ogBoxY + 2);
        $pdf->Cell($ageW, 4, 'AGE', 0, 0, 'C');
        $pdf->SetXY($dobX, $ogBoxY + 2);
        $pdf->Cell($dobW, 4, 'DATE OF BIRTH', 0, 0, 'L');

        $others = $this->decodeGuests($d['other_guests'] ?? '[]');
        $ry = $ogBoxY + 8;
        for ($i = 0; $i < 4; $i++) {
            $g    = $others[$i] ?? null;
            $name = $g ? trim((string)($g['name'] ?? '')) : '';
            $age  = $g ? trim((string)($g['age']  ?? '')) : '';
            $dob2 = $g ? $this->fmtDateShort((string)($g['dob'] ?? '')) : '';

            // row number
            $pdf->SetFont('helvetica', '', 8);
            $pdf->SetXY($ml + 2, $ry);
            $pdf->Cell(6, 4, ($i + 1) . '.', 0, 0, 'R');

            // name underline + value
            $pdf->Line($ml + 8, $ry + 4, $ml + 85, $ry + 4);
            if ($name !== '') {
                $pdf->SetFont('helvetica', '', 9);
                $pdf->SetXY($ml + 8, $ry);
                $pdf->Cell(77, 4, $this->truncate($name, 32), 0, 0, 'L');
            }

            // age underline + value
            $pdf->Line($ageX, $ry + 4, $ageX + $ageW, $ry + 4);
            if ($age !== '') {
                $pdf->SetFont('helvetica', '', 9);
                $pdf->SetXY($ageX, $ry);
                $pdf->Cell($ageW, 4, $age, 0, 0, 'C');
            }

            // dob underline + value
            $pdf->Line($dobX, $ry + 4, $dobX + $dobW, $ry + 4);
            if ($dob2 !== '') {
                $pdf->SetFont('helvetica', '', 9);
                $pdf->SetXY($dobX, $ry);
                $pdf->Cell($dobW, 4, $dob2, 0, 0, 'L');
            }

            $ry += 7;
        }

        // ── Stay details box ──────────────────────────────────────────────────
        $sdBoxY = $ogBoxY + $ogBoxH;
        $sdBoxH = 48.0;
        $pdf->SetLineWidth(0.5);
        $pdf->Rect($ml, $sdBoxY, $cw, $sdBoxH, 'D');

        // Two-column stay detail rows
        $rowDefs = [
            ['ARRIVAL DATE',   $this->fmtDateShort($d['check_in_date']  ?? ''),
             'DEPARTURE DATE', $this->fmtDateShort($d['check_out_date'] ?? '')],
            ['CHECK IN TIME',  $this->fmtTime($d['check_in_time']  ?? ''),
             'CHECK OUT TIME', $this->fmtTime($d['check_out_time'] ?? '')],
            ['NO. OF ADULT/S', (string)(int)($d['adults_count']   ?? 0),
             'NO. OF KID/S',   (string)(int)($d['children_count'] ?? 0)],
            ['ROOM NUMBER',    (string)($d['room_name'] ?? ''),
             'ROOM TYPE',      ucfirst((string)($d['room_type'] ?? ''))],
        ];

        $sry = $sdBoxY + 3;
        $half = $cw / 2;
        foreach ($rowDefs as $row) {
            [$ll, $lv, $rl, $rv] = $row;

            // Left label
            $pdf->SetFont('helvetica', '', 8);
            $pdf->SetXY($ml + 2, $sry);
            $pdf->Cell(32, 4, $ll, 0, 0, 'L');
            // Left underline + value
            $pdf->Line($ml + 35, $sry + 4, $ml + $half - 2, $sry + 4);
            $pdf->SetFont('helvetica', '', 9);
            $pdf->SetXY($ml + 35, $sry);
            $pdf->Cell($half - 37, 4, $lv, 0, 0, 'L');

            // Right label
            $pdf->SetFont('helvetica', '', 8);
            $pdf->SetXY($ml + $half + 2, $sry);
            $pdf->Cell(34, 4, $rl, 0, 0, 'L');
            // Right underline + value
            $pdf->Line($ml + $half + 38, $sry + 4, $ml + $cw - 2, $sry + 4);
            $pdf->SetFont('helvetica', '', 9);
            $pdf->SetXY($ml + $half + 38, $sry);
            $pdf->Cell($half - 40, 4, $rv, 0, 0, 'L');

            $sry += 7;
        }

        // REMARKS/SPECIAL ARRANGEMENTS
        $pdf->SetFont('helvetica', '', 8);
        $pdf->SetXY($ml + 2, $sry);
        $pdf->Cell($cw - 4, 4, 'REMARKS/SPECIAL ARRANGEMENTS', 0, 0, 'L');
        $sry += 5;

        $remarks = trim((string)($d['special_requests'] ?? ''));
        // Two remark lines
        $pdf->Line($ml + 2, $sry + 3, $ml + $cw - 2, $sry + 3);
        $pdf->SetFont('helvetica', '', 9);
        if ($remarks !== '') {
            $pdf->SetXY($ml + 2, $sry - 1);
            $pdf->MultiCell($cw - 4, 4, $remarks, 0, 'L', false, 1);
        }
        $sry += 7;
        $pdf->Line($ml + 2, $sry + 3, $ml + $cw - 2, $sry + 3);
        $sry += 8;

        // REGISTERED BY
        $pdf->SetFont('helvetica', '', 8);
        $pdf->SetXY($ml + 2, $sry);
        $pdf->Cell(28, 4, 'REGISTERED BY:', 0, 0, 'L');
        $regBy = trim((string)($d['terms_accepted_by'] ?? $d['registered_by'] ?? ''));
        $pdf->Line($ml + 32, $sry + 4, $ml + $half + 10, $sry + 4);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->SetXY($ml + 32, $sry);
        $pdf->Cell($half - 20, 4, $regBy, 0, 0, 'L');

        // ── Data Privacy section ──────────────────────────────────────────────
        $dpY = $sdBoxY + $sdBoxH + 4;
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetXY($ml, $dpY);
        $pdf->Cell($cw, 4, 'Data Privacy and Protection', 0, 1, 'L');
        $pdf->SetFont('helvetica', '', 8);
        $dpText = 'During your stay, information will be collected about you and your preferences '
            . 'in order to provide you with the best possible service. The information will be '
            . 'retained to facilitate future stays at BatStateU ARASOF Hostel. If there are any '
            . 'questions regarding this data privacy, feel free to let us know at '
            . 'hostel.nasugbu@g.batstate-u.edu.ph';
        $pdf->SetXY($ml, $dpY + 5);
        $pdf->MultiCell($cw, 4.2, $dpText, 0, 'L', false, 1);
        $dpY2 = $pdf->GetY() + 2;
        $pdf->SetXY($ml, $dpY2);
        $pdf->MultiCell($cw, 4.2,
            'By signing, you are expressly giving your consent to the collection and storage '
            . 'of your personal data as provided herein.',
            0, 'L', false, 1);

        // ── Signature line ────────────────────────────────────────────────────
        $sigY = $pdf->GetY() + 8;
        $sigX1 = $ml + 40;
        $sigX2 = $ml + $cw - 40;
        $pdf->Line($sigX1, $sigY, $sigX2, $sigY);
        $pdf->SetFont('helvetica', '', 8);
        $pdf->SetXY($sigX1, $sigY + 1);
        $pdf->Cell($sigX2 - $sigX1, 4, "PRINCIPAL GUEST'S NAME & SIGNATURE", 0, 0, 'C');
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  PAGE 2  –  Other guests continuation (24 slots)
    // ══════════════════════════════════════════════════════════════════════════
    private function page2(\TCPDF $pdf): void
    {
        $pdf->AddPage();
        $ml = self::ML;
        $cw = self::CW;

        $hy = $this->drawHeader($pdf);

        // Outer box
        $boxY = $hy + 2;
        $boxH = 192.0;
        $pdf->SetLineWidth(0.5);
        $pdf->Rect($ml, $boxY, $cw, $boxH, 'D');

        // Column header row
        $nameX = $ml + 4;  $nameW = 82;
        $ageX  = $ml + 88; $ageW  = 24;
        $dobX  = $ml + 114; $dobW = $cw - 116;

        $pdf->SetFont('helvetica', '', 8);
        $pdf->SetXY($nameX, $boxY + 3);
        $pdf->Cell($nameW, 4, 'OTHER GUESTS NAME', 0, 0, 'L');
        $pdf->SetXY($ageX, $boxY + 3);
        $pdf->Cell($ageW, 4, 'AGE', 0, 0, 'C');
        $pdf->SetXY($dobX, $boxY + 3);
        $pdf->Cell($dobW, 4, 'DATE OF BIRTH', 0, 0, 'L');

        $others = $this->decodeGuests($this->d['other_guests'] ?? '[]');
        $ry  = $boxY + 9;
        $lh  = 7.6;   // line height — fits 24 rows in ~182mm

        for ($i = 0; $i < 24; $i++) {
            $g    = $others[4 + $i] ?? null;   // page1 uses slots 0-3, page2 uses 4-27
            $name = $g ? trim((string)($g['name'] ?? '')) : '';
            $age  = $g ? trim((string)($g['age']  ?? '')) : '';
            $dob  = $g ? $this->fmtDateShort((string)($g['dob'] ?? '')) : '';

            $pdf->SetFont('helvetica', '', 8);
            $pdf->SetXY($ml + 2, $ry);
            $pdf->Cell(6, 4, ($i + 1) . '.', 0, 0, 'R');

            $pdf->Line($ml + 8, $ry + 4, $ml + 85, $ry + 4);
            if ($name !== '') {
                $pdf->SetFont('helvetica', '', 9);
                $pdf->SetXY($ml + 8, $ry);
                $pdf->Cell(77, 4, $this->truncate($name, 32), 0, 0, 'L');
            }

            $pdf->Line($ageX, $ry + 4, $ageX + $ageW, $ry + 4);
            if ($age !== '') {
                $pdf->SetFont('helvetica', '', 9);
                $pdf->SetXY($ageX, $ry);
                $pdf->Cell($ageW, 4, $age, 0, 0, 'C');
            }

            $pdf->Line($dobX, $ry + 4, $dobX + $dobW, $ry + 4);
            if ($dob !== '') {
                $pdf->SetFont('helvetica', '', 9);
                $pdf->SetXY($dobX, $ry);
                $pdf->Cell($dobW, 4, $dob, 0, 0, 'L');
            }

            $ry += $lh;
        }

        // Signature line
        $sigY = $boxY + $boxH + 8;
        $pdf->Line($ml + 40, $sigY, $ml + $cw - 40, $sigY);
        $pdf->SetFont('helvetica', '', 8);
        $pdf->SetXY($ml + 40, $sigY + 1);
        $pdf->Cell($cw - 80, 4, "PRINCIPAL GUEST'S NAME & SIGNATURE", 0, 0, 'C');
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  PAGE 3  –  Hostel Room Guidelines + signature
    // ══════════════════════════════════════════════════════════════════════════
    private function page3(\TCPDF $pdf): void
    {
        $pdf->AddPage();
        $ml = self::ML;
        $cw = self::CW;

        $hy = $this->drawHeader($pdf);
        $y  = $hy + 4;

        // Title
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->SetXY($ml, $y);
        $pdf->Cell($cw, 6, 'HOSTEL ROOM GUIDELINES', 0, 1, 'C');
        $y += 8;

        // Numbered guidelines
        $guidelines = [
            'BatStateU Hostel is a non-smoking area.',
            'Standard Check-in time at 2:00 pm and 12:00 noon check out time.',
            'The hostel is located at BatStateU ARASOF - Nasugbu Campus. Maintaining good relationships with faculty and students must be observed. Be generally mindful by their presence as they move around the building.',
            'Toned-down sounds between 7 AM until 6 PM are observed in consideration for the faculty and students during class hours.',
            'No Curfew administered for all the guests, however perceive not to disturb others upon returning to the Hostel late at night.',
            'Hostel Laundry Service for Php 100.00 per kilogram, inclusive of powder detergent w/ color protection and fabric softener. Housekeeping to assist with laundry provided with laundry bag.',
            'Trash Bins are placed around the Hostel. Proper throwing of trash helps us maintain the cleanliness of the facilities for the guests as well as for the faculty and students.',
            'Turning off the lights and air-conditioning as well as the faucet before leaving the Hostel room will help us conserve energy and water.',
            "BatStateU Hostel is not liable for any lost or damage of guest's personal belongings.",
            'Room Keys can be deposited at the reception. Any lost key will be charged accordingly.',
            "Incidental charges will apply for any loss or damages at the Hostel property during the guest's stay. Settlement must be done before check-out/departure and must be settled through cash.",
            'The management reserves the right to refuse entry/stay to individuals violating Hotel policies and guidelines.',
            'Hostel Housekeeping staff is authorized to enter your room with or without guests inside for a housekeeping operation.',
        ];

        $pdf->SetFont('helvetica', '', 8.5);
        foreach ($guidelines as $n => $text) {
            $num  = ($n + 1) . '.';
            $pdf->SetXY($ml, $y);
            $pdf->Cell(7, 4.5, $num, 0, 0, 'R');
            $pdf->SetXY($ml + 8, $y);
            $pdf->MultiCell($cw - 8, 4.5, $text, 0, 'L', false, 1);
            $y = $pdf->GetY() + 0.5;
        }

        $y += 3;
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetXY($ml, $y);
        $pdf->Cell($cw, 5, 'PROHIBITED ACTS', 0, 1, 'C');
        $y += 6;

        $prohibited = [
            'Uncooked foods and cooking inside the Hostel room of prohibited.',
            'Deadly weapons and illegal drugs are STRICTLY PROHIBITED inside the hostel.',
            'Drinking inside the Hostel room is not allowed. Hostel Bar on the ground floor can be used for any alcoholic beverage consumption.',
            'Pets are not allowed inside the property.',
            'Only registered guests are allowed to stay in the Hostel room.',
        ];

        $pdf->SetFont('helvetica', '', 8.5);
        foreach ($prohibited as $item) {
            $pdf->SetXY($ml + 3, $y);
            $pdf->Cell(5, 4.5, chr(149), 0, 0, 'L');  // bullet
            $pdf->SetXY($ml + 8, $y);
            $pdf->MultiCell($cw - 8, 4.5, $item, 0, 'L', false, 1);
            $y = $pdf->GetY() + 0.5;
        }

        $y += 3;
        $pdf->SetFont('helvetica', '', 8.5);
        $pdf->SetXY($ml, $y);
        $pdf->MultiCell($cw, 4.5,
            'For further clarification and queries please feel free to contact us at 09287842104 '
            . 'or email us at hostel.nasugbu@g.batstate-u.edu.ph',
            0, 'L', false, 1);
        $y = $pdf->GetY() + 2;

        $pdf->SetFont('helvetica', '', 8.5);
        $pdf->SetXY($ml, $y);
        $pdf->Cell($cw, 4.5, 'Thank you. We look forward in welcoming your group here at the Hostel!', 0, 1, 'L');
        $y += 7;

        // Acknowledgement italic box
        $ackText = 'I have read and understand all the guidelines and prohibited acts at BatStateU ARASOF Hostel during '
            . 'my stay. I am signing this freely and voluntarily to formally acknowledge our liabilities stated on the '
            . 'guidelines above.';
        $pdf->SetFont('helvetica', 'I', 8.5);
        $pdf->SetLineWidth(0.3);
        $pdf->SetXY($ml, $y);
        $pdf->MultiCell($cw, 4.5, $ackText, 'TLRB', 'L', false, 1);
        $y = $pdf->GetY() + 8;

        // PRINTED NAME / SIGNATURE / DATE lines
        $lineW = 46;
        $xs    = [$ml + 2, $ml + 66, $ml + 132];
        $lbls  = ['PRINTED NAME', 'SIGNATURE', 'DATE'];
        $pdf->SetLineWidth(0.3);
        $pdf->SetFont('helvetica', 'I', 8);
        foreach ($xs as $ci => $lx) {
            $pdf->Line($lx, $y, $lx + $lineW, $y);
            $pdf->SetXY($lx, $y + 1);
            $pdf->Cell($lineW, 4, $lbls[$ci], 0, 0, 'C');
        }
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  Shared header  –  logo area + title  (drawn on every page)
    // ══════════════════════════════════════════════════════════════════════════
    /**
     * Draws the university header and returns the Y position below it.
     */
    private function drawHeader(\TCPDF $pdf): float
    {
        $ml = self::ML;
        $cw = self::CW;

        $pdf->SetLineWidth(0.5);

        // Top rule
        $pdf->Line($ml, 8, $ml + $cw, 8);

        // University text block (centred)
        $pdf->SetFont('helvetica', '', 7.5);
        $pdf->SetXY($ml, 9);
        $pdf->Cell($cw, 3.5, 'Republic of the Philippines', 0, 1, 'C');
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetXY($ml, 12.5);
        $pdf->Cell($cw, 3.5, 'BATANGAS STATE UNIVERSITY', 0, 1, 'C');
        $pdf->SetFont('helvetica', '', 7.5);
        $pdf->SetXY($ml, 16);
        $pdf->Cell($cw, 3.5, 'ARASOF-Nasugbu', 0, 1, 'C');
        $pdf->SetXY($ml, 19.5);
        $pdf->Cell($cw, 3.5, 'Nasugbu, Batangas', 0, 1, 'C');

        // "BatStateU HOSTEL" in large italic
        $pdf->SetFont('times', 'BI', 20);
        $pdf->SetXY($ml, 23);
        $pdf->Cell($cw, 8, 'BatStateU HOSTEL', 0, 1, 'C');

        // Bottom rule under brand name
        $pdf->SetLineWidth(0.8);
        $pdf->Line($ml, 32, $ml + $cw, 32);
        $pdf->SetLineWidth(0.3);

        // Sub-title
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetXY($ml, 34);
        $pdf->Cell($cw, 5, "Hostel Room Guest's Registration Form", 0, 1, 'C');

        $pdf->SetLineWidth(0.5);
        return 40.0;   // Y after header
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function splitName(string $full): array
    {
        $full = trim($full);
        if ($full === '') return ['', '', ''];
        $last = $first = $mi = '';

        if (str_contains($full, ',')) {
            [$lastPart, $rest] = array_map('trim', explode(',', $full, 2));
            $last  = $lastPart;
            $parts = preg_split('/\s+/', $rest) ?: [];
            $first = $parts[0] ?? '';
            $miRaw = $parts[1] ?? '';
            $mi    = rtrim($miRaw, '.');
        } else {
            $parts = preg_split('/\s+/', $full) ?: [];
            $n     = count($parts);
            if ($n === 1) {
                $first = $parts[0];
            } elseif ($n === 2) {
                [$first, $last] = $parts;
            } else {
                $first = $parts[0];
                $last  = $parts[$n - 1];
                $mi    = rtrim($parts[1], '.');
            }
        }
        if ($mi !== '' && mb_strlen($mi) > 1) {
            $mi = mb_strtoupper(mb_substr($mi, 0, 1));
        }
        return [$last, $first, $mi];
    }

    private function decodeGuests(mixed $raw): array
    {
        if (is_array($raw)) return $raw;
        $decoded = json_decode((string)$raw, true);
        return is_array($decoded) ? $decoded : [];
    }

    private function fmtDate(string $date): string
    {
        $date = trim($date);
        if ($date === '' || $date === '0000-00-00') return '';
        $ts = strtotime($date);
        return $ts ? date('F d, Y', $ts) : '';
    }

    private function fmtDateShort(string $date): string
    {
        $date = trim($date);
        if ($date === '' || $date === '0000-00-00') return '';
        $ts = strtotime($date);
        return $ts ? date('M d, Y', $ts) : '';
    }

    private function fmtTime(string $time): string
    {
        $time = trim($time);
        if ($time === '') return '';
        $ts = strtotime('1970-01-01 ' . $time);
        return $ts ? date('h:i A', $ts) : '';
    }

    private function truncate(string $text, int $max): string
    {
        if (mb_strlen($text) <= $max) return $text;
        return mb_substr($text, 0, $max - 1) . "\u{2026}";
    }
}