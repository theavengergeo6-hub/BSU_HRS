<?php
/**
 * Run migration_site_info_carousel.sql once.
 * Open in browser: http://localhost/BSU_HRS/run_migration.php
 * Delete this file after use.
 */
require_once __DIR__ . '/inc/db_config.php';

$sql = file_get_contents(__DIR__ . '/database/migration_site_info_carousel.sql');
$lines = array_filter(array_map('trim', explode("\n", $sql)), function ($line) {
    return $line !== '' && $line[0] !== '-';
});
$stmt = '';
$done = [];
$errors = [];
foreach ($lines as $line) {
    if (stripos($line, 'USE ') === 0) continue;
    $stmt .= $line . "\n";
    if (substr(rtrim($line), -1) === ';') {
        $q = trim($stmt);
        $stmt = '';
        if ($q === '') continue;
        if (!@$conn->query($q)) {
            $err = $conn->error;
            if (strpos($err, 'Duplicate column') !== false) $done[] = $q;
            else $errors[] = $err . ' for: ' . substr($q, 0, 60) . '...';
        } else {
            $done[] = 'OK';
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Migration</title></head>
<body>
<h1>Migration run</h1>
<?php if (empty($errors)): ?>
<p>Done. Carousel, site info, and room type image columns are ready.</p>
<?php else: ?>
<p>Some errors (e.g. duplicate column means already run):</p>
<pre><?= htmlspecialchars(implode("\n", $errors)) ?></pre>
<?php endif; ?>
<p><a href="index.php">Home</a> | <a href="admin/carousel.php">Admin → Carousel</a></p>
<p><strong>Delete run_migration.php for security.</strong></p>
</body>
</html>
