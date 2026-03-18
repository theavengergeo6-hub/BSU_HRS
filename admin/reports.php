<?php
/**
 * admin/reports.php
 * Comprehensive reporting dashboard for BSU Hostel Management System.
 */
require_once __DIR__ . '/inc/auth.php';
requireAdminLogin();
$pageTitle = 'Reports';
require_once __DIR__ . '/inc/header.php';

// ── Date filter ───────────────────────────────────────────────────────────────
$month = (int)($_GET['month'] ?? date('m'));
$year  = (int)($_GET['year']  ?? date('Y'));
$month = max(1, min(12, $month));
$year  = max(2020, min(date('Y') + 1, $year));
$from  = sprintf('%04d-%02d-01', $year, $month);
$to    = date('Y-m-t', strtotime($from));

// ── Helper: safe query ────────────────────────────────────────────────────────
function rep_q(mysqli $c, string $sql): ?mysqli_result {
    $r = $c->query($sql);
    return ($r instanceof mysqli_result) ? $r : null;
}

// ══════════════════════════════════════════════════════════════════════════════
// FUNCTION ROOM STATS
// ══════════════════════════════════════════════════════════════════════════════
$fr_total = (int)(rep_q($conn,"SELECT COUNT(*) c FROM facility_reservations WHERE start_datetime BETWEEN '$from' AND '$to 23:59:59'")?->fetch_assoc()['c'] ?? 0);
$fr_pending   = (int)(rep_q($conn,"SELECT COUNT(*) c FROM facility_reservations WHERE status='pending'   AND start_datetime BETWEEN '$from' AND '$to 23:59:59'")?->fetch_assoc()['c'] ?? 0);
$fr_approved  = (int)(rep_q($conn,"SELECT COUNT(*) c FROM facility_reservations WHERE status='approved'  AND start_datetime BETWEEN '$from' AND '$to 23:59:59'")?->fetch_assoc()['c'] ?? 0);
$fr_cancelled = (int)(rep_q($conn,"SELECT COUNT(*) c FROM facility_reservations WHERE status IN('cancelled','denied') AND start_datetime BETWEEN '$from' AND '$to 23:59:59'")?->fetch_assoc()['c'] ?? 0);
$fr_participants = (int)(rep_q($conn,"SELECT IFNULL(SUM(participants_count),0) s FROM facility_reservations WHERE status='approved' AND start_datetime BETWEEN '$from' AND '$to 23:59:59'")?->fetch_assoc()['s'] ?? 0);

// ══════════════════════════════════════════════════════════════════════════════
// GUEST ROOM STATS
// ══════════════════════════════════════════════════════════════════════════════
$gr_total = (int)(rep_q($conn,"SELECT COUNT(*) c FROM guest_room_reservations WHERE deleted=0 AND check_in_date BETWEEN '$from' AND '$to'")?->fetch_assoc()['c'] ?? 0);
$gr_pending  = (int)(rep_q($conn,"SELECT COUNT(*) c FROM guest_room_reservations WHERE deleted=0 AND status='pending'    AND check_in_date BETWEEN '$from' AND '$to'")?->fetch_assoc()['c'] ?? 0);
$gr_confirmed= (int)(rep_q($conn,"SELECT COUNT(*) c FROM guest_room_reservations WHERE deleted=0 AND status='confirmed'  AND check_in_date BETWEEN '$from' AND '$to'")?->fetch_assoc()['c'] ?? 0);
$gr_checkedin= (int)(rep_q($conn,"SELECT COUNT(*) c FROM guest_room_reservations WHERE deleted=0 AND status='checked_in' AND check_in_date BETWEEN '$from' AND '$to'")?->fetch_assoc()['c'] ?? 0);
$gr_done     = (int)(rep_q($conn,"SELECT COUNT(*) c FROM guest_room_reservations WHERE deleted=0 AND status='checked_out' AND check_in_date BETWEEN '$from' AND '$to'")?->fetch_assoc()['c'] ?? 0);
$gr_cancelled= (int)(rep_q($conn,"SELECT COUNT(*) c FROM guest_room_reservations WHERE deleted=0 AND status='cancelled'  AND check_in_date BETWEEN '$from' AND '$to'")?->fetch_assoc()['c'] ?? 0);
$gr_revenue  = (float)(rep_q($conn,"SELECT IFNULL(SUM(total_amount),0) s FROM guest_room_reservations WHERE deleted=0 AND status IN('confirmed','checked_in','checked_out') AND check_in_date BETWEEN '$from' AND '$to'")?->fetch_assoc()['s'] ?? 0);
$gr_guests   = (int)(rep_q($conn,"SELECT IFNULL(SUM(total_guests),0) s FROM guest_room_reservations WHERE deleted=0 AND check_in_date BETWEEN '$from' AND '$to'")?->fetch_assoc()['s'] ?? 0);

