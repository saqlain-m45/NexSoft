<?php
require_once __DIR__ . '/auth.php';
adminCheck();
adminRequirePermission('dashboard');

$db = getDB();

$projectCount      = (int)$db->query("SELECT COUNT(*) FROM projects")->fetchColumn();
$blogCount         = (int)$db->query("SELECT COUNT(*) FROM blog_posts")->fetchColumn();
$registrationCount  = (int)$db->query("SELECT COUNT(*) FROM registrations")->fetchColumn();
$messageCount       = (int)$db->query("SELECT COUNT(*) FROM contact_messages")->fetchColumn();
$serviceCount       = (int)$db->query("SELECT COUNT(*) FROM services")->fetchColumn();
$testimonialCount   = (int)$db->query("SELECT COUNT(*) FROM testimonials")->fetchColumn();

$recentMessages = adminHasPermission('messages')
    ? $db->query("SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 5")->fetchAll()
    : [];
$recentRegs     = adminHasPermission('registrations')
    ? $db->query("SELECT * FROM registrations ORDER BY created_at DESC LIMIT 5")->fetchAll()
    : [];

$alerts = [];
if (getSetting('maintenance_mode', '0') === '1') {
    $alerts[] = ['type' => 'warning', 'text' => 'Maintenance mode is enabled. Frontend visitors currently see the maintenance page.'];
}
if (adminHasPermission('registrations') && $registrationCount > 0) {
    $pendingRegistrations = (int)$db->query("SELECT COUNT(*) FROM registrations WHERE status='pending'")->fetchColumn();
    if ($pendingRegistrations > 0) {
        $alerts[] = ['type' => 'info', 'text' => $pendingRegistrations . ' registration(s) are pending review.'];
    }
}
if (adminHasPermission('settings')) {
    $smtpHost = trim((string)getSetting('smtp_host', ''));
    $smtpUser = trim((string)getSetting('smtp_user', ''));
    $smtpPass = trim((string)getSetting('smtp_pass', ''));
    if ($smtpHost === '' || $smtpUser === '' || $smtpPass === '') {
        $alerts[] = ['type' => 'danger', 'text' => 'SMTP is incomplete. Email notifications may fail until SMTP host/user/password are configured.'];
    }
}

$pageTitle  = 'Dashboard — NexSoft Hub Admin';
$activePage = 'dashboard';
require_once __DIR__ . '/layout-header.php';
?>

<?php if (!empty($alerts)): ?>
<div class="admin-card mb-4" style="border-left:4px solid var(--secondary);">
    <div class="admin-card-header">
        <span class="admin-card-title"><i class="bi bi-bell-fill me-2" style="color:var(--secondary);"></i>System Alerts</span>
    </div>
    <div class="admin-card-body">
        <div class="d-flex flex-column gap-2">
            <?php foreach ($alerts as $alert): ?>
                <?php
                $bg = 'rgba(14,165,164,.1)';
                $color = '#0f766e';
                if ($alert['type'] === 'warning') { $bg = 'rgba(245,158,11,.14)'; $color = '#b45309'; }
                if ($alert['type'] === 'danger') { $bg = 'rgba(239,68,68,.12)'; $color = '#b91c1c'; }
                ?>
            <div style="background:<?php echo $bg; ?>;color:<?php echo $color; ?>;padding:10px 12px;border-radius:10px;font-size:.88rem;font-weight:600;">
                <?php echo htmlspecialchars($alert['text']); ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Stat Cards -->
<div class="row g-4 mb-4">
    <?php
    $stats = [
        ['icon'=>'bi-folder-fill','count'=>$projectCount,'label'=>'Total Projects','class'=>'blue','link'=>'projects.php'],
        ['icon'=>'bi-journal-richtext','count'=>$blogCount,'label'=>'Blog Posts','class'=>'teal','link'=>'blogs.php'],
        ['icon'=>'bi-people-fill','count'=>$registrationCount,'label'=>'Registrations','class'=>'green','link'=>'registrations.php'],
        ['icon'=>'bi-chat-left-text-fill','count'=>$messageCount,'label'=>'Messages','class'=>'orange','link'=>'messages.php'],
        ['icon'=>'bi-cpu','count'=>$serviceCount,'label'=>'Services','class'=>'cyan','link'=>'services.php'],
        ['icon'=>'bi-chat-quote','count'=>$testimonialCount,'label'=>'Testimonials','class'=>'yellow','link'=>'testimonials.php'],
    ];
    foreach($stats as $s):
        $perm = basename($s['link'], '.php');
        if (!adminHasPermission($perm)) {
            continue;
        }
    ?>
    <div class="col-6 col-lg-3">
        <a href="<?php echo adminUrl($s['link']); ?>" style="text-decoration:none;">
            <div class="stat-card">
                <div class="stat-icon <?php echo $s['class']; ?>"><i class="bi <?php echo $s['icon']; ?>"></i></div>
                <div class="stat-number"><?php echo $s['count']; ?></div>
                <div class="stat-label"><?php echo $s['label']; ?></div>
            </div>
        </a>
    </div>
    <?php endforeach; ?>
