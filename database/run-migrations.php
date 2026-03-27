<?php
/**
 * Database Migration Runner
 * Run this script to apply all pending migrations
 * 
 * Usage:
 * 1. Add new migration files to /database/migrations/ folder
 * 2. Name them sequentially: 003_feature_name.sql, 004_another_feature.sql
 * 3. Visit this file in browser or run from command line
 * 4. Applied migrations are tracked in 'migrations' table
 */

// Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'nexsoft_hub');
define('MIGRATIONS_DIR', __DIR__ . '/migrations');

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

// CLI or Browser output
$is_cli = php_sapi_name() === 'cli';

function log_msg($message, $type = 'info') {
    global $is_cli;
    
    if ($is_cli) {
        $colors = [
            'success' => "\033[32m",
            'error' => "\033[31m",
            'warning' => "\033[33m",
            'info' => "\033[36m",
            'reset' => "\033[0m"
        ];
        echo ($colors[$type] ?? $colors['info']) . $message . $colors['reset'] . "\n";
    } else {
        $class = "migration-{$type}";
        echo "<div class='{$class}'>" . htmlspecialchars($message) . "</div>\n";
    }
}

// Ensure migrations table exists
$create_table = "CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `migration_name` varchar(255) NOT NULL UNIQUE,
  `batch` int(11) NOT NULL,
  `executed_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `batch` (`batch`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

if (!$conn->query($create_table)) {
    log_msg("Error creating migrations table: " . $conn->error, 'error');
    die();
}

// Get list of applied migrations
$applied = [];
$result = $conn->query("SELECT migration_name FROM migrations");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $applied[] = $row['migration_name'];
    }
}

// Get current batch number
$batch_result = $conn->query("SELECT MAX(batch) as max_batch FROM migrations");
$batch_row = $batch_result->fetch_assoc();
$current_batch = ($batch_row['max_batch'] ?? 0) + 1;

// Find migration files
$migration_files = [];
if (is_dir(MIGRATIONS_DIR)) {
    $files = glob(MIGRATIONS_DIR . '/*.sql');
    sort($files);
    foreach ($files as $file) {
        $name = basename($file);
        if (preg_match('/^\d+_[a-z0-9_\-]+\.sql$/i', $name)) {
            $migration_files[] = $file;
        }
    }
}

if (empty($migration_files)) {
    log_msg("No migration files found in " . MIGRATIONS_DIR, 'warning');
    if (!$is_cli) echo "<style>.migration-info { padding: 10px; margin: 5px 0; background: #e3f2fd; border-left: 4px solid #2196F3; }</style>";
    exit;
}

if (!$is_cli) {
    echo "<style>
    .migration-success { padding: 10px; margin: 5px 0; background: #c8e6c9; border-left: 4px solid #4CAF50; }
    .migration-error { padding: 10px; margin: 5px 0; background: #ffcdd2; border-left: 4px solid #f44336; }
    .migration-warning { padding: 10px; margin: 5px 0; background: #fff3e0; border-left: 4px solid #FF9800; }
    .migration-info { padding: 10px; margin: 5px 0; background: #e3f2fd; border-left: 4px solid #2196F3; }
    body { font-family: Arial, sans-serif; margin: 20px; }
    h1 { color: #333; }
    .summary { margin-top: 20px; padding: 15px; background: #f5f5f5; border-radius: 4px; }
    </style>";
    echo "<h1>🔧 Database Migration Runner</h1>";
}

log_msg("═══════════════════════════════════════════", 'info');
log_msg("Running Migrations", 'info');
log_msg("═══════════════════════════════════════════", 'info');

$pending_count = 0;
$executed_count = 0;

foreach ($migration_files as $file) {
    $filename = basename($file);
    $migration_name = str_replace('.sql', '', $filename);
    
    if (in_array($migration_name, $applied)) {
        log_msg("⏭️  SKIP: " . $migration_name . " (already applied)", 'info');
        continue;
    }
    
    // Read migration file
    $sql = file_get_contents($file);
    
    if (empty($sql)) {
        log_msg("⚠️  SKIP: " . $migration_name . " (empty file)", 'warning');
        continue;
    }
    
    log_msg("⏳ Running: " . $migration_name, 'info');
    
    // Execute migration
    if ($conn->multi_query($sql)) {
        // Clear all results
        while ($conn->more_results()) {
            $conn->next_result();
        }

        // Some migration files may self-record in `migrations` table.
        // If already recorded, skip runner insert to avoid duplicate key errors.
        $already_recorded = false;
        $check_stmt = $conn->prepare("SELECT id FROM migrations WHERE migration_name = ? LIMIT 1");
        $check_stmt->bind_param("s", $migration_name);
        if ($check_stmt->execute()) {
            $check_result = $check_stmt->get_result();
            $already_recorded = ($check_result && $check_result->num_rows > 0);
        }
        $check_stmt->close();

        if ($already_recorded) {
            log_msg("✅ SUCCESS: " . $migration_name . " (self-recorded)", 'success');
            $executed_count++;
            $pending_count++;
        } else {
            // Record migration
            $stmt = $conn->prepare("INSERT INTO migrations (migration_name, batch) VALUES (?, ?)");
            $stmt->bind_param("si", $migration_name, $current_batch);

            if ($stmt->execute()) {
                log_msg("✅ SUCCESS: " . $migration_name, 'success');
                $executed_count++;
                $pending_count++;
            } else {
                log_msg("❌ ERROR: Failed to record " . $migration_name . " in migrations table", 'error');
            }
            $stmt->close();
        }
    } else {
        log_msg("❌ ERROR: " . $migration_name . " - " . $conn->error, 'error');
    }
}

log_msg("═══════════════════════════════════════════", 'info');

if ($executed_count === 0 && $pending_count === 0) {
    log_msg("✓ All migrations already applied! No new changes.", 'success');
} else if ($executed_count > 0) {
    log_msg("✓ Applied $executed_count migration(s) in batch $current_batch", 'success');
} else {
    log_msg("⚠️  No pending migrations to run", 'warning');
}

$conn->close();

if (!$is_cli) {
    echo "<div class='summary'>";
    echo "<p><strong>Next Steps:</strong></p>";
    echo "<ul>";
    echo "<li>✓ To add new migration: Create a file named <code>NNN_name.sql</code> in <code>/database/migrations/</code></li>";
    echo "<li>✓ To apply migrations: Refresh this page or run: <code>php run-migrations.php</code></li>";
    echo "<li>✓ All applied migrations are tracked automatically</li>";
    echo "<li>✓ Each migration runs only once - safe to run multiple times</li>";
    echo "</ul>";
    echo "</div>";
}
?>