// ── Guest room revenue by room ────────────────────────────────────────────────
$grr_by_room = [];
$r = rep_q($conn,"
    SELECT IFNULL(g.room_name,'Unknown') AS room, COUNT(*) AS bookings,
           IFNULL(SUM(gr.total_amount),0) AS revenue
    FROM guest_room_reservations gr
    LEFT JOIN guest_rooms g ON gr.guest_room_id = g.id
    WHERE gr.deleted=0 AND gr.status IN('confirmed','checked_in','checked_out')
      AND gr.check_in_date BETWEEN '$from' AND '$to'
    GROUP BY g.id, g.room_name ORDER BY revenue DESC
");
if ($r) while ($row = $r->fetch_assoc()) $grr_by_room[] = $row;

// ── Function room bookings by venue ──────────────────────────────────────────
$fr_by_venue = [];
$r = rep_q($conn,"
    SELECT v.name AS venue, COUNT(*) AS bookings
    FROM reservation_venues rv
    JOIN venues v ON rv.venue_id = v.id
    JOIN facility_reservations fr ON rv.reservation_id = fr.id
    WHERE fr.start_datetime BETWEEN '$from' AND '$to 23:59:59'
    GROUP BY v.id, v.name ORDER BY bookings DESC
");
if ($r) while ($row = $r->fetch_assoc()) $fr_by_venue[] = $row;

// ── Function room by event type ───────────────────────────────────────────────
$fr_by_event = [];
$r = rep_q($conn,"
    SELECT IFNULL(et.name, fr.event_type, 'Unknown') AS event_type, COUNT(*) AS cnt
    FROM facility_reservations fr
    LEFT JOIN event_types et ON fr.event_type_id = et.id
    WHERE fr.start_datetime BETWEEN '$from' AND '$to 23:59:59'
    GROUP BY et.id ORDER BY cnt DESC LIMIT 8
");
if ($r) while ($row = $r->fetch_assoc()) $fr_by_event[] = $row;

// Fallback if JOIN fails
if (empty($fr_by_event)) {
    $r = rep_q($conn,"
        SELECT IFNULL(event_type,'Unknown') AS event_type, COUNT(*) AS cnt
        FROM facility_reservations
        WHERE start_datetime BETWEEN '$from' AND '$to 23:59:59'
        GROUP BY event_type ORDER BY cnt DESC LIMIT 8
    ");
    if ($r) while ($row = $r->fetch_assoc()) $fr_by_event[] = $row;
}

// ── Daily bookings for the selected month (for bar chart) ─────────────────────
$daily_fr   = array_fill(1, (int)date('t', strtotime($from)), 0);
$daily_gr   = array_fill(1, (int)date('t', strtotime($from)), 0);
$r = rep_q($conn,"SELECT DAY(start_datetime) d, COUNT(*) c FROM facility_reservations WHERE start_datetime BETWEEN '$from' AND '$to 23:59:59' GROUP BY d");
if ($r) while ($row = $r->fetch_assoc()) $daily_fr[(int)$row['d']] = (int)$row['c'];
$r = rep_q($conn,"SELECT DAY(check_in_date) d, COUNT(*) c FROM guest_room_reservations WHERE deleted=0 AND check_in_date BETWEEN '$from' AND '$to' GROUP BY d");
if ($r) while ($row = $r->fetch_assoc()) $daily_gr[(int)$row['d']] = (int)$row['c'];

// ── Recent function room reservations ─────────────────────────────────────────
$recent_fr = [];
$r = rep_q($conn,"
    SELECT fr.booking_no, CONCAT(fr.last_name,', ',fr.first_name) AS name,
           fr.activity_name, fr.start_datetime, fr.status, fr.id
    FROM facility_reservations fr
    ORDER BY fr.created_at DESC LIMIT 8
");
if ($r) while ($row = $r->fetch_assoc()) $recent_fr[] = $row;

// ── Recent guest room reservations ───────────────────────────────────────────
$recent_gr = [];
$r = rep_q($conn,"
    SELECT gr.booking_no, gr.guest_name, g.room_name,
           gr.check_in_date, gr.check_out_date, gr.status, gr.total_amount, gr.id
    FROM guest_room_reservations gr
    LEFT JOIN guest_rooms g ON gr.guest_room_id = g.id
    WHERE gr.deleted=0
    ORDER BY gr.created_at DESC LIMIT 8
");
if ($r) while ($row = $r->fetch_assoc()) $recent_gr[] = $row;

// Month names for selector
$months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
$currentMonthName = $months[$month - 1];
?>

<style>
.reports-grid { display: grid; grid-template-columns: repeat(auto-fit,minmax(240px,1fr)); gap: 1.25rem; margin-bottom: 2rem; }
.rpt-card { background: white; border-radius: 14px; padding: 1.4rem 1.6rem; box-shadow: 0 2px 12px rgba(0,0,0,0.05); border-left: 5px solid var(--bsu-red); }
.rpt-card.blue  { border-color: #1565c0; }
.rpt-card.green { border-color: #2e7d32; }
.rpt-card.orange { border-color: #e65100; }
.rpt-card.purple { border-color: #6a1b9a; }
.rpt-card-label { font-size: .78rem; color: #888; font-weight: 600; text-transform: uppercase; letter-spacing: .4px; }
.rpt-card-value { font-size: 2rem; font-weight: 700; color: #212529; margin: .2rem 0; }
.rpt-card-sub   { font-size: .8rem; color: #aaa; }
.rpt-section { background: white; border-radius: 14px; padding: 1.5rem; box-shadow: 0 2px 12px rgba(0,0,0,0.04); margin-bottom: 1.5rem; }
.rpt-section h5 { font-weight: 700; color: #212529; margin-bottom: 1rem; font-size: 1rem; display: flex; align-items: center; gap: .5rem; }
.rpt-section h5 i { color: var(--bsu-red); }
.chart-wrap { position: relative; width:100%; height: 240px; }
.bar-track { display:flex; align-items:flex-end; gap:2px; height:180px; }
.bar-day { flex: 1; display:flex; flex-direction:column; align-items:center; }
.bar-day .bar-inner { width:100%; border-radius:4px 4px 0 0; min-height:2px; transition: opacity .2s; }
.bar-day .bar-inner:hover { opacity:.75; }
.bar-day .bar-lbl { font-size:.58rem; color:#aaa; margin-top:2px; }
.donut-wrap { display:flex; align-items:center; gap:2rem; flex-wrap:wrap; }
.donut-legend { flex:1; min-width:120px; }
.donut-legend div { display:flex; align-items:center; gap:.5rem; font-size:.82rem; margin-bottom:.5rem; }
.donut-legend span { width:12px; height:12px; border-radius:50%; flex-shrink:0; }
.filter-bar { background:white; border-radius:12px; padding:1rem 1.5rem; box-shadow:0 2px 8px rgba(0,0,0,0.05); margin-bottom:1.5rem; display:flex; align-items:center; gap:1rem; flex-wrap:wrap; }
.filter-bar label { font-weight:600; font-size:.85rem; color:#555; }
.filter-bar select { border:1.5px solid #dee2e6; border-radius:8px; padding:.4rem .75rem; font-size:.88rem; cursor:pointer; }
.filter-bar button { background:var(--bsu-red); color:white; border:none; border-radius:8px; padding:.45rem 1.2rem; font-size:.88rem; font-weight:600; cursor:pointer; }
.mini-table { width:100%; border-collapse:collapse; font-size:.83rem; }
.mini-table th { background:#f8f9fa; padding:.6rem .9rem; text-align:left; font-weight:600; color:#555; font-size:.75rem; text-transform:uppercase; }
.mini-table td { padding:.65rem .9rem; border-bottom:1px solid #f0f0f0; color:#333; vertical-align:middle; }
.mini-table tr:last-child td { border-bottom:none; }
.mini-badge { padding:.18rem .6rem; border-radius:20px; font-size:.72rem; font-weight:600; }
.mb-pending  { background:#fff3cd; color:#856404; }
.mb-approved,.mb-confirmed { background:#d4edda; color:#155724; }
.mb-checkedin { background:#cce5ff; color:#004085; }
.mb-cancelled,.mb-denied { background:#f8d7da; color:#721c24; }
.mb-checkedout { background:#e2e3ff; color:#3730a3; }
.progress-bar-custom { height:8px; border-radius:4px; background:#f0f0f0; overflow:hidden; }
.progress-bar-fill   { height:100%; border-radius:4px; background:var(--bsu-red); }
.export-btn { background:#fff; border:1.5px solid var(--bsu-red); color:var(--bsu-red); border-radius:8px; padding:.4rem .9rem; font-size:.82rem; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:.4rem; transition:all .2s; text-decoration:none; }
.export-btn:hover { background:var(--bsu-red); color:#fff; }
</style>

<div class="content-area">

  <!-- Page Header -->
  <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h1 class="page-title" style="margin:0;"><i class="bi bi-bar-chart-line-fill me-2" style="color:var(--bsu-red);"></i>Reports & Analytics</h1>
    <div class="d-flex gap-2">
      <a class="export-btn" href="?month=<?= $month ?>&year=<?= $year ?>&export=csv_fr"><i class="bi bi-download"></i> Export Function CSV</a>
      <a class="export-btn" href="?month=<?= $month ?>&year=<?= $year ?>&export=csv_gr"><i class="bi bi-download"></i> Export Guest CSV</a>
    </div>
  </div>

  <!-- Month/Year Filter -->
  <form method="GET" class="filter-bar">
    <label><i class="bi bi-calendar3 me-1"></i> Filter Period:</label>
    <select name="month">
      <?php foreach ($months as $mi => $mn): ?>
        <option value="<?= $mi+1 ?>" <?= ($mi+1)==$month?'selected':'' ?>><?= $mn ?></option>
      <?php endforeach; ?>
    </select>
    <select name="year">
      <?php for ($y=date('Y')+1;$y>=2024;$y--): ?>
        <option value="<?= $y ?>" <?= $y==$year?'selected':'' ?>><?= $y ?></option>
      <?php endfor; ?>
    </select>
    <button type="submit"><i class="bi bi-funnel me-1"></i> Apply</button>
    <span style="color:#aaa;font-size:.82rem;margin-left:.5rem;">Showing: <strong><?= $currentMonthName." ".$year ?></strong></span>
  </form>

  <!-- ══ Section heading ══ -->
  <div style="font-size:.75rem;font-weight:700;color:var(--bsu-red);text-transform:uppercase;letter-spacing:.8px;margin-bottom:.75rem;">
    <i class="bi bi-building me-1"></i>Function Room Reservations
  </div>

  <!-- Function Room KPI Cards -->
  <div class="reports-grid">
    <div class="rpt-card">
      <div class="rpt-card-label">Total Bookings</div>
      <div class="rpt-card-value"><?= $fr_total ?></div>
      <div class="rpt-card-sub">This month</div>
    </div>
    <div class="rpt-card blue">
      <div class="rpt-card-label">Pending</div>
      <div class="rpt-card-value" style="color:#1565c0;"><?= $fr_pending ?></div>
      <div class="rpt-card-sub">Awaiting approval</div>
    </div>
    <div class="rpt-card green">
      <div class="rpt-card-label">Approved</div>
      <div class="rpt-card-value" style="color:#2e7d32;"><?= $fr_approved ?></div>
      <div class="rpt-card-sub">Confirmed reservations</div>
    </div>
    <div class="rpt-card orange">
      <div class="rpt-card-label">Cancelled / Denied</div>
      <div class="rpt-card-value" style="color:#e65100;"><?= $fr_cancelled ?></div>
      <div class="rpt-card-sub">Not fulfilled</div>
    </div>
    <div class="rpt-card purple">
      <div class="rpt-card-label">Total Participants</div>
      <div class="rpt-card-value" style="color:#6a1b9a;"><?= number_format($fr_participants) ?></div>
      <div class="rpt-card-sub">Approved events only</div>
    </div>
  </div>

  <div class="row g-3 mb-3">

    <!-- Function Room: Daily bar chart -->
    <div class="col-lg-8">
      <div class="rpt-section" style="height:100%;">
        <h5><i class="bi bi-bar-chart"></i> Daily Function Room Bookings — <?= $currentMonthName." ".$year ?></h5>
        <div class="bar-track" id="barFR">
          <?php
          $maxFR = max(max($daily_fr), 1);
          foreach ($daily_fr as $d => $c):
            $h = max(4, round(($c/$maxFR)*160));
          ?>
          <div class="bar-day" title="Day <?= $d ?>: <?= $c ?> booking(s)">
            <div class="bar-inner" style="height:<?= $h ?>px;background:<?= $c>0?'#b71c1c':'#eee'; ?>;"></div>
            <div class="bar-lbl"><?= $d ?></div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <!-- Function Room: By Event Type -->
    <div class="col-lg-4">
      <div class="rpt-section" style="height:100%;">
        <h5><i class="bi bi-pie-chart"></i> By Event Type</h5>
        <?php if (empty($fr_by_event)): ?>
          <p class="text-muted small">No data for this period.</p>
        <?php else:
          $colors = ['#b71c1c','#1565c0','#2e7d32','#e65100','#6a1b9a','#00838f','#558b2f','#4e342e'];
          $maxEvt = max(array_column($fr_by_event, 'cnt'));
          foreach ($fr_by_event as $i => $ev):
            $pct = $maxEvt > 0 ? round($ev['cnt']/$maxEvt*100) : 0;
        ?>
          <div class="mb-2">
            <div style="display:flex;justify-content:space-between;font-size:.78rem;margin-bottom:2px;">
              <span><?= htmlspecialchars($ev['event_type']) ?></span>
              <strong><?= $ev['cnt'] ?></strong>
            </div>
            <div class="progress-bar-custom">
              <div class="progress-bar-fill" style="width:<?= $pct ?>%;background:<?= $colors[$i%count($colors)] ?>;"></div>
            </div>
          </div>
        <?php endforeach; endif; ?>
      </div>
    </div>

  </div>

  <!-- Venue Utilization -->
  <?php if (!empty($fr_by_venue)): ?>
  <div class="rpt-section">
    <h5><i class="bi bi-geo-alt"></i> Venue Utilization — Function Rooms</h5>
    <div class="row g-3">
      <?php
      $totalVenueBookings = array_sum(array_column($fr_by_venue,'bookings'));
      foreach ($fr_by_venue as $vn):
        $pct = $totalVenueBookings > 0 ? round($vn['bookings']/$totalVenueBookings*100) : 0;
      ?>
      <div class="col-md-4 col-sm-6">
        <div style="padding:.75rem;background:#f8f9fa;border-radius:10px;">
          <div style="font-weight:600;font-size:.88rem;margin-bottom:.4rem;color:#212529;">
            <i class="bi bi-building me-1" style="color:var(--bsu-red);"></i><?= htmlspecialchars($vn['venue']) ?>
          </div>
          <div class="progress-bar-custom">
            <div class="progress-bar-fill" style="width:<?= $pct ?>%;"></div>
          </div>
          <div style="font-size:.75rem;color:#888;margin-top:3px;"><?= $vn['bookings'] ?> booking(s) · <?= $pct ?>% share</div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>

  <!-- Recent Function Room Reservations -->
  <div class="rpt-section">
    <h5><i class="bi bi-clock-history"></i> Recent Function Room Reservations</h5>
    <?php if (empty($recent_fr)): ?>
      <p class="text-muted small">No function room reservations found.</p>
    <?php else: ?>
    <div class="table-responsive" style="padding:0;box-shadow:none;">
      <table class="mini-table">
        <thead><tr>
          <th>Booking No.</th><th>Name</th><th>Activity</th><th>Date</th><th>Status</th><th>Action</th>
        </tr></thead>
        <tbody>
          <?php foreach ($recent_fr as $r): ?>
          <tr>
            <td><code><?= htmlspecialchars($r['booking_no']) ?></code></td>
            <td><?= htmlspecialchars($r['name']) ?></td>
            <td><?= htmlspecialchars($r['activity_name']) ?></td>
            <td><?= $r['start_datetime'] ? date('M d, Y', strtotime($r['start_datetime'])) : '—' ?></td>
            <td><span class="mini-badge mb-<?= strtolower($r['status']) ?>"><?= strtoupper($r['status']) ?></span></td>
            <td><a href="reservation_details.php?id=<?= (int)$r['id'] ?>" style="color:var(--bsu-red);font-size:.8rem;">View →</a></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>

  <!-- ══ Guest Room Section ══ -->
  <div style="font-size:.75rem;font-weight:700;color:var(--bsu-red);text-transform:uppercase;letter-spacing:.8px;margin-bottom:.75rem;margin-top:1rem;">
    <i class="bi bi-door-open me-1"></i>Guest Room Reservations
  </div>

  <!-- Guest Room KPI Cards -->
  <div class="reports-grid">
    <div class="rpt-card">
      <div class="rpt-card-label">Total Bookings</div>
      <div class="rpt-card-value"><?= $gr_total ?></div>
      <div class="rpt-card-sub">Check-ins this month</div>
    </div>
    <div class="rpt-card green">
      <div class="rpt-card-label">Revenue</div>
      <div class="rpt-card-value" style="color:#2e7d32;font-size:1.5rem;">₱<?= number_format($gr_revenue,2) ?></div>
      <div class="rpt-card-sub">Confirmed / Stayed</div>
    </div>
    <div class="rpt-card blue">
      <div class="rpt-card-label">Total Guests</div>
      <div class="rpt-card-value" style="color:#1565c0;"><?= number_format($gr_guests) ?></div>
      <div class="rpt-card-sub">Across all bookings</div>
    </div>
    <div class="rpt-card orange">
      <div class="rpt-card-label">Pending</div>
      <div class="rpt-card-value" style="color:#e65100;"><?= $gr_pending ?></div>
      <div class="rpt-card-sub">Needs action</div>
    </div>
    <div class="rpt-card purple">
      <div class="rpt-card-label">Checked Out</div>
      <div class="rpt-card-value" style="color:#6a1b9a;"><?= $gr_done ?></div>
      <div class="rpt-card-sub">Completed stays</div>
    </div>
  </div>

  <div class="row g-3 mb-3">
    <!-- Guest Room daily bar chart -->
    <div class="col-lg-8">
      <div class="rpt-section" style="height:100%;">
        <h5><i class="bi bi-bar-chart"></i> Daily Guest Room Bookings — <?= $currentMonthName." ".$year ?></h5>
        <div class="bar-track">
          <?php
          $maxGR = max(max($daily_gr), 1);
          foreach ($daily_gr as $d => $c):
            $h = max(4, round(($c/$maxGR)*160));
          ?>
          <div class="bar-day" title="Day <?= $d ?>: <?= $c ?> booking(s)">
            <div class="bar-inner" style="height:<?= $h ?>px;background:<?= $c>0?'#1565c0':'#eee'; ?>;"></div>
            <div class="bar-lbl"><?= $d ?></div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <!-- Revenue by Room -->
    <div class="col-lg-4">
      <div class="rpt-section" style="height:100%;">
        <h5><i class="bi bi-currency-dollar"></i> Revenue by Room</h5>
        <?php if (empty($grr_by_room)): ?>
          <p class="text-muted small">No revenue data.</p>
        <?php else:
          $maxR = max(array_column($grr_by_room,'revenue'));
          foreach ($grr_by_room as $rm):
            $pct = $maxR>0 ? round($rm['revenue']/$maxR*100) : 0;
        ?>
          <div class="mb-2">
            <div style="display:flex;justify-content:space-between;font-size:.78rem;margin-bottom:2px;">
              <span><?= htmlspecialchars($rm['room']) ?></span>
              <strong>₱<?= number_format($rm['revenue']) ?></strong>
            </div>
            <div class="progress-bar-custom">
              <div class="progress-bar-fill" style="width:<?= $pct ?>%;background:#2e7d32;"></div>
            </div>
            <div style="font-size:.7rem;color:#aaa;"><?= $rm['bookings'] ?> booking(s)</div>
          </div>
        <?php endforeach; endif; ?>
      </div>
    </div>
  </div>

  <!-- Guest Room Status Breakdown -->
  <div class="rpt-section">
    <h5><i class="bi bi-pie-chart-fill"></i> Guest Room Status Breakdown</h5>
    <div class="donut-wrap">
      <canvas id="statusChart" width="160" height="160"></canvas>
      <div class="donut-legend">
        <?php
        $statuses = [
          ['Pending',     $gr_pending,   '#ffc107'],
          ['Confirmed',   $gr_confirmed, '#28a745'],
          ['Checked In',  $gr_checkedin, '#007bff'],
          ['Checked Out', $gr_done,      '#6f42c1'],
          ['Cancelled',   $gr_cancelled, '#dc3545'],
        ];
        foreach ($statuses as [$lbl,$val,$col]):
        ?>
        <div><span style="background:<?= $col ?>;"></span><?= $lbl ?>: <strong><?= $val ?></strong></div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <!-- Recent Guest Room Reservations -->
  <div class="rpt-section">
    <h5><i class="bi bi-clock-history"></i> Recent Guest Room Reservations</h5>
    <?php if (empty($recent_gr)): ?>
      <p class="text-muted small">No guest room reservations found.</p>
    <?php else: ?>
    <div class="table-responsive" style="padding:0;box-shadow:none;">
      <table class="mini-table">
        <thead><tr>
          <th>Booking No.</th><th>Guest</th><th>Room</th><th>Check-in</th><th>Check-out</th><th>Amount</th><th>Status</th><th>Action</th>
        </tr></thead>
        <tbody>
          <?php foreach ($recent_gr as $r): ?>
          <tr>
            <td><code><?= htmlspecialchars($r['booking_no']) ?></code></td>
            <td><?= htmlspecialchars($r['guest_name']) ?></td>
            <td><?= htmlspecialchars($r['room_name'] ?? '—') ?></td>
            <td><?= $r['check_in_date'] ? date('M d, Y', strtotime($r['check_in_date'])) : '—' ?></td>
            <td><?= $r['check_out_date'] ? date('M d, Y', strtotime($r['check_out_date'])) : '—' ?></td>
            <td>₱<?= number_format($r['total_amount'],2) ?></td>
            <td><span class="mini-badge mb-<?= strtolower($r['status']) ?>"><?= strtoupper($r['status']) ?></span></td>
            <td><a href="guest_reservation_details.php?id=<?= (int)$r['id'] ?>" style="color:var(--bsu-red);font-size:.8rem;">View →</a></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>

</div><!-- /content-area -->

<script>
// Simple donut chart using Canvas API
const canvas = document.getElementById('statusChart');
if (canvas) {
    const ctx = canvas.getContext('2d');
    const data = [<?= $gr_pending ?>,<?= $gr_confirmed ?>,<?= $gr_checkedin ?>,<?= $gr_done ?>,<?= $gr_cancelled ?>];
    const colors = ['#ffc107','#28a745','#007bff','#6f42c1','#dc3545'];
    const total = data.reduce((a,b) => a+b, 0);
    let startAngle = -Math.PI/2;
    const cx = 80, cy = 80, r = 65, ri = 40;
    ctx.clearRect(0,0,160,160);
    if (total === 0) {
        ctx.beginPath();
        ctx.arc(cx,cy,r,0,2*Math.PI);
        ctx.fillStyle = '#f0f0f0';
        ctx.fill();
    } else {
        data.forEach((v, i) => {
            if (v === 0) return;
            const slice = (v/total)*2*Math.PI;
            ctx.beginPath();
            ctx.moveTo(cx,cy);
            ctx.arc(cx,cy,r,startAngle,startAngle+slice);
            ctx.closePath();
            ctx.fillStyle = colors[i];
            ctx.fill();
            startAngle += slice;
        });
        // Donut hole
        ctx.beginPath();
        ctx.arc(cx,cy,ri,0,2*Math.PI);
        ctx.fillStyle = '#fff';
        ctx.fill();
        // Center text
        ctx.fillStyle = '#333';
        ctx.font = 'bold 18px Poppins, sans-serif';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText(total, cx, cy);
    }
}
</script>

<?php
// ── CSV Export ────────────────────────────────────────────────────────────────
if (!empty($_GET['export'])) {
    $export = $_GET['export'];
    if ($export === 'csv_fr') {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="function_room_reservations_'.$year.'_'.str_pad($month,2,'0',STR_PAD_LEFT).'.csv"');
        $out = fopen('php://output','w');
        fputcsv($out, ['Booking No','Last Name','First Name','Email','Contact','Activity','Start','End','Status','Participants','Created']);
        $r = rep_q($conn,"SELECT booking_no,last_name,first_name,email,contact_number,activity_name,start_datetime,end_datetime,status,participants_count,created_at FROM facility_reservations WHERE start_datetime BETWEEN '$from' AND '$to 23:59:59' ORDER BY start_datetime");
        if ($r) while ($row = $r->fetch_assoc()) fputcsv($out, $row);
        fclose($out); exit;
    }
    if ($export === 'csv_gr') {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="guest_room_reservations_'.$year.'_'.str_pad($month,2,'0',STR_PAD_LEFT).'.csv"');
        $out = fopen('php://output','w');
        fputcsv($out, ['Booking No','Guest Name','Email','Contact','Room','Check-in','Check-out','Adults','Children','Total Guests','Total Amount','Status','Created']);
        $r = rep_q($conn,"SELECT gr.booking_no,gr.guest_name,gr.guest_email,gr.guest_contact,g.room_name,gr.check_in_date,gr.check_out_date,gr.adults_count,gr.children_count,gr.total_guests,gr.total_amount,gr.status,gr.created_at FROM guest_room_reservations gr LEFT JOIN guest_rooms g ON gr.guest_room_id=g.id WHERE gr.deleted=0 AND gr.check_in_date BETWEEN '$from' AND '$to' ORDER BY gr.check_in_date");
        if ($r) while ($row = $r->fetch_assoc()) fputcsv($out, $row);
        fclose($out); exit;
    }
}
require_once __DIR__ . '/inc/footer.php';
?>
