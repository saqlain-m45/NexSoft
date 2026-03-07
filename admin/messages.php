<?php
require_once __DIR__ . '/auth.php';
adminCheck();
adminRequirePermission('messages');

$db  = getDB();
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    $id = (int)($_POST['id'] ?? 0);
    $db->prepare("DELETE FROM contact_messages WHERE id = ?")->execute([$id]);
    $msg = 'Message deleted.';
}

$messages = $db->query("SELECT * FROM contact_messages ORDER BY created_at DESC")->fetchAll();

$pageTitle  = 'Contact Messages — NexSoft Hub Admin';
$activePage = 'messages';
require_once __DIR__ . '/layout-header.php';
?>

<?php if (!empty($msg)): ?>
<div class="admin-alert-success mb-3"><i class="bi bi-check-circle me-2"></i><?php echo htmlspecialchars($msg); ?></div>
<?php endif; ?>

<div class="admin-card">
    <div class="admin-card-header">
        <span class="admin-card-title">
            <i class="bi bi-chat-left-text-fill me-2" style="color:var(--secondary);"></i>
            All Contact Messages (<?php echo count($messages); ?>)
        </span>
        <a href="/NexSoft/?route=contact" target="_blank" class="btn-admin-secondary">
            <i class="bi bi-box-arrow-up-right"></i> View Contact Page
        </a>
    </div>
    <div class="table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Message</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($messages)): ?>
                <tr><td colspan="6" style="text-align:center;padding:3rem;color:var(--text-muted);">
                    <i class="bi bi-envelope-open" style="font-size:2rem;display:block;margin-bottom:0.5rem;"></i>
                    No messages yet.
                </td></tr>
                <?php else: ?>
                <?php foreach($messages as $i => $msg_row): ?>
                <tr>
                    <td style="color:var(--text-muted);"><?php echo $i+1; ?></td>
                    <td><strong><?php echo htmlspecialchars($msg_row['name']); ?></strong></td>
                    <td>
                        <a href="mailto:<?php echo htmlspecialchars($msg_row['email']); ?>" style="color:var(--secondary);">
                            <?php echo htmlspecialchars($msg_row['email']); ?>
                        </a>
                    </td>
                    <td style="max-width:350px;">
                        <div style="background:var(--bg);border-radius:var(--radius-sm);padding:0.6rem 0.9rem;font-size:0.85rem;color:var(--text-muted);border-left:3px solid var(--secondary);">
                            <?php echo nl2br(htmlspecialchars($msg_row['message'])); ?>
                        </div>
                    </td>
                    <td><span class="badge-orange"><?php echo date('M d, Y', strtotime($msg_row['created_at'])); ?></span></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?php echo $msg_row['id']; ?>">
                            <button type="submit" class="btn-action btn-delete confirm-delete">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/layout-footer.php'; ?>
