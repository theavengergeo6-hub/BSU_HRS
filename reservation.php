<?php
/**
 * BSU Hostel - Facility Reservation (6-step form)
 * Step 1: Info | 2: Rooms | 3: Schedule | 4: Terms | 5: Miscellaneous | 6: Summary
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

$venues = $conn->query("SELECT id, name, floor, capacity, description FROM venues WHERE is_active = 1 ORDER BY name");
$function_venues = [];
$guest_venues = [];

if ($venues && $venues->num_rows > 0) {
    while ($venue = $venues->fetch_assoc()) {
        if (stripos($venue['name'], 'Function') !== false) {
            $function_venues[] = $venue;
        } else {
            $guest_venues[] = $venue;
        }
    }
} else {
    // Fallback if no venues found
    $function_venues = [
        ['id' => 1, 'name' => 'Function Room A', 'floor' => 'Ground Floor', 'capacity' => 30],
        ['id' => 2, 'name' => 'Function Room B', 'floor' => 'Ground Floor', 'capacity' => 40],
        ['id' => 3, 'name' => 'Function Room C', 'floor' => 'Ground Floor', 'capacity' => 50],
        ['id' => 4, 'name' => 'Function Room D', 'floor' => 'Ground Floor', 'capacity' => 20],
        ['id' => 5, 'name' => 'Function Room E', 'floor' => 'Ground Floor', 'capacity' => 35]
    ];
    $guest_venues = [
        ['id' => 6, 'name' => 'Guest Room 1', 'floor' => '2nd Floor', 'capacity' => 2],
        ['id' => 7, 'name' => 'Guest Room 2', 'floor' => '2nd Floor', 'capacity' => 2],
        ['id' => 8, 'name' => 'Guest Room 3', 'floor' => '2nd Floor', 'capacity' => 3],
        ['id' => 9, 'name' => 'Guest Room 4', 'floor' => '2nd Floor', 'capacity' => 2]
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
}
</style>

<main class="reservation-page">
    <div class="container">
        <div class="reservation-card">
            <h1>Facility Reservation</h1>
            <p class="subtitle">Book your preferred facility in just a few steps</p>

            <div class="progress-steps" id="progressSteps">
                <div class="progress-line" id="progressLine" style="width: 0%"></div>
                <div class="step active" data-step="1"><span>1</span><div class="step-label">Info</div></div>
                <div class="step" data-step="2"><span>2</span><div class="step-label">Rooms</div></div>
                <div class="step" data-step="3"><span>3</span><div class="step-label">Schedule</div></div>
                <div class="step" data-step="4"><span>4</span><div class="step-label">Terms</div></div>
                <div class="step" data-step="5"><span>5</span><div class="step-label">Misc</div></div>
                <div class="step" data-step="6"><span>6</span><div class="step-label">Summary</div></div>
            </div>

            <div id="resumeBookingContainer" style="display: none; margin-bottom: 1rem;">
                <div class="alert alert-info d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-arrow-repeat me-2"></i> You have an incomplete reservation from your last session.</span>
                    <button type="button" class="btn-outline-danger" onclick="clearSavedData()">Start Fresh</button>
                </div>
            </div>

            <form id="reservationForm">
                <!-- Step 1: Personal and Event Info -->
                <div class="form-step active" id="step1Form">
                    <div class="form-header"><h3>Reservation Information</h3><p>Enter your details and event information</p></div>
                    <div class="row">
                        <div class="col-md-4"><div class="form-group"><label>Last Name *</label><input type="text" class="form-control" name="last_name" id="last_name" required></div></div>
                        <div class="col-md-4"><div class="form-group"><label>First Name *</label><input type="text" class="form-control" name="first_name" id="first_name" required></div></div>
                        <div class="col-md-2"><div class="form-group"><label>M.I.</label><input type="text" class="form-control" name="middle_initial" id="middle_initial" maxlength="2"></div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6"><div class="form-group"><label>Email *</label><input type="email" class="form-control" name="email" id="email" required></div></div>
                        <div class="col-md-6"><div class="form-group"><label>Contact Number *</label><input type="tel" class="form-control" name="contact" id="contact" required></div></div>
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
                        <div></div>
                        <button type="button" class="btn-res btn-next" onclick="saveAndGo(2)">Continue</button>
                    </div>
                </div>

                <!-- Step 2: Venue selection -->
                <div class="form-step" id="step2Form">
                    <div class="form-header"><h3>Select Venue</h3><p>Choose function rooms and/or guest rooms to use</p></div>
                    
                    <?php if (!empty($function_venues)): ?>
                    <h5 class="mt-3 mb-2">Function Rooms</h5>
                    <?php foreach ($function_venues as $venue): ?>
                    <div class="room-select-card" 
                        data-id="<?= $venue['id'] ?>" 
                        data-name="<?= htmlspecialchars($venue['name']) ?>" 
                        data-floor="<?= htmlspecialchars($venue['floor']) ?>" 
                        data-capacity="<?= $venue['capacity'] ?>"
                        onclick="toggleVenue(this)">
                        <input type="checkbox" name="venue_ids[]" value="<?= $venue['id'] ?>" style="display:none">
                        <div class="d-flex justify-content-between align-items-center">
                            <strong><?= htmlspecialchars($venue['name']) ?></strong>
                            <span class="badge bg-light text-dark"><?= $venue['capacity'] ?> pax</span>
                        </div>
                        <small class="text-muted"><?= htmlspecialchars($venue['floor']) ?></small>
                        <p class="small text-muted mt-1 mb-0"><?= htmlspecialchars($venue['description'] ?? '') ?></p>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                    
                    <?php if (!empty($guest_venues)): ?>
                    <h5 class="mt-4 mb-2">Guest Rooms</h5>
                    <?php foreach ($guest_venues as $venue): ?>
                    <div class="room-select-card" 
                        data-id="<?= $venue['id'] ?>" 
                        data-name="<?= htmlspecialchars($venue['name']) ?>" 
                        data-floor="<?= htmlspecialchars($venue['floor']) ?>" 
                        data-capacity="<?= $venue['capacity'] ?>"
                        onclick="toggleVenue(this)">
                        <input type="checkbox" name="venue_ids[]" value="<?= $venue['id'] ?>" style="display:none">
                        <div class="d-flex justify-content-between align-items-center">
                            <strong><?= htmlspecialchars($venue['name']) ?></strong>
                            <span class="badge bg-light text-dark"><?= $venue['capacity'] ?> pax</span>
                        </div>
                        <small class="text-muted"><?= htmlspecialchars($venue['floor']) ?></small>
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

                <!-- Step 3: Schedule -->
                <div class="form-step" id="step3Form">
                    <p class="mb-3"><strong>Add date and time for each selected facility (7:00 AM - 11:00 PM only).</strong></p>
                    <div id="scheduleFacilitiesContainer"></div>
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
                    
                    <div class="signature-section">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Full Name *</label>
                                <input type="text" class="form-control" id="termsFullName" placeholder="Enter your full name" required>
                                <small class="text-muted">Type your full name as digital signature</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Designation/Position</label>
                                <input type="text" class="form-control" id="termsPosition" placeholder="e.g., Event Coordinator, Organization President">
                            </div>
                        </div>
                        
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="termsAgree" disabled>
                            <label class="form-check-label" for="termsAgree">
                                I have read, understood, and agree to abide by the terms and conditions above. I acknowledge that failure to comply may result in penalties or affect future reservation privileges.
                            </label>
                        </div>
                    </div>
                    
                    <div class="form-buttons">
                        <button type="button" class="btn-res btn-prev" onclick="goToStep(3)">Back</button>
                        <button type="button" class="btn-res btn-next" onclick="saveAndGo(5)" id="nextStep4Btn">Continue</button>
                    </div>
                </div>

                <!-- Step 5: Miscellaneous Needed -->
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
                                    <span class="limit-hint">max 2  </span>
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

<!-- Result Modal -->
<div class="res-modal" id="resultModal">
    <div class="modal-box" id="resultContent"></div>
</div>

<script>
var officesByType = <?= json_encode($offices_by_type) ?>;
var baseUrl = '<?= $base ?>';
var currentStep = 1;
var selectedVenues = [];
var facilitySchedules = {};
var timeModalContext = null;

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

// Generate time slots from 7:00 AM to 11:00 PM (30-min intervals)
var timeSlots = [];
for (var h = 7; h <= 23; h++) {
    for (var m = 0; m < 60; m += 30) {
        if (h === 23 && m > 0) continue;
        var t = (h < 10 ? '0' : '') + h + ':' + (m < 10 ? '0' : '') + m;
        timeSlots.push(t);
    }
}

// ========== SESSION STORAGE FUNCTIONS ==========
function saveFormData() {
    console.log('saveFormData called');
    
    var formData = {
        step: currentStep,
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
    
    console.log('Saving form data:', formData);
    
    sessionStorage.setItem('reservationFormData', JSON.stringify(formData));
    sessionStorage.setItem('reservationStep', currentStep);
    
    checkForSavedData();
}

function loadFormData() {
    var saved = sessionStorage.getItem('reservationFormData');
    if (!saved) return;
    
    try {
        var data = JSON.parse(saved);
        console.log('Loading form data:', data);
        
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
        if (step && step > 1 && data) {
            container.style.display = 'block';
        } else {
            container.style.display = 'none';
        }
    }
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
    if (n < 1 || n > 6) return;
    
    document.querySelectorAll('.form-step').forEach(function(s){ s.classList.remove('active'); });
    document.getElementById('step' + n + 'Form').classList.add('active');
    
    document.querySelectorAll('.progress-steps .step').forEach(function(s){
        var sn = parseInt(s.getAttribute('data-step'), 10);
        s.classList.remove('active', 'completed');
        if (sn < n) s.classList.add('completed');
        if (sn === n) s.classList.add('active');
    });
    
    document.getElementById('progressLine').style.width = ((n - 1) / 5 * 100) + '%';
    currentStep = n;
    
    if (n === 3) renderScheduleStep();
    if (n === 4) loadTermsForStep4();
    if (n === 6) buildSummary();
    
    saveFormData();
}

function goStep(n) {
    saveAndGo(n);
}

// ========== TERMS AND CONDITIONS FUNCTIONS ==========
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
}

// ========== VALIDATION ==========
function validateStep(n) {
    console.log('Validating step:', n);
    var step = document.getElementById('step' + n + 'Form');
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
        
        if (!document.getElementById('termsFullName').value.trim()) {
            showModalAlert('✍️', 'Signature Required', 'Please enter your full name as digital signature.');
            document.getElementById('termsFullName').focus();
            return false;
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
    
    selectedVenues.forEach(function(v) {
        var datePicker = document.getElementById('date-picker-' + v.id);
        if (datePicker) {
            datePicker.addEventListener('click', function(e) {
                e.preventDefault();
                console.log('Date picker clicked for venue:', v.id);
                openTimeModal(v.id, v.name);
            });
        } else {
            console.error('Date picker not found for venue:', v.id);
        }
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
            
            // Check if this is 2:00 PM
            if (slot === '14:00') {
                console.log('2:00 PM is DISABLED in dropdown for venue', venueId);
            }
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
                callback({ success: true, booked_slots: [], available_starts: generateAllStarts() });
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
            callback({ success: true, booked_slots: [], available_starts: generateAllStarts() });
        });
}

// Named handler references so we can cleanly remove them before adding new ones.
// This avoids the clone-replace hack which caused stale async callbacks to still
// fire against the live DOM after a new venue's modal had already opened.
var _timeModalDateHandler = null;
var _timeModalStartHandler = null;

function openTimeModal(venueId, venueName) {
    console.log('========== OPEN TIME MODAL ==========');
    console.log('Opening for venue:', venueId, venueName);
    
    if (!venueId) { 
        console.error('No venue ID provided'); 
        return; 
    }

    // *** KEY FIX: bump the generation counter FIRST so any in-flight fetch
    // for a previously-opened venue will see its generation is stale and abort
    // before touching the DOM. ***
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
        html += '<div class="summary-item"><strong>Date:</strong> ' + (document.getElementById('termsDate')?.value || '') + '</div>';
    }
    
    var misc = getMiscItemsJson();
    var miscKeys = Object.keys(misc);
    if (miscKeys.length > 0) {
        html += '<h6 class="text-danger mt-3 mb-2">Miscellaneous Items</h6>';
        miscKeys.forEach(function(k) {
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
    console.log('Selected venues:', selectedVenues);
    console.log('facilitySchedules before submit: ', facilitySchedules);
    
    closeConfirmModal();
    
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
    
    fd.append('action', 'submit');
    
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
    
    var btn = document.getElementById('btnSubmit');
    var originalText = btn.textContent;
    btn.disabled = true;
    btn.textContent = 'Submitting...';
    
    fetch('<?= $base ?>/ajax/reservation_submit.php', { 
        method: 'POST', 
        body: fd 
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
            content.innerHTML = '<h4>✅ Success</h4>' +
                               '<p>' + (data.message || 'Reservation submitted successfully!') + '</p>' +
                               '<button type="button" class="btn-res btn-next mt-3" onclick="closeResultModal()">Close</button>';
            clearSavedData();
            
            setTimeout(function() {
                window.location.href = 'index.php';
            }, 2000);
        } else {
            content.innerHTML = '<h4>❌ Error</h4>' +
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

document.getElementById('resultModal')?.addEventListener('click', function(e) { if (e.target === this) closeResultModal(); });
document.getElementById('banquetModal')?.addEventListener('click', function(e) { if (e.target === this) closeBanquetModal(); });
document.getElementById('confirmModal')?.addEventListener('click', function(e) { if (e.target === this) closeConfirmModal(); });
document.getElementById('timeSlotModal')?.addEventListener('click', function(e) { if (e.target === this) closeTimeModal(); });

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing...');
    
    loadBanquetStyles();
    
    checkForSavedData();
    
    var savedStep = sessionStorage.getItem('reservationStep');
    var savedData = sessionStorage.getItem('reservationFormData');
    
    console.log('Saved step:', savedStep);
    console.log('Saved data exists:', !!savedData);
    
    if (savedStep && savedStep > 1 && savedData) {
        console.log('Loading saved data for step:', savedStep);
        loadFormData();
        
        setTimeout(function() {
            goToStep(parseInt(savedStep));
        }, 500);
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
});
</script>

<?php require_once __DIR__ . '/inc/footer.php'; ?>