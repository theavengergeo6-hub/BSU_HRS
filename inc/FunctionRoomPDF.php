<?php
/**
 * inc/FunctionRoomPDF.php
 *
 * Generates a PDF that replicates the official BatStateU Hostel
 * "NEW Function Room RESERVATION FORM 2026" and overlays reservation
 * data from the submitted form. The result is emailed to the customer
 * automatically after submission.
 *
 * Requires: composer require tecnickcom/tcpdf
 */

class FunctionRoomPDF
{

    /** @var array */
    private $data;
    /** @var string */
    private $logoHostelPath;
    /** @var string */
    private $logoBsuPath;

    /**
     * @param array  $reservationData  Keys: booking_no, last_name, first_name,
     *                                  middle_initial, email, contact_number,
     *                                  activity_name, start_datetime, end_datetime,
     *                                  participants_count, miscellaneous_items,
     *                                  additional_instruction, terms_agreed_by,
     *                                  terms_position, terms_date,
     *                                  office_display, venue_names, banquet_name,
     *                                  office_type_id (1=College,2=Office,3=Student Org,4=External)
     * @param string $logoHostelPath   Absolute path to hostel.jpg (optional)
     * @param string $logoBsuPath      Absolute path to bsu-logo.jpg (optional)
     */
    public function __construct(array $reservationData, $logoHostelPath = '', $logoBsuPath = '')
    {
        $this->data = $reservationData;
        $this->logoHostelPath = $logoHostelPath;
        $this->logoBsuPath = $logoBsuPath;

        // Auto-extract terms prefixes from miscellaneous_items if not present in main array
        if (isset($this->data['miscellaneous_items'])) {
            $misc = json_decode($this->data['miscellaneous_items'], true) ?: [];
            if (!isset($this->data['terms_agreed_by']) && isset($misc['_terms_agreed_by'])) {
                $this->data['terms_agreed_by'] = $misc['_terms_agreed_by'];
            }
            if (!isset($this->data['terms_position']) && isset($misc['_terms_position'])) {
                $this->data['terms_position'] = $misc['_terms_position'];
            }
            if (!isset($this->data['terms_date']) && isset($misc['_terms_date'])) {
                $this->data['terms_date'] = $misc['_terms_date'];
            }
        }
    }

    /**
     * Generate the PDF and return raw binary for email attachment.
     */
    public function generate(): string
    {
        $pdf = $this->buildPDF();
        return $pdf->Output('', 'S');
    }

    /**
     * Stream the PDF directly to the browser.
     */
    public function stream(string $filename = 'function_room_reservation.pdf', bool $download = false): void
    {
        $pdf = $this->buildPDF();
        $pdf->Output($filename, $download ? 'D' : 'I');
    }

    // ─────────────────────────────────────────────────────────────────────────

    private function buildPDF(): \TCPDF
    {
        $autoload = dirname(__DIR__) . '/vendor/autoload.php';
        if (!file_exists($autoload)) {
            throw new RuntimeException('Composer autoload not found. Run: composer require tecnickcom/tcpdf');
        }
        require_once $autoload;

        $pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator('BSU HRS');
        $pdf->SetAuthor('BatStateU Hostel');
        $pdf->SetTitle('Function Room Reservation Form - ' . ($this->data['booking_no'] ?? ''));
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(0, 0, 0);
        $pdf->SetAutoPageBreak(false, 0);

        // ── Page 1: Reservation form ─────────────────────────────────────────
        $pdf->AddPage();
        $this->drawTemplateOrFallback($pdf);

        // ── Page 2+: Guidelines (appended as image pages) ────────────────────
        $this->appendGuidelinePages($pdf);

        return $pdf;
    }


    private function appendGuidelinePages(\TCPDF $pdf): void
    {
        $docDir = dirname(__DIR__) . '/documents/';

        if ($this->isCollegeOrStudentOrg()) {
            // CABEIHM Memo No.3.s.2025 — Guidelines for Utilizing the Hostel Function Rooms
            $pages = [
                $docDir . 'memo_guidelines_page1.jpg',
                $docDir . 'memo_guidelines_page2.jpg',
            ];
        } else {
            // Function Rooms House Rules 2026
            $pages = [
                $docDir . 'house_rules_page1.jpg',
                $docDir . 'house_rules_page2.jpg',
            ];
        }

        foreach ($pages as $imgPath) {
            if (!file_exists($imgPath))
                continue;
            $pdf->SetMargins(0, 0, 0);
            $pdf->SetAutoPageBreak(false, 0);
            $pdf->AddPage();
            // Render the page image full-bleed on A4 (210 × 297 mm)
            $pdf->Image($imgPath, 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0, false, false, false);
        }
    }

