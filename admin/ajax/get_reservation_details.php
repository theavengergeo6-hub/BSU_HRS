<?php
require_once __DIR__ . "/../../inc/db_config.php";
$id = (int)($_GET['id'] ?? 0);
$res = $conn->query("SELECT r.*, v.name as venue_name, v.floor FROM facility_reservations r JOIN venues v ON r.venue_id = v.id WHERE r.id = $id");
if ($row = $res->fetch_assoc()) {
    echo '<div class="event-detail-item"><div class="detail-icon"><i class="bi bi-person"></i></div><div class="detail-content"><div class="detail-label">Requester</div><div class="detail-value">' . htmlspecialchars($row['last_name'] . ', ' . $row['first_name']) . '</div></div></div>';
    echo '<div class="event-detail-item"><div class="detail-icon"><i class="bi bi-calendar"></i></div><div class="detail-content"><div class="detail-label">Date & Time</div><div class="detail-value">' . date('F d, Y h:i A', strtotime($row['start_datetime'])) . ' - ' . date('h:i A', strtotime($row['end_datetime'])) . '</div></div></div>';
    echo '<div class="event-detail-item"><div class="detail-icon"><i class="bi bi-building"></i></div><div class="detail-content"><div class="detail-label">Venue</div><div class="detail-value">' . htmlspecialchars($row['venue_name'] . ' (' . $row['floor'] . ')') . '</div></div></div>';
    echo '<div class="event-detail-item"><div class="detail-icon"><i class="bi bi-people"></i></div><div class="detail-content"><div class="detail-label">Participants</div><div class="detail-value">' . $row['participants_count'] . '</div></div></div>';
    echo '<div class="event-detail-item"><div class="detail-icon"><i class="bi bi-calendar-check"></i></div><div class="detail-content"><div class="detail-label">Status</div><div class="detail-value"><span class="badge badge-' . $row['status'] . '">' . ucfirst($row['status']) . '</span></div></div></div>';
}
