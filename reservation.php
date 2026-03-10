<?php
/**
 * BSU Hostel - Facility Reservation (7-step form)
 * Step 0: Type | 1: Info | 2: Rooms | 3: Schedule | 4: Terms | 5: Miscellaneous | 6: Summary
 */
$pageTitle = 'Reservation';
require_once __DIR__ . '/inc/link.php';

$base = rtrim(BASE_URL, '/');

$event_types = getEventTypes($conn);
$banquet_styles = getBanquetStyles($conn);

$office_types = [];
$offices_by_type = [];
try {
    $r = $conn->query("SELECT id, name FROM office_types ORDER BY name");
    if ($r && $r->num_rows > 0) $office_types = $r->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) { }
try {
    $r = $conn->query("SELECT id, office_type_id, name FROM offices ORDER BY name");
    if ($r && $r->num_rows > 0) {
        while ($row = $r->fetch_assoc()) {
            $tid = $row['office_type_id'];
            if (!isset($offices_by_type[$tid])) $offices_by_type[$tid] = [];
            $offices_by_type[$tid][] = $row;
        }
    }
} catch (Exception $e) { }
if (empty($office_types)) {
    $office_types = [['id'=>1,'name'=>'College'],['id'=>2,'name'=>'Office'],['id'=>3,'name'=>'Student Organization'],['id'=>4,'name'=>'External']];
}

// Get venues from the venues table
$guest_venues = [];
$function_venues = [];
$guest_venues    = [];