</div>

<!-- Quick Actions -->
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="admin-card">
            <div class="admin-card-header">
                <span class="admin-card-title"><i class="bi bi-lightning-fill me-2 text-warning"></i>Quick Actions</span>
            </div>
            <div class="admin-card-body">
                <div style="display:flex;gap:1rem;flex-wrap:wrap;">
                    <?php if (adminHasPermission('projects')): ?>
                    <a href="<?php echo adminUrl('projects.php?action=add'); ?>" class="btn-admin-primary"><i class="bi bi-plus-circle"></i> Add Project</a>
                    <?php endif; ?>
                    <?php if (adminHasPermission('blogs')): ?>
                    <a href="<?php echo adminUrl('blogs.php?action=add'); ?>" class="btn-admin-primary"><i class="bi bi-plus-circle"></i> Add Blog Post</a>
                    <?php endif; ?>
                    <?php if (adminHasPermission('registrations')): ?>
                    <a href="<?php echo adminUrl('registrations.php'); ?>" class="btn-admin-secondary"><i class="bi bi-people"></i> View Registrations</a>
                    <?php endif; ?>
                    <?php if (adminHasPermission('messages')): ?>
                    <a href="<?php echo adminUrl('messages.php'); ?>" class="btn-admin-secondary"><i class="bi bi-envelope"></i> View Messages</a>
                    <?php endif; ?>
                    <a href="/NexSoft/" target="_blank" class="btn-admin-secondary"><i class="bi bi-box-arrow-up-right"></i> View Website</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Data Tables -->
<div class="row g-4">
    <?php if (adminHasPermission('messages')): ?>
    <!-- Recent Messages -->
    <div class="col-lg-6">
        <div class="admin-card">
            <div class="admin-card-header">
                <span class="admin-card-title"><i class="bi bi-chat-left-text me-2" style="color:var(--secondary);"></i>Recent Messages</span>
                <a href="<?php echo adminUrl('messages.php'); ?>" class="btn-action btn-view">View All</a>
            </div>
            <div class="table-wrap">
                <table class="admin-table">
                    <thead><tr><th>Name</th><th>Email</th><th>Date</th></tr></thead>
                    <tbody>
                        <?php if (empty($recentMessages)): ?>
                        <tr><td colspan="3" style="text-align:center;color:var(--text-muted);padding:2rem;">No messages yet</td></tr>
                        <?php else: ?>
                        <?php foreach($recentMessages as $msg): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($msg['name']); ?></strong></td>
                            <td style="color:var(--text-muted);"><?php echo htmlspecialchars($msg['email']); ?></td>
                            <td><span class="badge-teal"><?php echo date('M d', strtotime($msg['created_at'])); ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if (adminHasPermission('registrations')): ?>
    <!-- Recent Registrations -->
    <div class="col-lg-6">
        <div class="admin-card">
            <div class="admin-card-header">
                <span class="admin-card-title"><i class="bi bi-people me-2" style="color:var(--secondary);"></i>Recent Registrations</span>
                <a href="<?php echo adminUrl('registrations.php'); ?>" class="btn-action btn-view">View All</a>
            </div>
            <div class="table-wrap">
                <table class="admin-table">
                    <thead><tr><th>Name</th><th>Skills</th><th>Date</th></tr></thead>
                    <tbody>
                        <?php if (empty($recentRegs)): ?>
                        <tr><td colspan="3" style="text-align:center;color:var(--text-muted);padding:2rem;">No registrations yet</td></tr>
                        <?php else: ?>
                        <?php foreach($recentRegs as $reg): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($reg['name']); ?></strong></td>
                            <td style="color:var(--text-muted);"><?php echo htmlspecialchars(mb_strimwidth($reg['skills'] ?? '', 0, 30, '...')); ?></td>
                            <td><span class="badge-green"><?php echo date('M d', strtotime($reg['created_at'])); ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/layout-footer.php'; ?>
