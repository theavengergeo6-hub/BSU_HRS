<?php
/**
 * Function Room Reservation PDF Generator (TCPDF).
 * Generates a clean, professional PDF with reservation details.
 * Requires: composer require tecnickcom/tcpdf
 */

class FunctionRoomPDF {

    /** @var TCPDF */
    private $pdf;
    /** @var array */
    private $data;
    /** @var string */
    private $logoHostel;
    /** @var string */
    private $logoBsu;

    /**
     * @param array $reservationData Single associative array with keys from DB + venue_names (string), office_display (string), banquet_name (string), venue_setup_name (string)
     * @param string $logoHostelPath Full path to hostel.jpg
     * @param string $logoBsuPath Full path to bsu-logo.jpg
     */
    public function __construct(array $reservationData, $logoHostelPath = '', $logoBsuPath = '') {
        $this->data = $reservationData;
        $this->logoHostel = $logoHostelPath;
        $this->logoBsu = $logoBsuPath;
    }

    /**
     * Generate PDF and return raw content for attachment.
     * @return string PDF binary content
     */
    public function generate() {
        $autoload = dirname(__DIR__) . '/vendor/autoload.php';
        if (!file_exists($autoload)) {
            throw new RuntimeException('Composer autoload not found. Run: composer install');
        }
        require_once $autoload;

        $this->pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $this->pdf->SetCreator('BSU HRS');
        $this->pdf->SetAuthor('BatStateU Hostel');
        $this->pdf->SetTitle('Function Room Reservation - ' . ($this->data['booking_no'] ?? ''));
        $this->pdf->SetMargins(15, 18, 15);
        $this->pdf->SetAutoPageBreak(true, 18);
        $this->pdf->SetFont('helvetica', '', 10);
        $this->pdf->AddPage();

        $y = $this->pdf->GetY();

        // ---------- Header: logos and title ----------
        if ($this->logoBsu && file_exists($this->logoBsu)) {
            $this->pdf->Image($this->logoBsu, 15, $y, 22, 0, '', '', '', false, 300);
        }
        if ($this->logoHostel && file_exists($this->logoHostel)) {
            $this->pdf->Image($this->logoHostel, 165, $y, 25, 0, '', '', '', false, 300);
        }
        $this->pdf->SetY($y + 12);
        $this->pdf->SetFont('helvetica', 'B', 14);
        $this->pdf->Cell(0, 6, 'BATANGAS STATE UNIVERSITY', 0, 1, 'C');
        $this->pdf->SetFont('helvetica', 'B', 12);
        $this->pdf->Cell(0, 6, 'BatStateU Hostel', 0, 1, 'C');
        $this->pdf->SetFont('helvetica', 'B', 11);
        $this->pdf->SetTextColor(183, 28, 28);
        $this->pdf->Cell(0, 6, 'Booking Reference: ' . ($this->data['booking_no'] ?? 'N/A'), 0, 1, 'C');
        $this->pdf->SetTextColor(0, 0, 0);
        $this->pdf->Ln(4);

        // ---------- Reservation Details ----------
        $this->pdf->SetFont('helvetica', 'B', 10);
        $this->pdf->SetFillColor(240, 240, 240);
        $this->pdf->Cell(0, 7, 'Reservation Details', 0, 1, 'L', true);
        $this->pdf->SetFont('helvetica', '', 9);
        $this->tableRow('Requestor Name', $this->requestorName());
        $this->tableRow('Office/College', $this->data['office_display'] ?? '—');
        $this->tableRow('Contact Number', $this->data['contact_number'] ?? '—');
        $this->tableRow('Email Address', $this->data['email'] ?? '—');
        $this->pdf->Ln(3);

        // ---------- Event Details ----------
        $this->pdf->SetFont('helvetica', 'B', 10);
        $this->pdf->Cell(0, 7, 'Event Details', 0, 1, 'L', true);
        $this->pdf->SetFont('helvetica', '', 9);
        $this->tableRow('Activity Name', $this->data['activity_name'] ?? '—');
        $this->tableRow('Venue', $this->data['venue_names'] ?? '—');
        $this->tableRow('Date', $this->formatDate($this->data['start_datetime'] ?? ''));
        $this->tableRow('Time', $this->formatTimeRange());
        $this->tableRow('Venue Setup', $this->data['venue_setup_name'] ?? $this->data['banquet_name'] ?? '—');
        $this->tableRow('Number of Participants', (string)($this->data['participants_count'] ?? '—'));
        $this->pdf->Ln(3);

        // ---------- Miscellaneous Items ----------
        $this->pdf->SetFont('helvetica', 'B', 10);
        $this->pdf->Cell(0, 7, 'Miscellaneous Items', 0, 1, 'L', true);
        $this->pdf->SetFont('helvetica', '', 9);
        $misc = $this->miscTable();
        foreach ($misc as $label => $value) {
            $this->tableRow($label, $value);
        }
        $this->pdf->Ln(3);

        // ---------- Additional Instructions ----------
        $this->pdf->SetFont('helvetica', 'B', 10);
        $this->pdf->Cell(0, 7, 'Additional Instructions', 0, 1, 'L', true);
        $this->pdf->SetFont('helvetica', '', 9);
        $inst = trim($this->data['additional_instruction'] ?? '');
        $this->pdf->MultiCell(0, 5, $inst !== '' ? $inst : 'None.', 0, 'L');
        $this->pdf->Ln(4);

        // ---------- Footer: signature lines + generated stamp ----------
        $this->pdf->SetFont('helvetica', '', 8);
        $this->pdf->Cell(0, 5, '_________________________________________', 0, 1, 'L');
        $this->pdf->Cell(0, 4, 'Authorized Signature', 0, 1, 'L');
        $this->pdf->Ln(2);
        $this->pdf->SetFont('helvetica', '', 7);
        $this->pdf->SetTextColor(100, 100, 100);
        $this->pdf->Cell(0, 4, 'Generated on ' . date('F j, Y \a\t g:i A'), 0, 1, 'R');
        $this->pdf->SetTextColor(0, 0, 0);

        return $this->pdf->Output('', 'S');
    }