// ── Function rooms: from venues table (name LIKE '%Function%') ──────────────
$func_q = $conn->query("
    SELECT id, name, floor, capacity, description, price,
           COALESCE(half_day_rate, 2000)    AS half_day_rate,
           COALESCE(whole_day_rate, 3000)   AS whole_day_rate,
           COALESCE(extension_rate, 400)    AS extension_rate,
           COALESCE(sound_system_fee, 1500) AS sound_system_fee
    FROM   venues
    WHERE  is_active = 1
    AND    name LIKE '%Function%'
    ORDER  BY name
");
if ($func_q && $func_q->num_rows > 0) {
    while ($row = $func_q->fetch_assoc()) {
        $function_venues[] = $row;
    }
}

// ── Guest / accommodation rooms: from venues table (NOT Function) ────────────
$guest_q = $conn->query("
    SELECT id,
           room_name       AS name,
           floor,
           max_guests      AS capacity,
           description,
           price_per_night AS price,
           extra_bed_available,
           extra_bed_price
    FROM   guest_rooms
    WHERE  COALESCE(is_active, 1) = 1
    ORDER  BY room_name
");
if ($guest_q && $guest_q->num_rows > 0) {
    while ($row = $guest_q->fetch_assoc()) {
        $guest_venues[] = $row;
    }
}

// ── Fallbacks if DB is empty or query failed ─────────────────────────────────
if (empty($function_venues)) {
    $function_venues = [
        ['id'=>1,'name'=>'Function Room A','floor'=>'Ground Floor','capacity'=>40,'description'=>'Spacious function room for meetings and events.','price'=>0,'half_day_rate'=>2000,'whole_day_rate'=>3000,'extension_rate'=>400,'sound_system_fee'=>1500],
        ['id'=>2,'name'=>'Function Room B','floor'=>'Ground Floor','capacity'=>40,'description'=>'Ideal for seminars and workshops.','price'=>0,'half_day_rate'=>2000,'whole_day_rate'=>3000,'extension_rate'=>400,'sound_system_fee'=>1500],
        ['id'=>3,'name'=>'Function Room C','floor'=>'Ground Floor','capacity'=>40,'description'=>'Largest function room with AV equipment.','price'=>0,'half_day_rate'=>2000,'whole_day_rate'=>3000,'extension_rate'=>400,'sound_system_fee'=>1500],
        ['id'=>4,'name'=>'Function Room D','floor'=>'Ground Floor','capacity'=>40,'description'=>'Small function room for intimate events.','price'=>0,'half_day_rate'=>2000,'whole_day_rate'=>3000,'extension_rate'=>400,'sound_system_fee'=>1500],
        ['id'=>5,'name'=>'Function Room E','floor'=>'Ground Floor','capacity'=>40,'description'=>'Versatile space for training and events.','price'=>0,'half_day_rate'=>2000,'whole_day_rate'=>3000,'extension_rate'=>400,'sound_system_fee'=>1500],
    ];
}

if (empty($guest_venues)) {
    $guest_venues = [
        ['id'=>1,'name'=>'Guest Room 1','floor'=>'2nd Floor','capacity'=>4,'description'=>'Comfortable guest room with queen bed.','price'=>2500,'extra_bed_available'=>1,'extra_bed_price'=>500],
        ['id'=>2,'name'=>'Guest Room 2','floor'=>'2nd Floor','capacity'=>5,'description'=>'Guest room with city view.','price'=>2500,'extra_bed_available'=>1,'extra_bed_price'=>500],
        ['id'=>3,'name'=>'Guest Room 3','floor'=>'2nd Floor','capacity'=>5,'description'=>'Spacious guest room for small families.','price'=>3000,'extra_bed_available'=>1,'extra_bed_price'=>500],
        ['id'=>4,'name'=>'Guest Room 4','floor'=>'2nd Floor','capacity'=>8,'description'=>'Cozy room for couples or solo travelers.','price'=>3500,'extra_bed_available'=>1,'extra_bed_price'=>500],
        ['id'=>5,'name'=>'Dormitory',   'floor'=>'Ground Floor','capacity'=>24,'description'=>'Spacious dormitory with 12 bunk beds.','price'=>8000,'extra_bed_available'=>0,'extra_bed_price'=>0],
    ];
}

// Fetch terms based on customer type if available in session
function getTermsByCustomerType($conn, $customer_type) {
    // Map customer types to term categories
    $term_categories = [
        '1' => 'college',       // College
        '2' => 'office',        // Office
        '3' => 'student_org',   // Student Organization
        '4' => 'external'       // External
    ];
    
    $term_category = $term_categories[$customer_type] ?? 'external';
    
    // Check if terms_and_conditions table exists
    $table_check = $conn->query("SHOW TABLES LIKE 'terms_and_conditions'");
    if ($table_check->num_rows == 0) {
        return null;
    }
    
    // Fetch from database
    $query = "SELECT * FROM terms_and_conditions WHERE customer_type = ? AND is_active = 1 LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $term_category);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    
    return null;
}

// Get selected customer type from session if available
$selected_customer_type = $_SESSION['reservation']['customer_type'] ?? '';
$terms = null;
if ($selected_customer_type) {
    $terms = getTermsByCustomerType($conn, $selected_customer_type);
}
?>
<?php require_once __DIR__ . '/inc/header.php'; ?>

<style>
/* Terms and Conditions Card Styling - ENLARGED */
.terms-container {
    border: 1px solid #dee2e6;
    border-radius: 16px;
    overflow: hidden;
    margin-bottom: 2rem;
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    background: white;
    width: 100%;
    min-height: 500px;
    display: flex;
    flex-direction: column;
}

.terms-header {
    background: linear-gradient(135deg, #b71c1c, #8b0000) !important;
    color: white !important;
    padding: 1.5rem 2rem !important;
    border-bottom: none !important;
    flex-shrink: 0;
}

.terms-header h5 {
    color: white !important;
    margin: 0 0 0.5rem 0 !important;
    font-size: 1.3rem !important;
    font-weight: 700 !important;
    line-height: 1.4;
}

.terms-header p {
    color: rgba(255,255,255,0.95) !important;
    margin: 0 !important;
    font-size: 1rem !important;
}

.terms-content {
    padding: 2rem !important;
    background: #fafafa;
    flex: 1;
    min-height: 350px;
    max-height: 450px;
    overflow-y: auto !important;
    border-bottom: 1px solid #eee;
}

.terms-content pre {
    white-space: pre-wrap !important;
    font-family: 'Poppins', sans-serif !important;
    font-size: 1rem !important;
    line-height: 1.8 !important;
    color: #2c3e50 !important;
    margin: 0 !important;
    background: transparent !important;
    border: none !important;
    padding: 0 !important;
}

#termsContainer {
    min-height: 550px;
    display: block;
    width: 100%;
}

.terms-content::-webkit-scrollbar {
    width: 10px;
}

.terms-content::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.terms-content::-webkit-scrollbar-thumb {
    background: #b71c1c;
    border-radius: 10px;
}

.terms-content::-webkit-scrollbar-thumb:hover {
    background: #8b0000;
}

.signature-section {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 16px;
    padding: 2rem;
    margin-top: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
}

.signature-section .row {
    margin-bottom: 1.5rem;
}

.signature-section .form-label {
    font-weight: 600;
    color: #2c3e50;
    font-size: 1rem;
    margin-bottom: 0.5rem;
    display: block;
}

.signature-section .form-control {
    border: 2px solid #e9ecef;
    border-radius: 10px;
    padding: 0.875rem 1.25rem;
    font-size: 1rem;
    width: 100%;
}

.form-check {
    padding-left: 2rem;
    margin-top: 1.5rem;
    margin-bottom: 0;
}

.form-check-input {
    width: 1.3rem;
    height: 1.3rem;
    margin-left: -2rem;
    cursor: pointer;
    border: 2px solid #ced4da;
}

.form-check-input:checked {
    background-color: #b71c1c;
    border-color: #b71c1c;
}

.form-check-label {
    font-size: 1rem;
    color: #2c3e50;
    cursor: pointer;
    padding-left: 0.5rem;
    line-height: 1.5;
}

.form-buttons {
    display: flex;
    justify-content: space-between;
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 2px solid #e9ecef;
}

.btn-res {
    padding: 0.875rem 2.5rem;
    border-radius: 50px;
    font-weight: 600;
    font-size: 1rem;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-prev {
    background: #e9ecef;
    color: #495057;
}

.btn-next {
    background: linear-gradient(135deg, #b71c1c, #8b0000);
    color: white;
    box-shadow: 0 4px 15px rgba(183,28,28,0.3);
}

.btn-next:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.terms-content .text-center {
    min-height: 400px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

.spinner-border.text-danger {
    width: 3rem;
    height: 3rem;
}

/* Miscellaneous Items Styling with Limits */
.misc-list {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    border: 1px solid #e9ecef;
    margin-bottom: 1rem;
}

.misc-item {
    border-bottom: 1px solid #f0f0f0;
    padding: 1.25rem 0;
}

.misc-item:last-child {
    border-bottom: none;
}

.misc-main-label {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1rem;
    cursor: pointer;
    font-weight: 500;
    color: #2c3e50;
}

.misc-main-label input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
    accent-color: #b71c1c;
}

.misc-title {
    font-size: 1rem;
    font-weight: 600;
    min-width: 150px;
}

.misc-limits {
    font-size: 0.8rem;
    color: #666;
    font-weight: normal;
    background: #f8f9fa;
    padding: 0.2rem 0.75rem;
    border-radius: 20px;
}

.misc-badge {
    font-size: 0.75rem;
    background: #e9ecef;
    color: #495057;
    padding: 0.2rem 0.75rem;
    border-radius: 20px;
    font-weight: normal;
}

.misc-checkbox-only {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 1rem 1.25rem;
    margin: 0.5rem 0;
}

.misc-checkbox-label {
    margin-bottom: 0 !important;
}

.misc-checkbox-label .misc-title {
    color: #b71c1c;
    font-weight: 600;
}

.misc-sub-items {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-left: 2.3rem;
}

.misc-sub-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.misc-sub-item label {
    min-width: 90px;
    font-size: 0.9rem;
    color: #555;
}

.misc-sub-item .form-control {
    width: 100px;
    padding: 0.5rem;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    font-size: 0.9rem;
}

.misc-sub-item .form-control:focus {
    border-color: #b71c1c;
    outline: none;
}

.misc-sub-item .form-control:disabled {
    background: #f8f9fa;
    opacity: 0.6;
}

.misc-single-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-left: 2.3rem;
    margin-top: 0.5rem;
}

.misc-single-item .form-control {
    width: 120px;
    padding: 0.5rem;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    font-size: 0.9rem;
}

.limit-hint {
    font-size: 0.75rem;
    color: #999;
    font-style: italic;
}

.misc-qty:invalid,
.misc-qty-inline:invalid {
    border-color: #dc3545;
}

.misc-qty:focus:invalid,
.misc-qty-inline:focus:invalid {
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

/* Schedule Step Styling */
.schedule-card {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 16px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 4px 12px rgba(0,0,0,0.03);
}

.room-name {
    font-size: 1.1rem;
    font-weight: 600;
    color: #b71c1c;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #f0f0f0;
}

.date-time-row {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr auto;
    gap: 1rem;
    align-items: flex-end;
    margin-bottom: 1rem;
}

.date-time-row .form-group {
    margin-bottom: 0;
}

.date-time-row .form-group label {
    font-size: 0.8rem;
    font-weight: 600;
    color: #666;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    margin-bottom: 0.3rem;
    display: block;
}

.date-time-row .form-control {
    background: #f8f9fa;
    border: 2px solid #e9ecef;
    border-radius: 10px;
    padding: 0.6rem 1rem;
    font-size: 0.9rem;
}

.date-time-row .form-control:focus {
    border-color: #b71c1c;
    background: white;
    outline: none;
}

.btn-add-schedule {
    background: #b71c1c;
    color: white;
    border: none;
    border-radius: 10px;
    padding: 0.6rem 1.2rem;
    font-weight: 500;
    font-size: 0.9rem;
    cursor: pointer;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    height: 42px;
}

.btn-add-schedule:hover {
    background: #8b0000;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(183,28,28,0.3);
}

.schedule-list {
    margin-top: 1rem;
    background: #f8f9fa;
    border-radius: 10px;
    padding: 0.75rem;
}

.schedule-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0.75rem;
    background: white;
    border-radius: 8px;
    margin-bottom: 0.5rem;
    border: 1px solid #e9ecef;
}

.schedule-item:last-child {
    margin-bottom: 0;
}

.schedule-text {
    font-size: 0.9rem;
    color: #2c3e50;
    font-weight: 500;
}

.btn-remove {
    background: none;
    border: none;
    color: #dc3545;
    font-size: 1.2rem;
    cursor: pointer;
    padding: 0 0.5rem;
    transition: all 0.2s ease;
}

.btn-remove:hover {
    color: #a71d2a;
    transform: scale(1.2);
}

.selected-facilities-box {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 2px dashed #e9ecef;
    font-size: 0.9rem;
    color: #666;
}

/* Guest Registration Form Styles */
.guest-registration-form {
    background: white;
    border-radius: 16px;
    padding: 2rem;
    border: 1px solid #e9ecef;
    box-shadow: 0 4px 12px rgba(0,0,0,0.03);
}

.guest-form-section {
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 2px solid #f0f0f0;
}

.guest-form-section:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.guest-form-section h4 {
    font-size: 1.1rem;
    font-weight: 600;
    color: #b71c1c;
    margin-bottom: 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.guest-form-section h4 i {
    font-size: 1.2rem;
}

.guest-name-row {
    display: grid;
    grid-template-columns: 2fr 2fr 1fr;
    gap: 1rem;
    margin-bottom: 1rem;
}

/* Dynamic Guest Cards Styling */
.guests-container {
    margin-top: 1.5rem;
    margin-bottom: 1.5rem;
}

.guest-card {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 12px;
    padding: 1.25rem;
    margin-bottom: 1rem;
    transition: all 0.3s ease;
}

.guest-card:hover {
    border-color: #b71c1c;
    box-shadow: 0 4px 12px rgba(183,28,28,0.1);
}

.guest-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid #dee2e6;
}

.guest-card-header h6 {
    font-size: 1rem;
    font-weight: 600;
    color: #2c3e50;
    margin: 0;
}

.btn-remove-guest {
    background: none;
    border: none;
    color: #dc3545;
    font-size: 1.2rem;
    cursor: pointer;
    padding: 0 0.5rem;
    transition: all 0.2s ease;
}

.btn-remove-guest:hover {
    color: #a71d2a;
    transform: scale(1.2);
}

.btn-add-guests {
    background: white;
    border: 2px dashed #b71c1c;
    color: #b71c1c;
    border-radius: 12px;
    padding: 1rem;
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    width: 100%;
    margin-top: 1rem;
}

.btn-add-guests:hover {
    background: #fff5f5;
    border-color: #8b0000;
    color: #8b0000;
}

.room-limit-badge {
    display: inline-block;
    background: #e9ecef;
    color: #495057;
    font-size: 0.75rem;
    padding: 0.25rem 0.75rem;
    border-radius: 50px;
    margin-left: 1rem;
}

.arrival-departure-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
}

.time-grid-small {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.75rem;
    margin-top: 0.5rem;
}

.room-selector {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.data-privacy-box {
    background: #f8f9fa;
    border-left: 4px solid #b71c1c;
    padding: 1.5rem;
    border-radius: 8px;
    margin: 2rem 0 1rem;
    font-size: 0.9rem;
    color: #495057;
    line-height: 1.6;
}

.data-privacy-box strong {
    color: #b71c1c;
}

@media (max-width: 768px) {
    .date-time-row {
        grid-template-columns: 1fr;
    }
    
    .misc-sub-items {
        grid-template-columns: 1fr;
        margin-left: 0;
    }
    
    .misc-single-item {
        margin-left: 0;
    }
    
    .misc-main-label {
        flex-wrap: wrap;
    }
    
    .misc-title {
        min-width: 120px;
    }
    
    .terms-container {
        min-height: 400px;
    }
    
    .terms-content {
        min-height: 300px;
        max-height: 350px;
        padding: 1.5rem !important;
    }
    
    .terms-header {
        padding: 1.25rem !important;
    }
    
    .terms-header h5 {
        font-size: 1.1rem !important;
    }
    
    .signature-section {
        padding: 1.5rem;
    }
    
    .btn-res {
        padding: 0.75rem 1.5rem;
    }
    
    .guest-name-row {
        grid-template-columns: 1fr;
    }
    
    .arrival-departure-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .room-selector {
        grid-template-columns: 1fr;
    }
}

/* Reservation Type Selector */
.reservation-type-selector {
    background: white;
    border-radius: 16px;
    padding: 2rem;
    margin-bottom: 2rem;
    border: 1px solid #e9ecef;
    box-shadow: 0 4px 12px rgba(0,0,0,0.03);
}

.type-option {
    display: flex;
    align-items: flex-start;
    gap: 1.5rem;
    padding: 1.5rem;
    border: 2px solid #e9ecef;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-bottom: 1rem;
}

.type-option:last-child {
    margin-bottom: 0;
}

.type-option:hover {
    border-color: #b71c1c;
    background: #fff5f5;
}

.type-option.selected {
    border-color: #b71c1c;
    background: #fdeae8;
}

.type-radio {
    margin-top: 0.3rem;
}

.type-radio input[type="radio"] {
    width: 20px;
    height: 20px;
    accent-color: #b71c1c;
}

.type-content {
    flex: 1;
}

.type-content h4 {
    font-size: 1.2rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 0.5rem;
}

.type-description {
    color: #666;
    font-size: 0.9rem;
    line-height: 1.6;
    margin-bottom: 0.5rem;
}

.type-note {
    color: #b71c1c;
    font-size: 0.8rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.3rem;
}

/* Enforced Reservation Card Size (Bypass Cache) */
.reservation-card {
    max-width: 900px !important;
    margin: 0 auto !important;
}
</style>

<main class="reservation-page">
    <div class="container">
        <div class="reservation-card">
            <h1>Facility Reservation</h1>
            <p class="subtitle">Book your preferred facility in just a few steps</p>

            <div class="progress-steps" id="progressSteps">
                <div class="progress-line" id="progressLine" style="width: 0%"></div>
                <div class="step active" data-step="0"><span>0</span><div class="step-label">Type</div></div>
                <div class="step" data-step="1"><span>1</span><div class="step-label">Info</div></div>
                <div class="step" data-step="2"><span>2</span><div class="step-label">Rooms</div></div>
                <div class="step" data-step="3"><span>3</span><div class="step-label">Schedule</div></div>
                <div class="step" data-step="4"><span>4</span><div class="step-label">Terms</div></div>
                <div class="step" data-step="5"><span>5</span><div class="step-label">Misc</div></div>
                <div class="step" data-step="6"><span>6</span><div class="step-label">Summary</div></div>
            </div>

            <div id="resumeBookingContainer" style="display: none; margin-bottom: 1rem;">
                <div class="alert alert-info d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <span><i class="bi bi-arrow-repeat me-2"></i> <span class="resume-msg">You have an incomplete reservation from your last session.</span></span>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-primary" onclick="resumeSavedSession()">
                            <i class="bi bi-play-fill me-1"></i> Resume
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearSavedData()">
                            <i class="bi bi-trash me-1"></i> Start Fresh
                        </button>
                    </div>
                </div>
            </div>

            <form id="reservationForm">
                <!-- Step 0: Reservation Type -->
                <div class="form-step active" id="step0Form">
                    <div class="form-header">
                        <h3>Select Reservation Type</h3>
                        <p>Choose the type of reservation you want to make</p>
                    </div>
                    
                    <div class="reservation-type-selector">
                        <div class="type-option" id="typeGuestOption" onclick="selectReservationType('guest')">
                            <div class="type-radio">
                                <input type="radio" name="reservation_type" id="typeGuest" value="guest">
                            </div>
                            <div class="type-content">
                                <h4>Guest Room Booking</h4>
                                <p class="type-description">For overnight stays and accommodations. Fill out the guest registration form with personal details, check-in/out dates, and guest information.</p>
                                <div class="type-note">
                                    <i class="bi bi-info-circle-fill"></i>
                                    <span>Includes data privacy consent and guest registration form</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="type-option" id="typeFunctionOption" onclick="selectReservationType('function')">
                            <div class="type-radio">
                                <input type="radio" name="reservation_type" id="typeFunction" value="function">
                            </div>
                            <div class="type-content">
                                <h4>Function Room Booking</h4>
                                <p class="type-description">For events, meetings, seminars, and other functions. Choose between pencil booking (tentative, valid for 1 week) or full reservation (official upon approval).</p>
                                <div class="type-note">
                                    <i class="bi bi-info-circle-fill"></i>
                                    <span>Includes event details, venue selection, schedule, and miscellaneous items</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-buttons">
                        <div></div>
                        <button type="button" class="btn-res btn-next" onclick="saveAndGo(1)">Continue</button>
                    </div>
                </div>

                <!-- Step 1: Personal and Event Info (for Function Rooms) -->
                <div class="form-step" id="step1Form">
                    <div class="form-header"><h3>Reservation Information</h3><p>Enter your details and event information</p></div>
                    <div class="row">
                        <div class="col-md-4"><div class="form-group"><label>Last Name *</label><input type="text" class="form-control" name="last_name" id="last_name" required></div></div>
                        <div class="col-md-4"><div class="form-group"><label>First Name *</label><input type="text" class="form-control" name="first_name" id="first_name" required></div></div>
                        <div class="col-md-2"><div class="form-group"><label>M.I.</label><input type="text" class="form-control" name="middle_initial" id="middle_initial" maxlength="2"></div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6"><div class="form-group"><label>Email *</label><input type="email" class="form-control" name="email" id="email" required>
                    </div></div>
                        <div class="col-md-6"><div class="form-group"><label>Contact Number *</label><input type="tel" class="form-control" name="contact" id="contact" maxlength="11" pattern="[0-9]{11}" required></div></div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-6"><div class="form-group"><label>Office Type *</label>
                            <select class="form-select" name="office_type_id" id="officeType" required>
                                <option value="">Select Type</option>
                                <?php foreach ($office_types as $ot): ?>
                                <option value="<?= (int)$ot['id'] ?>" data-name="<?= htmlspecialchars($ot['name']) ?>"><?= htmlspecialchars($ot['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div></div>
                        <div class="col-md-6" id="officeSelectWrap"><div class="form-group"><label>Office / College / Organization *</label>
                            <select class="form-select" name="office_id" id="officeSelect"><option value="">Select Type First</option></select>
                        </div></div>
                        <div class="col-md-6" id="officeExternalWrap" style="display:none"><div class="form-group"><label>Office/Organization Name *</label>
                            <input type="text" class="form-control" name="office_external_name" id="officeExternal" placeholder="Enter name">
                        </div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6"><div class="form-group"><label>Event Type *</label>
                            <select class="form-select" name="event_type_id" id="eventTypeId" required>
                                <option value="">Select</option>
                                <?php foreach ($event_types as $et): ?>
                                <option value="<?= (int)$et['id'] ?>"><?= htmlspecialchars($et['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div></div>
                        <div class="col-md-6"><div class="form-group"><label>Activity / Event Name *</label><input type="text" class="form-control" name="activity_name" id="activity_name" required></div></div>
                    </div>
                    
                    <div class="form-group mt-4">
                        <label class="form-label fw-bold">Banquet Style (Optional)</label>
                        <div class="d-flex align-items-center">
                            <button type="button" class="btn-res btn-next" onclick="openBanquetModal()" style="padding: 0.6rem 1.5rem;">
                                <i class="bi bi-image me-2"></i> Choose Banquet Style
                            </button>
                            <span id="selectedBanquetName" class="ms-3 text-muted small"></span>
                        </div>
                        <small class="text-muted">Select a banquet style for your event layout</small>
                    </div>
                    
                    <div class="form-group"><label>Number of Guests *</label><input type="number" class="form-control" name="participants" id="participants" min="1" max="200" value="1" required></div>
                    
                    <input type="hidden" name="banquet_style_id" id="banquetStyleId" value="">
                    
                    <div class="form-buttons">
                        <button type="button" class="btn-res btn-prev" onclick="goToStep(0)">Back</button>
                        <button type="button" class="btn-res btn-next" onclick="saveAndGo(2)">Continue</button>
                    </div>
                </div>

                <!-- Step 1G: Guest Registration Form (for Guest Rooms) -->
                <div class="form-step" id="step1GForm" style="display: none;">
                    <div class="form-header">
                        <h3><i class="bi bi-person-badge me-2"></i>Guest Registration Form</h3>
                        <p>Please fill out the guest information form</p>
                    </div>
                    
                    <div class="guest-registration-form">
                        <!-- Guest's Information -->
                        <div class="guest-form-section">
                            <h4><i class="bi bi-person-circle"></i> Principal Guest's Information</h4>
                            <div class="guest-name-row">
                                <div class="form-group">
                                    <label>Last Name *</label>
                                    <input type="text" class="form-control" name="guest_last_name" id="guest_last_name" required>
                                </div>
                                <div class="form-group">
                                    <label>First Name *</label>
                                    <input type="text" class="form-control" name="guest_first_name" id="guest_first_name" required>
                                </div>
                                <div class="form-group">
                                    <label>M.I.</label>
                                    <input type="text" class="form-control" name="guest_middle_initial" id="guest_middle_initial" maxlength="2">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Date of Birth *</label>
                                        <input type="date" class="form-control" name="guest_dob" id="guest_dob" required>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label>Address *</label>
                                        <input type="text" class="form-control" name="guest_address" id="guest_address" placeholder="Street, City, Province" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Email Address *</label>
                                        <input type="email" class="form-control" name="guest_email" id="guest_email" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Contact Number *</label>
                                        <input type="tel" class="form-control" name="guest_contact" id="guest_contact" maxlength="11" pattern ="[0-9]{11}" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                             <!-- Room Selection -->
                        <div class="guest-form-section">
                            <h4><i class="bi bi-door-open"></i> Room Selection</h4>
                            
                            <div class="room-selector">
                                <div class="form-group">
                                    <label>Room *</label>
                                    <select class="form-select" name="guest_room_id" id="guest_room_id" required onchange="updateRoomCapacity(this)">
                                        <option value="">Select Room</option>
                                        <?php foreach ($guest_venues as $room): ?>
                                        <option value="<?= $room['id'] ?>" data-capacity="<?= $room['capacity'] ?>" data-name="<?= htmlspecialchars($room['name']) ?>">
                                            <?= htmlspecialchars($room['name']) ?> (Max <?= $room['capacity'] ?> guests)
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mt-3">
                                <div class="form-group">
                                    <label>Remarks / Special Arrangements</label>
                                    <textarea class="form-control" name="guest_remarks" id="guest_remarks" rows="3" placeholder="Any special requests or arrangements...(Extra Beds, etc.)"></textarea>
                                </div>
                            </div>
                        </div>

                        
                        <!-- Other Guest Names - Dynamic Cards -->
                        <div class="guest-form-section">
                            <h4><i class="bi bi-people-fill"></i> Other Guest Names</h4>
                            <p class="small text-muted mb-2">Click "Add Guests" button to add more guests. Each room has a maximum capacity.</p>
                            
                            <div id="guests-container" class="guests-container">
                                <!-- Guest cards will be dynamically added here -->
                            </div>
                            
                            <button type="button" class="btn-add-guests" onclick="showGuestInputDialog()">
                                <i class="bi bi-plus-circle"></i> Add Guests
                            </button>
                            
                            <div class="mt-2">
                                <small class="text-muted">
                                    <i class="bi bi-info-circle"></i> 
                                    <span id="roomLimitInfo">Select a room to see guest limits</span>
                                </small>
                            </div>
                        </div>
                        
                        <!-- Arrival & Departure -->
                        <div class="guest-form-section">
                            <h4><i class="bi bi-calendar-check"></i> Stay Details</h4>
                            
                            <div class="arrival-departure-grid">
                                <div>
                                    <div class="form-group">
                                        <label>Arrival Date *</label>
                                        <input type="date" class="form-control" name="arrival_date" id="arrival_date" min="<?= date('Y-m-d') ?>" required>
                                    </div>
                                    <div class="time-grid-small">
                                        <div class="form-group">
                                            <label>Check-in Time *</label>
                                            <select class="form-select" name="checkin_time" id="checkin_time" required>
                                                <option value="">Select time</option>
                                                <?php for ($h = 11; $h <= 23; $h++): 
                                                    $time = sprintf('%02d:00', $h);
                                                    $display = date('g:i A', strtotime($time));
                                                ?>
                                                <option value="<?= $time ?>"><?= $display ?></option>
                                                <?php endfor; ?>
                                            </select>
                                            <small class="text-muted">Check-in: 11:00 AM - 11:00 PM</small>
                                        </div>
                                        <div class="form-group">
                                            <label>Check-out Time *</label>
                                            <select class="form-select" name="checkout_time" id="checkout_time" required>
                                                <option value="">Select time</option>
                                                <?php for ($h = 0; $h <= 12; $h++): 
                                                    $time = sprintf('%02d:00', $h);
                                                    $display = date('g:i A', strtotime($time));
                                                ?>
                                                <option value="<?= $time ?>"><?= $display ?></option>
                                                <?php endfor; ?>
                                            </select>
                                            <small class="text-muted">Check-out: 12:00 AM - 12:00 PM</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div>
                                    <div class="form-group">
                                        <label>Departure Date *</label>
                                        <input type="date" class="form-control" name="departure_date" id="departure_date" min="<?= date('Y-m-d', strtotime('+1 day')) ?>" required>
                                    </div>
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label>No. of Adults *</label>
                                                <input type="number" class="form-control" name="adults_count" id="adults_count" min="1" max="10" value="1" required>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label>No. of Kids *</label>
                                                <input type="number" class="form-control" name="kids_count" id="kids_count" min="0" max="10" value="0">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        
                        <!-- Registered By -->
                        <div class="guest-form-section">
                            <h4><i class="bi bi-pencil-square"></i> Registered By</h4>
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Name of Person Registering *</label>
                                        <input type="text" class="form-control" name="registered_by" id="registered_by" placeholder="Full name of the person filling out this form" required>
                                    </div>
                                </div>
                            </div>
                            <!-- Digital Signature (draw) -->
                            <div class="signature-section">
                                <label class="form-label">Digital Signature *</label>
                                <div class="signature-pad" id="guestSignatureWrap">
                                    <canvas id="guestSignaturePad" aria-label="Draw your signature"></canvas>
                                </div>
                                <div class="signature-actions">
                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="guestSigClear">
                                        <i class="bi bi-eraser"></i> Clear
                                    </button>
                                    <small class="text-muted">Use your finger (mobile) or mouse (PC) to sign.</small>
                                </div>
                            </div>
                            <input type="hidden" id="guest_signature" value="">
                            <input type="hidden" id="guest_form_date" value="<?= date('Y-m-d') ?>">
                        </div>
                        
                        <!-- Data Privacy -->
                        <div class="data-privacy-box">
                            <p><strong>Data Privacy and Protection</strong></p>
                            <p>During your stay, information will be collected about you and your preferences in order to provide you with the best possible service. The information will be retained to facilitate future stays at BatStateU ARASOF Hostel. If there are any questions regarding this data privacy, feel free to let us know at hostel.nasugbu@g.batstate-u.edu.ph</p>
                            <p class="mb-0">By signing, you are expressly giving your consent to the collection and storage of your personal data as provided herein.</p>
                        </div>
                        
                        <div class="form-check mt-3">
                            <input class="form-check-input" type="checkbox" id="guestConsent" required>
                            <label class="form-check-label" for="guestConsent">
                                I have read and agree to the data privacy policy. I consent to the collection and storage of my personal information.
                            </label>
                        </div>
                    </div> <!-- Close guest-registration-form -->
                    
                    <div class="form-buttons">
                        <button type="button" class="btn-res btn-prev" onclick="goToStep(0)">Back</button>
                        <button type="button" class="btn-res btn-next" onclick="saveGuestAndGo(4)">Continue</button>
                    </div>
                </div>


                <!-- Step 2: Venue selection (for Function Rooms only) -->
                <div class="form-step" id="step2Form">
                    <div class="form-header"><h3>Select Venue</h3><p>Choose function rooms to use</p></div>

                    <!-- Pricing info box — shown only for External --------------------------->
                    <div id="externalPricingBox" style="display:none; margin-bottom:1.25rem;">
                        <div style="background:linear-gradient(135deg,#fff5f5,#ffe8e8);border:1.5px solid #f5c6cb;border-radius:12px;padding:1rem 1.2rem;">
                            <div style="display:flex;align-items:center;gap:.6rem;margin-bottom:.75rem;">
                                <span style="background:#b71c1c;color:white;border-radius:8px;padding:.3rem .6rem;font-size:.78rem;font-weight:700;">💰 External Client Rates</span>
                                <small style="color:#721c24;font-size:.72rem;">Rates apply per event, regardless of which rooms are selected.</small>
                            </div>
                            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:.75rem;margin-bottom:.9rem;">
                                <div id="priceCard_half"  style="background:white;border:1.5px solid #f5c6cb;border-radius:10px;padding:.75rem;text-align:center;">
                                    <div style="font-size:.75rem;color:#888;margin-bottom:.2rem;">⏱ Half Day (up to 4 hrs)</div>
                                    <div style="font-size:1.35rem;font-weight:800;color:#b71c1c;" id="display_half_day">₱2,000.00</div>
                                    <div style="font-size:.68rem;color:#aaa;">Incl. aircon, tables &amp; chairs, restrooms, parking</div>
                                </div>
                                <div id="priceCard_whole" style="background:white;border:1.5px solid #f5c6cb;border-radius:10px;padding:.75rem;text-align:center;">
                                    <div style="font-size:.75rem;color:#888;margin-bottom:.2rem;">🌅 Whole Day (up to 8 hrs)</div>
                                    <div style="font-size:1.35rem;font-weight:800;color:#b71c1c;" id="display_whole_day">₱3,000.00</div>
                                    <div style="font-size:.68rem;color:#aaa;">Incl. aircon, tables &amp; chairs, restrooms, parking</div>
                                </div>
                                <div id="priceCard_ext"   style="background:white;border:1.5px solid #f5c6cb;border-radius:10px;padding:.75rem;text-align:center;">
                                    <div style="font-size:.75rem;color:#888;margin-bottom:.2rem;">⏰ Extension (per hour)</div>
                                    <div style="font-size:1.35rem;font-weight:800;color:#b71c1c;" id="display_ext_rate">₱400.00</div>
                                    <div style="font-size:.68rem;color:#aaa;">Beyond the rented period</div>
                                </div>
                                <div id="priceCard_sound" style="background:white;border:1.5px solid #f5c6cb;border-radius:10px;padding:.75rem;text-align:center;">
                                    <div style="font-size:.75rem;color:#888;margin-bottom:.2rem;">🔊 Basic Sound System</div>
                                    <div style="font-size:1.35rem;font-weight:800;color:#b71c1c;" id="display_sound_fee">₱1,500.00</div>
                                    <div style="font-size:.68rem;color:#aaa;">2 wireless mics &amp; speakers (optional)</div>
                                </div>
                            </div>
                            <!-- Important notes -->
                            <details style="border-top:1px solid #f5c6cb;padding-top:.75rem;cursor:pointer;">
                                <summary style="font-size:.78rem;font-weight:700;color:#8b0000;list-style:none;display:flex;align-items:center;gap:.4rem;">
                                    <i class="bi bi-info-circle-fill"></i> Important Policies &amp; Notes <span style="font-size:.7rem;opacity:.7;">(click to expand)</span>
                                </summary>
                                <ul style="margin:.6rem 0 0 .5rem;padding-left:1rem;font-size:.76rem;color:#555;line-height:1.9;">
                                    <li>Payment must be <strong>settled before the day of the event</strong></li>
                                    <li>Additional fees apply for overtime work on <strong>weekends &amp; evening events on weekdays</strong></li>
                                    <li>Air-conditioning is turned on <strong>30 minutes before</strong> scheduled event</li>
                                    <li>Advance setup subject to approval (AC off during setup)</li>
                                    <li><strong>No Wi-Fi</strong> service included</li>
                                    <li>Balloons and adhesives <strong>not allowed</strong></li>
                                    <li><strong>No disposable water bottles</strong></li>
                                    <li>Buffet-style food arrangement <strong>only</strong></li>
                                    <li>No single-use plastics</li>
                                    <li>No tarpaulins (per OUP Memorandum)</li>
                                    <li>Proper waste segregation required</li>
                                </ul>
                            </details>
                        </div>
                    </div>

                    <!-- Internal notice -->
                    <div id="internalPricingBox" style="display:none;margin-bottom:1.25rem;">
                        <div style="background:linear-gradient(135deg,#f0fff4,#c6f6d5);border:1.5px solid #9ae6b4;border-radius:12px;padding:.85rem 1.2rem;display:flex;align-items:center;gap:.75rem;">
                            <i class="bi bi-check-circle-fill" style="color:#16a34a;font-size:1.4rem;flex-shrink:0;"></i>
                            <div>
                                <strong style="color:#14532d;font-size:.88rem;">Free for Internal University Use</strong>
                                <div style="font-size:.75rem;color:#166534;margin-top:.1rem;">Function rooms are available at no charge for university colleges, offices, and student organizations.</div>
                            </div>
                        </div>
                    </div>

                    <?php if (!empty($function_venues)): ?>
                    <h5 class="mt-3 mb-2">Function Rooms</h5>
                    <?php
                    // Use rates from first venue for display (all function rooms share same rate panel)
                    $firstVenue = $function_venues[0];
                    $phd  = number_format($firstVenue['half_day_rate'], 2);
                    $pwd  = number_format($firstVenue['whole_day_rate'], 2);
                    $per  = number_format($firstVenue['extension_rate'], 2);
                    $psf  = number_format($firstVenue['sound_system_fee'], 2);
                    ?>
                    <!-- Pricing data embedded for JS -->
                    <div id="venuePricingData"
                         data-half-day="<?= (float)$firstVenue['half_day_rate'] ?>"
                         data-whole-day="<?= (float)$firstVenue['whole_day_rate'] ?>"
                         data-extension="<?= (float)$firstVenue['extension_rate'] ?>"
                         data-sound-fee="<?= (float)$firstVenue['sound_system_fee'] ?>"
                         style="display:none;"></div>
                    <?php foreach ($function_venues as $venue): ?>
                    <div class="room-select-card"
                        data-id="<?= $venue['id'] ?>"
                        data-name="<?= htmlspecialchars($venue['name']) ?>"
                        data-floor="<?= htmlspecialchars($venue['floor'] ?? '') ?>"
                        data-capacity="<?= $venue['capacity'] ?>"
                        data-half-day="<?= (float)$venue['half_day_rate'] ?>"
                        data-whole-day="<?= (float)$venue['whole_day_rate'] ?>"
                        data-extension="<?= (float)$venue['extension_rate'] ?>"
                        data-sound-fee="<?= (float)$venue['sound_system_fee'] ?>"
                        onclick="toggleVenue(this)">
                        <input type="checkbox" name="venue_ids[]" value="<?= $venue['id'] ?>" style="display:none">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-1">
                            <strong><?= htmlspecialchars($venue['name']) ?></strong>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-light text-dark"><?= $venue['capacity'] ?> pax</span>
                            </div>
                        </div>
                        <small class="text-muted"><?= htmlspecialchars($venue['floor'] ?? '') ?></small>
                        <p class="small text-muted mt-1 mb-0"><?= htmlspecialchars($venue['description'] ?? '') ?></p>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>

                    <p class="text-danger small mt-2" id="venueError" style="display:none">Please select at least one venue.</p>

                    <div class="form-buttons">
                        <button type="button" class="btn-res btn-prev" onclick="goToStep(1)">Back</button>
                        <button type="button" class="btn-res btn-next" onclick="saveAndGo(3)">Continue</button>
                    </div>
                </div>

                <!-- Step 3: Schedule (for Function Rooms only) -->
                <div class="form-step" id="step3Form">
                    <p class="mb-3"><strong>Add date and time for each selected facility (7:00 AM - 11:00 PM only).</strong></p>
                    <div id="scheduleFacilitiesContainer"></div>
                    <!-- Live pricing summary for external clients -->
                    <div id="priceSummaryBox" style="display:none;margin-top:1rem;border:1.5px solid #f5c6cb;border-radius:12px;background:linear-gradient(135deg,#fff5f5,#ffe8e8);padding:1rem 1.2rem;">
                        <div style="font-size:.82rem;font-weight:700;color:#8b0000;margin-bottom:.6rem;"><i class="bi bi-calculator me-1"></i>Estimated Cost Breakdown</div>
                        <div id="priceSummaryLines" style="font-size:.8rem;color:#555;line-height:2;"></div>
                        <div style="border-top:1px solid #f5c6cb;margin-top:.6rem;padding-top:.6rem;display:flex;justify-content:space-between;align-items:center;">
                            <strong style="color:#8b0000;font-size:.85rem;">Estimated Total</strong>
                            <strong style="color:#b71c1c;font-size:1.1rem;" id="priceSummaryTotal">₱0.00</strong>
                        </div>
                        <div style="font-size:.7rem;color:#888;margin-top:.4rem;"><i class="bi bi-info-circle me-1"></i>Sound system fee added at checkout if selected. Actual bill may vary.</div>
                    </div>
                    <p class="text-danger small mt-2" id="scheduleError" style="display:none">Please add at least one schedule for each selected facility.</p>
                    <div class="form-buttons">
                        <button type="button" class="btn-res btn-prev" onclick="goToStep(2)">Back</button>
                        <button type="button" class="btn-res btn-next" onclick="saveAndGo(4)">Continue</button>
                    </div>
                </div>


                <!-- Step 4: Terms -->
                <div class="form-step" id="step4Form">
                    <div class="form-header">
                        <h3>Terms & Conditions</h3>
                        <p>Please read and accept the terms for your reservation type</p>
                    </div>
                    
                    <div id="termsContainer" class="terms-container">
                        <!-- Content will be loaded here -->
                    </div>
                    
                    <input type="hidden" id="termsFullName" value="">
                    <input type="hidden" id="termsPosition" value="">
                    <input type="hidden" id="termsDate" value="<?= date('F j, Y') ?>">

                    <div class="form-check mt-3">
                            <input class="form-check-input" type="checkbox" id="termsAgree" disabled>
                            <label class="form-check-label" for="termsAgree">
                                I have read, understood, and agree to abide by the terms and conditions above. I acknowledge that failure to comply may result in penalties or affect future reservation privileges.
                            </label>
                        </div>
                    
                    <div class="form-buttons">
                        <button type="button" class="btn-res btn-prev" onclick="goToStep(3)">Back</button>
                        <button type="button" class="btn-res btn-next" onclick="saveAndGo(5)" id="nextStep4Btn">Continue</button>
                    </div>
                </div>

                <!-- Step 5: Miscellaneous Needed (for Function Rooms only) -->
                <div class="form-step" id="step5Form">
                    <div class="form-header"><h3>Miscellaneous Needed</h3><p>Please check the items you need and specify quantities where applicable.</p></div>
                    <div class="misc-list">
                        <!-- Basic Sound System -->
                        <div class="misc-item" data-key="basic_sound_system">
                            <label class="misc-main-label">
                                <input type="checkbox" class="misc-cb" data-key="basic_sound_system"> 
                                <span class="misc-title">Basic Sound System</span>
                                <span class="misc-limits">(Max: 2 speakers, 2 microphones)</span>
                            </label>
                            <div class="misc-sub-items">
                                <div class="misc-sub-item">
                                    <label>Speakers:</label>
                                    <input type="number" class="form-control misc-qty misc-sound" data-field="speaker" 
                                           min="0" max="2" value="0" placeholder="0" disabled>
                                    <span class="limit-hint">max 2</span>
                                </div>
                                <div class="misc-sub-item">
                                    <label>Microphones:</label>
                                    <input type="number" class="form-control misc-qty misc-sound" data-field="mic" 
                                           min="0" max="2" value="0" placeholder="0" disabled>
                                    <span class="limit-hint">max 2</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Round Table -->
                        <div class="misc-item">
                            <label class="misc-main-label">
                                <input type="checkbox" class="misc-cb" data-key="round_table"> 
                                <span class="misc-title">Round Table</span>
                                <span class="misc-limits">(Max: 16)</span>
                            </label>
                            <div class="misc-single-item">
                                <input type="number" class="form-control misc-qty-inline" data-key="round_table" 
                                       min="0" max="16" value="0" placeholder="Quantity" disabled>
                                <span class="limit-hint">max 16</span>
                            </div>
                        </div>
                        
                        <!-- Banquet Chairs -->
                        <div class="misc-item">
                            <label class="misc-main-label">
                                <input type="checkbox" class="misc-cb" data-key="banquet_chairs"> 
                                <span class="misc-title">Banquet Chairs</span>
                                <span class="misc-limits">(Max: 190)</span>
                            </label>
                            <div class="misc-single-item">
                                <input type="number" class="form-control misc-qty-inline" data-key="banquet_chairs" 
                                       min="0" max="190" value="0" placeholder="Quantity" disabled>
                                <span class="limit-hint">max 190</span>
                            </div>
                        </div>
                        
                        <!-- View Board (Checkbox only - no quantity) -->
                        <div class="misc-item misc-checkbox-only">
                            <label class="misc-main-label misc-checkbox-label">
                                <input type="checkbox" class="misc-cb" data-key="view_board"> 
                                <span class="misc-title">View Board</span>
                                <span class="misc-badge">(1 unit provided)</span>
                            </label>
                            <input type="hidden" name="view_board_qty" value="1" data-key="view_board">
                        </div>
                        
                        <!-- Rectangular Table -->
                        <div class="misc-item">
                            <label class="misc-main-label">
                                <input type="checkbox" class="misc-cb" data-key="rectangular_table"> 
                                <span class="misc-title">Rectangular Table</span>
                                <span class="misc-limits">(Max: 10)</span>
                            </label>
                            <div class="misc-single-item">
                                <input type="number" class="form-control misc-qty-inline" data-key="rectangular_table" 
                                       min="0" max="10" value="0" placeholder="Quantity" disabled>
                                <span class="limit-hint">max 10</span>
                            </div>
                        </div>
                    </div>
                
                    <input type="hidden" name="banquet_style_id" id="banquetStyleId" value="">
                    <div class="form-group mt-3">
                        <label>Additional Instruction</label>
                        <textarea class="form-control" name="additional_instruction" id="additionalInstruction" rows="4" placeholder="Special requests or instructions..."></textarea>
                    </div>
                    <div class="form-buttons">
                        <button type="button" class="btn-res btn-prev" onclick="goToStep(4)">Back</button>
                        <button type="button" class="btn-res btn-next" onclick="saveAndGo(6)">Continue</button>
                    </div>
                </div>

                <!-- Step 6: Summary -->
                <div class="form-step" id="step6Form">
                    <div class="form-header d-flex align-items-center gap-2"><i class="bi bi-check-circle-fill text-danger"></i><h3 class="mb-0">Reservation Summary</h3></div>
                    <div id="summaryBox" class="mb-4"></div>
                    <p class="small text-muted fst-italic">This event/activity will be officially reserved upon approval.</p>
                    <div class="form-buttons">
                        <button type="button" class="btn-res btn-prev" onclick="goToStep(5)">Back</button>
                        <button type="button" class="btn-res btn-submit" id="btnSubmit" onclick="submitForm()">Submit Reservation</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</main>

<!-- Time slot modal -->
<div class="res-modal time-slot-modal" id="timeSlotModal">
    <div class="modal-box">
        <h4>Add Schedule</h4>
        <div class="form-group">
            <label>Date *</label>
            <input type="date" class="form-control" id="timeModalDateInput" min="<?= date('Y-m-d') ?>">
        </div>
        <div id="availabilityStatus" style="display:none; margin-bottom:0.75rem; padding:0.6rem 0.9rem; border-radius:8px; font-size:0.875rem;"></div>
        <div class="time-grid">
            <div class="form-group">
                <label>Start Time * (7:00 AM - 10:30 PM)</label>
                <select class="form-select" id="timeModalStart">
                    <option value="">Select date first</option>
                </select>
            </div>
            <div class="form-group">
                <label>End Time * (7:30 AM - 11:00 PM)</label>
                <select class="form-select" id="timeModalEnd">
                    <option value="">Select start time first</option>
                </select>
            </div>
        </div>
        <div id="bookedSlotsInfo" style="display:none; margin-top:0.5rem; font-size:0.8rem; color:#721c24; background:#f8d7da; padding:0.5rem 0.75rem; border-radius:6px;"></div>
        <div id="timeModalError" style="display:none; margin-top:0.75rem; padding:0.65rem 0.9rem; border-radius:8px; font-size:0.875rem; background:#f8d7da; color:#721c24; border-left:3px solid #dc3545;"></div>
        <div class="d-flex gap-2 justify-content-end mt-3">
            <button type="button" class="btn-res btn-prev" onclick="closeTimeModal()">Cancel</button>
            <button type="button" class="btn-res btn-next" id="addScheduleBtn" onclick="confirmTimeSlot()">Add Schedule</button>
        </div>
    </div>
</div>

<!-- System Alert Modal -->
<div class="res-modal" id="systemAlertModal">
    <div class="modal-box" style="max-width:420px; text-align:center; padding:2rem 1.75rem;">
        <div id="systemAlertIcon" style="font-size:2.5rem; margin-bottom:0.75rem;"></div>
        <h4 id="systemAlertTitle" style="margin-bottom:0.5rem; font-size:1.1rem; color:#2c3e50;"></h4>
        <p id="systemAlertMessage" style="color:#555; font-size:0.95rem; margin-bottom:1.5rem; line-height:1.6;"></p>
        <button type="button" class="btn-res btn-next" style="min-width:100px;" onclick="closeSystemAlert()">OK</button>
    </div>
</div>

<!-- Banquet Style Modal -->
<div class="res-modal" id="banquetModal">
    <div class="modal-box banquet-modal-box">
        <h4>Choose Banquet Style</h4>
        <p class="small text-muted mb-3">Select one banquet style for your event, then confirm.</p>
        <div class="banquet-cards" id="banquetCards">
            <!-- Banquet styles will be loaded via AJAX -->
        </div>
        <div class="d-flex justify-content-end gap-2 mt-3">
            <button type="button" class="btn-res btn-prev" onclick="closeBanquetModal()">Cancel</button>
            <button type="button" class="btn-res btn-next" id="banquetConfirmBtn" onclick="confirmBanquetSelection()">Confirm Selection</button>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div class="res-modal" id="confirmModal">
    <div class="modal-box">
        <h4>Confirm Reservation</h4>
        <p>Are you sure you want to submit this reservation?</p>
        <div class="d-flex gap-2 justify-content-end mt-3">
            <button type="button" class="btn-res btn-prev" onclick="closeConfirmModal()">Cancel</button>
            <button type="button" class="btn-res btn-next" onclick="doSubmit()">Confirm</button>
        </div>
    </div>
</div>

<!-- Guest Count Input Modal -->
<div class="res-modal" id="guestCountModal">
    <div class="modal-box" style="max-width:420px; padding:2rem 1.75rem;">
        <div style="font-size:2rem; margin-bottom:0.5rem; text-align:center;">&#128101;</div>
        <h4 style="margin-bottom:0.25rem; font-size:1.1rem; color:#2c3e50; text-align:center;">Add Additional Guests</h4>
        <p id="guestCountInfo" style="color:#555; font-size:0.9rem; margin-bottom:1.25rem; text-align:center; line-height:1.5;"></p>
        <div style="margin-bottom:1.25rem;">
            <label style="font-size:0.875rem; font-weight:600; color:#2c3e50; display:block; margin-bottom:0.5rem;">Number of guests to add</label>
            <div style="display:flex; align-items:center; gap:0.75rem;">
                <button type="button" onclick="guestCountStep(-1)" style="width:38px;height:38px;border-radius:50%;border:2px solid #b71c1c;background:white;color:#b71c1c;font-size:1.25rem;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;flex-shrink:0;">&#8722;</button>
                <input type="number" id="guestCountInput" min="1" max="99" value="1"
                    style="flex:1;text-align:center;font-size:1.25rem;font-weight:700;border:2px solid #dee2e6;border-radius:8px;padding:0.4rem 0.5rem;color:#2c3e50;outline:none;"
                    oninput="clampGuestCount()">
                <button type="button" onclick="guestCountStep(1)" style="width:38px;height:38px;border-radius:50%;border:2px solid #b71c1c;background:#b71c1c;color:white;font-size:1.25rem;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;flex-shrink:0;">+</button>
            </div>
            <p id="guestCountSlots" style="font-size:0.8rem;color:#888;margin-top:0.5rem;text-align:center;"></p>
        </div>
        <div style="display:flex;gap:0.75rem;justify-content:flex-end;">
            <button type="button" class="btn-res btn-prev" onclick="closeGuestCountModal()">Cancel</button>
            <button type="button" class="btn-res btn-next" onclick="confirmGuestCount()">Add Guests</button>
        </div>
    </div>
</div>

<!-- Guest Remove Confirm Modal -->
<div class="res-modal" id="guestRemoveModal">
    <div class="modal-box" style="max-width:400px;padding:2rem 1.75rem;text-align:center;">
        <div style="font-size:2.5rem;margin-bottom:0.75rem;">&#128465;</div>
        <h4 style="margin-bottom:0.5rem;font-size:1.1rem;color:#2c3e50;">Remove Guest?</h4>
        <p id="guestRemoveName" style="color:#555;font-size:0.95rem;margin-bottom:1.5rem;line-height:1.6;"></p>
        <div style="display:flex;gap:0.75rem;justify-content:center;">
            <button type="button" class="btn-res btn-prev" onclick="closeGuestRemoveModal()">Cancel</button>
            <button type="button" class="btn-res btn-next" style="background:#b71c1c;" onclick="confirmGuestRemove()">Yes, Remove</button>
        </div>
    </div>
</div>

<!-- Result Modal -->
<div class="res-modal" id="resultModal">
    <div class="modal-box" id="resultContent"></div>
</div>

<script>
var officesByType = <?= json_encode($offices_by_type) ?>;
var baseUrl = '<?= $base ?>';
var currentStep = 0;
var selectedVenues = [];
var facilitySchedules = {};
var timeModalContext = null;
var reservationType = ''; // No default — user must choose

// Guest limits per room
var guestLimits = {
    'Guest Room 1': 4,
    'Guest Room 2': 5,
    'Guest Room 3': 5,
    'Guest Room 4': 8,
    'Dormitory': 24
};

let guestCount = 0;

// Misc limits configuration
var miscLimits = {
    'basic_sound_system': {
        'speaker': { min: 0, max: 2, label: 'Speakers' },
        'mic': { min: 0, max: 2, label: 'Microphones' }
    },
    'round_table': { min: 0, max: 16, label: 'Round Tables' },
    'banquet_chairs': { min: 0, max: 190, label: 'Banquet Chairs' },
    'view_board': { type: 'boolean', label: 'View Board' },
    'rectangular_table': { min: 0, max: 10, label: 'Rectangular Tables' }
};

// Store terms content for college and external
var collegeTerms = `We would like to remind all colleges of the following rules and regulations in order to assure the proper use and maintenance of the ARASOF-Nasugbu Hostel function rooms:

1. Reservation Process: The function rooms are available on a first-come, first-served basis, with priority given to events approved by the Office of the Chancellor.

2. Reservation Form: To ensure appropriate service, all necessary information must be filled out on the hostel reservation form.

3. Use of Equipment and Supplies: Any requests for tablecloths, napkins, dining utensils, or laboratory tools and equipment must be submitted using a requisition form and coordinated with the Hostel Laboratory Assistant.

4. Decorations: Event decorations are allowed, but tapes, adhesives, nails, and screws are strictly prohibited to prevent damage. Balloons are not allowed.

5. Water Dispensers: The organizer is responsible for providing drinking water during the event.

6. Audio-Visual Equipment: Coordination with ICT or the Audio Room (PFM) is necessary for the use of a view board, projector, microphone, or sound system.

7. Event Setup and Clean-Up: Facilitators are responsible for setting up chairs and tables prior to the event and cleaning up the function room afterward.

8. Environmental Sustainability Guidelines:
   • No Disposable Water Bottles – Bring personal tumblers and use refill stations.
   • Buffet-Style Food Only – To minimize waste, only buffet-style meals are allowed.
   • No Single-Use Plastics – Items like disposable food wrappers, containers, cups, and straws are strictly prohibited.
   • CLAYGO Policy – All event participants must clean as they go.
   • No Tarpaulins – The use of tarpaulins in university events is prohibited.
   • Proper Waste Segregation – Waste must be sorted into designated bins.
   • No Laminated Paper Products – Food containers, paper cups, and plates made from laminated paper are discouraged. Bringing personal food containers and tumblers is required for take-out meals.

9. Post-Event Cleanliness: The function room must be returned to its original, clean, and damage-free condition following its use.

All colleges must ensure strict compliance to these guidelines. Failure to comply with these regulations could affect future reservations. For concerns, please reach out to the Hostel Management Office.

Thank you for your cooperation.`;

var externalTerms = `1. The function room reservation in the hostel operates on a first-come, first-served basis. We prioritize events with approved letters signed by the Office of the Chancellor.

2. Make sure to fill out all the necessary information in the hostel reservation form so that we can better assist you with your events.

3. If the event requires tablecloths, napkins, or even kitchen utensils and other laboratory tools and equipment, the facilitators must fill out the requisition form and coordinate with the Hostel Laboratory Assistant.

4. Decorations and props are allowed to fit the theme of the event however, the use of tapes and all kinds of adhesives and nails/screws on the wall are not allowed to avoid chipping of paint and or leaving adhesive marks. The use of balloons as décor is not allowed.

5. If water dispensers are needed for the said event, the person in charge is responsible for providing a gallon of water for the event.

6. If the event requires the following: View board, Projector, Microphone, or basic sound system, the person in charge is responsible for coordinating with ICT or the Audio Room (PFM) to request the said items and assistance in setting up the equipment.

7. If students or colleagues are the facilitators of the event, they will be responsible to set up the chairs and tables before the events and clean the function rooms after use.

8. Environmental Sustainability Guidelines:
   • No Disposable Water Bottles – The use of disposable water bottles is strictly prohibited. All members are encouraged to bring their own tumblers or use the water refill stations available across the campus.
   • Buffet-Style Food Only – To reduce food waste, only buffet-style meals will be allowed during university events. Please take only what you can finish.
   • No Single-Use Plastics or Disposables – The use of single-use plastics, including food wrappers, containers, balloons, paper and plastic cups, straws, plastic stirrers, and similar items, is strictly prohibited.
   • Practice CLAYGO (Clean As You Go) – All event participants must practice the CLAYGO policy to maintain cleanliness and reduce the volume of waste.
   • Prohibition on Tarpaulins – The use of tarpaulins is prohibited in all university activities and events.
   • Proper Waste Segregation – All waste must be properly segregated according to the designated bins for biodegradable, recyclable, and non-recyclable materials.

9. The use of laminated paper products such as food containers, paper cups, and paper plates is strictly discouraged. "Bring your food container" policy shall be implemented for "take out" food and bringing of personal sustainable tumbler/mug for water refilling is highly encouraged.

10. The organizer must ensure that the function room is clean and damage-free after the activity.`;

// NEW: Guest terms for fallback
var guestTerms = `# HOSTEL ROOM GUIDELINES

1. BatStateU_Hostel is a non-smoking area.  
2. Standard Check-in time at 2:00 pm and 12:00 noon check out time.  
3. The hostel is located at BatStateU_ARASOF - Nasugbu Campus. Maintaining good relationships with faculty and students must be observed. Be generally mindful by their presence as they move around the building.  
4. Toned-down sounds between 7 AM until 6 PM are observed in consideration for the faculty and students during class hours.  
5. No Curfew administered for all the guests, however perceive not to disturb others upon returning to the Hostel late at night.  
6. Hostel Laundry Service for Php 100.00 per kilogram, inclusive of powder detergent w/ color protection and fabric softener. Housekeeping to assist with laundry provided with laundry bag.  
7. Trash Bins are placed around the Hostel. Proper throwing of trash helps us maintain the cleanliness of the facilities for the guests as well as for the faculty and students.  
8. Turning off the lights and air-conditioning as well as the faucet before leaving the Hostel room will help us conserve energy and water.  
9. BatStateU_Hostel is not liable for any lost or damage of guest’s personal belongings.  
10. Room Keys can be deposited at the reception. Any lost key will be charged accordingly.  
11. Incidental charges will apply for any loss or damages at the Hostel property during the guest’s stay. Settlement must be done before check-out/departure and must be settled through cash.  
12. The management reserves the right to refuse entry/stay to individuals violating Hotel policies and guidelines.  
13. Hostel Housekeeping staff is authorized to enter your room with or without guests inside for a housekeeping operation.  

---

# PROHIBITED ACTS

- Uncooked foods and cooking inside the Hostel room of prohibited.  
- Deadly weapons and illegal drugs are STRICTLY PROHIBITED inside the hostel.  
- Drinking inside the Hostel room is not allowed. Hostel Bar on the ground floor can be used for any alcoholic beverage consumption.  
- Pets are not allowed inside the property.  
- Only registered guests are allowed to stay in the Hostel room.  

For further clarification and queries please feel free to contact us at 09287842104 or email us at **hostel.nasugbu@g.batstate-u.edu.ph**.  

Thank you. We look forward in welcoming your group here at the Hostel!`;

// ========== PRICING UTILITIES ==========
function formatPHP(num) {
    return '₱' + parseFloat(num).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function isExternalOffice() {
    var ot = document.getElementById('officeType');
    if (!ot) return false;
    var opt = ot.options[ot.selectedIndex];
    return opt && opt.getAttribute('data-name') === 'External';
}

function getVenueRates() {
    var data = document.getElementById('venuePricingData');
    if (!data) return { halfDay: 2000, wholeDay: 3000, extension: 400, soundFee: 1500 };
    return {
        halfDay: parseFloat(data.getAttribute('data-half-day')) || 2000,
        wholeDay: parseFloat(data.getAttribute('data-whole-day')) || 3000,
        extension: parseFloat(data.getAttribute('data-extension')) || 400,
        soundFee: parseFloat(data.getAttribute('data-sound-fee')) || 1500
    };
}

function calcVenueCost(hours, rates) {
    if (hours <= 0) return { cost: 0, type: 'Invalid' };
    if (hours <= 4) return { cost: rates.halfDay, type: 'Half Day' };
    if (hours <= 8) return { cost: rates.wholeDay, type: 'Whole Day' };
    var extHours = Math.ceil(hours - 8);
    return { cost: rates.wholeDay + (extHours * rates.extension), type: 'Whole Day + ' + extHours + 'hr Ext.' };
}

function updateVenuePriceBadges() {
    var isExt = isExternalOffice();
    var extBox = document.getElementById('externalPricingBox');
    var intBox = document.getElementById('internalPricingBox');
    
    if (extBox) extBox.style.display = isExt ? 'block' : 'none';
    if (intBox) intBox.style.display = isExt ? 'none' : 'block';
    
    if (isExt) {
        var rates = getVenueRates();
        var dh = document.getElementById('display_half_day');
        var dw = document.getElementById('display_whole_day');
        var de = document.getElementById('display_ext_rate');
        var ds = document.getElementById('display_sound_fee');
        if (dh) dh.textContent = formatPHP(rates.halfDay);
        if (dw) dw.textContent = formatPHP(rates.wholeDay);
        if (de) de.textContent = formatPHP(rates.extension);
        if (ds) ds.textContent = formatPHP(rates.soundFee);
    }
}

function updateSchedulePriceSummary() {
    var summaryBox = document.getElementById('priceSummaryBox');     if (!summaryBox) return;
    if (!isExternalOffice() || Object.keys(facilitySchedules).length === 0) {
        summaryBox.style.display = 'none';
        return;
    }
    
    var rates = getVenueRates();
    var linesHtml = '';
    var totalCost = 0;
    var hasSchedules = false;
    
    for (var vid in facilitySchedules) {
        var schedules = facilitySchedules[vid];
        if (!schedules || schedules.length === 0) continue;
        hasSchedules = true;
        
        var venue = selectedVenues.find(v => v.id == vid);
        var venueName = venue ? venue.name : 'Venue';
        
        schedules.forEach(function(s) {
            var sm = timeToMins(s.start);
            var em = timeToMins(s.end);
            var hrs = (em - sm) / 60;
            var calc = calcVenueCost(hrs, rates);
            totalCost += calc.cost;
            var displayDate = s.date; // or formatted
            
            linesHtml += '<div style="display:flex;justify-content:space-between;padding:.2rem 0;border-bottom:1px solid #ffe8e8;">' +
                         '<span>' + venueName + ' ('+hrs.toFixed(1)+'h - ' + calc.type + ')</span>' +
                         '<strong>' + formatPHP(calc.cost) + '</strong></div>';
        });
    }
    
    if (!hasSchedules) {
        summaryBox.style.display = 'none';
        return;
    }
    
    summaryBox.style.display = 'block';
    
    var misc = getMiscItemsJson();
    if (misc && misc['basic_sound_system']) {
        totalCost += rates.soundFee;
        linesHtml += '<div style="display:flex;justify-content:space-between;padding:.2rem 0;border-bottom:1px solid #ffe8e8;color:#b71c1c;">' +
                     '<span>🔊 Basic Sound System</span>' +
                     '<strong>' + formatPHP(rates.soundFee) + '</strong></div>';
    }
    
    document.getElementById('priceSummaryLines').innerHTML = linesHtml;
    document.getElementById('priceSummaryTotal').textContent = formatPHP(totalCost);
}

// Generate time slots from 7:00 AM to 11:00 PM (30-min intervals)
var timeSlots = [];
for (var h = 7; h <= 23; h++) {
    for (var m = 0; m < 60; m += 30) {
        if (h === 23 && m > 0) continue;
        var t = (h < 10 ? '0' : '') + h + ':' + (m < 10 ? '0' : '') + m;
        timeSlots.push(t);
    }
}

// ========== GUEST CARD FUNCTIONS ==========
function updateRoomCapacity(select) {
    var selectedOption = select.options[select.selectedIndex];
    var roomName = selectedOption.getAttribute('data-name') || '';
    var capacity = parseInt(selectedOption.getAttribute('data-capacity') || '0');
    
    var limitInfo = document.getElementById('roomLimitInfo');
    if (roomName && capacity) {
        limitInfo.innerHTML = `<i class="bi bi-info-circle"></i> Selected: ${roomName} — Max ${capacity} total (1 principal + ${Math.max(0,capacity-1)} additional guest${Math.max(0,capacity-1)!==1?'s':''})`;
    } else {
        limitInfo.innerHTML = '<i class="bi bi-info-circle"></i> Select a room to see guest limits';
    }
    validateGuestCountAgainstCapacity();
}

function validateGuestCountAgainstCapacity() {
    var roomSelect = document.getElementById('guest_room_id');
    if (!roomSelect.value) return true;
    var selectedOption = roomSelect.options[roomSelect.selectedIndex];
    var roomName = selectedOption.getAttribute('data-name') || '';
    var capacity = parseInt(selectedOption.getAttribute('data-capacity') || '0');
    var maxAdditional = Math.max(0, capacity - 1);
    var currentGuests = document.querySelectorAll('.guest-card').length;
    if (currentGuests > maxAdditional) {
        showModalAlert('⚠️', 'Exceeds Capacity',
            `${roomName} allows ${capacity} total (1 principal + ${maxAdditional} additional). Please remove ${currentGuests - maxAdditional} guest(s).`);
        return false;
    }
    return true;
}

function showGuestInputDialog() {
    var roomSelect = document.getElementById('guest_room_id');
    if (!roomSelect.value) {
        showModalAlert('🏠', 'Select Room First', 'Please select a room before adding guests.');
        roomSelect.focus();
        return;
    }
    var selectedOption = roomSelect.options[roomSelect.selectedIndex];
    var roomName = selectedOption.getAttribute('data-name') || '';
    var capacity = parseInt(selectedOption.getAttribute('data-capacity') || '0');
    var maxAdditional = Math.max(0, capacity - 1);
    var currentGuests = document.querySelectorAll('.guest-card').length;
    var availableSlots = maxAdditional - currentGuests;
    if (availableSlots <= 0) {
        showModalAlert('⚠️', 'Maximum Reached',
            `${roomName} is full (${capacity} total: 1 principal + ${maxAdditional} additional). No more guests can be added.`);
        return;
    }
    window._guestDialogSlots = availableSlots;
    document.getElementById('guestCountInfo').textContent =
        `${roomName} — ${currentGuests} of ${maxAdditional} additional slot${maxAdditional!==1?'s':''} used.`;
    document.getElementById('guestCountSlots').textContent =
        `You can add up to ${availableSlots} more guest${availableSlots!==1?'s':''}.`;
    var input = document.getElementById('guestCountInput');
    input.max = availableSlots;
    input.value = 1;
    document.getElementById('guestCountModal').classList.add('show');
    setTimeout(function() { input.focus(); input.select(); }, 100);
}

function guestCountStep(delta) {
    var input = document.getElementById('guestCountInput');
    var val = parseInt(input.value) || 1;
    var max = parseInt(input.max) || 1;
    input.value = Math.min(max, Math.max(1, val + delta));
}

function clampGuestCount() {
    var input = document.getElementById('guestCountInput');
    var val = parseInt(input.value) || 1;
    var max = parseInt(input.max) || 1;
    if (val < 1) input.value = 1;
    if (val > max) input.value = max;
}

function closeGuestCountModal() {
    document.getElementById('guestCountModal').classList.remove('show');
}

function confirmGuestCount() {
    var input = document.getElementById('guestCountInput');
    var n = Math.min(parseInt(input.value) || 1, window._guestDialogSlots || 1);
    closeGuestCountModal();
    for (var i = 0; i < n; i++) { addGuestCard(); }
}

function addGuestCard(guestData = null) {
    var container = document.getElementById('guests-container');
    guestCount++;
    
    var card = document.createElement('div');
    card.className = 'guest-card';
    card.dataset.guestId = guestCount;
    card.innerHTML = `
        <div class="guest-card-header">
            <h6><i class="bi bi-person"></i> Guest #${guestCount}</h6>
            <button type="button" class="btn-remove-guest" onclick="removeGuestCard(this)" title="Remove guest">
                <i class="bi bi-trash"></i>
            </button>
        </div>
        <div class="row">
            <div class="col-md-6 mb-2">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" class="form-control" name="guest_names[]" value="${guestData?.name || ''}" required>
                </div>
            </div>
            <div class="col-md-3 mb-2">
                <div class="form-group">
                    <label>Date of Birth</label>
                    <input type="date" class="form-control" name="guest_dobs[]" value="${guestData?.dob || ''}" required>
                </div>
            </div>
            <div class="col-md-3 mb-2">
                <div class="form-group">
                    <label>Age</label>
                    <input type="number" class="form-control" name="guest_ages[]" value="${guestData?.age || ''}" min="0" max="120" required readonly>
                </div>
            </div>
        </div>
    `;
    
    container.appendChild(card);
    
    // Add age calculation
    var dobInput = card.querySelector('input[name="guest_dobs[]"]');
    var ageInput = card.querySelector('input[name="guest_ages[]"]');
    
    dobInput.addEventListener('change', function() {
        ageInput.value = calculateAge(this.value);
    });
    
    saveFormData();
}

function removeGuestCard(button) {
    var card = button.closest('.guest-card');
    var nameInput = card.querySelector('input[name="guest_names[]"]');
    var guestName = (nameInput && nameInput.value.trim()) ? nameInput.value.trim() : 'this guest';
    window._pendingRemoveCard = card;
    document.getElementById('guestRemoveName').textContent =
        `Are you sure you want to remove "${guestName}" from the guest list?`;
    document.getElementById('guestRemoveModal').classList.add('show');
}

function closeGuestRemoveModal() {
    document.getElementById('guestRemoveModal').classList.remove('show');
    window._pendingRemoveCard = null;
}

function confirmGuestRemove() {
    if (window._pendingRemoveCard) {
        window._pendingRemoveCard.remove();
        window._pendingRemoveCard = null;
        saveFormData();
    }
    closeGuestRemoveModal();
}

function calculateAge(dob) {
    if (!dob) return 0;
    var birthDate = new Date(dob);
    var today = new Date();
    var age = today.getFullYear() - birthDate.getFullYear();
    var monthDiff = today.getMonth() - birthDate.getMonth();
    
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }
    
    return age;
}

// ========== RESERVATION TYPE FUNCTIONS ==========
function selectReservationType(type) {
    reservationType = type;
    
    // Update UI
    document.querySelectorAll('.type-option').forEach(opt => opt.classList.remove('selected'));
    document.getElementById('type' + type.charAt(0).toUpperCase() + type.slice(1) + 'Option').classList.add('selected');
    document.getElementById('type' + type.charAt(0).toUpperCase() + type.slice(1)).checked = true;
    
    saveFormData();
}

// ========== SESSION STORAGE FUNCTIONS ==========
function saveFormData() {
    console.log('saveFormData called');
    
    var formData = {
        step: currentStep,
        reservationType: reservationType,
        personal: {
            last_name: document.getElementById('last_name')?.value || '',
            first_name: document.getElementById('first_name')?.value || '',
            middle_initial: document.getElementById('middle_initial')?.value || '',
            email: document.getElementById('email')?.value || '',
            contact: document.getElementById('contact')?.value || ''
        },
        office: {
            type: document.getElementById('officeType')?.value || '',
            isExternal: document.getElementById('officeExternalWrap')?.style.display !== 'none',
            externalName: document.getElementById('officeExternal')?.value || '',
            selectedOffice: document.getElementById('officeSelect')?.value || ''
        },
        event: {
            type: document.getElementById('eventTypeId')?.value || '',
            activity: document.getElementById('activity_name')?.value || '',
            participants: document.getElementById('participants')?.value || '1'
        },
        guest: {
            last_name: document.getElementById('guest_last_name')?.value || '',
            first_name: document.getElementById('guest_first_name')?.value || '',
            middle_initial: document.getElementById('guest_middle_initial')?.value || '',
            dob: document.getElementById('guest_dob')?.value || '',
            address: document.getElementById('guest_address')?.value || '',
            email: document.getElementById('guest_email')?.value || '',
            contact: document.getElementById('guest_contact')?.value || '',
            arrival_date: document.getElementById('arrival_date')?.value || '',
            departure_date: document.getElementById('departure_date')?.value || '',
            checkin_time: document.getElementById('checkin_time')?.value || '',
            checkout_time: document.getElementById('checkout_time')?.value || '',
            adults_count: document.getElementById('adults_count')?.value || '',
            kids_count: document.getElementById('kids_count')?.value || '',
            guest_room_id: document.getElementById('guest_room_id')?.value || '',
            registered_by: document.getElementById('registered_by')?.value || '',
            guest_signature: document.getElementById('guest_signature')?.value || '',
            consent: document.getElementById('guestConsent')?.checked || false,
            other_guests: []
        },
        venues: selectedVenues,
        schedules: facilitySchedules,
        banquet: {
            styleId: document.getElementById('banquetStyleId')?.value || '',
            styleName: document.getElementById('selectedBanquetName')?.textContent || ''
        },
        additional: document.getElementById('additionalInstruction')?.value || '',
        terms: {
            agreed: document.getElementById('termsAgree')?.checked || false,
            fullName: document.getElementById('termsFullName')?.value || '',
            position: document.getElementById('termsPosition')?.value || '',
            date: document.getElementById('termsDate')?.value || ''
        },
        misc: getMiscItemsJson()
    };
    
    // Collect dynamic other guests
    var guestCards = document.querySelectorAll('.guest-card');
    guestCards.forEach(function(card, index) {
        var nameInput = card.querySelector('input[name="guest_names[]"]');
        var dobInput = card.querySelector('input[name="guest_dobs[]"]');
        var ageInput = card.querySelector('input[name="guest_ages[]"]');
        
        if (nameInput && nameInput.value.trim()) {
            formData.guest.other_guests.push({
                name: nameInput.value,
                dob: dobInput?.value || '',
                age: ageInput?.value || ''
            });
        }
    });
    
    console.log('Saving form data:', formData);
    
    sessionStorage.setItem('reservationFormData', JSON.stringify(formData));
    sessionStorage.setItem('reservationStep', currentStep);
    
    checkForSavedData();
}

function saveGuestAndGo(n) {
    if (!validateGuestForm()) return;
    saveFormData();
    goToStep(n);
}

function validateGuestForm() {
    var requiredFields = [
        { id: 'guest_last_name',  label: 'Last Name' },
        { id: 'guest_first_name', label: 'First Name' },
        { id: 'guest_dob',        label: 'Date of Birth' },
        { id: 'guest_address',    label: 'Address' },
        { id: 'guest_email',      label: 'Email Address' },
        { id: 'guest_contact',    label: 'Contact Number' },
        { id: 'arrival_date',     label: 'Arrival Date' },
        { id: 'checkin_time',     label: 'Check-in Time' },
        { id: 'departure_date',   label: 'Departure Date' },
        { id: 'checkout_time',    label: 'Check-out Time' },
        { id: 'adults_count',     label: 'Number of Adults' },
        { id: 'guest_room_id',    label: 'Room Selection' },
        { id: 'registered_by',    label: 'Name of Person Registering' }
    ];

    for (var i = 0; i < requiredFields.length; i++) {
        var field = requiredFields[i];
        var el = document.getElementById(field.id);
        var val = el ? el.value.trim() : '';
        console.log('Checking field:', field.id, '| value:', JSON.stringify(val), '| el found:', !!el);
        if (!el || !val) {
            showModalAlert('⚠️', 'Required Field', '"' + field.label + '" is required. Please fill it in before continuing.');
            if (el) el.focus();
            return false;
        }
    }
    
    // Check consent
    if (!document.getElementById('guestConsent').checked) {
        showModalAlert('📋', 'Consent Required', 'Please agree to the data privacy policy.');
        return false;
    }
    
    // Email validation - MUST be gmail.com
    var email = document.getElementById('guest_email').value.trim();
    var emailRegex = /^[a-zA-Z0-9._%+-]+@gmail\.com$/;
    if (!emailRegex.test(email)) {
        showModalAlert('📧', 'Invalid Email', 'Please use a valid Gmail address (@gmail.com).');
        document.getElementById('guest_email').focus();
        return false;
    }
    
    // Validate dates
    var arrival = new Date(document.getElementById('arrival_date').value);
    var departure = new Date(document.getElementById('departure_date').value);
    if (departure <= arrival) {
        showModalAlert('⚠️', 'Invalid Dates', 'Departure date must be after arrival date.');
        return false;
    }
    
    // Get room capacity
    var roomSelect = document.getElementById('guest_room_id');
    var selectedOption = roomSelect.options[roomSelect.selectedIndex];
    var capacity = parseInt(selectedOption.getAttribute('data-capacity') || '2');
    
    // Calculate total guests (principal + other guests)
    var totalGuests = 1; // Principal guest
    totalGuests += document.querySelectorAll('.guest-card').length;
    
    if (totalGuests > capacity) {
        showModalAlert('⚠️', 'Exceeds Capacity', `Total guests (${totalGuests}) exceeds room capacity (${capacity}). Please remove some guests.`);
        return false;
    }
    
    return true;
}

function loadFormData() {
    var saved = sessionStorage.getItem('reservationFormData');
    if (!saved) return;
    
    try {
        var data = JSON.parse(saved);
        console.log('Loading form data:', data);
        
        // Set reservation type
        if (data.reservationType) {
            reservationType = data.reservationType;
            var radio = document.getElementById('type' + reservationType.charAt(0).toUpperCase() + reservationType.slice(1));
            if (radio) radio.checked = true;
            document.querySelectorAll('.type-option').forEach(opt => opt.classList.remove('selected'));
            var opt = document.getElementById('type' + reservationType.charAt(0).toUpperCase() + reservationType.slice(1) + 'Option');
            if (opt) opt.classList.add('selected');
        }
        
        if (data.personal) {
            setValue('last_name', data.personal.last_name);
            setValue('first_name', data.personal.first_name);
            setValue('middle_initial', data.personal.middle_initial);
            setValue('email', data.personal.email);
            setValue('contact', data.personal.contact);
        }
        
        if (data.office) {
            setValue('officeType', data.office.type);
            
            setTimeout(function() {
                var event = new Event('change');
                document.getElementById('officeType')?.dispatchEvent(event);
                
                setTimeout(function() {
                    if (data.office.isExternal) {
                        setValue('officeExternal', data.office.externalName);
                    } else {
                        setValue('officeSelect', data.office.selectedOffice);
                    }
                }, 300);
            }, 100);
        }
        
        if (data.event) {
            setValue('eventTypeId', data.event.type);
            setValue('activity_name', data.event.activity);
            setValue('participants', data.event.participants);
        }
        
        // Load guest data
        if (data.guest) {
            setValue('guest_last_name', data.guest.last_name);
            setValue('guest_first_name', data.guest.first_name);
            setValue('guest_middle_initial', data.guest.middle_initial);
            setValue('guest_dob', data.guest.dob);
            setValue('guest_address', data.guest.address);
            setValue('guest_email', data.guest.email);
            setValue('guest_contact', data.guest.contact);
            setValue('arrival_date', data.guest.arrival_date);
            setValue('departure_date', data.guest.departure_date);
            setValue('checkin_time', data.guest.checkin_time);
            setValue('checkout_time', data.guest.checkout_time);
            setValue('adults_count', data.guest.adults_count);
            setValue('kids_count', data.guest.kids_count);
            setValue('guest_room_id', data.guest.guest_room_id);
            setValue('registered_by', data.guest.registered_by);
            setValue('guest_signature', data.guest.guest_signature);
            var consentBox = document.getElementById('guestConsent');
            if (consentBox && data.guest.consent) consentBox.checked = true;
            
            // Clear any existing cards first
            document.getElementById('guests-container').innerHTML = '';
            
            // Load other guests dynamically
            if (data.guest.other_guests && data.guest.other_guests.length > 0) {
                data.guest.other_guests.forEach(function(guest) {
                    addGuestCard(guest);
                });
            }
        }
        
        if (data.venues && data.venues.length > 0) {
            console.log('Loading venues:', data.venues);
            selectedVenues = data.venues;
            
            setTimeout(function() {
                selectedVenues.forEach(function(venue) {
                    var cards = document.querySelectorAll('.room-select-card');
                    cards.forEach(function(card) {
                        if (card.getAttribute('data-id') == venue.id) {
                            card.classList.add('selected');
                            var cb = card.querySelector('input[type="checkbox"]');
                            if (cb) cb.checked = true;
                        }
                    });
                });
            }, 500);
        }
        
        if (data.schedules) {
            console.log('Restoring schedules:', data.schedules);
            facilitySchedules = data.schedules;
        }
        
        if (data.banquet) {
            setValue('banquetStyleId', data.banquet.styleId);
            document.getElementById('selectedBanquetName').textContent = data.banquet.styleName || '';
        }
        
        if (data.additional) {
            setValue('additionalInstruction', data.additional);
        }
        
        if (data.terms) {
            setValue('termsFullName', data.terms.fullName);
            setValue('termsPosition', data.terms.position);
            setValue('termsDate', data.terms.date);
        }
        
        console.log('Form data loaded successfully');
        
    } catch (e) {
        console.error('Error loading saved data:', e);
    }
}

function setValue(id, value) {
    var el = document.getElementById(id);
    if (el) el.value = value || '';
}

function checkForSavedData() {
    var step = sessionStorage.getItem('reservationStep');
    var data = sessionStorage.getItem('reservationFormData');
    var container = document.getElementById('resumeBookingContainer');
    
    if (container) {
        if (step && parseInt(step, 10) > 0 && data) {
            var stepLabels = ['Type','Info','Rooms','Schedule','Terms','Misc','Summary'];
            var label = stepLabels[parseInt(step, 10)] || ('Step ' + step);
            var msgEl = container.querySelector('.resume-msg');
            if (msgEl) msgEl.textContent = 'You have an incomplete reservation from your last session (saved at: ' + label + ').';
            container.style.display = 'block';
        } else {
            container.style.display = 'none';
        }
    }
}

function resumeSavedSession() {
    var savedStep = sessionStorage.getItem('reservationStep');
    var savedData  = sessionStorage.getItem('reservationFormData');
    if (!savedStep || !savedData) return;
    // Hide the banner immediately
    var container = document.getElementById('resumeBookingContainer');
    if (container) container.style.display = 'none';
    // Restore all fields, then navigate to the saved step
    loadFormData();
    setTimeout(function() {
        goToStep(parseInt(savedStep, 10));
    }, 400);
}

function clearSavedData() {
    sessionStorage.removeItem('reservationFormData');
    sessionStorage.removeItem('reservationStep');
    location.reload();
}

function saveAndGo(n) {
    if (n > currentStep && !validateStep(currentStep)) return;
    saveFormData();
    goToStep(n);
}

function goToStep(n) {
    if (n < 0 || n > 6) return;
    
    // REDIRECTION LOGIC FOR GUEST ROOM BOOKINGS (Skip Steps 2, 3, and 5)
    if (typeof reservationType !== 'undefined' && reservationType === 'guest') {
        if (n === 2 || n === 3) {
            // Forward from Step 1G -> Step 4, or Backward from Step 4 -> Step 1G
            n = (n > (typeof currentStep !== 'undefined' ? currentStep : 0)) ? 4 : 1;
        } else if (n === 5) {
            // Forward from Step 4 -> Step 6, or Backward from Step 6 -> Step 4
            n = (n > (typeof currentStep !== 'undefined' ? currentStep : 0)) ? 6 : 4;
        }
    }
    
    // Hide all form steps
    document.querySelectorAll('.form-step').forEach(function(s){ s.classList.remove('active'); s.style.display = 'none'; });
    
    // Show appropriate step based on reservation type
    if (n === 1 && reservationType === 'guest') {
        document.getElementById('step1GForm').style.display = 'block';
        document.getElementById('step1GForm').classList.add('active');
    } else if (n === 1) {
        document.getElementById('step1Form').style.display = 'block';
        document.getElementById('step1Form').classList.add('active');
    } else if (n === 2 && reservationType === 'guest') {
        // For guest reservations, skip to terms
        goToStep(4);
        return;
    } else {
        var stepId = 'step' + n + 'Form';
        var stepEl = document.getElementById(stepId);
        if (stepEl) {
            stepEl.style.display = 'block';
            stepEl.classList.add('active');
        }
    }
    
    // Update progress steps
    document.querySelectorAll('.progress-steps .step').forEach(function(s){
        var sn = parseInt(s.getAttribute('data-step'), 10);
        s.classList.remove('active', 'completed');
        if (sn < n) s.classList.add('completed');
        if (sn === n) s.classList.add('active');
    });
    
    document.getElementById('progressLine').style.width = (n / 6 * 100) + '%';
    currentStep = n;
    
    if (n === 3 && reservationType !== 'guest') renderScheduleStep();
    if (n === 2 && reservationType !== 'guest') {
        updateVenuePricingDisplay();
    }
    if (n === 3 && reservationType !== 'guest') {
        updateSchedulePriceSummary();
    }
    if (n === 4) {
        if (reservationType === 'guest') {
            loadGuestTerms();
        } else {
            loadTermsForStep4();
        }
    }
    if (n === 6) buildSummary();
    
    saveFormData();

    // Scroll back to top to ensure the next step content is visible
    setTimeout(function() {
        const formContainer = document.querySelector('.reservation-card');
        if (formContainer) {
            const yOffset = -80; 
            const y = formContainer.getBoundingClientRect().top + window.pageYOffset + yOffset;
            window.scrollTo({top: y, behavior: 'smooth'});
        } else {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    }, 50);
}

/* ──────────────────────────────────────────────────────────────
   PRICING HELPERS
   ────────────────────────────────────────────────────────────── */

/** Returns true if the currently selected officeType is "External" */
function isExternalOffice() {
    var sel = document.getElementById('officeType');
    if (!sel || !sel.value) return false;
    var opt = sel.options[sel.selectedIndex];
    return opt && opt.getAttribute('data-name') === 'External';
}

/** Get the rate values from the embedded PHP data element */
function getVenueRates() {
    var el = document.getElementById('venuePricingData');
    if (!el) return { halfDay: 2000, wholeDay: 3000, extension: 400, soundFee: 1500 };
    return {
        halfDay:   parseFloat(el.getAttribute('data-half-day'))  || 2000,
        wholeDay:  parseFloat(el.getAttribute('data-whole-day')) || 3000,
        extension: parseFloat(el.getAttribute('data-extension')) || 400,
        soundFee:  parseFloat(el.getAttribute('data-sound-fee')) || 1500
    };
}

/** Format Philippine Peso */
function formatPHP(amount) {
    return '₱' + parseFloat(amount).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

/**
 * Calculate the venue-rental cost for a given duration in decimal hours.
 * < 4h  → half-day rate
 * 4–8h  → whole-day rate
 * > 8h  → whole-day + extension for each additional hour (ceiling)
 */
function calcVenueCost(hours, rates) {
    var type, cost;
    if (hours <= 4) {
        type = 'Half Day';
        cost = rates.halfDay;
    } else if (hours <= 8) {
        type = 'Whole Day';
        cost = rates.wholeDay;
    } else {
        var overHours = Math.ceil(hours - 8);
        type = 'Whole Day + ' + overHours + 'h Extension';
        cost = rates.wholeDay + (overHours * rates.extension);
    }
    return { type: type, cost: cost };
}

/** Show / hide pricing info box in Step 2 based on office type. Also update displayed rate values. */
function updateVenuePricingDisplay() {
    var extBox  = document.getElementById('externalPricingBox');
    var intBox  = document.getElementById('internalPricingBox');
    if (!extBox || !intBox) return;

    var isExt = isExternalOffice();
    extBox.style.display = isExt ? 'block' : 'none';
    intBox.style.display = (!isExt) ? 'block' : 'none';

    if (isExt) {
        var rates = getVenueRates();
        var hd = document.getElementById('display_half_day');
        var wd = document.getElementById('display_whole_day');
        var er = document.getElementById('display_ext_rate');
        var sf = document.getElementById('display_sound_fee');
        if (hd) hd.textContent = formatPHP(rates.halfDay);
        if (wd) wd.textContent = formatPHP(rates.wholeDay);
        if (er) er.textContent = formatPHP(rates.extension);
        if (sf) sf.textContent = formatPHP(rates.soundFee);
    }
}

/** Build / refresh the live cost summary in Step 3 (External only). */
function updateSchedulePriceSummary() {
    var box = document.getElementById('priceSummaryBox');
    var linesEl = document.getElementById('priceSummaryLines');
    var totalEl = document.getElementById('priceSummaryTotal');
    if (!box || !linesEl || !totalEl) return;

    if (!isExternalOffice()) {
        box.style.display = 'none';
        return;
    }

    var rates = getVenueRates();
    var lines = '';
    var grandTotal = 0;
    var hasSchedules = false;

    // Loop over selected venues and their schedules
    Object.keys(facilitySchedules || {}).forEach(function(vid) {
        var scheds = facilitySchedules[vid];
        if (!scheds || scheds.length === 0) return;
        // Find the venue name
        var venueObj = (selectedVenues || []).find(function(v){ return String(v.id) === String(vid); });
        var vname = venueObj ? venueObj.name : 'Venue #' + vid;

        scheds.forEach(function(sched) {
            if (!sched.start || !sched.end) return;
            hasSchedules = true;
            // Compute hours
            var startParts = sched.start.split(':').map(Number);
            var endParts   = sched.end.split(':').map(Number);
            var startMins  = startParts[0] * 60 + (startParts[1] || 0);
            var endMins    = endParts[0]   * 60 + (endParts[1]   || 0);
            if (endMins <= startMins) endMins += 24 * 60; // overnight
            var hours = (endMins - startMins) / 60;

            var calc = calcVenueCost(hours, rates);
            grandTotal += calc.cost;

            lines += '<div style="display:flex;justify-content:space-between;">' +
                     '<span>' + escapeHtml(vname) + ' — ' + escapeHtml(sched.date || '') + ' (' + hours.toFixed(1) + 'h, ' + calc.type + ')</span>' +
                     '<strong style="color:#b71c1c;">' + formatPHP(calc.cost) + '</strong>' +
                     '</div>';
        });
    });

    if (!hasSchedules) {
        box.style.display = 'none';
        return;
    }

    linesEl.innerHTML = lines;
    totalEl.textContent = formatPHP(grandTotal);
    box.style.display = 'block';
}


function goStep(n) {
    saveAndGo(n);
}

// ========== TERMS AND CONDITIONS FUNCTIONS ==========
// NEW: Load guest terms
function loadGuestTerms() {
    document.getElementById('termsContainer').innerHTML = '<div class="terms-content" style="min-height: 300px; display: flex; align-items: center; justify-content: center;"><div class="text-center"><div class="spinner-border text-danger" role="status" style="width: 3rem; height: 3rem;"><span class="visually-hidden">Loading...</span></div><p class="mt-3" style="color: #666;">Loading guest room guidelines...</p></div></div>';
    document.getElementById('termsAgree').disabled = true;
    document.getElementById('termsAgree').checked = false;
    
    // Try to fetch from database first
    fetch(baseUrl + '/ajax/get_terms.php?customer_type=guest')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.terms) {
                var termsHtml = '<div class="terms-header">' +
                    '<h5>' + escapeHtml(data.terms.title) + '</h5>' +
                    (data.terms.version ? '<p>Version: ' + escapeHtml(data.terms.version) + '</p>' : '') +
                    '</div>' +
                    '<div class="terms-content">' +
                    '<pre>' + escapeHtml(data.terms.content) + '</pre>' +
                    '</div>';
                
                document.getElementById('termsContainer').innerHTML = termsHtml;
            } else {
                showGuestFallbackTerms();
            }
            
            setupGuestTermsScrollListener();
        })
        .catch(error => {
            console.error('Error loading guest terms:', error);
            showGuestFallbackTerms();
            setupGuestTermsScrollListener();
        });
}

// NEW: Show hardcoded guest terms as fallback
function showGuestFallbackTerms() {
    var termsHtml = '<div class="terms-header">' +
        '<h5>HOSTEL ROOM GUIDELINES AND PROHIBITED ACTS</h5>' +
        '<p>For Guest Room Bookings</p>' +
        '</div>' +
        '<div class="terms-content">' +
        '<pre>' + guestTerms + '</pre>' +
        '</div>';
    
    document.getElementById('termsContainer').innerHTML = termsHtml;
}

// NEW: Special scroll listener for guest terms
function setupGuestTermsScrollListener() {
    var termsContainer = document.querySelector('.terms-content');
    if (!termsContainer) return;
    
    var termsAgree = document.getElementById('termsAgree');
    var fullNameInput = document.getElementById('termsFullName');
    
    window.termsReached = false;
    
    function checkTermsScroll() {
        if (window.termsReached) {
            if (termsAgree) {
                termsAgree.disabled = false;
                termsAgree.style.opacity = '1';
                termsAgree.style.cursor = 'pointer';
            }
            return;
        }
        
        var st = termsContainer.scrollTop;
        var sh = termsContainer.scrollHeight;
        var ch = termsContainer.clientHeight;
        
        if (sh <= ch + 5 || st + ch >= sh - 20) {
            window.termsReached = true;
            if (termsAgree) {
                termsAgree.disabled = false;
                termsAgree.style.opacity = '1';
                termsAgree.style.cursor = 'pointer';
            }
            
            // termsFullName auto-fill removed (not used for guests)
        }
    }
    
    termsContainer.addEventListener('scroll', checkTermsScroll);
    
    var savedData = sessionStorage.getItem('reservationFormData');
    if (savedData) {
        try {
            var data = JSON.parse(savedData);
            if (data.terms) {
                if (data.terms.fullName) setValue('termsFullName', data.terms.fullName);
                if (data.terms.position) setValue('termsPosition', data.terms.position);
                if (data.terms.date) setValue('termsDate', data.terms.date);
            }
        } catch (e) {}
    }
    
    setTimeout(checkTermsScroll, 500);
    
    window.addEventListener('resize', function() {
        setTimeout(checkTermsScroll, 100);
    });
}

// Original function room terms loader
function loadTermsForStep4() {
    var officeTypeSelect = document.getElementById('officeType');
    if (!officeTypeSelect) return;
    
    var selectedOption = officeTypeSelect.options[officeTypeSelect.selectedIndex];
    var officeTypeName = selectedOption ? selectedOption.getAttribute('data-name') : '';
    var officeTypeId = officeTypeSelect.value;
    
    document.getElementById('termsContainer').innerHTML = '<div class="terms-content" style="min-height: 300px; display: flex; align-items: center; justify-content: center;"><div class="text-center"><div class="spinner-border text-danger" role="status" style="width: 3rem; height: 3rem;"><span class="visually-hidden">Loading...</span></div><p class="mt-3" style="color: #666;">Loading terms and conditions...</p></div></div>';
    document.getElementById('termsAgree').disabled = true;
    document.getElementById('termsAgree').checked = false;
    
    fetch(baseUrl + '/ajax/get_terms.php?office_type_id=' + officeTypeId)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.terms) {
                var termsHtml = '<div class="terms-header">' +
                    '<h5>' + escapeHtml(data.terms.title) + '</h5>' +
                    (data.terms.version ? '<p>Version: ' + escapeHtml(data.terms.version) + '</p>' : '') +
                    '</div>' +
                    '<div class="terms-content">' +
                    '<pre>' + escapeHtml(data.terms.content) + '</pre>' +
                    '</div>';
                
                document.getElementById('termsContainer').innerHTML = termsHtml;
            } else {
                showFallbackTerms(officeTypeName);
            }
            
            setupTermsScrollListener();
        })
        .catch(error => {
            console.error('Error loading terms:', error);
            showFallbackTerms(officeTypeName);
            setupTermsScrollListener();
        });
}

function showFallbackTerms(officeTypeName) {
    var isCollegeOrStudentOrg = officeTypeName === 'College' || officeTypeName === 'Student Organization';
    
    var termsHtml = '';
    
    if (isCollegeOrStudentOrg) {
        termsHtml = '<div class="terms-header">' +
            '<h5>CABEIHM Memo No. 3 Series of 2025 - Guidelines for Utilizing the Hostel Function Rooms</h5>' +
            '<p>For Colleges and Student Organizations</p>' +
            '</div>' +
            '<div class="terms-content">' +
            '<pre>' + collegeTerms + '</pre>' +
            '</div>';
    } else {
        termsHtml = '<div class="terms-header">' +
            '<h5>HOSTEL FUNCTION ROOM AND EVENTS RULES AND GUIDELINES</h5>' +
            '<p>For External Clients and Offices</p>' +
            '</div>' +
            '<div class="terms-content">' +
            '<pre>' + externalTerms + '</pre>' +
            '</div>';
    }
    
    document.getElementById('termsContainer').innerHTML = termsHtml;
}

function setupTermsScrollListener() {
    var termsContainer = document.querySelector('.terms-content');
    if (!termsContainer) return;
    
    var termsAgree = document.getElementById('termsAgree');
    var fullNameInput = document.getElementById('termsFullName');
    
    window.termsReached = false;
    
    function checkTermsScroll() {
        if (window.termsReached) {
            if (termsAgree) {
                termsAgree.disabled = false;
                termsAgree.style.opacity = '1';
                termsAgree.style.cursor = 'pointer';
            }
            return;
        }
        
        var st = termsContainer.scrollTop;
        var sh = termsContainer.scrollHeight;
        var ch = termsContainer.clientHeight;
        
        if (sh <= ch + 5 || st + ch >= sh - 20) {
            window.termsReached = true;
            if (termsAgree) {
                termsAgree.disabled = false;
                termsAgree.style.opacity = '1';
                termsAgree.style.cursor = 'pointer';
            }
            
            if (fullNameInput && fullNameInput.value === '') {
                var firstName = document.getElementById('first_name')?.value || '';
                var lastName = document.getElementById('last_name')?.value || '';
                var middleInitial = document.getElementById('middle_initial')?.value || '';
                
                var fullName = firstName + ' ' + (middleInitial ? middleInitial + ' ' : '') + lastName;
                fullNameInput.value = fullName.trim();
                
                fullNameInput.style.background = '#fff3e0';
                fullNameInput.style.borderColor = '#b71c1c';
            }
        }
    }
    
    termsContainer.addEventListener('scroll', checkTermsScroll);
    
    var savedData = sessionStorage.getItem('reservationFormData');
    if (savedData) {
        try {
            var data = JSON.parse(savedData);
            if (data.terms) {
                if (data.terms.fullName) setValue('termsFullName', data.terms.fullName);
                if (data.terms.position) setValue('termsPosition', data.terms.position);
                if (data.terms.date) setValue('termsDate', data.terms.date);
            }
        } catch (e) {}
    }
    
    setTimeout(checkTermsScroll, 500);
    
    window.addEventListener('resize', function() {
        setTimeout(checkTermsScroll, 100);
    });
}

// ========== OFFICE TYPE HANDLER ==========
function handleOfficeTypeChange() {
    var select = document.getElementById('officeType');
    if (!select) return;
    
    var val = select.value;
    var opt = select.options[select.selectedIndex];
    var isExt = opt && opt.getAttribute('data-name') === 'External';
    
    var officeSelectWrap = document.getElementById('officeSelectWrap');
    var officeExternalWrap = document.getElementById('officeExternalWrap');
    var officeSelect = document.getElementById('officeSelect');
    var officeExternal = document.getElementById('officeExternal');
    
    if (isExt) {
        officeSelectWrap.style.display = 'none';
        officeExternalWrap.style.display = 'block';
        officeSelect.disabled = true;
        officeSelect.required = false;
        officeExternal.disabled = false;
        officeExternal.required = true;
        
        officeSelect.innerHTML = '<option value="">Select Type First</option>';
        
    } else if (val) {
        officeSelectWrap.style.display = 'block';
        officeExternalWrap.style.display = 'none';
        officeSelect.disabled = false;
        officeSelect.required = true;
        officeExternal.disabled = true;
        officeExternal.required = false;
        
        officeSelect.innerHTML = '<option value="">Select...</option>';
        
        if (officesByType[val]) {
            officesByType[val].forEach(function(o) {
                var opt = document.createElement('option');
                opt.value = o.id;
                opt.textContent = o.name;
                officeSelect.appendChild(opt);
            });
        }
    } else {
        officeSelectWrap.style.display = 'block';
        officeExternalWrap.style.display = 'none';
        officeSelect.disabled = true;
        officeSelect.required = false;
        officeExternal.disabled = true;
        officeExternal.required = false;
        officeSelect.innerHTML = '<option value="">Select Type First</option>';
    }
    
    saveFormData();
    updateVenuePriceBadges();
}

// ========== VALIDATION ==========
function validateStep(n) {
    console.log('Validating step:', n);
    
    if (n === 0) {
        if (!reservationType) {
            showModalAlert('⚠️', 'Select a Reservation Type', 'Please choose between Guest Room Booking or Function Room Reservation before continuing.');
            return false;
        }
        return true;
    }
    
    if (n === 1 && reservationType === 'guest') {
        return validateGuestForm();
    }
    
    var step = document.getElementById('step' + n + 'Form');
    if (!step) return true;
    
    var req = step.querySelectorAll('[required]');
    
    for (var i = 0; i < req.length; i++) {
        if (!req[i].value || !req[i].value.trim()) {
            req[i].focus();
            req[i].reportValidity ? req[i].reportValidity() : showModalAlert('⚠️', 'Required Field', 'Please fill in all required fields.');
            return false;
        }
    }
    
    if (n === 1) {
    var ot = document.getElementById('officeType').value;
    var opt = document.getElementById('officeType').options[document.getElementById('officeType').selectedIndex];
    var isExt = opt && opt.getAttribute('data-name') === 'External';
    
    if (isExt) { 
        if (!document.getElementById('officeExternal').value.trim()) { 
            showModalAlert('⚠️', 'Missing Information', 'Please enter your office or organization name.'); 
            return false; 
        }
    } else { 
        if (!document.getElementById('officeSelect').value) { 
            showModalAlert('⚠️', 'Missing Information', 'Please select your office.');
            return false; 
        }
    }
    
    // ADD THIS EMAIL VALIDATION FOR FUNCTION ROOMS
    var email = document.getElementById('email').value.trim();
    var emailRegex = /^[a-zA-Z0-9._%+-]+@gmail\.com$/;
    if (!emailRegex.test(email)) {
        showModalAlert('📧', 'Invalid Email', 'Please use a valid Gmail address (@gmail.com).');
        document.getElementById('email').focus();
        return false;
    }
}
    
    if (n === 2) {
        console.log('Validating step 2 - venues');
        selectedVenues = [];
        document.querySelectorAll('.room-select-card.selected').forEach(function(c){
            selectedVenues.push({ 
                id: c.getAttribute('data-id'), 
                name: c.getAttribute('data-name'),
                floor: c.getAttribute('data-floor')
            });
        });
        
        console.log('Selected venues:', selectedVenues);
        
        if (selectedVenues.length === 0) {
            document.getElementById('venueError').style.display = 'block';
            return false;
        }
        document.getElementById('venueError').style.display = 'none';
    }
    
    if (n === 3) {
        console.log('Validating step 3 - schedules');
        var ok = true;
        
        for (var i = 0; i < selectedVenues.length; i++) {
            var vid = selectedVenues[i].id;
            if (!facilitySchedules[vid] || facilitySchedules[vid].length === 0) { 
                ok = false; 
                console.log('Missing schedule for venue:', selectedVenues[i]);
                break; 
            }
        }
        
        if (!ok) { 
            document.getElementById('scheduleError').style.display = 'block'; 
            return false; 
        }
        document.getElementById('scheduleError').style.display = 'none';
    }
    
    if (n === 4) { 
        if (!document.getElementById('termsAgree').checked) { 
            showModalAlert('📋', 'Terms Required', 'Please read and accept the terms and conditions before continuing.');
            return false; 
        }
        
        if (reservationType !== 'guest') {
            var fnEl = document.getElementById('termsFullName');
            if (fnEl && !fnEl.value.trim()) {
                showModalAlert('✍️', 'Signature Required', 'Please enter your full name as digital signature.');
                fnEl.focus();
                return false;
            }
        }
    }
    
    if (n === 5) {
        var miscItems = getMiscItemsJson();
        if (miscItems === null) {
            return false;
        }
    }
    
    return true;
}

function getMiscItemsJson() {
    var out = {};
    var hasError = false;
    
    document.querySelectorAll('.misc-cb:checked').forEach(function(cb) {
        var key = cb.getAttribute('data-key');
        
        if (key === 'basic_sound_system') {
            var item = document.querySelector('.misc-item[data-key="basic_sound_system"]');
            var speaker = item ? parseInt(item.querySelector('[data-field="speaker"]')?.value || 0) : 0;
            var mic = item ? parseInt(item.querySelector('[data-field="mic"]')?.value || 0) : 0;
            
            if (speaker > 4) {
                showModalAlert('⚠️', 'Limit Exceeded', 'Maximum 4 speakers allowed');
                hasError = true;
                return;
            }
            if (mic > 6) {
                showModalAlert('⚠️', 'Limit Exceeded', 'Maximum 6 microphones allowed');
                hasError = true;
                return;
            }
            
            out[key] = { speaker: speaker, mic: mic };
        } 
        else if (key === 'view_board') {
            out[key] = { requested: true };
        }
        else {
            var qty = document.querySelector('.misc-qty-inline[data-key="' + key + '"]');
            var value = parseInt(qty?.value || 0);
            var limits = miscLimits[key];
            
            if (limits && value > limits.max) {
                showModalAlert('⚠️', 'Limit Exceeded', 'Maximum ' + limits.max + ' ' + limits.label + ' allowed');
                hasError = true;
                return;
            }
            
            out[key] = { quantity: value };
        }
    });
    
    return hasError ? null : out;
}

function loadBanquetStyles() {
    fetch('<?= $base ?>/ajax/get_banquet_styles.php')
        .then(response => response.json())
        .then(data => {
            var container = document.getElementById('banquetCards');
            if (!container) return;
            
            if (data.length === 0) {
                container.innerHTML = '<p class="text-muted">No banquet styles available.</p>';
            } else {
                var html = data.map(function(b) {
                    var imgHtml;
                    if (b.image) {
                        imgHtml = '<img src="' + baseUrl + '/assets/images/banquet/' + b.image + 
                                 '" alt="' + b.name + '" loading="lazy" onerror="this.onerror=null;this.classList.add(\'error\');this.parentElement.classList.add(\'no-image\');this.remove();">';
                    } else {
                        imgHtml = '<div class="no-image"><span>No image available</span></div>';
                    }
                    
                    return '<div class="banquet-card" data-id="' + b.id + '" data-name="' + b.name + '" data-image="' + (b.image || '') + '" onclick="selectBanquet(this)">' +
                           '<div class="banquet-image">' + imgHtml + '</div>' +
                           '<div class="banquet-card-body">' +
                           '<strong>' + b.name + '</strong>' +
                           '<p>' + (b.description || '') + '</p>' +
                           '</div></div>';
                }).join('');
                container.innerHTML = html;
                
                var savedId = document.getElementById('banquetStyleId')?.value;
                if (savedId) {
                    var savedCard = document.querySelector('.banquet-card[data-id="' + savedId + '"]');
                    if (savedCard) {
                        savedCard.classList.add('selected');
                    }
                }
            }
        })
        .catch(error => {
            console.error('Error loading banquet styles:', error);
            var container = document.getElementById('banquetCards');
            if (container) {
                container.innerHTML = '<p class="text-danger">Error loading banquet styles. Please try again.</p>';
            }
        });
}

function selectBanquet(el) {
    document.querySelectorAll('.banquet-card').forEach(function(c){ c.classList.remove('selected'); });
    el.classList.add('selected');
}

function openBanquetModal() {
    var id = document.getElementById('banquetStyleId').value;
    document.querySelectorAll('.banquet-card').forEach(function(c){
        c.classList.toggle('selected', c.getAttribute('data-id') === id);
    });
    document.getElementById('banquetModal').classList.add('show');
}

function closeBanquetModal() {
    document.getElementById('banquetModal').classList.remove('show');
}

function confirmBanquetSelection() {
    var el = document.querySelector('.banquet-card.selected');
    if (el) {
        document.getElementById('banquetStyleId').value = el.getAttribute('data-id') || '';
        document.getElementById('selectedBanquetName').textContent = 'Selected: ' + (el.getAttribute('data-name') || '');
        closeBanquetModal();
        saveFormData();
    } else {
        showModalAlert('🎨', 'Banquet Style Required', 'Please select a banquet style first.');
    }
}

function toggleVenue(el) {
    el.classList.toggle('selected');
    var cb = el.querySelector('input[type="checkbox"]');
    cb.checked = el.classList.contains('selected');
    
    var venueId = el.getAttribute('data-id');
    var venueName = el.getAttribute('data-name');
    var venueFloor = el.getAttribute('data-floor');
    
    if (cb.checked) {
        selectedVenues.push({ 
            id: venueId, 
            name: venueName,
            floor: venueFloor
        });
    } else {
        selectedVenues = selectedVenues.filter(v => v.id != venueId);
    }
    saveFormData();
}

/* ── Show pricing on venue cards based on internal vs external office type ── */
function updateVenuePriceBadges() {
    var select = document.getElementById('officeType');
    var isExternal = false;
    if (select && select.value) {
        var opt = select.options[select.selectedIndex];
        isExternal = (opt && opt.getAttribute('data-name') === 'External');
    }
    document.querySelectorAll('.room-select-card').forEach(function(card) {
        var badge = card.querySelector('.venue-price-badge');
        if (!badge) return;
        if (!select || !select.value) { badge.style.display = 'none'; return; }
        var price = parseFloat(card.getAttribute('data-price') || '0');
        badge.style.display = 'inline-flex';
        if (isExternal) {
            badge.style.background = '#b71c1c'; badge.style.color = 'white';
            badge.textContent = price > 0 ? '₱' + price.toLocaleString('en-PH') + ' (External Rate)' : 'Rate on Request';
        } else {
            badge.style.background = '#d4edda'; badge.style.color = '#155724';
            badge.textContent = 'Free for Internal Use';
        }
    });
}

function renderScheduleStep() {
    console.log('Rendering schedule step with venues:', selectedVenues);
    
    var container = document.getElementById('scheduleFacilitiesContainer');
    if (!container) {
        console.error('scheduleFacilitiesContainer not found!');
        return;
    }
    
    if (!selectedVenues || selectedVenues.length === 0) {
        console.warn('No venues selected');
        container.innerHTML = '<p class="text-danger">Please select at least one venue first.</p>';
        return;
    }
    
    var html = '';
    selectedVenues.forEach(function(v, index) {
        if (!facilitySchedules[v.id]) facilitySchedules[v.id] = [];
        
        html += '<div class="schedule-card" data-venue-id="' + v.id + '" data-venue-index="' + index + '">';
        html += '<div class="room-name"><i class="bi bi-building me-1"></i>' + escapeHtml(v.name) + ' <small class="text-muted">(' + escapeHtml(v.floor || '') + ')</small></div>';
        html += '<div class="date-time-row">';
        html += '<div class="form-group">';
        html += '<label>Date *</label>';
        html += '<input type="text" class="form-control date-input date-picker" id="date-picker-' + v.id + '" data-venue-id="' + v.id + '" placeholder="Click to select date" readonly style="cursor:pointer; background-color:#fff;">';
        html += '</div>';
        html += '<div class="form-group">';
        html += '<label>Start Time *</label>';
        html += '<input type="text" class="form-control time-display" id="start-display-' + v.id + '" placeholder="Not set" readonly>';
        html += '</div>';
        html += '<div class="form-group">';
        html += '<label>End Time *</label>';
        html += '<input type="text" class="form-control time-display" id="end-display-' + v.id + '" placeholder="Not set" readonly>';
        html += '</div>';
        html += '<button type="button" class="btn-add-schedule" onclick="openTimeModal(\'' + v.id + '\', \'' + v.name + '\')">';
        html += '<i class="bi bi-plus-circle"></i> Add';
        html += '</button>';
        html += '</div>';
        html += '<div class="schedule-list" id="schedList-' + v.id + '"></div>';
        html += '<div class="selected-facilities-box">';
        html += '<strong>Schedules:</strong> <span id="schedCount-' + v.id + '">0</span>';
        html += '</div>';
        html += '</div>';
    });
    
    container.innerHTML = html;
    
    selectedVenues.forEach(function(v) {
        updateScheduleList(v.id);
    });
    
    console.log('Schedule step rendered successfully');
}

function escapeHtml(text) {
    if (!text) return '';
    var div = document.createElement('div');
    div.appendChild(document.createTextNode(text));
    return div.innerHTML;
}

var availabilityCache = {};

// ---- Generation counter: incremented every time a new modal open begins.
// Every async callback captures its own generation value and aborts if stale.
var timeModalGeneration = 0;

function formatTimeDisplay(timeValue) {
    var parts = timeValue.split(':');
    var h = parseInt(parts[0]);
    var m = parts[1];
    var ampm = h >= 12 ? 'PM' : 'AM';
    var displayH = h > 12 ? h - 12 : (h === 0 ? 12 : h);
    return displayH + ':' + m + ' ' + ampm;
}

function populateTimeDropdowns(availableStarts, bookedSlots, venueId) {
    console.log('Populating dropdowns for venue', venueId, 'with available starts:', availableStarts);
    console.log('Booked slots:', bookedSlots);
    
    var startSel = document.getElementById('timeModalStart');
    var endSel   = document.getElementById('timeModalEnd');
    var statusEl = document.getElementById('availabilityStatus');
    var bookedInfo = document.getElementById('bookedSlotsInfo');

    startSel.innerHTML = '<option value="">Select Start Time</option>';
    endSel.innerHTML   = '<option value="">Select start time first</option>';

    // Generate all possible start slots 07:00–22:30
    var allStarts = [];
    for (var h = 7; h <= 22; h++) {
        for (var m = 0; m < 60; m += 30) {
            allStarts.push(sprintf2('%02d:%02d', h, m));
        }
    }

    var hasAvailable = false;
    allStarts.forEach(function(slot) {
        var isAvailable = availableStarts.indexOf(slot) !== -1;
        var opt = document.createElement('option');
        opt.value = slot;
        
        // Format display time
        var displayTime = formatTimeDisplay(slot);
        
        if (isAvailable) {
            opt.textContent = displayTime;
            hasAvailable = true;
        } else {
            opt.textContent = displayTime + ' — Unavailable';
            opt.disabled = true;
            opt.style.color = '#aaa';
        }
        startSel.appendChild(opt);
    });

    // Show booked slots info if any
    if (bookedSlots && bookedSlots.length > 0) {
        var lines = bookedSlots.map(function(b) {
            return formatTimeDisplay(b.start) + ' – ' + formatTimeDisplay(b.end) +
                   ' (unavailable until ' + formatTimeDisplay(b.buffer_end) + ')';
        });
        bookedInfo.innerHTML = '<strong>⚠ Already booked for this venue:</strong><br>' + lines.join('<br>');
        bookedInfo.style.display = 'block';
    } else {
        bookedInfo.style.display = 'none';
    }

    // Status banner
    if (!hasAvailable) {
        statusEl.style.display = 'block';
        statusEl.style.background = '#f8d7da';
        statusEl.style.color = '#721c24';
        statusEl.innerHTML = '❌ This venue is fully booked on the selected date.';
        document.getElementById('addScheduleBtn').disabled = true;
    } else {
        statusEl.style.display = 'block';
        statusEl.style.background = '#d4edda';
        statusEl.style.color = '#155724';
        statusEl.innerHTML = '✅ Green slots are available for this venue. Gray slots are unavailable due to existing bookings.';
        document.getElementById('addScheduleBtn').disabled = false;
    }
}

function sprintf2(fmt, h, m) {
    return (h < 10 ? '0' : '') + h + ':' + (m < 10 ? '0' : '') + m;
}

function fetchAvailability(venueId, date, callback, generation) {
    console.log('fetchAvailability called for venue ID:', venueId, 'date:', date, 'generation:', generation);
    var cacheKey = 'venue_' + venueId + '_' + date;

    // Guard: if this fetch is already stale (user opened a different venue), do nothing
    if (generation !== undefined && generation !== timeModalGeneration) {
        console.log('fetchAvailability: stale generation (' + generation + ' vs ' + timeModalGeneration + '), aborting');
        return;
    }

    // Check if we already have cached data for THIS SPECIFIC venue and date
    if (availabilityCache[cacheKey]) {
        console.log('Using cached data for', cacheKey);
        // Still guard before invoking callback in case modal changed while we were here
        if (generation !== undefined && generation !== timeModalGeneration) return;
        callback(availabilityCache[cacheKey]);
        return;
    }
    
    var statusEl = document.getElementById('availabilityStatus');
    statusEl.style.display = 'block';
    statusEl.style.background = '#fff3cd';
    statusEl.style.color = '#856404';
    statusEl.innerHTML = '⏳ Checking availability...';

    document.getElementById('timeModalStart').innerHTML = '<option value="">Loading...</option>';
    document.getElementById('timeModalEnd').innerHTML   = '<option value="">Loading...</option>';
    document.getElementById('addScheduleBtn').disabled = true;

    var url = baseUrl + '/ajax/check_availability.php?venue_id=' + venueId + '&date=' + date;
    console.log('Fetching availability for venue', venueId, 'date', date, 'URL:', url);
    
    fetch(url)
        .then(function(r) { 
            console.log('Response status:', r.status);
            return r.json(); 
        })
        .then(function(data) {
            // *** KEY FIX: discard result if user already opened a different venue ***
            if (generation !== undefined && generation !== timeModalGeneration) {
                console.log('fetchAvailability response: stale generation, discarding data for venue', venueId);
                return;
            }

            console.log('RAW AVAILABILITY DATA for venue', venueId, 'on', date, ':', data);
            
            if (data.success) {
                console.log('Booked slots:', data.booked_slots);
                console.log('Available starts:', data.available_starts);
                
                // Store in cache with venue-specific key
                availabilityCache[cacheKey] = data;
                callback(data);
            } else {
                console.warn('Availability check failed:', data.message);
                var statusEl2 = document.getElementById('availabilityStatus');
                if (statusEl2) {
                    statusEl2.style.background = '#f8d7da';
                    statusEl2.style.color = '#721c24';
                    statusEl2.innerHTML = '⚠ Could not check availability. All slots shown.';
                }
                callback({ success: true, booked_slots: [], available_starts: [] });
            }
        })
        .catch(function(error) {
            if (generation !== undefined && generation !== timeModalGeneration) return;
            console.error('Fetch error:', error);
            var statusEl2 = document.getElementById('availabilityStatus');
            if (statusEl2) {
                statusEl2.style.background = '#f8d7da';
                statusEl2.style.color = '#721c24';
                statusEl2.innerHTML = '⚠ Could not check availability. All slots shown.';
            }
            callback({ success: true, booked_slots: [], available_starts: [] });
        });
}

// Named handler references so we can cleanly remove them before adding new ones.
var _timeModalDateHandler = null;
var _timeModalStartHandler = null;

function openTimeModal(venueId, venueName) {
    console.log('========== OPEN TIME MODAL ==========');
    console.log('Opening for venue:', venueId, venueName);
    
    if (!venueId) { 
        console.error('No venue ID provided'); 
        return; 
    }

    timeModalGeneration++;
    var myGeneration = timeModalGeneration;
    console.log('Modal generation bumped to', myGeneration, 'for venue', venueId);
    
    // Clear modal context immediately
    timeModalContext = null;
    
    // Reset modal UI elements
    document.getElementById('timeModalDateInput').value = '';
    document.getElementById('timeModalStart').innerHTML = '<option value="">Select date first</option>';
    document.getElementById('timeModalEnd').innerHTML = '<option value="">Select start time first</option>';
    document.getElementById('bookedSlotsInfo').style.display = 'none';
    document.getElementById('bookedSlotsInfo').innerHTML = '';
    document.getElementById('availabilityStatus').style.display = 'none';
    document.getElementById('availabilityStatus').innerHTML = '';
    document.getElementById('addScheduleBtn').disabled = true;
    
    var venueCard = document.querySelector('.schedule-card[data-venue-id="' + venueId + '"]');
    if (!venueCard) { 
        console.error('Venue card not found for ID:', venueId); 
        return; 
    }
    
    // Get current date value from this venue's date picker, or default to today
    var datePicker = document.getElementById('date-picker-' + venueId);
    var dateVal = datePicker && datePicker.value ? datePicker.value : '';
    if (!dateVal) {
        var today = new Date();
        dateVal = today.getFullYear() + '-' +
                  String(today.getMonth() + 1).padStart(2, '0') + '-' +
                  String(today.getDate()).padStart(2, '0');
    }

    // Set modal context with this venue's ID
    timeModalContext = { venueId: venueId, venueName: venueName || 'Venue' };
    console.log('Set timeModalContext:', timeModalContext);

    var dateInput = document.getElementById('timeModalDateInput');
    if (!dateInput) { 
        console.error('timeModalDateInput not found'); 
        return; 
    }
    dateInput.value = dateVal;

    // ---- Clean removal of previous named listeners (no clone needed) ----
    if (_timeModalDateHandler) {
        dateInput.removeEventListener('change', _timeModalDateHandler);
        _timeModalDateHandler = null;
    }
    var startSel = document.getElementById('timeModalStart');
    if (_timeModalStartHandler) {
        startSel.removeEventListener('change', _timeModalStartHandler);
        _timeModalStartHandler = null;
    }

    // Fetch availability then populate dropdowns, guarded by myGeneration
    fetchAvailability(venueId, dateVal, function(data) {
        console.log('Got availability data for venue', venueId, ':', data);
        populateTimeDropdowns(data.available_starts, data.booked_slots, venueId);
    }, myGeneration);

    // Re-fetch when date changes (named function so it can be removed later)
    _timeModalDateHandler = function() {
        var newDate = this.value;
        if (!newDate) return;
        // Guard: only handle if this is still the current modal session
        if (myGeneration !== timeModalGeneration) return;
        console.log('Date changed to', newDate, 'for venue', venueId);
        document.getElementById('timeModalEnd').innerHTML = '<option value="">Select start time first</option>';
        fetchAvailability(venueId, newDate, function(data) {
            populateTimeDropdowns(data.available_starts, data.booked_slots, venueId);
        }, myGeneration);
    };
    document.getElementById('timeModalDateInput').addEventListener('change', _timeModalDateHandler);

    // Populate end times when start time is selected (named function so it can be removed)
    _timeModalStartHandler = function() {
        if (myGeneration !== timeModalGeneration) return;
        var startVal = this.value;
        var endSel = document.getElementById('timeModalEnd');
        endSel.innerHTML = '<option value="">Select End Time</option>';

        if (!startVal) return;

        var startMins = timeToMins(startVal);
        var currentDate = document.getElementById('timeModalDateInput').value;
        var cacheKey = 'venue_' + venueId + '_' + currentDate;
        var cached = availabilityCache[cacheKey];
        var bookedSlots = cached ? cached.booked_slots : [];

        // Generate end time options from 30 min after start up to 23:00
        for (var h = 7; h <= 23; h++) {
            for (var m = 0; m < 60; m += 30) {
                if (h === 23 && m > 0) continue;
                var slot = sprintf2('%02d:%02d', h, m);
                var slotMins = timeToMins(slot);
                
                if (slotMins <= startMins) continue;

                var wouldOverlap = bookedSlots.some(function(b) {
                    return startVal < b.buffer_end && slot > b.start;
                });

                var opt = document.createElement('option');
                opt.value = slot;
                opt.textContent = formatTimeDisplay(slot);
                
                if (wouldOverlap) {
                    opt.textContent += ' — Would conflict';
                    opt.disabled = true;
                    opt.style.color = '#aaa';
                }
                endSel.appendChild(opt);
            }
        }
    };
    document.getElementById('timeModalStart').addEventListener('change', _timeModalStartHandler);

    // Show modal
    var modal = document.getElementById('timeSlotModal');
    if (modal) {
        modal.classList.add('show');
    } else {
        console.error('timeSlotModal not found');
    }
}

function timeToMins(t) {
    var p = t.split(':');
    return parseInt(p[0]) * 60 + parseInt(p[1]);
}

function closeTimeModal() {
    var modal = document.getElementById('timeSlotModal');
    if (modal) {
        modal.classList.remove('show');
    }
    // Invalidate any in-flight availability fetch
    timeModalGeneration++;
    timeModalContext = null;
}

// Auto-capitalize middle initial
function autoCapitalizeMiddleInitial() {
    var miInput = document.getElementById('middle_initial');
    if (miInput) {
        miInput.addEventListener('input', function() {
            this.value = this.value.toUpperCase().replace(/[^A-Z]/g, '').substring(0, 1);
        });
    }
    
    var guestMiInput = document.getElementById('guest_middle_initial');
    if (guestMiInput) {
        guestMiInput.addEventListener('input', function() {
            this.value = this.value.toUpperCase().replace(/[^A-Z]/g, '').substring(0, 1);
        });
    }
}

// Call it in DOMContentLoaded
document.addEventListener('DOMContentLoaded', function() {
    autoCapitalizeMiddleInitial();
});

function showModalAlert(icon, title, message) {
    document.getElementById('systemAlertIcon').textContent = icon;
    document.getElementById('systemAlertTitle').textContent = title;
    document.getElementById('systemAlertMessage').textContent = message;
    document.getElementById('systemAlertModal').classList.add('show');
}

function closeSystemAlert() {
    document.getElementById('systemAlertModal').classList.remove('show');
}

function confirmTimeSlot() {
    console.log('========== CONFIRM TIME SLOT CALLED ==========');
    console.log('Current timeModalContext:', timeModalContext);
    
    if (!timeModalContext) {
        console.error('No timeModalContext - session expired');
        showModalAlert('⚠️', 'Session Expired', 'Your session has expired. Please close this modal and try again.');
        closeTimeModal();
        return;
    }
    
    var venueId = timeModalContext.venueId;
    var venueName = timeModalContext.venueName;
    var dateVal = document.getElementById('timeModalDateInput').value;
    var start   = document.getElementById('timeModalStart').value;
    var end     = document.getElementById('timeModalEnd').value;
    
    console.log('Attempting to book:');
    console.log('- Venue ID:', venueId, '(', venueName, ')');
    console.log('- Date:', dateVal);
    console.log('- Start time:', start);
    console.log('- End time:', end);
    
    // Check if the selected start time is actually in the available list
    var cacheKey = 'venue_' + venueId + '_' + dateVal;
    console.log('Cache key:', cacheKey);
    console.log('Availability cache contents:', availabilityCache);
    
    var cached = availabilityCache[cacheKey];
    console.log('Cached data for this venue/date:', cached);
    
    if (!cached) {
        console.warn('No cached availability data found for', cacheKey);
    } else {
        console.log('Available starts from cache:', cached.available_starts);
        console.log('Booked slots from cache:', cached.booked_slots);
        
        // Check if selected start is in available starts
        if (cached.available_starts.indexOf(start) === -1) {
            console.warn('⚠️ Selected start time', start, 'is NOT in available starts list!');
        } else {
            console.log('✅ Selected start time', start, 'IS in available starts list');
        }
    }
    
    if (!dateVal) {
        console.log('Validation failed: No date selected');
        showModalAlert('📅', 'Date Required', 'Please select a date before adding a schedule.');
        return;
    }
    if (!start) {
        console.log('Validation failed: No start time selected');
        showModalAlert('🕐', 'Start Time Required', 'Please select a start time.');
        return;
    }
    if (!end) {
        console.log('Validation failed: No end time selected');
        showModalAlert('🕐', 'End Time Required', 'Please select an end time.');
        return;
    }
    
    var startM = timeToMins(start);
    var endM   = timeToMins(end);
    console.log('Time in minutes - Start:', startM, 'End:', endM);
    
    if (endM <= startM) {
        console.log('Validation failed: End time must be after start time');
        showModalAlert('⚠️', 'Invalid Time Range', 'End time must be after start time. Please adjust your selection.');
        return;
    }
    
    // Check against server-cached booked slots
    var bookedSlots = cached ? cached.booked_slots : [];
    console.log('Checking against', bookedSlots.length, 'booked slots');

    var serverConflict = bookedSlots.some(function(b, index) {
        console.log(`Checking against booked slot ${index + 1}:`, b);
        console.log(`  - Proposed: ${start} to ${end}`);
        console.log(`  - Booked: ${b.start} to ${b.end} (buffer until ${b.buffer_end})`);
        
        // Convert to comparable format
        var conflict = start < b.end && end > b.start;
        if (conflict) {
            console.log(`  ❌ CONFLICT DETECTED! Proposed (${start}-${end}) overlaps with booked (${b.start}-${b.end})`);
        } else {
            console.log(`  ✅ No conflict with this booked slot`);
        }
        return conflict;
    });

    if (serverConflict) {
        console.log('❌ Final result: CONFLICT - cannot book');
        showModalAlert(
            '🚫',
            'Time Slot Unavailable',
            'This time slot overlaps with an approved reservation or its 1-hour cleaning buffer for this venue. Please choose a different time from the available slots shown.'
        );
        return;
    }

    // Check for overlap with already-added schedules for this venue (same session)
    if (!facilitySchedules[venueId]) facilitySchedules[venueId] = [];
    
    console.log('Current facilitySchedules for venue', venueId, ':', facilitySchedules[venueId]);
    
    var hasOverlap = facilitySchedules[venueId].some(function(s, index) {
        if (s.date !== dateVal) return false;
        var sStartM = timeToMins(s.start);
        var sEndM   = timeToMins(s.end);
        var overlap = (startM < sEndM && endM > sStartM);
        if (overlap) {
            console.log(`❌ Overlaps with existing schedule ${index + 1}:`, s);
        }
        return overlap;
    });
    
    if (hasOverlap) {
        console.log('❌ Final result: CONFLICT with existing session schedule');
        showModalAlert(
            '⚠️',
            'Schedule Conflict',
            'This time overlaps with a schedule you have already added for this venue on the same date. Please choose a different time.'
        );
        return;
    }
    
    // All good — add the schedule
    console.log('✅ No conflicts found! Adding schedule...');
    var entry = { date: dateVal, start: start, end: end };
    
    if (!facilitySchedules[venueId]) {
        facilitySchedules[venueId] = [];
    }
    
    facilitySchedules[venueId].push(entry);
    console.log('Schedule added for venue ' + venueId + ':', entry);
    console.log('Updated facilitySchedules:', facilitySchedules);
    
    updateScheduleList(venueId);
    console.log('Schedule list updated for venue', venueId);
    
    var datePicker = document.getElementById('date-picker-' + venueId);
    if (datePicker) {
        datePicker.value = dateVal;
        console.log('Date picker updated to', dateVal);
    }
    
    closeTimeModal();
    console.log('Time modal closed');
    
    saveFormData();
    console.log('saveFormData called after adding schedule');
    console.log('========== CONFIRM TIME SLOT COMPLETE ==========');
}

function updateScheduleList(venueId) {
    console.log('Updating schedule list for venue:', venueId);
    
    var list = document.getElementById('schedList-' + venueId);
    var countEl = document.getElementById('schedCount-' + venueId);
    var datePicker = document.getElementById('date-picker-' + venueId);
    var startDisplay = document.getElementById('start-display-' + venueId);
    var endDisplay = document.getElementById('end-display-' + venueId);
    
    if (!list) {
        console.warn('Schedule list element not found for venue:', venueId);
        return;
    }
    
    var schedules = facilitySchedules[venueId] || [];
    console.log('Schedules for venue', venueId, ':', schedules);
    
    if (schedules.length === 0) {
        list.innerHTML = '';
        if (countEl) countEl.textContent = '0';
        
        if (datePicker) datePicker.value = '';
        if (startDisplay) startDisplay.value = 'Not set';
        if (endDisplay) endDisplay.value = 'Not set';
        return;
    }
    
    var latestSchedule = schedules[schedules.length - 1];
    
    if (datePicker) datePicker.value = latestSchedule.date;
    if (startDisplay) startDisplay.value = formatTime(latestSchedule.start);
    if (endDisplay) endDisplay.value = formatTime(latestSchedule.end);
    
    var html = '';
    schedules.forEach(function(s, index) {
        var displayDate = formatDateDisplay(s.date);
        var displayStart = formatTime(s.start);
        var displayEnd = formatTime(s.end);
        
        html += '<div class="schedule-item">';
        html += '<span class="schedule-text">' + displayDate + ' • ' + displayStart + ' - ' + displayEnd + '</span>';
        html += '<button type="button" class="btn-remove" onclick="removeSchedule(\'' + venueId + '\', ' + index + ')" title="Remove">×</button>';
        html += '</div>';
    });
    
    list.innerHTML = html;
    
    if (countEl) {
        countEl.textContent = schedules.length;
    }

    // Refresh live pricing summary for external clients
    if (typeof updateSchedulePriceSummary === 'function') {
        updateSchedulePriceSummary();
    }
}

function removeSchedule(venueId, index) {
    console.log('Removing schedule at index', index, 'for venue:', venueId);
    
    if (facilitySchedules[venueId] && facilitySchedules[venueId][index]) {
        facilitySchedules[venueId].splice(index, 1);
        updateScheduleList(venueId);
        saveFormData();
    }
}

function formatTime(timeStr) {
    if (!timeStr) return '';
    
    var parts = timeStr.split(':');
    var hours = parseInt(parts[0]);
    var minutes = parts[1];
    
    var ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12;
    hours = hours ? hours : 12;
    
    return hours + ':' + minutes + ' ' + ampm;
}

function formatDateDisplay(dateStr) {
    if (!dateStr) return '';
    
    var parts = dateStr.split('-');
    if (parts.length < 3) return dateStr;
    
    var year = parseInt(parts[0]);
    var month = parseInt(parts[1]) - 1;
    var day = parseInt(parts[2]);
    
    var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 
                  'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    
    return months[month] + ' ' + day + ', ' + year;
}

function buildSummary() {
    console.log('Building summary with venues:', selectedVenues);
    console.log('Facility schedules:', facilitySchedules);
    
    var f = document.getElementById('reservationForm');
    
    if (reservationType === 'guest') {
        buildGuestSummary();
    } else {
        buildFunctionSummary();
    }
}

function buildGuestSummary() {
    var html = '<h6 class="text-danger mt-0 mb-2">Guest Information</h6>';
    
    // Principal Guest
    var lastName = document.getElementById('guest_last_name')?.value || '';
    var firstName = document.getElementById('guest_first_name')?.value || '';
    var middleInit = document.getElementById('guest_middle_initial')?.value || '';
    var fullName = firstName + ' ' + (middleInit ? middleInit + ' ' : '') + lastName;
    
    html += '<div class="summary-item"><strong>Principal Guest:</strong> ' + fullName + '</div>';
    html += '<div class="summary-item"><strong>Email:</strong> ' + (document.getElementById('guest_email')?.value || '') + '</div>';
    html += '<div class="summary-item"><strong>Contact:</strong> ' + (document.getElementById('guest_contact')?.value || '') + '</div>';
    html += '<div class="summary-item"><strong>Address:</strong> ' + (document.getElementById('guest_address')?.value || '') + '</div>';
    
    // Other Guests
    var guestCards = document.querySelectorAll('.guest-card');
    if (guestCards.length > 0) {
        html += '<h6 class="text-danger mt-3 mb-2">Other Guests</h6>';
        guestCards.forEach(function(card, index) {
            var name = card.querySelector('input[name="guest_names[]"]')?.value || '';
            var age = card.querySelector('input[name="guest_ages[]"]')?.value || '';
            if (name) {
                html += '<div class="summary-item">Guest ' + (index + 1) + ': ' + name + ' (Age: ' + age + ')</div>';
            }
        });
    }
    
    // Stay Details
    html += '<h6 class="text-danger mt-3 mb-2">Stay Details</h6>';
    var arrival = document.getElementById('arrival_date')?.value || '';
    var departure = document.getElementById('departure_date')?.value || '';
    var checkin = document.getElementById('checkin_time')?.value || '';
    var checkout = document.getElementById('checkout_time')?.value || '';
    
    html += '<div class="summary-item"><strong>Arrival:</strong> ' + formatDateDisplay(arrival) + ' at ' + formatTime(checkin) + '</div>';
    html += '<div class="summary-item"><strong>Departure:</strong> ' + formatDateDisplay(departure) + ' at ' + formatTime(checkout) + '</div>';
    
    var adults = document.getElementById('adults_count')?.value || '0';
    var kids = document.getElementById('kids_count')?.value || '0';
    html += '<div class="summary-item"><strong>Guests:</strong> ' + adults + ' Adults, ' + kids + ' Kids</div>';
    
    // Room
    var roomSelect = document.getElementById('guest_room_id');
    var roomName = roomSelect.options[roomSelect.selectedIndex]?.text || '';
    html += '<div class="summary-item"><strong>Room:</strong> ' + roomName + '</div>';
    
    var remarks = document.getElementById('guest_remarks')?.value;
    if (remarks) {
        html += '<div class="summary-item"><strong>Remarks:</strong> ' + remarks + '</div>';
    }
    
    // Registered By
    html += '<h6 class="text-danger mt-3 mb-2">Registration Details</h6>';
    html += '<div class="summary-item"><strong>Registered By:</strong> ' + (document.getElementById('registered_by')?.value || '') + '</div>';
    html += '<div class="summary-item"><strong>Date Registered:</strong> ' + (document.getElementById('guest_form_date')?.value || '') + '</div>';
    
    document.getElementById('summaryBox').innerHTML = html;
}

function buildFunctionSummary() {
    var f = document.getElementById('reservationForm');
    var ln = (f.querySelector('[name="last_name"]')?.value || '').trim();
    var fn = (f.querySelector('[name="first_name"]')?.value || '').trim();
    var mi = (f.querySelector('[name="middle_initial"]')?.value || '').trim();
    var fullName = ln + (fn ? ', ' + fn : '') + (mi ? ' ' + mi : '');
    
    var eventTypeSel = document.getElementById('eventTypeId');
    var eventTypeName = eventTypeSel?.options[eventTypeSel.selectedIndex]?.text || '';
    
    var html = '<h6 class="text-danger mt-0 mb-2">Personal Information</h6>';
    html += '<div class="summary-item"><strong>Full Name:</strong> ' + fullName + '</div>';
    html += '<div class="summary-item"><strong>Email:</strong> ' + (f.querySelector('[name="email"]')?.value || '') + '</div>';
    html += '<div class="summary-item"><strong>Contact:</strong> ' + (f.querySelector('[name="contact"]')?.value || '') + '</div>';
    
    html += '<h6 class="text-danger mt-3 mb-2">Office Details</h6>';
    html += '<div class="summary-item"><strong>Office Type:</strong> ' + (document.getElementById('officeType').options[document.getElementById('officeType').selectedIndex]?.text || '') + '</div>';
    
    var off = document.getElementById('officeExternalWrap').style.display !== 'none' 
        ? document.getElementById('officeExternal').value 
        : (document.getElementById('officeSelect').options[document.getElementById('officeSelect').selectedIndex]?.text || '');
    html += '<div class="summary-item"><strong>Office/Organization:</strong> ' + off + '</div>';
    
    html += '<h6 class="text-danger mt-3 mb-2">Event Details</h6>';
    html += '<div class="summary-item"><strong>Activity Name:</strong> ' + (f.querySelector('[name="activity_name"]')?.value || '') + '</div>';
    html += '<div class="summary-item"><strong>Event Type:</strong> ' + eventTypeName + '</div>';
    
    var venueNames = selectedVenues.map(function(v){ return v.name; }).join(', ');
    html += '<div class="summary-item"><strong>Venue(s):</strong> ' + (venueNames || '-') + '</div>';
    html += '<div class="summary-item"><strong>Participants:</strong> ' + (f.querySelector('[name="participants"]')?.value || '') + '</div>';
    
    html += '<h6 class="text-danger mt-3 mb-2">Schedule</h6>';
    
    for (var vid in facilitySchedules) {
        var venue = selectedVenues.find(function(v){ return v.id == vid; });
        var venueName = venue ? venue.name : 'Venue';
        
        facilitySchedules[vid].forEach(function(s, i) {
            var startDt = s.date + ' ' + s.start;
            var endDt = s.date + ' ' + s.end;
            var durH = ((new Date(endDt) - new Date(startDt)) / (1000*60*60)).toFixed(1);
            html += '<div class="summary-item"><strong>Date:</strong> ' + formatDateDisplay(s.date) + '</div>';
            html += '<div class="summary-item"><strong>Time:</strong> ' + formatTime(s.start) + ' to ' + formatTime(s.end) + ' (' + durH + ' hrs) - ' + venueName + '</div>';
        });
    }
    
    var termsName = document.getElementById('termsFullName')?.value || '';
    if (termsName) {
        html += '<h6 class="text-danger mt-3 mb-2">Terms Acknowledgment</h6>';
        html += '<div class="summary-item"><strong>Agreed by:</strong> ' + termsName + '</div>';
        html += '<div class="summary-item"><strong>Position:</strong> ' + (document.getElementById('termsPosition')?.value || '') + '</div>';
        html += '<div class="summary-item"><strong>Date:</strong> ' + (document.getElementById('termsDate')?.value || '') + '</div>';
    }
    
    var misc = getMiscItemsJson();
    var hasSoundSystem = misc && misc['basic_sound_system'] !== undefined;
    if (misc && Object.keys(misc).length > 0) {
        html += '<h6 class="text-danger mt-3 mb-2">Miscellaneous Items</h6>';
        Object.keys(misc).forEach(function(k) {
            var v = misc[k];
            if (k === 'basic_sound_system') {
                html += '<div class="summary-item"><strong>Basic Sound System:</strong> Speaker: ' + (v.speaker||0) + ', Mic: ' + (v.mic||0) + '</div>';
            } else if (k === 'view_board') {
                html += '<div class="summary-item"><strong>View Board:</strong> Yes (1 unit)</div>';
            } else {
                var label = k.replace(/_/g, ' ').replace(/\b\w/g, function(c){ return c.toUpperCase(); });
                html += '<div class="summary-item"><strong>' + label + ':</strong> ' + (v.quantity||0) + ' pcs</div>';
            }
        });
    }
    
    // ── Cost Breakdown (External clients only) ──────────────────────────
    if (isExternalOffice()) {
        var rates = getVenueRates();
        var summaryLines = '';
        var sumTotal = 0;
        var hasSched = false;

        for (var cvid in facilitySchedules) {
            var cVenue = selectedVenues.find(function(v){ return v.id == cvid; });
            var cName = cVenue ? cVenue.name : 'Venue';
            (facilitySchedules[cvid] || []).forEach(function(cs) {
                if (!cs.start || !cs.end) return;
                hasSched = true;
                var sp = cs.start.split(':').map(Number);
                var ep = cs.end.split(':').map(Number);
                var sm = sp[0]*60 + (sp[1]||0);
                var em = ep[0]*60 + (ep[1]||0);
                if (em <= sm) em += 24*60;
                var hrs = (em - sm) / 60;
                var calc = calcVenueCost(hrs, rates);
                sumTotal += calc.cost;
                summaryLines += '<div style="display:flex;justify-content:space-between;font-size:.82rem;padding:.2rem 0;">' +
                    '<span>' + escapeHtml(cName) + ' — ' + escapeHtml(cs.date||'') + ' (' + hrs.toFixed(1) + 'h, ' + calc.type + ')</span>' +
                    '<strong style="color:#b71c1c;">' + formatPHP(calc.cost) + '</strong></div>';
            });
        }

        if (hasSched) {
            var soundFeeNote = '';
            if (hasSoundSystem) {
                sumTotal += rates.soundFee;
                soundFeeNote = '<div style="display:flex;justify-content:space-between;font-size:.82rem;padding:.2rem 0;">' +
                    '<span>🔊 Basic Sound System</span>' +
                    '<strong style="color:#b71c1c;">' + formatPHP(rates.soundFee) + '</strong></div>';
            }

            html += '<h6 class="text-danger mt-3 mb-2">💰 Cost Breakdown</h6>';
            html += '<div style="background:linear-gradient(135deg,#fff5f5,#ffe8e8);border:1.5px solid #f5c6cb;border-radius:10px;padding:.85rem 1rem;margin-top:.5rem;">';
            html += summaryLines;
            html += soundFeeNote;
            html += '<div style="border-top:1px dashed #f5c6cb;margin:.5rem 0;"></div>';
            html += '<div style="display:flex;justify-content:space-between;font-size:.9rem;">' +
                    '<strong style="color:#8b0000;">Total Amount Due</strong>' +
                    '<strong style="color:#b71c1c;font-size:1.1rem;">' + formatPHP(sumTotal) + '</strong></div>';
            html += '<div style="font-size:.72rem;color:#b71c1c;margin-top:.5rem;"><i class="bi bi-exclamation-circle me-1"></i>Payment must be settled <strong>before the day of the event.</strong></div>';
            html += '</div>';
        }
    }
    
    var banquetId = document.getElementById('banquetStyleId')?.value;
    if (banquetId) {
        var banquetCard = document.querySelector('.banquet-card[data-id="' + banquetId + '"]');
        if (banquetCard) {
            var banquetName = banquetCard.getAttribute('data-name');
            var banquetImg = banquetCard.getAttribute('data-image');
            html += '<h6 class="text-danger mt-3 mb-2">Banquet Style</h6>';
            html += '<div class="summary-item d-flex align-items-center">';
            if (banquetImg) html += '<img src="' + baseUrl + '/assets/images/banquet/' + banquetImg + '" alt="" class="summary-banquet-thumb" onerror="this.style.display=\'none\'">';
            html += '<span>' + banquetName + '</span></div>';
        }
    }
    
    var addInst = document.getElementById('additionalInstruction')?.value?.trim();
    if (addInst) { 
        html += '<h6 class="text-danger mt-3 mb-2">Additional Instruction</h6>'; 
        html += '<div class="summary-item">' + addInst.replace(/\n/g, '<br>') + '</div>'; 
    }
    
    document.getElementById('summaryBox').innerHTML = html;
}

function submitForm() {
    document.getElementById('confirmModal').classList.add('show');
}

function closeConfirmModal() {
    document.getElementById('confirmModal').classList.remove('show');
}

function doSubmit() {
    console.log('doSubmit called');
    console.log('Reservation type:', reservationType);
    
    closeConfirmModal();
    
    if (reservationType === 'guest') {
        submitGuestReservation();
    } else {
        submitFunctionReservation();
    }
}

function submitFunctionReservation() {
    console.log('Submitting function room reservation');
    console.log('Selected venues:', selectedVenues);
    console.log('facilitySchedules before submit: ', facilitySchedules);
    
    if (!selectedVenues || selectedVenues.length === 0) {
        showModalAlert('🏢', 'Venue Required', 'Please select at least one venue before submitting.');
        return;
    }
    
    if (!facilitySchedules || Object.keys(facilitySchedules).length === 0) {
        showModalAlert('📅', 'Schedule Required', 'Please add at least one schedule before submitting.');
        return;
    }
    
    var missingSchedules = false;
    var missingVenues = [];
    
    selectedVenues.forEach(function(venue) {
        if (!facilitySchedules[venue.id] || facilitySchedules[venue.id].length === 0) {
            missingSchedules = true;
            missingVenues.push(venue.name);
            console.error('Missing schedule for venue:', venue);
        }
    });
    
    if (missingSchedules) {
        showModalAlert('📅', 'Schedule Required', 'Please add schedules for the following venues:\n' + missingVenues.join(', '));
        return;
    }
    
    var f = document.getElementById('reservationForm');
    var fd = new FormData(f);
    
    fd.append('action', 'submit_function');
    fd.append('reservation_type', 'function');
    
    var etSel = document.getElementById('eventTypeId');
    fd.append('event_type', etSel?.options[etSel?.selectedIndex]?.text || '');
    fd.append('event_type_id', etSel?.value || '');
    
    var scheduleData = { 
        venues: selectedVenues, 
        schedules: facilitySchedules 
    };
    
    if (selectedVenues.length > 0) {
        fd.append('venue_id', selectedVenues[0].id);
        console.log('Adding venue_id:', selectedVenues[0].id);
    }
    
    var schedulesJson = JSON.stringify(scheduleData);
    console.log('Schedules JSON:', schedulesJson);
    fd.append('facilities_schedules', schedulesJson);
    
    var miscItems = getMiscItemsJson();
    console.log('Misc items:', miscItems);
    fd.append('miscellaneous_items', JSON.stringify(miscItems));
    
    fd.append('banquet_style_id', document.getElementById('banquetStyleId')?.value || '');
    fd.append('additional_instruction', document.getElementById('additionalInstruction')?.value || '');
    
    fd.append('terms_agreed_by', document.getElementById('termsFullName')?.value || '');
    fd.append('terms_position', document.getElementById('termsPosition')?.value || '');
    fd.append('terms_date', document.getElementById('termsDate')?.value || '');
    
    fd.delete('room_ids[]');
    
    if (document.getElementById('officeExternalWrap').style.display !== 'none') {
        fd.append('office_external_name', document.getElementById('officeExternal').value);
        fd.delete('office_id');
    } else {
        fd.append('office_id', document.getElementById('officeSelect').value);
        fd.delete('office_external_name');
    }
    
    console.log('FormData entries:');
    for (var pair of fd.entries()) {
        console.log(pair[0] + ':', pair[1]);
    }
    
    submitToServer(fd, '<?= $base ?>/ajax/reservation_submit.php');
}

function submitGuestReservation() {
    console.log('Submitting guest room reservation');
    
    // Validate guest form
    if (!validateGuestForm()) return;

    // Require a drawn signature
    var sigVal = document.getElementById('guest_signature')?.value || '';
    if (!sigVal) {
        showModalAlert('✍️', 'Signature Required', 'Please draw your digital signature before submitting.');
        return;
    }
    
    var fd = new FormData();
    fd.append('action', 'submit_guest');
    fd.append('reservation_type', 'guest');
    
    // Principal Guest Info
    fd.append('last_name', document.getElementById('guest_last_name').value);
    fd.append('first_name', document.getElementById('guest_first_name').value);
    fd.append('middle_initial', document.getElementById('guest_middle_initial').value || '');
    fd.append('date_of_birth', document.getElementById('guest_dob').value);
    fd.append('address', document.getElementById('guest_address').value);
    fd.append('email', document.getElementById('guest_email').value);
    fd.append('contact_number', document.getElementById('guest_contact').value);
    
    // Other Guests - Dynamic
    var otherGuests = [];
    var guestCards = document.querySelectorAll('.guest-card');
    guestCards.forEach(function(card) {
        var name = card.querySelector('input[name="guest_names[]"]')?.value || '';
        var dob = card.querySelector('input[name="guest_dobs[]"]')?.value || '';
        var age = card.querySelector('input[name="guest_ages[]"]')?.value || '';
        
        if (name) {
            otherGuests.push({
                name: name,
                dob: dob,
                age: age
            });
        }
    });
    fd.append('other_guests', JSON.stringify(otherGuests));
    
    // Stay Details
    fd.append('arrival_date', document.getElementById('arrival_date').value);
    fd.append('departure_date', document.getElementById('departure_date').value);
    fd.append('checkin_time', document.getElementById('checkin_time').value);
    fd.append('checkout_time', document.getElementById('checkout_time').value);
    fd.append('adults_count', document.getElementById('adults_count').value);
    fd.append('kids_count', document.getElementById('kids_count').value);
    fd.append('room_id', document.getElementById('guest_room_id').value);
    fd.append('room_type', 'Guest Room');
    fd.append('remarks', document.getElementById('guest_remarks').value || '');
    fd.append('registered_by', document.getElementById('registered_by').value);
    
    // Consent & Signature
    fd.append('data_privacy_consent', document.getElementById('guestConsent').checked ? '1' : '0');
    fd.append('digital_signature', sigVal);
    fd.append('guest_form_date', document.getElementById('guest_form_date')?.value || '<?= date("Y-m-d") ?>');
    
    // Terms acceptance (from step 4)
    fd.append('terms_agreed_by', document.getElementById('termsFullName')?.value || '');
    fd.append('terms_position', document.getElementById('termsPosition')?.value || '');
    fd.append('terms_date', document.getElementById('termsDate')?.value || '');
    
    console.log('Guest FormData entries:');
    for (var pair of fd.entries()) {
        console.log(pair[0] + ':', pair[1]);
    }
    
    submitToServer(fd, '<?= $base ?>/ajax/guest_reservation_submit.php');
}

/* ── Guest signature pad ────────────────────────────────────────────────── */
function initGuestSignaturePad() {
    var canvas = document.getElementById('guestSignaturePad');
    var wrap = document.getElementById('guestSignatureWrap');
    var hidden = document.getElementById('guest_signature');
    var clearBtn = document.getElementById('guestSigClear');
    if (!canvas || !wrap || !hidden) return;

    var ctx = canvas.getContext('2d');
    var drawing = false;
    var hasInk = false;
    var last = { x: 0, y: 0 };

    function resizeCanvas() {
        var rect = wrap.getBoundingClientRect();
        var dpr = window.devicePixelRatio || 1;
        var w = Math.max(280, Math.floor(rect.width));
        var h = 160;

        var img = null;
        if (hasInk) {
            try { img = new Image(); img.src = canvas.toDataURL('image/png'); } catch (e) { img = null; }
        }

        canvas.width = Math.floor(w * dpr);
        canvas.height = Math.floor(h * dpr);
        canvas.style.width = w + 'px';
        canvas.style.height = h + 'px';
        ctx.setTransform(dpr, 0, 0, dpr, 0, 0);

        // background
        ctx.fillStyle = '#fff';
        ctx.fillRect(0, 0, w, h);
        ctx.lineWidth = 2.2;
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';
        ctx.strokeStyle = '#111';

        if (img) {
            img.onload = function () {
                ctx.drawImage(img, 0, 0, w, h);
            };
        }
    }

    function getPos(ev) {
        var r = canvas.getBoundingClientRect();
        var x = 0, y = 0;
        if (ev.touches && ev.touches.length) {
            x = ev.touches[0].clientX;
            y = ev.touches[0].clientY;
        } else {
            x = ev.clientX;
            y = ev.clientY;
        }
        return { x: x - r.left, y: y - r.top };
    }

    function start(ev) {
        ev.preventDefault();
        drawing = true;
        last = getPos(ev);
    }
    function move(ev) {
        if (!drawing) return;
        ev.preventDefault();
        var p = getPos(ev);
        ctx.beginPath();
        ctx.moveTo(last.x, last.y);
        ctx.lineTo(p.x, p.y);
        ctx.stroke();
        last = p;
        hasInk = true;
    }
    function end() {
        if (!drawing) return;
        drawing = false;
        if (hasInk) {
            try { hidden.value = canvas.toDataURL('image/png'); } catch (e) { /* ignore */ }
        }
    }
    function clear() {
        hasInk = false;
        hidden.value = '';
        resizeCanvas();
    }

    // pointer events (best), fallback to mouse/touch
    if (window.PointerEvent) {
        canvas.addEventListener('pointerdown', start);
        canvas.addEventListener('pointermove', move);
        canvas.addEventListener('pointerup', end);
        canvas.addEventListener('pointercancel', end);
        canvas.style.touchAction = 'none';
    } else {
        canvas.addEventListener('mousedown', start);
        canvas.addEventListener('mousemove', move);
        document.addEventListener('mouseup', end);
        canvas.addEventListener('touchstart', start, { passive: false });
        canvas.addEventListener('touchmove', move, { passive: false });
        canvas.addEventListener('touchend', end);
        canvas.addEventListener('touchcancel', end);
    }

    if (clearBtn) clearBtn.addEventListener('click', clear);

    resizeCanvas();
    window.addEventListener('resize', function () {
        // debounce-ish
        clearTimeout(initGuestSignaturePad._t);
        initGuestSignaturePad._t = setTimeout(resizeCanvas, 120);
    });
}

document.addEventListener('DOMContentLoaded', function () {
    initGuestSignaturePad();
});

function submitToServer(formData, url) {
    var btn = document.getElementById('btnSubmit');
    var originalText = btn.textContent;
    btn.disabled = true;
    btn.textContent = 'Submitting...';
    
    fetch(url, { 
        method: 'POST', 
        body: formData 
    })
    .then(function(response) {
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(function(data) {
        console.log('Response data:', data);
        
        var content = document.getElementById('resultContent');
        content.className = 'modal-box';
        
        if (data.success) {
            content.innerHTML =
                '<button type="button" class="modal-close-btn" onclick="closeResultModal()">&times;</button>' +
                '<h4>✅ Reservation Submitted!</h4>' +
                '<p>' + (data.message || 'Your reservation has been submitted successfully!') + '</p>' +
                (data.booking_no ? '<p><strong>Booking No:</strong> ' + data.booking_no + '</p>' : '') +
                '<p class="text-muted" style="font-size:0.875rem;">You will be notified once your reservation is confirmed.</p>' +
                '<button type="button" class="btn-res btn-next mt-3" onclick="closeResultModal(); window.location.href=\'index.php\';">OK, Go to Home</button>';
            clearSavedData();
        } else {
            content.innerHTML =
                '<button type="button" class="modal-close-btn" onclick="closeResultModal()">&times;</button>' +
                '<h4>❌ Error</h4>' +
                '<p>' + (data.message || 'An error occurred.') + '</p>' +
                '<button type="button" class="btn-res btn-next mt-3" onclick="closeResultModal()">Close</button>';
        }
        
        document.getElementById('resultModal').classList.add('show');
    })
    .catch(function(error) {
        console.error('Submission error:', error);
        document.getElementById('resultContent').innerHTML = '<h4>❌ Error</h4>' +
            '<p>Could not submit. Please check your connection and try again.</p>' +
            '<button type="button" class="btn-res btn-next mt-3" onclick="closeResultModal()">Close</button>';
        document.getElementById('resultModal').classList.add('show');
    })
    .finally(function() {
        btn.disabled = false;
        btn.textContent = originalText;
    });
}

function closeResultModal() {
    document.getElementById('resultModal').classList.remove('show');
}

// Do NOT close result modal on backdrop click so users have time to read it.
document.getElementById('guestCountModal')?.addEventListener('click', function(e) { if (e.target === this) closeGuestCountModal(); });
document.getElementById('guestRemoveModal')?.addEventListener('click', function(e) { if (e.target === this) closeGuestRemoveModal(); });
document.getElementById('banquetModal')?.addEventListener('click', function(e) { if (e.target === this) closeBanquetModal(); });
document.getElementById('confirmModal')?.addEventListener('click', function(e) { if (e.target === this) closeConfirmModal(); });
document.getElementById('timeSlotModal')?.addEventListener('click', function(e) { if (e.target === this) closeTimeModal(); });

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing...');
    
    loadBanquetStyles();
    
    // Automatically resume session to prevent data loss on accidental refresh
    if (sessionStorage.getItem('reservationFormData') && sessionStorage.getItem('reservationStep')) {
        resumeSavedSession();
    }
    
    var officeType = document.getElementById('officeType');
    if (officeType) {
        officeType.addEventListener('change', handleOfficeTypeChange);
        console.log('Office type change handler attached');
    } else {
        console.error('Office type select not found');
    }
    
    var inputs = document.querySelectorAll('input, select, textarea');
    inputs.forEach(function(input) {
        input.addEventListener('change', function() { setTimeout(saveFormData, 100); });
        input.addEventListener('blur', function() { setTimeout(saveFormData, 100); });
    });
    
    document.querySelectorAll('.misc-cb').forEach(function(cb) {
        cb.addEventListener('change', function() {
            var key = this.getAttribute('data-key');
            var checked = this.checked;
            
            if (key === 'basic_sound_system') {
                var item = document.querySelector('.misc-item[data-key="basic_sound_system"]');
                if (item) {
                    item.querySelectorAll('.misc-sound').forEach(function(inp) {
                        inp.disabled = !checked;
                        if (checked && inp.value === '') {
                            inp.value = '0';
                        } else if (!checked) {
                            inp.value = '0';
                        }
                    });
                }
            } else if (key === 'view_board') {
                console.log('View board ' + (checked ? 'checked' : 'unchecked'));
            } else {
                var qty = document.querySelector('.misc-qty-inline[data-key="' + key + '"]');
                if (qty) { 
                    qty.disabled = !checked; 
                    if (checked && qty.value === '') {
                        qty.value = '0';
                    } else if (!checked) {
                        qty.value = '0';
                    }
                }
            }
            saveFormData();
            if (typeof updateSchedulePriceSummary === 'function') {
                updateSchedulePriceSummary();
            }
        });
    });
    
    document.querySelectorAll('.misc-qty, .misc-qty-inline').forEach(function(input) {
        input.addEventListener('change', function() {
            var min = parseInt(this.min) || 0;
            var max = parseInt(this.max) || 999;
            var val = parseInt(this.value) || 0;
            
            if (val < min) {
                this.value = min;
                showModalAlert('⚠️', 'Invalid Quantity', 'Value cannot be less than ' + min);
            } else if (val > max) {
                this.value = max;
                showModalAlert('⚠️', 'Limit Reached', 'Maximum allowed is ' + max);
            }
        });
        
        input.addEventListener('keyup', function() {
            var max = parseInt(this.max) || 999;
            var val = parseInt(this.value) || 0;
            
            if (val > max) {
                this.value = max;
            }
        });
    });
    
    var termsAgree = document.getElementById('termsAgree');
    if (termsAgree) {
        termsAgree.addEventListener('change', function() {
            console.log('Terms checkbox changed:', this.checked);
            saveFormData();
        });
    }
    
    // Initialize guest card if none exist and we're on guest step
    if (reservationType === 'guest' && document.getElementById('guests-container')?.children.length === 0) {
        addGuestCard();
    }
});
</script>

<?php require_once __DIR__ . '/inc/footer.php'; ?>