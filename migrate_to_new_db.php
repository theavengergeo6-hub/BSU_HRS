<?php
/**
 * Database Migration Script
 * Migrates all data from hostelweb to bsu_hrs_schema
 * Run once: http://localhost/BSU_HRS/migrate_to_new_db.php
 * DELETE THIS FILE AFTER RUNNING FOR SECURITY!
 */

// Error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Connect to existing database
$old_host = 'localhost';
$old_user = 'root';
$old_pass = '';
$old_db = 'hostelweb';

$old_conn = new mysqli($old_host, $old_user, $old_pass, $old_db);
if ($old_conn->connect_error) {
    die("Connection to old database failed: " . $old_conn->connect_error);
}

// Connect to MySQL server (without database) to create new one
$server_conn = new mysqli($old_host, $old_user, $old_pass);
if ($server_conn->connect_error) {
    die("Connection to MySQL server failed: " . $server_conn->connect_error);
}

// Create new database if it doesn't exist
$new_db = 'bsu_hrs_schema';
$server_conn->query("DROP DATABASE IF EXISTS $new_db");
$server_conn->query("CREATE DATABASE $new_db CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
echo "✅ Created new database: $new_db<br>";

// Connect to new database
$new_conn = new mysqli($old_host, $old_user, $old_pass, $new_db);
if ($new_conn->connect_error) {
    die("Connection to new database failed: " . $new_conn->connect_error);
}

// Start migration
echo "<h1>Migrating Data from hostelweb to bsu_hrs_schema</h1>";
echo "<pre>";

// Get all tables from old database
$tables_result = $old_conn->query("SHOW TABLES");
$tables = [];
while ($row = $tables_result->fetch_array()) {
    $tables[] = $row[0];
}

echo "Found " . count($tables) . " tables to migrate.<br><br>";

// Migration order (respect foreign keys)
$migration_order = [
    'admin_cred',
    'settings',
    'contact_details',
    'types_room',
    'features',
    'facilities',
    'request',
    'banguet',
    'hostel',
    'rooms',
    'room_image',
    'room_images',
    'room_facilities',
    'room_features',
    'user_reg',
    'carousel_slides', // Create this if it doesn't exist
    'reservations',
    'room_reservation',
    'room_guests',
    'room_reviews',
    'function_room_reviews',
    'notifications',
    'admin_notifications',
    'chat_messages',
    'messages',
    'liabilities',
    'hidden_users',
    'user_message',
    'team_details',
    'testimonials'
];

// First, create the carousel_slides table if it doesn't exist in old DB
$carousel_exists = $old_conn->query("SHOW TABLES LIKE 'carousel_slides'")->num_rows > 0;
if (!$carousel_exists) {
    $new_conn->query("
        CREATE TABLE IF NOT EXISTS carousel_slides (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            subtitle TEXT,
            button_text VARCHAR(100) DEFAULT 'View Rooms',
            button_url VARCHAR(255) DEFAULT 'rooms.php',
            image_path VARCHAR(255) NOT NULL,
            sort_order INT DEFAULT 0,
            is_active BOOLEAN DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
    ");
    echo "✅ Created carousel_slides table<br>";
}

// Migrate each table
foreach ($migration_order as $table) {
    echo "<strong>Migrating table: $table...</strong> ";
    
    // Check if table exists in old database
    $table_exists = $old_conn->query("SHOW TABLES LIKE '$table'")->num_rows > 0;
    if (!$table_exists) {
        echo "⏭️ Table doesn't exist in old DB, skipping.<br>";
        continue;
    }
    
    // Get table structure from old database
    $create_table_result = $old_conn->query("SHOW CREATE TABLE $table");
    if (!$create_table_result) {
        echo "❌ Error getting table structure: " . $old_conn->error . "<br>";
        continue;
    }
    
    $create_row = $create_table_result->fetch_assoc();
    $create_sql = $create_row['Create Table'];
    
    // Modify the CREATE statement to use new database name
    $create_sql = preg_replace('/CREATE TABLE `' . $table . '`/', 'CREATE TABLE IF NOT EXISTS `' . $table . '`', $create_sql);
    
    // Create table in new database
    if (!$new_conn->query($create_sql)) {
        echo "❌ Error creating table: " . $new_conn->error . "<br>";
        continue;
    }
    
    // Get all data from old table
    $data_result = $old_conn->query("SELECT * FROM $table");
    if (!$data_result) {
        echo "❌ Error selecting data: " . $old_conn->error . "<br>";
        continue;
    }
    
    $row_count = $data_result->num_rows;
    if ($row_count == 0) {
        echo "✅ Table created, no data to migrate.<br>";
        continue;
    }
    
    // Get column names
    $columns = [];
    while ($field = $data_result->fetch_field()) {
        $columns[] = $field->name;
    }
    $column_list = implode("`, `", $columns);
    
    // Prepare INSERT statement
    $insert_sql = "INSERT INTO `$table` (`$column_list`) VALUES ";
    $values = [];
    
    while ($row = $data_result->fetch_assoc()) {
        $escaped_values = [];
        foreach ($row as $value) {
            if ($value === null) {
                $escaped_values[] = "NULL";
            } else {
                $escaped_values[] = "'" . $new_conn->real_escape_string($value) . "'";
            }
        }
        $values[] = "(" . implode(", ", $escaped_values) . ")";
    }
    
    $insert_sql .= implode(", ", $values);
    
    // Execute insert
    if ($new_conn->query($insert_sql)) {
        echo "✅ Migrated $row_count rows<br>";
    } else {
        echo "❌ Error inserting data: " . $new_conn->error . "<br>";
        // Try inserting row by row for debugging
        echo "&nbsp;&nbsp;Trying row-by-row insertion...<br>";
        $data_result->data_seek(0);
        $success_count = 0;
        while ($row = $data_result->fetch_assoc()) {
            $single_insert = "INSERT INTO `$table` (`$column_list`) VALUES (";
            $single_values = [];
            foreach ($row as $value) {
                if ($value === null) {
                    $single_values[] = "NULL";
                } else {
                    $single_values[] = "'" . $new_conn->real_escape_string($value) . "'";
                }
            }
            $single_insert .= implode(", ", $single_values) . ")";
            
            if ($new_conn->query($single_insert)) {
                $success_count++;
            } else {
                echo "&nbsp;&nbsp;❌ Failed on row: " . $new_conn->error . "<br>";
            }
        }
        echo "&nbsp;&nbsp;✅ Migrated $success_count of $row_count rows<br>";
    }
}

// Special handling for room_images table (ensure all image paths are correct)
echo "<br><strong>Verifying image paths...</strong><br>";

// Update room_images paths if needed (make sure they point to correct location)
$new_conn->query("
    UPDATE room_images 
    SET image_path = CONCAT('rooms/', SUBSTRING_INDEX(image_path, '/', -1))
    WHERE image_path NOT LIKE 'rooms/%' AND image_path != ''
");

$new_conn->query("
    UPDATE room_image 
    SET image = CONCAT('rooms/', SUBSTRING_INDEX(image, '/', -1))
    WHERE image NOT LIKE 'rooms/%' AND image != ''
");

echo "✅ Image paths verified<br>";

// Insert default carousel slides if none exist
$carousel_count = $new_conn->query("SELECT COUNT(*) as count FROM carousel_slides")->fetch_assoc()['count'];
if ($carousel_count == 0) {
    $new_conn->query("
        INSERT INTO carousel_slides (title, subtitle, button_text, button_url, image_path, sort_order) VALUES
        ('Welcome to BSU Hostel', 'The perfect venue for your events. Spacious function rooms and comfortable guest rooms for meetings, celebrations, and group stays. Reserve your space today.', 'View Rooms', 'rooms.php', 'hostel/hostel2.png', 1),
        ('Book Your Function or Guest Room', 'Check availability and reserve your stay in minutes.', 'Check Availability', 'rooms.php', 'rooms/IMG_19689.jpg', 2),
        ('Your Comfort, Our Priority', 'Modern amenities and a welcoming environment for every guest.', 'See Amenities', 'facilities.php', 'rooms/IMG_85146.png', 3),
        ('Stay With Us', 'Ideal for students, groups, and travelers visiting BSU.', 'Get in Touch', 'contact.php', 'hostel/hostel2.png', 4)
    ");
    echo "✅ Added default carousel slides<br>";
}

echo "</pre>";
echo "<h2>✅ Migration Complete!</h2>";
echo "<p>All data from <strong>hostelweb</strong> has been migrated to <strong>bsu_hrs_schema</strong></p>";
echo "<h3>Next Steps:</h3>";
echo "<ol>";
echo "<li>Update your <code>inc/db_config.php</code> to use the new database:</li>";
echo "<pre style='background:#f0f0f0; padding:10px;'>";
echo "&lt;?php\n";
echo "\$hname = 'localhost';\n";
echo "\$uname = 'root';\n";
echo "\$pass = '';\n";
echo "\$db = 'bsu_hrs_schema';  // Changed from 'hostelweb'\n\n";
echo "\$conn = new mysqli(\$hname, \$uname, \$pass, \$db);\n";
echo "?>";
echo "</pre>";
echo "<li>Test your website: <a href='index.php'>Go to Homepage</a></li>";
echo "<li><strong style='color:red;'>DELETE THIS FILE (migrate_to_new_db.php) FOR SECURITY!</strong></li>";
echo "</ol>";

// Close connections
$old_conn->close();
$new_conn->close();
$server_conn->close();
?>