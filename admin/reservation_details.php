<?php
require_once __DIR__ . '/inc/db_config.php';
require_once __DIR__ . '/inc/essentials.php';
require_once __DIR__ . '/inc/auth.php';

if (!isAdminLoggedIn()) {
    redirect('index.php');
}

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    redirect('reservations.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_status = $_POST['status'] ?? '';
    $admin_remarks = clean($_POST['admin_remarks'] ?? '');
    $reservation_id = (int)($_POST['reservation_id'] ?? 0);

    if ($reservation_id === $id && in_array($new_status, ['pending', 'pencil_booked', 'approved', 'cancelled'])) {
        $remark_entry = "\n--- " . date("Y-m-d H:i:s") . " (" . ucfirst(str_replace('_', ' ', $new_status)) . ") ---\n" . $admin_remarks;
        $update_stmt = $conn->prepare("UPDATE facility_reservations SET status = ?, admin_remarks = CONCAT(IFNULL(admin_remarks, ''), ?) WHERE id = ?");
        $update_stmt->bind_param("ssi", $new_status, $remark_entry, $id);
        if ($update_stmt->execute()) {
            $_SESSION['success_message'] = "Reservation status updated successfully!";
        } else {
            $_SESSION['error_message'] = "Error updating status: " . $conn->error;
        }
        redirect("reservation_details.php?id=$id");
        exit;
    }
}

$query = "
    SELECT r.*, 
           ot.name as office_type_name, 
           o.name as office_name, 
           et.name as event_type_name, 
           vs.name as venue_setup_name, 
           b.name as banquet_style_name, 
           b.image as banquet_image 
    FROM facility_reservations r 
    LEFT JOIN office_types ot ON r.office_type_id = ot.id 
    LEFT JOIN offices o ON r.office_id = o.id 
    LEFT JOIN event_types et ON r.event_type_id = et.id 
    LEFT JOIN venue_setups vs ON r.venue_setup_id = vs.id 
    LEFT JOIN banquet b ON r.banquet_style_id = b.id 
    WHERE r.id = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error_message'] = "Reservation not found.";
    redirect('reservations.php');
}

$reservation = $result->fetch_assoc();
$stmt->close();

// Get all venues from reservation_venues pivot table
$all_venues = [];

