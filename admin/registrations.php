<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../config/mailer.php';
adminCheck();
adminRequirePermission('registrations');

$db     = getDB();
$action = $_GET['action'] ?? 'list';
$msg    = '';
$error  = '';

// Auto-add status column if it doesn't exist yet
try {
    $db->exec("ALTER TABLE registrations ADD COLUMN `status` ENUM('pending','verified','rejected') NOT NULL DEFAULT 'pending' AFTER `message`");
} catch (\PDOException $e) {
    // Column already exists — ignore
}

// ─── Handle POST actions ───────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $actionPost = $_POST['action'] ?? '';
    $id         = (int)($_POST['id'] ?? 0);

    // ── Verify applicant ──
    if ($actionPost === 'verify' && $id) {
        $db->prepare("UPDATE registrations SET status='verified' WHERE id=?")->execute([$id]);
        adminLogAction('registrations.verify', 'Verified registration id=' . $id);
        $reg = $db->prepare("SELECT name, email FROM registrations WHERE id=?");
        $reg->execute([$id]);
        $u = $reg->fetch();
        if ($u) {
            $sent = sendMail($u['email'], $u['name'], '🎉 Your Application is Approved — NexSoft Hub', emailTemplateApproved($u['name']));
            $msg = 'Application verified! ' . ($sent ? 'Approval email sent to ' . htmlspecialchars($u['email']) . '.' : 'Note: Could not send email — check SMTP settings in config/mailer.php.');
        }
    }

    // ── Reject applicant ──
    elseif ($actionPost === 'reject' && $id) {
        $db->prepare("UPDATE registrations SET status='rejected' WHERE id=?")->execute([$id]);
        adminLogAction('registrations.reject', 'Rejected registration id=' . $id);
        $reg = $db->prepare("SELECT name, email FROM registrations WHERE id=?");
        $reg->execute([$id]);
        $u = $reg->fetch();
        if ($u) {
            $sent = sendMail($u['email'], $u['name'], 'Your Application Update — NexSoft Hub', emailTemplateRejected($u['name']));
            $msg = 'Application rejected. ' . ($sent ? 'Notification email sent to ' . htmlspecialchars($u['email']) . '.' : 'Note: Could not send email — check SMTP settings in config/mailer.php.');
        }
    }

    // ── Delete ──
    elseif ($actionPost === 'delete' && $id) {
        $db->prepare("DELETE FROM registrations WHERE id=?")->execute([$id]);
        adminLogAction('registrations.delete', 'Deleted registration id=' . $id);
        $msg = 'Registration deleted.';
        $action = 'list';
    }

    // ── Edit / Save ──
    elseif ($actionPost === 'edit' && $id) {
        $name     = trim($_POST['name'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $phone    = trim($_POST['phone'] ?? '');
        $skills   = trim($_POST['skills'] ?? '');
        $portfolio = trim($_POST['portfolio'] ?? '');
        $message  = trim($_POST['message'] ?? '');

        if (empty($name) || empty($email)) {
            $error = 'Name and email are required.';
        } else {
            $db->prepare("UPDATE registrations SET name=?, email=?, phone=?, skills=?, portfolio_link=?, message=? WHERE id=?")
               ->execute([$name, $email, $phone, $skills, $portfolio, $message, $id]);
                adminLogAction('registrations.update', 'Updated registration id=' . $id . ' name=' . $name);
            $msg = 'Registration updated.';
        }
        $action = 'list';
    }
}

// ─── Fetch registration for view/edit ─────────────────────────────
$viewReg = null;
if ($action === 'view' || $action === 'edit') {
    $id = (int)($_GET['id'] ?? 0);
    $s  = $db->prepare("SELECT * FROM registrations WHERE id=?");
    $s->execute([$id]);
    $viewReg = $s->fetch();
    if (!$viewReg) $action = 'list';
}

// ─── Fetch all registrations ───────────────────────────────────────
$filter = $_GET['filter'] ?? 'all';
if ($filter === 'pending')  $regs = $db->query("SELECT * FROM registrations WHERE status='pending'  ORDER BY created_at DESC")->fetchAll();
elseif ($filter === 'verified') $regs = $db->query("SELECT * FROM registrations WHERE status='verified' ORDER BY created_at DESC")->fetchAll();
elseif ($filter === 'rejected') $regs = $db->query("SELECT * FROM registrations WHERE status='rejected' ORDER BY created_at DESC")->fetchAll();
else $regs = $db->query("SELECT * FROM registrations ORDER BY created_at DESC")->fetchAll();

$counts = [
    'all'      => (int)$db->query("SELECT COUNT(*) FROM registrations")->fetchColumn(),
    'pending'  => (int)$db->query("SELECT COUNT(*) FROM registrations WHERE status='pending'")->fetchColumn(),
    'verified' => (int)$db->query("SELECT COUNT(*) FROM registrations WHERE status='verified'")->fetchColumn(),
    'rejected' => (int)$db->query("SELECT COUNT(*) FROM registrations WHERE status='rejected'")->fetchColumn(),
];

$pageTitle  = 'Registrations — NexSoft Hub Admin';
$activePage = 'registrations';
require_once __DIR__ . '/layout-header.php';
?>

<?php if (!empty($msg)): ?>
<div class="admin-alert-success mb-3"><i class="bi bi-check-circle-fill me-2"></i><?php echo $msg; ?></div>
<?php endif; ?>
<?php if (!empty($error)): ?>
<div class="admin-alert-error mb-3"><i class="bi bi-exclamation-circle-fill me-2"></i><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>


<?php if ($action === 'view' && $viewReg): ?>
<!-- ═══════════════════ DETAIL VIEW ══════════════════════════ -->
<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;align-items:start;">

    <!-- Left: Full details -->
    <div class="admin-card">
        <div class="admin-card-header">
            <span class="admin-card-title"><i class="bi bi-person-fill me-2" style="color:var(--secondary);"></i>Applicant Detail</span>
            <a href="<?php echo adminUrl('registrations.php'); ?>" class="btn-action btn-view"><i class="bi bi-arrow-left"></i> Back</a>
        </div>
        <div class="admin-card-body">
            <table style="width:100%;border-collapse:collapse;font-size:0.9rem;">
                <?php
                $fields = [
                    'Full Name'   => $viewReg['name'],
                    'Email'       => '<a href="mailto:' . htmlspecialchars($viewReg['email']) . '" style="color:var(--secondary);">' . htmlspecialchars($viewReg['email']) . '</a>',
                    'Phone'       => htmlspecialchars($viewReg['phone'] ?? '—'),
                    'Skills'      => htmlspecialchars($viewReg['skills'] ?? '—'),
                    'Portfolio'   => !empty($viewReg['portfolio_link']) ? '<a href="' . htmlspecialchars($viewReg['portfolio_link']) . '" target="_blank" style="color:var(--secondary);">' . htmlspecialchars($viewReg['portfolio_link']) . '</a>' : '—',
                    'Applied On'  => date('F d, Y, g:i A', strtotime($viewReg['created_at'])),
                    'Status'      => '<span class="badge-' . ($viewReg['status'] === 'verified' ? 'green' : ($viewReg['status'] === 'rejected' ? 'orange' : 'blue')) . '">' . ucfirst($viewReg['status'] ?? 'pending') . '</span>',
                ];
                foreach ($fields as $label => $val): ?>
                <tr style="border-bottom:1px solid var(--border);">
                    <td style="padding:10px 12px;font-weight:700;color:var(--text-muted);font-size:0.8rem;text-transform:uppercase;letter-spacing:0.5px;width:130px;"><?php echo $label; ?></td>
                    <td style="padding:10px 12px;color:var(--text);"><?php echo $val; ?></td>
                </tr>
                <?php endforeach; ?>
            </table>

            <?php if (!empty($viewReg['message'])): ?>
            <div style="margin-top:1.25rem;padding:1rem 1.25rem;background:var(--bg);border-left:3px solid var(--secondary);border-radius:0 var(--radius-sm) var(--radius-sm) 0;">
                <div style="font-size:0.75rem;font-weight:700;text-transform:uppercase;color:var(--text-muted);margin-bottom:6px;">Cover Message</div>
                <p style="font-size:0.9rem;color:var(--text);line-height:1.7;margin:0;"><?php echo nl2br(htmlspecialchars($viewReg['message'])); ?></p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Right: Actions -->
    <div style="display:flex;flex-direction:column;gap:1rem;">
        <!-- Verify / Reject -->
        <div class="admin-card">
            <div class="admin-card-header"><span class="admin-card-title"><i class="bi bi-shield-check me-2" style="color:var(--secondary);"></i>Decision</span></div>
            <div class="admin-card-body">
                <p style="font-size:0.85rem;color:var(--text-muted);margin-bottom:1.25rem;">Once you click Verify or Reject, an automated email will be sent to the applicant.</p>
                <div style="display:flex;gap:0.75rem;flex-wrap:wrap;">
                    <?php if (($viewReg['status'] ?? 'pending') !== 'verified'): ?>
                    <form method="POST">
                        <?php echo adminCsrfField(); ?>
                        <input type="hidden" name="action" value="verify">
                        <input type="hidden" name="id" value="<?php echo $viewReg['id']; ?>">
                        <button type="submit" class="btn-admin-primary" style="background:linear-gradient(135deg,#22c55e,#16a34a);">
                            <i class="bi bi-check-circle-fill"></i> Verify & Send Approval Email
                        </button>
                    </form>
                    <?php else: ?>
                    <span class="badge-green" style="padding:10px 18px;font-size:0.85rem;"><i class="bi bi-check-circle-fill me-1"></i>Already Verified</span>
                    <?php endif; ?>

                    <?php if (($viewReg['status'] ?? 'pending') !== 'rejected'): ?>
                    <form method="POST">
                        <?php echo adminCsrfField(); ?>
                        <input type="hidden" name="action" value="reject">
                        <input type="hidden" name="id" value="<?php echo $viewReg['id']; ?>">
                        <button type="submit" class="btn-admin-primary" style="background:linear-gradient(135deg,#ef4444,#dc2626);">
                            <i class="bi bi-x-circle-fill"></i> Reject & Notify Applicant
                        </button>
                    </form>
                    <?php else: ?>
                    <span class="badge-orange" style="padding:10px 18px;font-size:0.85rem;"><i class="bi bi-x-circle-fill me-1"></i>Already Rejected</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Edit form -->
        <div class="admin-card">
            <div class="admin-card-header"><span class="admin-card-title"><i class="bi bi-pencil me-2" style="color:var(--secondary);"></i>Edit Details</span></div>
            <div class="admin-card-body">
                <form method="POST" class="admin-form">
                    <?php echo adminCsrfField(); ?>
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" value="<?php echo $viewReg['id']; ?>">
                    <div class="row g-2">
                        <div class="col-12"><label>Name</label><input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($viewReg['name']); ?>" required></div>
                        <div class="col-12"><label>Email</label><input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($viewReg['email']); ?>" required></div>
                        <div class="col-12"><label>Phone</label><input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($viewReg['phone'] ?? ''); ?>"></div>
                        <div class="col-12"><label>Skills</label><input type="text" name="skills" class="form-control" value="<?php echo htmlspecialchars($viewReg['skills'] ?? ''); ?>"></div>
                        <div class="col-12"><label>Portfolio URL</label><input type="url" name="portfolio" class="form-control" value="<?php echo htmlspecialchars($viewReg['portfolio_link'] ?? ''); ?>"></div>
                        <div class="col-12"><label>Message</label><textarea name="message" class="form-control" rows="3"><?php echo htmlspecialchars($viewReg['message'] ?? ''); ?></textarea></div>
                        <div class="col-12 pt-1 d-flex gap-2">
                            <button type="submit" class="btn-admin-primary"><i class="bi bi-save"></i> Save Changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Danger zone -->
        <div class="admin-card" style="border:1px solid rgba(239,68,68,0.2);">
            <div class="admin-card-header" style="background:rgba(239,68,68,0.04);">
                <span class="admin-card-title" style="color:#dc2626;"><i class="bi bi-exclamation-triangle me-2"></i>Danger Zone</span>
            </div>
            <div class="admin-card-body">
                <p style="font-size:0.85rem;color:var(--text-muted);margin-bottom:1rem;">This will permanently delete this applicant's record.</p>
                <form method="POST">
                    <?php echo adminCsrfField(); ?>
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?php echo $viewReg['id']; ?>">
                    <button type="submit" class="btn-action btn-delete confirm-delete" style="padding:8px 20px;font-size:0.88rem;">
                        <i class="bi bi-trash-fill"></i> Delete This Record
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php else: ?>
<!-- ═══════════════════ LIST VIEW ══════════════════════════════ -->

<!-- Filter Tabs -->
<div style="display:flex;gap:0.75rem;flex-wrap:wrap;margin-bottom:1.5rem;">
    <?php
    $tabs = [
        'all'      => ['label'=>'All Applications', 'icon'=>'bi-list-ul'],
        'pending'  => ['label'=>'Pending',           'icon'=>'bi-clock'],
        'verified' => ['label'=>'Verified',          'icon'=>'bi-check-circle'],
        'rejected' => ['label'=>'Rejected',          'icon'=>'bi-x-circle'],
    ];
    foreach ($tabs as $key => $tab):
        $isActive = $filter === $key;
    ?>
    <a href="<?php echo adminUrl('registrations.php?filter=' . $key); ?>"
       style="display:inline-flex;align-items:center;gap:6px;padding:8px 18px;border-radius:50px;font-size:0.82rem;font-weight:700;border:1.5px solid <?php echo $isActive ? 'var(--secondary)' : 'var(--border)'; ?>;background:<?php echo $isActive ? 'rgba(14,165,164,0.08)' : 'var(--white)'; ?>;color:<?php echo $isActive ? 'var(--secondary)' : 'var(--text-muted)'; ?>;transition:var(--transition);">
        <i class="bi <?php echo $tab['icon']; ?>"></i>
        <?php echo $tab['label']; ?>
        <span style="background:<?php echo $isActive ? 'var(--secondary)' : 'var(--border)'; ?>;color:<?php echo $isActive ? 'white' : 'var(--text-muted)'; ?>;border-radius:50px;padding:2px 8px;font-size:0.72rem;"><?php echo $counts[$key]; ?></span>
    </a>
    <?php endforeach; ?>
</div>

<div class="admin-card">
    <div class="admin-card-header">
        <span class="admin-card-title">
            <i class="bi bi-people-fill me-2" style="color:var(--secondary);"></i>
            <?php echo ucfirst($filter); ?> Registrations (<?php echo count($regs); ?>)
        </span>
        <a href="/NexSoft/?route=register" target="_blank" class="btn-admin-secondary">
            <i class="bi bi-box-arrow-up-right"></i> View Form
        </a>
    </div>
    <div class="table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Skills</th>
                    <th>Applied</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($regs)): ?>
                <tr><td colspan="7" style="text-align:center;padding:3rem;color:var(--text-muted);">
                    <i class="bi bi-people" style="font-size:2rem;display:block;margin-bottom:0.5rem;"></i>
                    No <?php echo $filter !== 'all' ? $filter . ' ' : ''; ?>registrations yet.
                </td></tr>
                <?php else: ?>
                <?php foreach ($regs as $i => $reg):
                    $status = $reg['status'] ?? 'pending';
                    $badgeClass = $status === 'verified' ? 'badge-green' : ($status === 'rejected' ? 'badge-orange' : 'badge-blue');
                    $statusIcon = $status === 'verified' ? 'bi-check-circle-fill' : ($status === 'rejected' ? 'bi-x-circle-fill' : 'bi-clock-fill');
                ?>
                <tr>
                    <td style="color:var(--text-muted);"><?php echo $i + 1; ?></td>
                    <td>
                        <div style="font-weight:700;color:var(--primary);"><?php echo htmlspecialchars($reg['name']); ?></div>
                        <?php if (!empty($reg['phone'])): ?>
                        <div style="font-size:0.78rem;color:var(--text-muted);"><?php echo htmlspecialchars($reg['phone']); ?></div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="mailto:<?php echo htmlspecialchars($reg['email']); ?>" style="color:var(--secondary);font-size:0.88rem;">
                            <?php echo htmlspecialchars($reg['email']); ?>
                        </a>
                    </td>
                    <td style="max-width:180px;color:var(--text-muted);font-size:0.85rem;">
                        <?php echo htmlspecialchars(mb_strimwidth($reg['skills'] ?? '—', 0, 45, '...')); ?>
                    </td>
                    <td><span class="badge-teal"><?php echo date('M d, Y', strtotime($reg['created_at'])); ?></span></td>
                    <td>
                        <span class="<?php echo $badgeClass; ?>" style="display:inline-flex;align-items:center;gap:4px;">
                            <i class="bi <?php echo $statusIcon; ?>"></i>
                            <?php echo ucfirst($status); ?>
                        </span>
                    </td>
                    <td>
                        <div style="display:flex;gap:5px;flex-wrap:wrap;">
                            <!-- View / Edit -->
                            <a href="<?php echo adminUrl('registrations.php?action=view&id=' . $reg['id']); ?>"
                               class="btn-action btn-view"><i class="bi bi-eye"></i> View</a>

                            <!-- Quick Verify -->
                            <?php if ($status !== 'verified'): ?>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="verify">
                                <input type="hidden" name="id" value="<?php echo $reg['id']; ?>">
                                <button type="submit" class="btn-action confirm-delete"
                                        style="background:rgba(34,197,94,0.1);color:#16a34a;"
                                        onclick="return confirm('Verify <?php echo htmlspecialchars(addslashes($reg['name'])); ?> and send approval email?')">
                                    <i class="bi bi-check-circle"></i>
                                </button>
                            </form>
                            <?php endif; ?>

                            <!-- Quick Reject -->
                            <?php if ($status !== 'rejected'): ?>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="reject">
                                <input type="hidden" name="id" value="<?php echo $reg['id']; ?>">
                                <button type="submit" class="btn-action"
                                        style="background:rgba(249,115,22,0.1);color:#ea580c;"
                                        onclick="return confirm('Reject <?php echo htmlspecialchars(addslashes($reg['name'])); ?> and send rejection email?')">
                                    <i class="bi bi-x-circle"></i>
                                </button>
                            </form>
                            <?php endif; ?>

                            <!-- Delete -->
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo $reg['id']; ?>">
                                <button type="submit" class="btn-action btn-delete confirm-delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/layout-footer.php'; ?>
