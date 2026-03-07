<?php
require_once __DIR__ . '/auth.php';
adminCheck();
adminRequirePermission('backups');

$db = getDB();
$msg = '';
$error = '';

$allowedTables = [
    'projects',
    'blog_posts',
    'team_members',
    'services',
    'testimonials',
    'users',
    'site_settings',
    'registrations',
    'contact_messages',
];

function buildTableBackupSql(PDO $db, string $table, bool $includeSchema = true): string {
    $sql = "\n-- --------------------------------------------------------\n";
    $sql .= "-- Table: `{$table}`\n";
    $sql .= "-- --------------------------------------------------------\n";

    if ($includeSchema) {
        $create = $db->query('SHOW CREATE TABLE `' . $table . '`')->fetch(PDO::FETCH_ASSOC);
        if (!empty($create['Create Table'])) {
            $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";
            $sql .= $create['Create Table'] . ";\n\n";
        }
    } else {
        $sql .= "TRUNCATE TABLE `{$table}`;\n";
    }

    $rows = $db->query('SELECT * FROM `' . $table . '`')->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $row) {
        $columns = array_map(static fn ($col) => '`' . str_replace('`', '``', (string)$col) . '`', array_keys($row));
        $values = array_map(static function ($val) use ($db) {
            if ($val === null) {
                return 'NULL';
            }
            return $db->quote((string)$val);
        }, array_values($row));

        $sql .= "INSERT INTO `{$table}` (" . implode(', ', $columns) . ') VALUES (' . implode(', ', $values) . ");\n";
    }

    $sql .= "\n";
    return $sql;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'export_table') {
        $table = trim((string)($_POST['table'] ?? ''));

        if (!in_array($table, $allowedTables, true)) {
            $error = 'Invalid table selected for export.';
        } else {
            try {
                $filename = $table . '_backup_' . date('Ymd_His') . '.sql';
                $sql = "-- NexSoft backup for table `{$table}`\n";
                $sql .= '-- Generated at: ' . date('Y-m-d H:i:s') . "\n\n";
                $sql .= buildTableBackupSql($db, $table, false);

                adminLogAction('backups.export_table', 'Exported table: ' . $table);
                header('Content-Type: application/sql');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                header('Content-Length: ' . strlen($sql));
                echo $sql;
                exit;
            } catch (Throwable $e) {
                $error = 'Export failed: ' . $e->getMessage();
            }
        }
    } elseif ($action === 'export_full') {
        try {
            $filename = 'nexsoft_full_backup_' . date('Ymd_His') . '.sql';
            $sql = "-- NexSoft Full Backup\n";
            $sql .= '-- Generated at: ' . date('Y-m-d H:i:s') . "\n";
            $sql .= '-- Includes table schema + data for configured backup tables.\n\n';
            $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

            foreach ($allowedTables as $table) {
                $sql .= buildTableBackupSql($db, $table, true);
            }

            $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

            adminLogAction('backups.export_full', 'Exported full backup');
            header('Content-Type: application/sql');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Content-Length: ' . strlen($sql));
            echo $sql;
            exit;
        } catch (Throwable $e) {
            $error = 'Full backup failed: ' . $e->getMessage();
        }
    }
}

$recentLogs = [];
try {
    ensureAuditLogSchema($db);
    $recentLogs = $db->query('SELECT admin_username, action, details, created_at FROM admin_activity_logs ORDER BY created_at DESC LIMIT 25')->fetchAll();
} catch (Throwable $e) {
    // keep page usable if logs are unavailable
}

$pageTitle = 'Backups & Export - NexSoft Hub Admin';
$activePage = 'backups';
require_once __DIR__ . '/layout-header.php';
?>

<?php if ($msg !== ''): ?>
<div class="admin-alert-success mb-3"><i class="bi bi-check-circle me-2"></i><?php echo htmlspecialchars($msg); ?></div>
<?php endif; ?>
<?php if ($error !== ''): ?>
<div class="admin-alert-error mb-3"><i class="bi bi-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="admin-card">
            <div class="admin-card-header">
                <span class="admin-card-title"><i class="bi bi-database-down me-2" style="color:var(--secondary);"></i>Export Table Backup</span>
            </div>
            <div class="admin-card-body">
                <form method="POST" class="admin-form">
                    <?php echo adminCsrfField(); ?>
                    <input type="hidden" name="action" value="export_table">
                    <div class="mb-3">
                        <label>Select Table</label>
                        <select name="table" class="form-control" required>
                            <option value="">Choose table...</option>
                            <?php foreach ($allowedTables as $table): ?>
                            <option value="<?php echo htmlspecialchars($table); ?>"><?php echo htmlspecialchars($table); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <p style="font-size:.85rem;color:var(--text-muted);">Exports SQL insert script for selected table. Keep backups safely.</p>
                    <button type="submit" class="btn-admin-primary"><i class="bi bi-download"></i> Download Backup</button>
                </form>

                <hr>

                <form method="POST" class="admin-form">
                    <?php echo adminCsrfField(); ?>
                    <input type="hidden" name="action" value="export_full">
                    <p style="font-size:.85rem;color:var(--text-muted);margin-bottom:.75rem;">
                        Need full backup? Download a single SQL file with schema + data for all key tables.
                    </p>
                    <button type="submit" class="btn-admin-secondary"><i class="bi bi-cloud-arrow-down"></i> Download Full Backup</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="admin-card">
            <div class="admin-card-header">
                <span class="admin-card-title"><i class="bi bi-clock-history me-2" style="color:var(--secondary);"></i>Recent Admin Activity</span>
            </div>
            <div class="table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Admin</th>
                            <th>Action</th>
                            <th>Details</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentLogs)): ?>
                        <tr><td colspan="4" style="text-align:center;padding:2rem;color:var(--text-muted);">No activity logs yet.</td></tr>
                        <?php else: ?>
                        <?php foreach ($recentLogs as $log): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars((string)$log['admin_username']); ?></strong></td>
                            <td><span class="badge-teal"><?php echo htmlspecialchars((string)$log['action']); ?></span></td>
                            <td style="max-width:260px;color:var(--text-muted);"><?php echo htmlspecialchars(mb_strimwidth((string)($log['details'] ?? ''), 0, 80, '...')); ?></td>
                            <td><span class="badge-blue"><?php echo htmlspecialchars(date('M d, H:i', strtotime((string)$log['created_at']))); ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/layout-footer.php'; ?>