$venue_stmt = $conn->prepare("
    SELECT v.id, v.name, v.floor, rv.start_datetime, rv.end_datetime
    FROM reservation_venues rv
    JOIN venues v ON rv.venue_id = v.id
    WHERE rv.reservation_id = ?
    ORDER BY rv.start_datetime ASC
");
$venue_stmt->bind_param("i", $id);
$venue_stmt->execute();
$venues_result = $venue_stmt->get_result();
while ($vrow = $venues_result->fetch_assoc()) {
    $all_venues[] = $vrow;
}
$venue_stmt->close();

// Fallback for old records
if (empty($all_venues)) {
    $fb_stmt = $conn->prepare("
        SELECT v.id, v.name, v.floor,
               r.start_datetime, r.end_datetime
        FROM facility_reservations r
        JOIN venues v ON r.venue_id = v.id
        WHERE r.id = ?
    ");
    $fb_stmt->bind_param("i", $id);
    $fb_stmt->execute();
    $fb_result = $fb_stmt->get_result();
    while ($vrow = $fb_result->fetch_assoc()) {
        $all_venues[] = $vrow;
    }
    $fb_stmt->close();
}

function getStatusBadgeClass($status) {
    switch (strtolower($status)) {
        case 'pending':       return 'status-pending';
        case 'pencil_booked': return 'status-pencil';
        case 'approved':      return 'status-approved';
        case 'cancelled':     return 'status-cancelled';
        case 'denied':        return 'status-denied';
        case 'completed':     return 'status-completed';
        default:              return 'status-default';
    }
}

$start = new DateTime($reservation['start_datetime']);
$end   = new DateTime($reservation['end_datetime']);
$interval = $start->diff($end);
$duration_hours = $interval->h + ($interval->days * 24);
if ($interval->i > 0) $duration_hours += $interval->i / 60;
$duration_text = round($duration_hours, 1) . " hours";

$page_title = "Reservation Details - " . htmlspecialchars($reservation['booking_no']);
include 'inc/header.php';
?>

<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap');

:root {
    --red:        #b71c1c;
    --red-dark:   #8b0000;
    --red-muted:  rgba(183,28,28,0.08);
    --bg:         #f4f5f7;
    --surface:    #ffffff;
    --border:     #e8eaed;
    --text-main:  #1a1f2e;
    --text-sub:   #6b7280;
    --text-label: #9ca3af;
    --radius-lg:  14px;
    --radius-md:  10px;
    --radius-sm:  6px;
    --shadow-sm:  0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
    --shadow-md:  0 4px 16px rgba(0,0,0,0.07), 0 1px 4px rgba(0,0,0,0.04);
    --transition: 0.18s ease;
}

*, *::before, *::after { box-sizing: border-box; }

body {
    background: var(--bg);
    font-family: 'DM Sans', sans-serif;
    color: var(--text-main);
}

.content-area {
    padding: 1.75rem 2rem;
    max-width: 1400px;
    margin: 0 auto;
}

/* ── Page header ── */
.page-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.5rem;
    gap: 1rem;
    flex-wrap: wrap;
}

.page-title {
    font-size: 1.35rem;
    font-weight: 700;
    color: var(--text-main);
    margin: 0;
    letter-spacing: -0.3px;
}

.page-title span { color: var(--red); }

.btn-back {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.55rem 1.1rem;
    background: var(--surface);
    color: var(--text-sub);
    border: 1px solid var(--border);
    border-radius: 50px;
    font-size: 0.85rem;
    font-weight: 500;
    text-decoration: none;
    transition: var(--transition);
}
.btn-back:hover {
    border-color: var(--red);
    color: var(--red);
    background: var(--red-muted);
}

/* ── Booking hero bar ── */
.booking-hero {
    background: linear-gradient(120deg, var(--red) 0%, var(--red-dark) 100%);
    border-radius: var(--radius-lg);
    padding: 1.25rem 1.75rem;
    margin-bottom: 1.75rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    flex-wrap: wrap;
    box-shadow: 0 6px 24px rgba(183,28,28,0.22);
}

.booking-hero-left { display: flex; flex-direction: column; gap: 0.2rem; }

.booking-no {
    font-family: 'DM Mono', monospace;
    font-size: 1.4rem;
    font-weight: 500;
    color: #fff;
    letter-spacing: 0.5px;
    line-height: 1.2;
}

.booking-meta {
    font-size: 0.8rem;
    color: rgba(255,255,255,0.72);
    letter-spacing: 0.2px;
}

.booking-hero-right {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 0.35rem;
}

.status-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.35rem 0.9rem;
    border-radius: 50px;
    font-size: 0.78rem;
    font-weight: 700;
    letter-spacing: 0.5px;
    text-transform: uppercase;
}
.status-pill::before {
    content: '';
    width: 6px; height: 6px;
    border-radius: 50%;
    background: currentColor;
    opacity: 0.7;
}
.status-pending   { background: #fff3cd; color: #92600a; }
.status-pencil    { background: #ede9fe; color: #5b21b6; }
.status-approved  { background: #dcfce7; color: #166534; }
.status-cancelled,
.status-denied    { background: #fee2e2; color: #991b1b; }
.status-completed { background: #dbeafe; color: #1d4ed8; }
.status-default   { background: #f3f4f6; color: #374151; }

.submitted-on {
    font-size: 0.75rem;
    color: rgba(255,255,255,0.6);
}

/* ── Layout: left main + right sidebar ── */
.rd-layout {
    display: grid;
    grid-template-columns: 1fr 340px;
    gap: 1.5rem;
    align-items: start;
}

.rd-main { display: flex; flex-direction: column; gap: 1.25rem; }
.rd-sidebar { display: flex; flex-direction: column; gap: 1.25rem; }

/* ── Cards ── */
.rd-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    transition: box-shadow var(--transition), border-color var(--transition);
}
.rd-card:hover {
    box-shadow: var(--shadow-md);
    border-color: #d1d5db;
}

.rd-card-header {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    padding: 0.9rem 1.25rem;
    border-bottom: 1px solid var(--border);
    background: #fafafa;
}

.rd-card-header .ch-icon {
    width: 30px; height: 30px;
    display: flex; align-items: center; justify-content: center;
    background: var(--red-muted);
    color: var(--red);
    border-radius: var(--radius-sm);
    font-size: 0.9rem;
    flex-shrink: 0;
}

.rd-card-header .ch-title {
    font-size: 0.82rem;
    font-weight: 700;
    color: var(--text-main);
    text-transform: uppercase;
    letter-spacing: 0.6px;
}

.rd-card-body { padding: 1.25rem; }

/* ── Field rows ── */
.field-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0;
}

.field-item {
    padding: 0.75rem 0;
    border-bottom: 1px solid #f3f4f6;
}

.field-item:nth-child(odd) { padding-right: 1.5rem; }
.field-item:nth-child(even) { padding-left: 1.5rem; border-left: 1px solid #f3f4f6; }

.field-item.full {
    grid-column: 1 / -1;
    padding-right: 0;
    padding-left: 0;
    border-left: none;
}

.field-item:last-child,
.field-item:nth-last-child(2):nth-child(odd) { border-bottom: none; }

.f-label {
    font-size: 0.68rem;
    font-weight: 600;
    color: var(--text-label);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.2rem;
    display: flex;
    align-items: center;
    gap: 0.3rem;
}

.f-label i { font-size: 0.75rem; color: var(--red); }

.f-value {
    font-size: 0.9rem;
    font-weight: 500;
    color: var(--text-main);
    line-height: 1.4;
}

.f-value.muted { color: var(--text-sub); font-style: italic; font-weight: 400; }

/* ── Venues ── */
.venue-date-group {
    margin-bottom: 0.75rem;
}

.venue-date-group:last-child { margin-bottom: 0; }

.vdg-date {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    font-size: 0.75rem;
    font-weight: 700;
    color: var(--red);
    text-transform: uppercase;
    letter-spacing: 0.4px;
    background: var(--red-muted);
    padding: 0.25rem 0.65rem;
    border-radius: 50px;
    margin-bottom: 0.6rem;
}

.venue-row {
    display: grid;
    grid-template-columns: 1fr auto;
    align-items: center;
    gap: 0.75rem;
    padding: 0.6rem 0.9rem;
    background: #f9fafb;
    border-radius: var(--radius-md);
    border: 1px solid var(--border);
    margin-bottom: 0.4rem;
}
.venue-row:last-child { margin-bottom: 0; }

.vr-name {
    font-size: 0.88rem;
    font-weight: 600;
    color: var(--text-main);
}

.vr-floor {
    font-size: 0.73rem;
    color: var(--text-sub);
    font-weight: 400;
}

.vr-time {
    font-family: 'DM Mono', monospace;
    font-size: 0.78rem;
    font-weight: 500;
    color: var(--red);
    white-space: nowrap;
    background: white;
    border: 1px solid #fecaca;
    padding: 0.25rem 0.6rem;
    border-radius: var(--radius-sm);
}

.venue-stats {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.75rem;
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid var(--border);
}

.vstat {
    text-align: center;
    background: #f9fafb;
    border-radius: var(--radius-md);
    padding: 0.7rem;
    border: 1px solid var(--border);
}

.vstat-num {
    font-size: 1.6rem;
    font-weight: 700;
    color: var(--red);
    line-height: 1.1;
}

.vstat-label {
    font-size: 0.68rem;
    color: var(--text-label);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-top: 0.15rem;
}

/* ── Misc items ── */
.misc-chip-list {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.misc-chip {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.55rem 0.85rem;
    background: #f9fafb;
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    font-size: 0.85rem;
}

.mc-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    color: var(--text-main);
}

.mc-label i { color: var(--red); font-size: 0.85rem; }

.mc-value {
    font-size: 0.8rem;
    color: var(--text-sub);
    background: white;
    border: 1px solid var(--border);
    padding: 0.2rem 0.6rem;
    border-radius: 50px;
}

/* ── Sidebar cards ── */
/* Admin actions */
.rd-card-header.header-action .ch-icon {
    background: #fff3f3;
}

.form-field { margin-bottom: 1rem; }

.form-field:last-of-type { margin-bottom: 0; }

.fl-label {
    font-size: 0.72rem;
    font-weight: 700;
    color: var(--text-sub);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.35rem;
    display: block;
}

.fl-select, .fl-textarea {
    width: 100%;
    border: 1.5px solid var(--border);
    border-radius: var(--radius-md);
    padding: 0.6rem 0.8rem;
    font-size: 0.875rem;
    font-family: 'DM Sans', sans-serif;
    color: var(--text-main);
    background: white;
    transition: border-color var(--transition);
    outline: none;
}

.fl-select:focus, .fl-textarea:focus {
    border-color: var(--red);
    box-shadow: 0 0 0 3px rgba(183,28,28,0.08);
}

.fl-textarea { resize: vertical; min-height: 80px; }

.current-status-tag {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    font-size: 0.72rem;
    color: var(--text-sub);
    margin-top: 0.3rem;
}

.current-status-tag strong { color: var(--text-main); }

.btn-update {
    width: 100%;
    background: linear-gradient(135deg, var(--red), var(--red-dark));
    color: white;
    border: none;
    padding: 0.7rem 1rem;
    border-radius: var(--radius-md);
    font-size: 0.875rem;
    font-weight: 600;
    font-family: 'DM Sans', sans-serif;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.4rem;
    transition: all var(--transition);
    margin-top: 1rem;
    box-shadow: 0 3px 10px rgba(183,28,28,0.25);
}

.btn-update:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(183,28,28,0.35);
}

.btn-update:active { transform: translateY(0); }

/* Remarks history */
.remark-entry {
    padding: 0.75rem;
    background: #f9fafb;
    border-radius: var(--radius-md);
    border: 1px solid var(--border);
    margin-bottom: 0.6rem;
    font-size: 0.82rem;
    line-height: 1.55;
}
.remark-entry:last-child { margin-bottom: 0; }

.remark-header {
    font-size: 0.7rem;
    font-weight: 700;
    color: var(--red);
    text-transform: uppercase;
    letter-spacing: 0.4px;
    margin-bottom: 0.35rem;
    display: flex;
    align-items: center;
    gap: 0.3rem;
}

.remark-text {
    color: var(--text-sub);
    white-space: pre-wrap;
}

/* Instructions */
.instructions-text {
    font-size: 0.875rem;
    line-height: 1.6;
    color: var(--text-sub);
    background: #f9fafb;
    border-radius: var(--radius-md);
    padding: 0.85rem 1rem;
    border: 1px solid var(--border);
    white-space: pre-wrap;
}

/* Banquet thumbnail */
.banquet-preview {
    display: flex;
    align-items: center;
    gap: 0.6rem;
}
.banquet-thumb {
    width: 36px; height: 36px;
    object-fit: cover;
    border-radius: var(--radius-sm);
    border: 1px solid var(--border);
    flex-shrink: 0;
}

/* Responsive */
@media (max-width: 1100px) {
    .rd-layout { grid-template-columns: 1fr; }
    .rd-sidebar { flex-direction: row; flex-wrap: wrap; }
    .rd-sidebar .rd-card { flex: 1 1 280px; }
}

@media (max-width: 640px) {
    .content-area { padding: 1rem; }
    .field-grid { grid-template-columns: 1fr; }
    .field-item:nth-child(even) { border-left: none; padding-left: 0; }
    .booking-no { font-size: 1.1rem; }
    .rd-sidebar { flex-direction: column; }
}
</style>

<div class="content-area">

    <!-- Page header -->
    <div class="page-header">
        <h1 class="page-title">
            <i class="bi bi-file-earmark-text me-1" style="color:var(--red)"></i>
            Reservation <span>Details</span>
        </h1>
        <a href="reservations.php" class="btn-back">
            <i class="bi bi-arrow-left"></i> Back to Reservations
        </a>
    </div>

    <?php display_messages(); ?>

    <!-- Booking hero bar -->
    <div class="booking-hero">
        <div class="booking-hero-left">
            <div class="booking-no"><?= htmlspecialchars($reservation['booking_no']) ?></div>
            <div class="booking-meta">
                Reservation No: <?= htmlspecialchars($reservation['reservation_no']) ?>
            </div>
        </div>
        <div class="booking-hero-right">
            <span class="status-pill <?= getStatusBadgeClass($reservation['status']) ?>">
                <?= htmlspecialchars(str_replace('_', ' ', $reservation['status'])) ?>
            </span>
            <span class="submitted-on">
                <i class="bi bi-send me-1"></i>Submitted <?= date("M d, Y", strtotime($reservation['created_at'])) ?>
            </span>
        </div>
    </div>

    <!-- Main layout: left content + right sidebar -->
    <div class="rd-layout">

        <!-- ════════ LEFT MAIN COLUMN ════════ -->
        <div class="rd-main">

            <!-- Card 1: Requester + Event Info combined -->
            <div class="rd-card">
                <div class="rd-card-header">
                    <div class="ch-icon"><i class="bi bi-person-circle"></i></div>
                    <div class="ch-title">Requester Information</div>
                </div>
                <div class="rd-card-body">
                    <div class="field-grid">
                        <div class="field-item">
                            <div class="f-label"><i class="bi bi-person-badge"></i> Full Name</div>
                            <div class="f-value"><?= htmlspecialchars($reservation['last_name'] . ', ' . $reservation['first_name'] . ' ' . $reservation['middle_initial']) ?></div>
                        </div>
                        <div class="field-item">
                            <div class="f-label"><i class="bi bi-building"></i> Office</div>
                            <div class="f-value">
                                <?php
                                echo htmlspecialchars($reservation['office_type_name'] ?? 'N/A');
                                if (($reservation['office_type_name'] ?? '') === 'External') {
                                    echo ' — ' . htmlspecialchars($reservation['external_office_name'] ?? '');
                                } else {
                                    if (!empty($reservation['office_name'])) echo ' — ' . htmlspecialchars($reservation['office_name']);
                                }
                                ?>
                            </div>
                        </div>
                        <div class="field-item">
                            <div class="f-label"><i class="bi bi-envelope"></i> Email</div>
                            <div class="f-value"><?= htmlspecialchars($reservation['email']) ?></div>
                        </div>
                        <div class="field-item">
                            <div class="f-label"><i class="bi bi-telephone"></i> Contact</div>
                            <div class="f-value"><?= htmlspecialchars($reservation['contact_number']) ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Event Details -->
            <div class="rd-card">
                <div class="rd-card-header">
                    <div class="ch-icon"><i class="bi bi-calendar-event"></i></div>
                    <div class="ch-title">Event Details</div>
                </div>
                <div class="rd-card-body">
                    <div class="field-grid">
                        <div class="field-item">
                            <div class="f-label"><i class="bi bi-flag"></i> Activity</div>
                            <div class="f-value"><?= htmlspecialchars($reservation['activity_name']) ?></div>
                        </div>
                        <div class="field-item">
                            <div class="f-label"><i class="bi bi-tag"></i> Event Type</div>
                            <div class="f-value"><?= htmlspecialchars($reservation['event_type_name'] ?? 'N/A') ?></div>
                        </div>
                        <div class="field-item">
                            <div class="f-label"><i class="bi bi-people"></i> Participants</div>
                            <div class="f-value"><?= htmlspecialchars($reservation['participants_count']) ?></div>
                        </div>
                        <div class="field-item">
                            <div class="f-label"><i class="bi bi-hourglass-split"></i> Duration (First Venue)</div>
                            <div class="f-value"><?= $duration_text ?></div>
                        </div>
                        <div class="field-item">
                            <div class="f-label"><i class="bi bi-easel2"></i> Venue Setup</div>
                            <div class="f-value <?= empty($reservation['venue_setup_name']) ? 'muted' : '' ?>">
                                <?= !empty($reservation['venue_setup_name']) ? htmlspecialchars($reservation['venue_setup_name']) : 'Not specified' ?>
                            </div>
                        </div>
                        <div class="field-item">
                            <div class="f-label"><i class="bi bi-grid-1x2"></i> Banquet Style</div>
                            <div class="f-value">
                                <?php if (!empty($reservation['banquet_style_name'])): ?>
                                    <div class="banquet-preview">
                                        <?php if (!empty($reservation['banquet_image'])): ?>
                                            <img src="../assets/images/banquet/<?= htmlspecialchars($reservation['banquet_image']) ?>"
                                                 alt="<?= htmlspecialchars($reservation['banquet_style_name']) ?>"
                                                 class="banquet-thumb"
                                                 onerror="this.style.display='none'">
                                        <?php endif; ?>
                                        <span><?= htmlspecialchars($reservation['banquet_style_name']) ?></span>
                                    </div>
                                <?php else: ?>
                                    <span class="muted">Not selected</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 3: Venues -->
            <div class="rd-card">
                <div class="rd-card-header">
                    <div class="ch-icon"><i class="bi bi-pin-map"></i></div>
                    <div class="ch-title">Venues &nbsp;<span style="font-weight:400;color:var(--text-sub)">(<?= count($all_venues) ?>)</span></div>
                </div>
                <div class="rd-card-body">
                    <?php if (!empty($all_venues)):
                        $venues_by_date = [];
                        foreach ($all_venues as $venue) {
                            $dk = date("M j, Y", strtotime($venue['start_datetime']));
                            $venues_by_date[$dk][] = $venue;
                        }
                    ?>
                        <?php foreach ($venues_by_date as $date_key => $dvs): ?>
                            <div class="venue-date-group">
                                <div class="vdg-date">
                                    <i class="bi bi-calendar3"></i><?= $date_key ?>
                                </div>
                                <?php foreach ($dvs as $v): ?>
                                    <div class="venue-row">
                                        <div>
                                            <div class="vr-name"><?= htmlspecialchars($v['name']) ?></div>
                                            <div class="vr-floor"><?= htmlspecialchars($v['floor']) ?></div>
                                        </div>
                                        <div class="vr-time">
                                            <?= date("g:i A", strtotime($v['start_datetime'])) ?> — <?= date("g:i A", strtotime($v['end_datetime'])) ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>

                        <div class="venue-stats">
                            <div class="vstat">
                                <div class="vstat-num"><?= count($all_venues) ?></div>
                                <div class="vstat-label">Total Venues</div>
                            </div>
                            <div class="vstat">
                                <div class="vstat-num"><?= count($venues_by_date) ?></div>
                                <div class="vstat-label">Total Days</div>
                            </div>
                        </div>
                    <?php else: ?>
                        <p class="text-muted text-center py-3 mb-0"><i class="bi bi-exclamation-circle me-1"></i>No venue information available.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Card 4: Miscellaneous Items -->
            <div class="rd-card">
                <div class="rd-card-header">
                    <div class="ch-icon"><i class="bi bi-box-seam"></i></div>
                    <div class="ch-title">Miscellaneous Items</div>
                </div>
                <div class="rd-card-body">
                    <?php
                    $misc_items = json_decode($reservation['miscellaneous_items'], true);
                    $pricing_client_type = $misc_items['_client_type'] ?? null;
                    $pricing_total_amount = $misc_items['_estimated_total'] ?? null;
                    $pricing_breakdown = $misc_items['_price_breakdown'] ?? null;
                    
                    // Remove internal pricing fields from regular misc items early
                    if (is_array($misc_items)) {
                        unset($misc_items['_client_type'], $misc_items['_estimated_total'], $misc_items['_price_breakdown']);
                    }

                    if (json_last_error() === JSON_ERROR_NONE && !empty($misc_items)):
                    ?>
                        <div class="misc-chip-list">
                        <?php foreach ($misc_items as $key => $item):
                            $label = ucwords(str_replace('_', ' ', $key));
                            $detail = '';
                            if (is_array($item)) {
                                $parts = [];
                                foreach ($item as $sk => $sv) {
                                    if (is_array($sv)) continue; // safeguard
                                    if ($sk === 'requested' && $sv === true) $parts[] = 'Yes';
                                    else $parts[] = ucfirst($sk) . ': ' . htmlspecialchars($sv);
                                }
                                $detail = implode(' · ', $parts);
                            } else {
                                $detail = htmlspecialchars($item);
                            }
                        ?>
                            <div class="misc-chip">
                                <div class="mc-label">
                                    <i class="bi bi-check2-circle"></i>
                                    <?= htmlspecialchars($label) ?>
                                </div>
                                <div class="mc-value"><?= $detail ?></div>
                            </div>
                        <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted text-center py-3 mb-0">No miscellaneous items requested.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Card: Rental Cost Breakdown (External Only) -->
            <?php if ($pricing_client_type === 'External' && $pricing_breakdown): ?>
            <div class="rd-card" style="border-left: 4px solid var(--red);">
                <div class="rd-card-header" style="background:#fffcfc;">
                    <div class="ch-icon"><i class="bi bi-receipt"></i></div>
                    <div class="ch-title">Rental Cost Breakdown</div>
                </div>
                <div class="rd-card-body">
                    <div style="background: linear-gradient(135deg, #fff5f5, #ffe8e8); border-radius: 10px; padding: 1.25rem;">
                        <div style="display: flex; flex-direction: column; gap: 0.6rem;">
                            <?php foreach ($pricing_breakdown as $pb): ?>
                                <div style="display: flex; justify-content: space-between; font-size: 0.85rem; border-bottom: 1px dashed #f5c6cb; padding-bottom: 0.6rem;">
                                    <?php if (isset($pb['venue_id'])): 
                                        $vname = ''; 
                                        foreach ($all_venues as $v) if ($v['id'] == $pb['venue_id']) { $vname = $v['name']; break; }
                                    ?>
                                        <span>
                                            <strong><?= htmlspecialchars($vname) ?></strong><br>
                                            <span style="color:var(--text-sub); font-size:0.75rem;">
                                                <?= date("M j, Y", strtotime($pb['date'])) ?> (<?= $pb['hours'] ?>h, <?= htmlspecialchars($pb['rate_type']) ?>)
                                            </span>
                                        </span>
                                    <?php else: ?>
                                        <span><strong><?= htmlspecialchars($pb['rate_type']) ?></strong></span>
                                    <?php endif; ?>
                                    <strong style="color: var(--text-main);">₱<?= number_format($pb['cost'], 2) ?></strong>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-top: 1rem; padding-top: 1rem; border-top: 2px solid #f5c6cb;">
                            <strong style="color: var(--red-dark); font-size: 1rem;">Total Amount</strong>
                            <strong style="color: var(--red); font-size: 1.2rem;">₱<?= number_format($pricing_total_amount, 2) ?></strong>
                        </div>
                    </div>
                </div>
            </div>
            <?php elseif ($reservation['office_type_name'] !== 'External'): ?>
            <div class="rd-card" style="border-left: 4px solid #166534;">
                <div class="rd-card-header" style="background:#f0fdf4;">
                    <div class="ch-icon" style="background:#dcfce7; color:#166534;"><i class="bi bi-patch-check"></i></div>
                    <div class="ch-title" style="color:#166534;">Internal Booking</div>
                </div>
                <div class="rd-card-body">
                    <div style="display:flex; align-items:center; gap: 1rem; color:#166534; font-weight:500;">
                        <i class="bi bi-info-circle fs-4"></i>
                        <span>This is an internal university reservation. The use of function rooms is <strong style="text-transform:uppercase;">free of charge</strong>.</span>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Card 5: Instructions -->
            <div class="rd-card">
                <div class="rd-card-header">
                    <div class="ch-icon"><i class="bi bi-chat-text"></i></div>
                    <div class="ch-title">Additional Instructions</div>
                </div>
                <div class="rd-card-body">
                    <?php if (!empty($reservation['additional_instruction'])): ?>
                        <div class="instructions-text">
                            <?= nl2br(htmlspecialchars($reservation['additional_instruction'])) ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted text-center py-3 mb-0">No additional instructions provided.</p>
                    <?php endif; ?>
                </div>
            </div>

        </div><!-- /rd-main -->

        <!-- ════════ RIGHT SIDEBAR ════════ -->
        <div class="rd-sidebar">

            <!-- Sidebar card: Admin Actions -->
            <div class="rd-card">
                <div class="rd-card-header header-action">
                    <div class="ch-icon"><i class="bi bi-pencil-square"></i></div>
                    <div class="ch-title">Admin Actions</div>
                </div>
                <div class="rd-card-body">
                    <form id="statusUpdateForm" method="POST">
                        <input type="hidden" name="reservation_id" value="<?= $id ?>">

                        <div class="form-field">
                            <label class="fl-label"><i class="bi bi-tag me-1"></i> Update Status</label>
                            <select class="fl-select" id="status" name="status">
                                <option value="pending"       <?= $reservation['status'] === 'pending'       ? 'selected' : '' ?>>Pending</option>
                                <option value="pencil_booked" <?= $reservation['status'] === 'pencil_booked' ? 'selected' : '' ?>>Pencil Booked</option>
                                <option value="approved"      <?= $reservation['status'] === 'approved'      ? 'selected' : '' ?>>Approved</option>
                                <option value="cancelled"     <?= $reservation['status'] === 'cancelled'     ? 'selected' : '' ?>>Cancelled</option>
                            </select>
                            <div class="current-status-tag">
                                <i class="bi bi-info-circle"></i>
                                Current: <strong><?= str_replace('_', ' ', ucwords($reservation['status'])) ?></strong>
                            </div>
                        </div>

                        <div class="form-field">
                            <label class="fl-label"><i class="bi bi-chat me-1"></i> Admin Remarks</label>
                            <textarea class="fl-textarea" id="admin_remarks" name="admin_remarks"
                                rows="4" placeholder="Add your remarks here..." required></textarea>
                        </div>

                        <button type="submit" class="btn-update">
                            <i class="bi bi-check-circle"></i> Update Status
                        </button>
                    </form>
                </div>
            </div>

            <!-- Sidebar card: Remarks History -->
            <div class="rd-card">
                <div class="rd-card-header">
                    <div class="ch-icon"><i class="bi bi-clock-history"></i></div>
                    <div class="ch-title">Remarks History</div>
                </div>
                <div class="rd-card-body">
                    <?php if (!empty($reservation['admin_remarks'])):
                        // Parse remark entries by the "--- DATE (Status) ---" separator
                        $raw = trim($reservation['admin_remarks']);
                        $entries = preg_split('/\n(?=---\s)/', $raw);
                    ?>
                        <?php foreach (array_reverse($entries) as $entry):
                            $entry = trim($entry);
                            if (empty($entry)) continue;
                            preg_match('/---\s*(.*?)\s*\((.*?)\)\s*---/', $entry, $m);
                            $timestamp = $m[1] ?? '';
                            $status_label = $m[2] ?? '';
                            $remark_body = trim(preg_replace('/---.*?---/', '', $entry));
                        ?>
                            <div class="remark-entry">
                                <?php if ($timestamp): ?>
                                    <div class="remark-header">
                                        <i class="bi bi-clock"></i>
                                        <?= htmlspecialchars($timestamp) ?>
                                        <?php if ($status_label): ?>
                                            &nbsp;·&nbsp; <?= htmlspecialchars($status_label) ?>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                                <div class="remark-text"><?= nl2br(htmlspecialchars($remark_body)) ?></div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted text-center py-3 mb-0">No remarks have been added yet.</p>
                    <?php endif; ?>
                </div>
            </div>

        </div><!-- /rd-sidebar -->

    </div><!-- /rd-layout -->
</div><!-- /content-area -->

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('statusUpdateForm');
    if (!form) return;
    
    form.addEventListener('submit', function(e) {
        const statusSelect = document.getElementById('status');
        const remarksText  = document.getElementById('admin_remarks');
        const currentStatus = '<?= $reservation['status'] ?>';

        if (statusSelect.value === currentStatus) {
            e.preventDefault();
            alert('Please select a different status than the current one.');
            return;
        }
        
        if (remarksText.value.trim() === '') {
            e.preventDefault();
            alert('Admin remarks are required when updating reservation status.');
            return;
        }
        
        let statusDisplay = statusSelect.value.replace('_', ' ').toUpperCase();
        if (!confirm('Are you sure you want to change this reservation to ' + statusDisplay + '?')) {
            e.preventDefault();
        }
    });
});
</script>

<?php include 'inc/footer.php'; ?>