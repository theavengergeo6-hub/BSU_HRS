<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../debug.log');

// Custom log file in your project directory
$log_file = __DIR__ . '/../debug.log';

function write_log($message) {
    global $log_file;
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($log_file, "[$timestamp] $message\n", FILE_APPEND);
}

write_log("========== START reservation_submit.php ==========");

require_once __DIR__ . '/../inc/db_config.php';
require_once __DIR__ . '/../inc/essentials.php';

header('Content-Type: application/json');

// Log ALL raw POST data
write_log("RAW POST data: " . print_r($_POST, true));

// Specifically log activity_name
write_log("SPECIFIC CHECK - activity_name: '" . ($_POST['activity_name'] ?? 'NOT SET') . "'");
write_log("SPECIFIC CHECK - event_type_id: '" . ($_POST['event_type_id'] ?? 'NOT SET') . "'");

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Generate unique booking numbers
    $booking_no = 'FAC-' . date('Ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
    $reservation_no = 'RES-' . date('Ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
    
    write_log("Generated numbers - booking_no: $booking_no, reservation_no: $reservation_no");

    // Get and sanitize POST data
    $last_name = clean($_POST['last_name'] ?? '');
    $first_name = clean($_POST['first_name'] ?? '');
    $middle_initial = clean($_POST['middle_initial'] ?? '');
    $email = clean($_POST['email'] ?? '');
    $contact = clean($_POST['contact'] ?? '');
    $office_type_id = (int)($_POST['office_type_id'] ?? 0);
    $office_id = isset($_POST['office_id']) && $_POST['office_id'] !== '' ? (int)$_POST['office_id'] : null;
    $external_office_name = clean($_POST['office_external_name'] ?? '');
    
    // Get activity_name
    $activity_name = $_POST['activity_name'] ?? '';
    write_log("BEFORE CLEAN - activity_name: '" . $activity_name . "'");
    $activity_name = clean($activity_name);
    write_log("AFTER CLEAN - activity_name: '" . $activity_name . "'");
    
    $event_type_id = (int)($_POST['event_type_id'] ?? 0);
    $participants = (int)($_POST['participants'] ?? 0);
    $banquet_style_id = isset($_POST['banquet_style_id']) && $_POST['banquet_style_id'] !== '' ? (int)$_POST['banquet_style_id'] : null;
    $additional_instruction = clean($_POST['additional_instruction'] ?? '');
    $venue_setup_id = 1; // Default value
    $venue_id = (int)($_POST['venue_id'] ?? 0);
    
    write_log("event_type_id: " . $event_type_id);
    write_log("FINAL activity_name: '$activity_name'");

    // Validate required fields
    $errors = [];
    if (!$last_name) $errors[] = 'Last name is required';
    if (!$first_name) $errors[] = 'First name is required';
    if (!$email) $errors[] = 'Email is required';
    if (!$contact) $errors[] = 'Contact number is required';
    if (!$office_type_id) $errors[] = 'Office type is required';
    if (!$venue_id) $errors[] = 'Venue is required';
    
    if (!empty($errors)) {
        throw new Exception(implode(', ', $errors));
    }

    // Parse facilities_schedules JSON
    if (!isset($_POST['facilities_schedules'])) {
        throw new Exception('No schedule data found - facilities_schedules not set');
    }
    
    $facilities_schedules = json_decode($_POST['facilities_schedules'], true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON: ' . json_last_error_msg());
    }
    
    write_log("facilities_schedules decoded: " . print_r($facilities_schedules, true));

    if (!$facilities_schedules) {
        throw new Exception('No schedule data found - empty after decode');
    }

    // Get the first schedule for main reservation times
    $venues_json = $facilities_schedules['venues'] ?? [];
    $schedules = $facilities_schedules['schedules'] ?? [];

    if (empty($venues_json)) {
        throw new Exception('No venues in schedule data');
    }
    if (empty($schedules)) {
        throw new Exception('No schedules in schedule data');
    }

    $first_venue_id = $venues_json[0]['id'] ?? null;
    if (!$first_venue_id) {
        throw new Exception('No venue ID found');
    }

    $first_schedule = $schedules[$first_venue_id][0] ?? null;
    if (!$first_schedule) {
        throw new Exception('No schedule found for first venue');
    }

    if (!isset($first_schedule['date']) || !isset($first_schedule['start']) || !isset($first_schedule['end'])) {
        throw new Exception('Schedule missing required fields');
    }

    $start_datetime = $first_schedule['date'] . ' ' . $first_schedule['start'];
    $end_datetime = $first_schedule['date'] . ' ' . $first_schedule['end'];
    
    write_log("Times - start_datetime: $start_datetime, end_datetime: $end_datetime");

    // Check office validation based on type
    $office_type_query = $conn->query("SELECT name FROM office_types WHERE id = $office_type_id");
    if (!$office_type_query) {
        throw new Exception('Error checking office type: ' . $conn->error);
    }
    
    $office_type_name = '';
    if ($office_type_query->num_rows > 0) {
        $office_type_name = $office_type_query->fetch_assoc()['name'];
    }

    if ($office_type_name === 'External') {
        if (!$external_office_name) {
            throw new Exception('External office name is required');
        }
    } else {
        if (!$office_id) {
            throw new Exception('Office selection is required');
        }
    }

    if (!$activity_name) {
        write_log("ACTIVITY NAME IS EMPTY!");
        throw new Exception('Activity name is required');
    }
    if (!$event_type_id) throw new Exception('Event type is required');
    if ($participants < 1) throw new Exception('Number of participants is required');

    // -------------------------------------------------------
    // Server-side availability conflict check (race condition guard)
    // Checks ALL selected venues against approved reservations
    // -------------------------------------------------------
    $venue_ids_to_check = $_POST['venue_ids'] ?? [];
    if (!is_array($venue_ids_to_check)) $venue_ids_to_check = [$venue_ids_to_check];
    if (!in_array($venue_id, array_map('intval', $venue_ids_to_check))) {
        $venue_ids_to_check[] = $venue_id;
    }

    $conflict_check_sql = "
        SELECT COUNT(*) as conflict
        FROM reservation_venues rv
        JOIN facility_reservations fr ON rv.reservation_id = fr.id
        WHERE rv.venue_id = ?
          AND rv.start_datetime < ?
          AND rv.end_datetime > DATE_ADD(?, INTERVAL -1 HOUR)
          AND fr.status = 'approved'
    ";
    $conflict_stmt = $conn->prepare($conflict_check_sql);
    if (!$conflict_stmt) {
        throw new Exception('Conflict check prepare failed: ' . $conn->error);
    }

    foreach ($venue_ids_to_check as $check_vid) {
        $check_vid = (int)$check_vid;
        if (!$check_vid) continue;

        // Get this venue's schedule from the JSON
        $v_schedules = $schedules[$check_vid] ?? [];
        $v_start = !empty($v_schedules) ? $v_schedules[0]['date'] . ' ' . $v_schedules[0]['start'] : $start_datetime;
        $v_end   = !empty($v_schedules) ? $v_schedules[0]['date'] . ' ' . $v_schedules[0]['end']   : $end_datetime;

        $conflict_stmt->bind_param("iss", $check_vid, $v_end, $v_start);
        $conflict_stmt->execute();
        $conflict_result = $conflict_stmt->get_result()->fetch_assoc();

        if ((int)$conflict_result['conflict'] > 0) {
            $venue_name_q = $conn->query("SELECT name FROM venues WHERE id = $check_vid");
            $venue_name   = $venue_name_q ? $venue_name_q->fetch_assoc()['name'] : "Venue #$check_vid";
            throw new Exception("$venue_name is already booked for the selected time (or within the 1-hour cleaning buffer). Please choose a different time.");
        }
    }
    $conflict_stmt->close();
    write_log("Conflict check passed for all venues");

    // Prepare miscellaneous items
    $miscellaneous_items = $_POST['miscellaneous_items'] ?? '{}';
    
    // Insert into database
    $sql = "INSERT INTO facility_reservations (
        booking_no, reservation_no, last_name, first_name, middle_initial, 
        email, contact_number, office_type_id, office_id, external_office_name, 
        activity_name, event_type_id, venue_id, venue_setup_id, banquet_style_id,
        start_datetime, end_datetime, participants_count, miscellaneous_items, 
        additional_instruction
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    write_log("SQL: $sql");
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception('Database prepare failed: ' . $conn->error);
    }

    // Bind parameters - 20 variables, 20 type characters
    // s=booking_no, s=reservation_no, s=last_name, s=first_name, s=middle_initial,
    // s=email, s=contact, i=office_type_id, s=office_id, s=external_office_name,
    // s=activity_name, i=event_type_id, i=venue_id, i=venue_setup_id, s=banquet_style_id,
    // s=start_datetime, s=end_datetime, i=participants, s=miscellaneous_items, s=additional_instruction
    $stmt->bind_param(
        "sssssssisssiiisssiss",
        $booking_no,
        $reservation_no,
        $last_name,
        $first_name,
        $middle_initial,
        $email,
        $contact,
        $office_type_id,
        $office_id,
        $external_office_name,
        $activity_name,
        $event_type_id,
        $venue_id,
        $venue_setup_id,
        $banquet_style_id,
        $start_datetime,
        $end_datetime,
        $participants,
        $miscellaneous_items,
        $additional_instruction
    );

    if (!$stmt->execute()) {
        throw new Exception('Execute failed: ' . $stmt->error);
    }

    $insert_id = $stmt->insert_id;
    $stmt->close();
    
    write_log("Success! Inserted ID: $insert_id");
    write_log("Inserted activity_name: " . $activity_name);

    // -------------------------------------------------------
    // Insert all selected venues into reservation_venues
    // -------------------------------------------------------
    $venue_ids = $_POST['venue_ids'] ?? [];
    if (!is_array($venue_ids)) {
        $venue_ids = [$venue_ids];
    }

    // Ensure the primary venue_id is always included
    if (!in_array($venue_id, array_map('intval', $venue_ids))) {
        $venue_ids[] = $venue_id;
    }

    $venue_insert_stmt = $conn->prepare(
        "INSERT INTO reservation_venues (reservation_id, venue_id, start_datetime, end_datetime) 
         VALUES (?, ?, ?, ?)"
    );
    if (!$venue_insert_stmt) {
        throw new Exception('Failed to prepare venue insert: ' . $conn->error);
    }

    $venues_inserted = 0;
    foreach ($venue_ids as $vid) {
        $vid = (int)$vid;
        if (!$vid) continue;

        // Get this venue's specific schedule from the JSON, fall back to main schedule
        $v_schedules = $schedules[$vid] ?? [];
        $v_start = !empty($v_schedules) ? $v_schedules[0]['date'] . ' ' . $v_schedules[0]['start'] : $start_datetime;
        $v_end   = !empty($v_schedules) ? $v_schedules[0]['date'] . ' ' . $v_schedules[0]['end']   : $end_datetime;

        $venue_insert_stmt->bind_param("iiss", $insert_id, $vid, $v_start, $v_end);
        if (!$venue_insert_stmt->execute()) {
            write_log("Warning: Failed to insert venue $vid into reservation_venues: " . $venue_insert_stmt->error);
        } else {
            $venues_inserted++;
        }
    }
    $venue_insert_stmt->close();
    write_log("Inserted $venues_inserted venue(s) into reservation_venues for reservation ID $insert_id");

    echo json_encode([
        'success' => true,
        'message' => 'Reservation submitted successfully!',
        'booking_no' => $booking_no,
        'reservation_no' => $reservation_no,
        'id' => $insert_id,
        'debug_activity' => $activity_name,
        'venues_count' => $venues_inserted
    ]);

} catch (Exception $e) {
    write_log("ERROR: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>