    private function drawTemplateOrFallback(\TCPDF $pdf): void
    {
        $preferred = dirname(__DIR__) . '/documents/NEW Function Room RESERVATION FORM 2026_page-0001.jpg';
        $fallback = dirname(__DIR__) . '/documents/function_form_template.png';

        $bg = file_exists($preferred) ? $preferred : (file_exists($fallback) ? $fallback : '');
        if ($bg !== '') {
            $pdf->Image($bg, 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0, false, false, false);
            $this->overlayFields($pdf);
            return;
        }
        // Fallback: programmatic drawing
        $this->drawForm($pdf);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // OVERLAY MODE — places data over the background image template
    // ─────────────────────────────────────────────────────────────────────────

    private function overlayFields(\TCPDF $pdf): void
    {
        $d = $this->data;
        $pdf->SetTextColor(0, 0, 0);

        /**
         * Place a single-line value — only for truly short fields that never wrap:
         * Event No., participant count, and misc quantity blanks.
         */
        $put = function (float $x, float $y, string $text, float $size = 9.5) use ($pdf): void {
            $text = trim($text);
            if ($text === '')
                return;
            $pdf->SetFont('helvetica', '', $size);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetXY($x, $y);
            $pdf->Cell(0, 0, $text, 0, 0, 'L', false, '', 0, false, 'T', 'T');
        };

        /**
         * Place text strictly confined to a box of width $w.
         * Tries 9.2 → 8.5 → 8.0 → 7.5 pt before wrapping, so short/medium
         * values stay on one line and only genuinely long values wrap.
         */
        $putMulti = function (float $x, float $y, string $text, float $w, float $size = 9.2, float $lineH = 4.5) use ($pdf): void {
            $text = trim($text);
            if ($text === '')
                return;
            // Step font down before resorting to line-wrapping
            foreach ([$size, 8.5, 8.0, 7.5] as $fs) {
                $pdf->SetFont('helvetica', '', $fs);
                if ($pdf->GetStringWidth($text) <= $w)
                    break;
            }
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetXY($x, $y);
            $pdf->MultiCell($w, $lineH, $text, 0, 'L', false, 0);
        };

        // ── Per-field coordinates (right column needs independent alignment) ─
        // Left column value area
        $xLeft = 70.0;
        $leftColW = 77.0;   // space from $xLeft to the centre divider

        // Right column value areas — set per row for better alignment
        $xOffice = 155.0;
        $xVenue = 160.0;
        $xSetup = 160.0;
        $xContact = 160.0;

        $rightColWOffice = 47.0;
        $rightColWVenue = 47.0;
        $rightColWSetup = 47.0;
        $rightColWContact = 47.0;

        // ── Row Y positions — top of each data row in the template ───────────
        // Measured from the top of the A4 page (0 mm) to the top of the row's
        // value area. Each row is ~14 mm tall; values sit ~2 mm from the top.
        $yEvent = 77.5;   // "Event No." line near the top
        $y1 = 87.0;   // Row 1 — Name / Office
        $y2 = 105.0;   // Row 2 — Activity / Venue
        $y3 = 125.0;   // Row 3 — Date & Time / Venue set-up
        $y4 = 137.5;   // Row 4 — Participants / Contact

        // Event No.
        $put(30.0, $yEvent, (string) ($d['booking_no'] ?? ''), 9.5);

        // Row 1 — Name | Office/College
        $putMulti($xLeft, $y1, $this->requestorName(), $leftColW, 9.2, 4.2);
        $office = trim((string) ($d['office_display'] ?? ''));
        if ($office !== '') {
            $words = preg_split('/\s+/', $office);
            $lines = [];
            $current = '';

            // max ~28 characters per line works well for this cell
            $maxChars = 28;

            foreach ($words as $w) {
                $try = $current === '' ? $w : $current . ' ' . $w;
                if (strlen($try) > $maxChars && $current !== '') {
                    $lines[] = $current;
                    $current = $w;
                } else {
                    $current = $try;
                }
            }
            if ($current !== '') {
                $lines[] = $current;
            }

            $office = implode("\n", $lines);
        }

        $putMulti($xOffice, $y1, $office, $rightColWOffice, 8.0, 3.8);

        // Row 2 — Activity | Venue (may be multiple rooms)
        $putMulti($xLeft, $y2, (string) ($d['activity_name'] ?? ''), $leftColW, 9.2, 4.2);
        // One function room per line (Function Room A\nFunction Room B\n...)
        $venueRaw = trim((string) ($d['venue_names'] ?? ''));
        if ($venueRaw !== '') {
            $venueParts = array_values(array_filter(array_map('trim', explode(',', $venueRaw)), fn($v) => $v !== ''));
            if (count($venueParts) > 1) {
                $venueRaw = implode("\n", $venueParts);
            }
        }
        $putMulti($xVenue, $y2, $venueRaw, $rightColWVenue, 8.5, 4.0);

        // Row 3 — Date & Time (date on line 1, time range on line 2) | Venue set-up
        [$dateLine, $timeLine] = $this->formatDateTimeLines();
        $pdf->SetFont('helvetica', '', 9.0);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetXY($xLeft, $y3);
        $pdf->MultiCell($leftColW, 4.2, $dateLine . "\n" . $timeLine, 0, 'L', false, 0);

        $putMulti($xSetup, $y3, (string) ($d['banquet_name'] ?? $d['venue_setup_name'] ?? ''), $rightColWSetup, 8.8, 4.0);

        // Row 4 — Participants | Contact number / email
        $put($xLeft, $y4, (string) ($d['participants_count'] ?? ''), 9.2);
        $contactStr = trim((string) ($d['contact_number'] ?? '') . ' / ' . (string) ($d['email'] ?? ''));
        $putMulti($xContact, $y4, $contactStr, $rightColWContact, 8.0, 4.0);

        // ── Miscellaneous items ──────────────────────────────────────────────
        $misc = $this->parseMisc($d['miscellaneous_items'] ?? '{}');

        $qty = function ($v): int {
            if ($v === null)
                return 0;
            if (is_array($v))
                return (int) ($v['quantity'] ?? 0);
            return (int) $v;
        };

        $basicArr = is_array($misc['basic_sound_system'] ?? null) ? $misc['basic_sound_system'] : [];
        $spCnt = (int) ($basicArr['speaker'] ?? 0);
        $mcCnt = (int) ($basicArr['mic'] ?? 0);
        $basicOn = !empty($misc['basic_sound_system']) || $spCnt > 0 || $mcCnt > 0;
        $roundQty = $qty($misc['round_table'] ?? null);
        $banqQty = $qty($misc['banquet_chairs'] ?? null);
        $viewOn = !empty($misc['view_board']);
        $rectQty = $qty($misc['rectangular_table'] ?? null);
        $monoQty = $qty($misc['mono_block_chairs'] ?? null);

        // Draw checkmark (✔) inside a template checkbox at (x, y).
        // Template already has empty boxes — we only overlay the check glyph.
        $cb = function (float $x, float $y, bool $on, float $dx = 0.0, float $dy = 0.0) use ($pdf): void {
            if (!$on)
                return;
            $pdf->SetFont('zapfdingbats', '', 9); // consistent 9pt for all checks
            $pdf->SetTextColor(0, 0, 0);
            // Fine-tunable offsets so the glyph is centered inside the printed box
            $pdf->SetXY($x + 0.2 + $dx, $y + 0.2 + $dy);
            $pdf->Cell(4, 4, chr(52), 0, 0, 'C');
        };

        // ── Checkbox positions — LEFT column ────────────────────────────────
        // Row 1: Basic Sound System
        $cbYBasic = 161.5;
        $cb(20.0, $cbYBasic, $basicOn);

        // Row 2: Round Table
        $cbYRound = 166.5;
        $cb(20.0, $cbYRound, $roundQty > 0);

        // Row 3: Banquet Chairs
        $cbYBanq = 171.5;
        $cb(20.0, $cbYBanq, $banqQty > 0);

        // Row 4: View Board
        $cbYView = 176.5;
        $cb(20.0, $cbYView, $viewOn);

        // ── Checkbox positions — RIGHT column ───────────────────────────────
        // Row 1: Rectangular Table
        $cbYRect = 167;
        $cb(135.5, $cbYRect, $rectQty > 0);

        // Row 2: Mono Block Chairs
        $cbYMono = 172.5;
        $cb(135.5, $cbYMono, $monoQty > 0);

        // ── Quantity / value blanks ──────────────────────────────────────────
        // Basic Sound System — always print counts if > 0 (even if checkbox logic is off)
        if ($spCnt > 0)
            $put(70.0, $cbYBasic + 0.2, (string) $spCnt, 9.0);
        if ($mcCnt > 0)
            $put(110.0, $cbYBasic + 0.2, (string) $mcCnt, 9.0);

        // Round Table quantity
        if ($roundQty > 0) {
            $put(80.0, $cbYRound + 0.2, (string) $roundQty, 9.0);
        }

        // Banquet Chairs quantity
        if ($banqQty > 0) {
            $put(80.0, $cbYBanq + 1.2, (string) $banqQty, 9.0);
        }

        // Rectangular Table quantity
        if ($rectQty > 0) {
            $put(185.0, $cbYRect + 0.2, (string) $rectQty, 9.0);
        }

        // Mono Block Chairs quantity
        if ($monoQty > 0) {
            $put(185.0, $cbYMono + 0.2, (string) $monoQty, 9.0);
        }

        // Others — positioned to the right of the Mono Block row on template
        $othersText = $this->miscOthers($misc);
        if ($othersText !== '—' && $othersText !== '') {
            $putMulti(112.0, 220.0, $othersText, 80.0, 8.5);
        }

        // ── Additional instruction box ───────────────────────────────────────
        $inst = trim((string) ($d['additional_instruction'] ?? ''));
        if ($inst !== '') {
            $pdf->SetFont('helvetica', '', 9.0);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetXY(67.0, 187.0);
            $pdf->MultiCell(120.0, 4.5, $inst, 0, 'L', false, 1);
        }

        // ── Signatures Overlay (Panel 0: Requestor) ──────────────────────────
        // COORDINATES: X=14.0, Y=215.5 | FONT SIZE: 10
        // Set border to 1 for debugging the box. Set to 0 when finished.
        $reqName = $this->requestedByName();
        if ($reqName !== '') {
            $pdf->SetFont('times', '', 11.5);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetXY(18.0, 215.5);
            $pdf->Cell(45.0, 5, strtoupper($reqName), 0, 0, 'C');
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PROGRAMMATIC FALLBACK — draws the entire form without a background image
    // ─────────────────────────────────────────────────────────────────────────

    private function drawForm(\TCPDF $pdf): void
    {
        $d = $this->data;
        $lm = 12;   // left margin mm
        $W = 210;  // A4 width mm
        $pw = $W - $lm * 2; // usable width

        // ── 1. FULL-PAGE BORDER ───────────────────────────────────────────────
        $pdf->SetDrawColor(150, 30, 30);
        $pdf->SetLineWidth(0.8);
        $pdf->Rect($lm - 2, 5, $W - $lm * 2 + 4, 287, 'D');

        // ── 2. HEADER ─────────────────────────────────────────────────────────
        $y = 8;

        if ($this->logoBsuPath && file_exists($this->logoBsuPath)) {
            $pdf->Image($this->logoBsuPath, $lm, $y, 22, 0, '', '', '', false, 150);
        }
        if ($this->logoHostelPath && file_exists($this->logoHostelPath)) {
            $pdf->Image($this->logoHostelPath, $W - $lm - 22, $y, 22, 0, '', '', '', false, 150);
        }

        $pdf->SetFont('helvetica', 'B', 8.5);
        $pdf->SetTextColor(80, 80, 80);
        $pdf->SetXY($lm + 24, $y);
        $pdf->MultiCell(
            $pw - 48,
            4,
            "Republic of the Philippines\nBATANGAS STATE UNIVERSITY\nThe National Engineering University\n(ARASOF-Nasugbu Campus)\nR. Martinez St., Brgy. Bucana, Nasugbu, Batangas",
            0,
            'C',
            false,
            1
        );

        $pdf->SetFont('helvetica', '', 7);
        $pdf->SetXY($lm + 24, $y + 22);
        $pdf->Cell(
            $pw - 48,
            4,
            'Tel.: +63 43 416 0350 local 205  |  +63 928 784 2104  |  hostel.nasugbu@g.batstate-u.edu.ph',
            0,
            1,
            'C'
        );

        $pdf->SetFont('helvetica', '', 8);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetXY($W - $lm - 48, 8);
        $pdf->Cell(48, 5, 'Event No.: ' . ($d['booking_no'] ?? ''), 0, 0, 'R');

        // ── 3. FORM TITLE ─────────────────────────────────────────────────────
        $y = 38;
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->SetTextColor(183, 28, 28);
        $pdf->SetXY($lm, $y);
        $pdf->Cell($pw, 7, 'FUNCTION ROOM RESERVATION FORM', 0, 1, 'C');
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 7);
        $pdf->SetX($lm);
        $pdf->Cell($pw, 4, 'BatStateU Hostel — Nasugbu Campus', 0, 1, 'C');

        $pdf->SetDrawColor(183, 28, 28);
        $pdf->SetLineWidth(0.8);
        $y = 50;
        $pdf->Line($lm, $y, $W - $lm, $y);

        // ── 4. MAIN TABLE — reservation details ──────────────────────────────
        $pdf->SetLineWidth(0.3);
        $pdf->SetDrawColor(180, 180, 180);
        $y = 52;
        $half = $pw / 2;

        // Calculate dynamic row heights: venue row may need extra height
        $venueText = (string) ($d['venue_names'] ?? '—');
        $venueNeedsWrap = (strlen($venueText) > 28); // rough threshold

        $rows = [
            [
                ['Name (Last, First, MI):', $this->requestorName()],
                ['Office / College:', $d['office_display'] ?? '—'],
                14, // row height
            ],
            [
                ['Name of Activity:', $d['activity_name'] ?? '—'],
                ['Venue:', $venueText],
                $venueNeedsWrap ? 18 : 14,        // taller if venue wraps
            ],
            [
                ['Date & Time:', $this->formatDateTimeRange()],
                ['Venue Set-up / Banquet Style:', $d['banquet_name'] ?? $d['venue_setup_name'] ?? '—'],
                14,
            ],
            [
                ['No. of Participants:', (string) ($d['participants_count'] ?? '—')],
                ['Contact Number / Email:', trim(($d['contact_number'] ?? '') . '  ' . ($d['email'] ?? ''))],
                14,
            ],
        ];

        foreach ($rows as $row) {
            $rowH = $row[2];

            $pdf->SetDrawColor(180, 180, 180);
            $pdf->SetLineWidth(0.3);
            $pdf->Rect($lm, $y, $pw, $rowH, 'D');
            $pdf->Line($lm + $half, $y, $lm + $half, $y + $rowH);

            [$label0, $val0] = $row[0];
            $this->drawCell($pdf, $lm + 1, $y + 1, $half - 2, $rowH, $label0, $val0);

            [$label1, $val1] = $row[1];
            $this->drawCell($pdf, $lm + $half + 1, $y + 1, $half - 2, $rowH, $label1, $val1);

            $y += $rowH;
        }

        // ── 5. MISCELLANEOUS NEEDED ───────────────────────────────────────────
        $y += 3;
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetXY($lm, $y);
        $pdf->Cell($pw, 5, 'Miscellaneous Needed: Please indicate the number of items in the blank.', 0, 1, 'L');
        $pdf->SetFont('helvetica', 'I', 7.5);
        $pdf->SetTextColor(100, 100, 100);
        $pdf->SetX($lm);
        $pdf->Cell($pw, 4, 'Please indicate the number of items in the blank.', 0, 1, 'L');
        $y += 10;

        $misc = $this->parseMisc($d['miscellaneous_items'] ?? '{}');

        $leftItems = [
            ['Basic Sound System', $this->miscSoundText($misc)],
            ['Round Table', $this->miscQtyText($misc, 'round_table')],
            ['Banquet Chairs', $this->miscQtyText($misc, 'banquet_chairs')],
            ['View Board', $this->miscViewBoardText($misc)],
        ];
        $rightItems = [
            ['Rectangular Table', $this->miscQtyText($misc, 'rectangular_table')],
            ['Mono Block Chairs', $this->miscQtyText($misc, 'mono_block_chairs')],
            ['Others', $this->miscOthers($misc)],
        ];

        $pdf->SetDrawColor(100, 100, 100);
        $pdf->SetLineWidth(0.2);
        $mh = 8;  // misc row height — slightly taller for readability
        $leftW = $half - 2;
        $rightW = $half - 2;

        $maxRows = max(count($leftItems), count($rightItems));
        for ($i = 0; $i < $maxRows; $i++) {
            $xLeft = $lm + 1;
            $xRight = $lm + $half + 1;

            if (isset($leftItems[$i])) {
                [$lbl, $val] = $leftItems[$i];
                $this->drawMiscRow($pdf, $xLeft, $y, $leftW, $mh, $lbl, $val);
            }
            if (isset($rightItems[$i])) {
                [$lbl, $val] = $rightItems[$i];
                $this->drawMiscRow($pdf, $xRight, $y, $rightW, $mh, $lbl, $val);
            }
            $y += $mh;
        }

        // ── 6. ADDITIONAL INSTRUCTIONS ───────────────────────────────────────
        $y += 3;
        $inst = trim($d['additional_instruction'] ?? '');
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetXY($lm, $y);
        $pdf->Cell($pw, 5, 'Additional Instruction:', 0, 1, 'L');
        $y += 5;
        $pdf->SetFont('helvetica', '', 9);
        $pdf->SetXY($lm, $y);
        $pdf->MultiCell($pw, 5, $inst !== '' ? $inst : 'None.', 0, 'L');
        $y = $pdf->GetY() + 3;

        // Clamp: ensure signatures don't fall off the A4 page (max ~293 mm)
        // Signature section needs ~42 mm + footer ~10 mm = 52 mm
        if ($y > 235) {
            $y = 235;
        }

        $pdf->SetDrawColor(183, 28, 28);
        $pdf->SetLineWidth(0.5);
        $pdf->Line($lm, $y, $W - $lm, $y);
        $y += 4;

        // ── 7. SIGNATURE SECTION ─────────────────────────────────────────────
        $pdf->SetDrawColor(180, 180, 180);
        $pdf->SetLineWidth(0.25);
        $panelW = $pw / 3;
        $panelH = 38;

        for ($p = 0; $p < 3; $p++) {
            $pdf->Rect($lm + $p * $panelW, $y, $panelW, $panelH, 'D');
        }

        // Panel 0 — Requested by (name from reservation, First Last MI, all caps)
        $this->sigPanel(
            $pdf,
            $lm + 1,
            $y + 2,
            $panelW - 2,
            'Requested by:',
            $this->requestedByName()
        );

        // Panel 1 — Request received by
        $this->sigPanelFixed(
            $pdf,
            $lm + $panelW + 1,
            $y + 2,
            $panelW - 2,
            'Request received by:',
            'Mr. EMERISH JEM R. DELA VEGA',
            'Household Attendant III',
            ''
        );

        // Panel 2 — Approved by
        $this->sigPanelFixed(
            $pdf,
            $lm + $panelW * 2 + 1,
            $y + 2,
            $panelW - 2,
            'Approved by:',
            'Dr. MARVIN C. HERNANDEZ',
            'Dean, CABEIHM',
            ''
        );

        $y += $panelH + 3;

        // ── 8. FOOTER TEXT ────────────────────────────────────────────────────
        $pdf->SetFont('helvetica', 'I', 7.5);
        $pdf->SetTextColor(183, 28, 28);
        $pdf->SetXY($lm, $y);
        $pdf->Cell($pw, 5, '"Leading Innovations, Transforming Lives, Building the Nation"', 0, 1, 'C');

        $pdf->SetFont('helvetica', '', 6.5);
        $pdf->SetTextColor(160, 160, 160);
        $pdf->SetX($lm);
        $pdf->Cell(
            $pw,
            4,
            'Generated on ' . date('F j, Y \a\t g:i A') .
            '  |  This is a system-generated pre-filled form. A physical signed copy is required for approval.',
            0,
            1,
            'C'
        );
    }

    // ── Drawing helpers ───────────────────────────────────────────────────────

    /**
     * Draw a single info cell: bold small label on top, larger value below,
     * with a faint red underline at the cell bottom.
     * Value uses MultiCell so long strings (e.g. multiple venue names) wrap
     * instead of overflowing the cell.
     */
    private function drawCell(\TCPDF $pdf, float $x, float $y, float $w, float $h, string $label, string $value): void
    {
        // Label
        $pdf->SetFont('helvetica', 'B', 7.5);
        $pdf->SetTextColor(80, 80, 80);
        $pdf->SetXY($x, $y);
        $pdf->Cell($w, 4.5, $label, 0, 1, 'L');

        // Value — reduce font if string is wide
        $fontSize = 9.5;
        $pdf->SetFont('helvetica', '', $fontSize);
        $strW = $pdf->GetStringWidth($value);
        if ($strW > ($w - 2) && $fontSize > 8.0) {
            $fontSize = 8.0;
            $pdf->SetFont('helvetica', '', $fontSize);
        }

        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetXY($x + 1, $y + 5);
        // Use MultiCell so wrapped text stays inside the cell
        $pdf->MultiCell($w - 2, 4.5, $value, 0, 'L', false, 0);

        // Bottom underline
        $pdf->SetDrawColor(183, 28, 28);
        $pdf->SetLineWidth(0.3);
        $pdf->Line($x, $y + $h - 1.5, $x + $w, $y + $h - 1.5);
    }

    /**
     * Draw a miscellaneous item row with:
     *   [checkbox] Label: ______value______
     * A short underline is drawn under the value area to mimic a fill-in blank.
     */
    private function drawMiscRow(\TCPDF $pdf, float $x, float $y, float $w, float $h, string $label, string $value): void
    {
        $hasValue = ($value !== '—' && $value !== '' && $value !== 'No');

        // Checkbox square — vertically centred in row
        $cbSize = 3.5;
        $cbx = $x + 1.5;
        $cby = $y + ($h - $cbSize) / 2;

        $pdf->SetDrawColor(60, 60, 60);
        $pdf->SetLineWidth(0.35);
        $pdf->Rect($cbx, $cby, $cbSize, $cbSize, 'D');

        if ($hasValue) {
            $pdf->SetFont('zapfdingbats', '', 8);
            $pdf->SetTextColor(183, 28, 28);
            // Centre the glyph inside the box
            $pdf->SetXY($cbx - 0.2, $cby - 0.2);
            $pdf->Cell($cbSize, $cbSize, chr(52), 0, 0, 'C');
        }

        // Label text
        $labelW = ($w - 7) * 0.58;
        $valueW = ($w - 7) * 0.42 - 2;

        $pdf->SetFont('helvetica', '', 8);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetXY($x + 7, $y + ($h - 5) / 2);
        $pdf->Cell($labelW, 5, $label . ':', 0, 0, 'L');

        // Value (bold, red if present)
        $vx = $x + 7 + $labelW;
        $vy = $y + ($h - 5) / 2;

        if ($hasValue) {
            $pdf->SetFont('helvetica', 'B', 8.5);
            $pdf->SetTextColor(183, 28, 28);
            $pdf->SetXY($vx, $vy);
            $pdf->Cell($valueW, 5, $value, 0, 0, 'L');
        }

        // Underline beneath the value area (blank line style)
        $pdf->SetDrawColor(120, 120, 120);
        $pdf->SetLineWidth(0.25);
        $underlineY = $y + $h - 1.5;
        $pdf->Line($vx, $underlineY, $vx + $valueW, $underlineY);
    }

    /**
     * Signature panel where the requestor fills in their name and signs.
     */
    private function sigPanel(\TCPDF $pdf, float $x, float $y, float $w, string $header, string $name): void
    {
        $pdf->SetFont('times', 'B', 8);
        $pdf->SetTextColor(80, 80, 80);
        $pdf->SetXY($x, $y);
        $pdf->Cell($w, 5, $header, 0, 1, 'C');

        $pdf->SetFont('times', 'B', 10);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetXY($x, $y + 10);
        $pdf->MultiCell($w, 5, $name !== '' ? strtoupper($name) : '', 0, 'C', false, 1);

        $pdf->SetDrawColor(0, 0, 0);
        $pdf->SetLineWidth(0.3);
        $afterName = $pdf->GetY();
        $lineY = max($afterName + 2, $y + 21);
        $pdf->Line($x + 3, $lineY, $x + $w - 3, $lineY);

        $pdf->SetFont('times', '', 7.5);
        $pdf->SetTextColor(80, 80, 80);
        $pdf->SetXY($x, $lineY + 1);
        $pdf->Cell($w, 4, 'Signature over Printed Name', 0, 1, 'C');
    }

    /**
     * Signature panel for pre-filled staff / authority names.
     * The underline is placed below the title text so it never collides
     * if the title wraps to two lines.
     */
    private function sigPanelFixed(\TCPDF $pdf, float $x, float $y, float $w, string $header, string $name, string $title, string $date): void
    {
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetTextColor(80, 80, 80);
        $pdf->SetXY($x, $y);
        $pdf->Cell($w, 5, $header, 0, 1, 'C');

        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetXY($x, $y + 10);
        $pdf->MultiCell($w, 4.5, $name, 0, 'C', false, 1);

        $pdf->SetFont('helvetica', '', 7.5);
        $pdf->SetTextColor(80, 80, 80);
        // Position at current Y after name, not at a fixed offset
        $afterName = $pdf->GetY();
        $pdf->SetXY($x, $afterName);
        $pdf->MultiCell($w, 4, $title, 0, 'C', false, 1);

        // Underline placed 2 mm below the title — never overlaps
        $lineY = max($pdf->GetY() + 2, $y + 25);
        $pdf->SetDrawColor(0, 0, 0);
        $pdf->SetLineWidth(0.3);
        $pdf->Line($x + 3, $lineY, $x + $w - 3, $lineY);

        $pdf->SetFont('helvetica', '', 7.5);
        $pdf->SetTextColor(80, 80, 80);
        $pdf->SetXY($x, $lineY + 1);
        $pdf->Cell($w, 4, 'Date: ____________________', 0, 0, 'C');
    }

    // ── Data helpers ──────────────────────────────────────────────────────────

    private function requestorName(): string
    {
        $l = trim($this->data['last_name'] ?? '');
        $f = trim($this->data['first_name'] ?? '');
        $m = trim($this->data['middle_initial'] ?? '');
        if ($l === '' && $f === '')
            return '—';
        $name = $l . ($f !== '' ? ', ' . $f : '');
        if ($m !== '')
            $name .= ' ' . $m . '.';
        return $name;
    }

    /**
     * Returns the name for the "Requested by" signature panel:
     * format is "FIRSTNAME LASTNAME MI." — all uppercase.
     * e.g. "THE WEEKND J."
     */
    private function requestedByName(): string
    {
        $f = trim($this->data['first_name'] ?? '');
        $m = trim($this->data['middle_initial'] ?? '');
        $l = trim($this->data['last_name'] ?? '');

        if ($f === '' && $l === '') {
            return '';
        }

        $name = $f;
        if ($m !== '') {
            $name .= ' ' . rtrim($m, '.') . '.';
        }
        if ($l !== '') {
            $name .= ' ' . $l;
        }

        return strtoupper(trim($name));
    }

    /**
     * Returns true when the client is a College or Student Organization.
     * These get the CABEIHM Memo No.3 guideline pages.
     * Offices and External clients get the House Rules pages instead.
     */
    private function isCollegeOrStudentOrg(): bool
    {
        // office_type_id: 1=College, 2=Office, 3=Student Org, 4=External
        $tid = (string) ($this->data['office_type_id'] ?? '');
        // Also accept a string label stored in _client_type by older code
        $ct = strtolower((string) ($this->data['_client_type'] ?? ''));
        return in_array($tid, ['1', '3'], true)
            || in_array($ct, ['college', 'student_org', 'student organization'], true);
    }

    private function formatDateTimeRange(): string
    {
        $s = $this->data['start_datetime'] ?? null;
        $e = $this->data['end_datetime'] ?? null;
        if (!$s)
            return '—';
        $dateS = date('F j, Y', strtotime($s));
        $dateE = $e ? date('F j, Y', strtotime($e)) : $dateS;
        $start = date('g:i A', strtotime($s));
        $end = $e ? date('g:i A', strtotime($e)) : '—';

        if ($dateS === $dateE) {
            return $dateS . ', ' . $start . ' – ' . $end;
        } else {
            return $dateS . ' ' . $start . ' to ' . $dateE . ' ' . $end;
        }
    }

    /**
     * Returns [dateLine, timeLine] so the overlay can place them on two rows.
     * e.g. ['March 20, 2026', '1:00 PM – 10:00 PM']
     */
    private function formatDateTimeLines(): array
    {
        $s = $this->data['start_datetime'] ?? null;
        $e = $this->data['end_datetime'] ?? null;
        if (!$s)
            return ['—', ''];
        $dateS = date('F j, Y', strtotime($s));
        $dateE = $e ? date('F j, Y', strtotime($e)) : $dateS;
        $start = date('g:i A', strtotime($s));
        $end = $e ? date('g:i A', strtotime($e)) : '—';

        if ($dateS === $dateE) {
            return [$dateS, $start . ' – ' . $end];
        } else {
            return [$dateS . ' to ' . $dateE, $start . ' – ' . $end];
        }
    }

    private function parseMisc($raw): array
    {
        if (is_array($raw))
            return $raw;
        if (!$raw || $raw === 'null')
            return [];
        return json_decode($raw, true) ?: [];
    }

    private function miscSoundText(array $misc): string
    {
        if (empty($misc['basic_sound_system']))
            return 'No';
        $s = (int) ($misc['basic_sound_system']['speaker'] ?? 0);
        $m = (int) ($misc['basic_sound_system']['mic'] ?? 0);
        if ($s === 0 && $m === 0)
            return 'No';
        return $s . ' Speaker(s), ' . $m . ' Mic(s)';
    }

    private function miscQtyText(array $misc, string $key): string
    {
        $v = $misc[$key] ?? null;
        if ($v === null)
            return '—';
        if (is_array($v))
            $qty = (int) ($v['quantity'] ?? 0);
        else
            $qty = (int) $v;
        return $qty > 0 ? $qty . ' unit(s)' : '—';
    }

    private function miscViewBoardText(array $misc): string
    {
        return !empty($misc['view_board']) ? 'Yes (1 unit)' : 'No';
    }

    private function miscOthers(array $misc): string
    {
        $out = [];
        $known = [
            'basic_sound_system',
            'round_table',
            'banquet_chairs',
            'view_board',
            'rectangular_table',
            'mono_block_chairs',
            '_price_breakdown',
            '_estimated_total',
            '_client_type',
            '_terms_agreed_by',
            '_terms_position',
            '_terms_date',
        ];
        foreach ($misc as $k => $v) {
            if (in_array($k, $known, true))
                continue;
            $label = ucwords(str_replace('_', ' ', $k));
            if (is_array($v) && isset($v['quantity'])) {
                $out[] = $label . ': ' . (int) $v['quantity'];
            } elseif (is_numeric($v) && (int) $v > 0) {
                $out[] = $label . ': ' . (int) $v;
            }
        }
        return implode(', ', $out) ?: '—';
    }
}