    private function requestorName() {
        $l = trim($this->data['last_name'] ?? '');
        $f = trim($this->data['first_name'] ?? '');
        $m = trim($this->data['middle_initial'] ?? '');
        $parts = array_filter([$l, $f]);
        $name = implode(', ', $parts);
        if ($m !== '') {
            $name .= ' ' . $m . '.';
        }
        return $name !== '' ? $name : '—';
    }

    private function formatDate($datetime) {
        if ($datetime === '' || $datetime === null) return '—';
        $ts = strtotime($datetime);
        return $ts ? date('F j, Y', $ts) : '—';
    }

    private function formatTimeRange() {
        $start = $this->data['start_datetime'] ?? null;
        $end = $this->data['end_datetime'] ?? null;
        if (!$start || !$end) return '—';
        $s = date('g:i A', strtotime($start));
        $e = date('g:i A', strtotime($end));
        $dur = $this->duration($start, $end);
        return $s . ' - ' . $e . ' (' . $dur . ')';
    }

    private function duration($start, $end) {
        $a = strtotime($start);
        $b = strtotime($end);
        if ($b <= $a) return '0 hours';
        $h = ($b - $a) / 3600;
        if ($h < 1) return round($h * 60) . ' min';
        return round($h, 1) . ' hour(s)';
    }

    private function tableRow($label, $value) {
        $this->pdf->Cell(55, 6, $label . ':', 0, 0, 'L');
        $this->pdf->Cell(0, 6, $value, 0, 1, 'L');
    }

    private function miscTable() {
        $raw = $this->data['miscellaneous_items'] ?? '{}';
        $misc = is_string($raw) ? (json_decode($raw, true) ?: []) : $raw;
        $out = [];
        $out['Basic Sound System'] = 'No';
        if (!empty($misc['basic_sound_system'])) {
            $s = (int)($misc['basic_sound_system']['speaker'] ?? 0);
            $m = (int)($misc['basic_sound_system']['mic'] ?? 0);
            $out['Basic Sound System'] = $s . ' speaker(s), ' . $m . ' microphone(s)';
        }
        $out['Round Table'] = $this->miscQty($misc, 'round_table') . ' unit(s)';
        $out['Banquet Chairs'] = $this->miscQty($misc, 'banquet_chairs') . ' unit(s)';
        $out['View Board'] = !empty($misc['view_board']['requested']) ? 'Yes' : 'No';
        $out['Rectangular Table'] = $this->miscQty($misc, 'rectangular_table') . ' unit(s)';
        return $out;
    }

    private function miscQty(array $misc, $key) {
        $v = $misc[$key] ?? null;
        if (is_array($v) && isset($v['quantity'])) return (string)(int)$v['quantity'];
        if (is_numeric($v)) return (string)(int)$v;
        return '0';
    }
}
