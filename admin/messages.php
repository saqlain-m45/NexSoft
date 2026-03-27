<?php
require_once __DIR__ . '/auth.php';
adminCheck();
adminRequirePermission('messages');

$db  = getDB();
$msg = '';
$error = '';

// Handle Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    $id = (int)($_POST['id'] ?? 0);
    $db->prepare("DELETE FROM contact_messages WHERE id = ?")->execute([$id]);
    adminLogAction('messages.delete', 'Deleted contact message id=' . $id);
    $msg = 'Message deleted.';
}

// Handle Quick Reply (Single)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'quick_reply') {
    $message_id = (int)($_POST['message_id'] ?? 0);
    $reply_subject = trim($_POST['reply_subject'] ?? '');
    $reply_message = trim($_POST['reply_message'] ?? '');
    
    if ($message_id && !empty($reply_message)) {
        try {
            $contact = $db->prepare("SELECT * FROM contact_messages WHERE id = ?")->execute([$message_id])->fetch();
            if ($contact) {
                $db->prepare("INSERT INTO message_replies (message_id, reply_subject, reply_message, sent_by, created_at) VALUES (?, ?, ?, ?, NOW())")
                    ->execute([$message_id, $reply_subject, $reply_message, $_SESSION['user_id'] ?? 1]);
                $msg = "Reply sent to " . htmlspecialchars($contact['name']) . "!";
                adminLogAction('messages.reply', "Sent reply to message ID: $message_id");
            }
        } catch (Exception $e) {
            $error = "Error: " . $e->getMessage();
        }
    } else {
        $error = "Please write a reply message.";
    }
}

// Handle Send to All
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'send_all') {
    $reply_subject = trim($_POST['reply_subject'] ?? '');
    $reply_message = trim($_POST['reply_message'] ?? '');
    
    if (!empty($reply_message)) {
        try {
            $messages_list = $db->query("SELECT * FROM contact_messages")->fetchAll();
            $success_count = 0;
            foreach ($messages_list as $contact) {
                $db->prepare("INSERT INTO message_replies (message_id, reply_subject, reply_message, sent_by, created_at) VALUES (?, ?, ?, ?, NOW())")
                    ->execute([$contact['id'], $reply_subject, $reply_message, $_SESSION['user_id'] ?? 1]);
                $success_count++;
            }
            $msg = "Reply sent to all $success_count message(s)!";
            adminLogAction('messages.reply_all', "Sent replies to all $success_count messages");
        } catch (Exception $e) {
            $error = "Error: " . $e->getMessage();
        }
    } else {
        $error = "Please write a reply message.";
    }
}

$messages = $db->query("SELECT * FROM contact_messages ORDER BY created_at DESC")->fetchAll();

$pageTitle  = 'Contact Messages — NexSoft Hub Admin';
$activePage = 'messages';
require_once __DIR__ . '/layout-header.php';
?>

<?php if (!empty($msg)): ?>
<div class="admin-alert-success mb-3"><i class="bi bi-check-circle me-2"></i><?php echo htmlspecialchars($msg); ?></div>
<?php endif; ?>
<?php if (!empty($error)): ?>
<div class="admin-alert-error mb-3"><i class="bi bi-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<!-- Send to All Section -->
<?php if (!empty($messages)): ?>
<div class="admin-card mb-4">
    <div class="admin-card-header">
        <h5 class="mb-0"><i class="bi bi-send-fill me-2"></i> Send Reply to All Messages</h5>
    </div>
    <div class="admin-card-body">
        <form method="POST">
            <?php echo adminCsrfField(); ?>
            <input type="hidden" name="action" value="send_all">
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Subject (Optional)</label>
                        <input type="text" class="form-control" name="reply_subject" placeholder="e.g., Thank you for your message">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Recipients</label>
                        <input type="text" class="form-control" value="<?php echo count($messages); ?> messages" readonly style="background:#f5f5f5;">
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Message</label>
                <textarea class="form-control" name="reply_message" rows="4" placeholder="Type your reply to send to all..." required></textarea>
            </div>
            <button type="submit" class="btn-admin-primary">
                <i class="bi bi-send-fill me-1"></i> Send to All <?php echo count($messages); ?> Messages
            </button>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- Messages Table -->
<div class="admin-card">
    <div class="admin-card-header">
        <span class="admin-card-title">
            <i class="bi bi-chat-left-text-fill me-2" style="color:var(--secondary);"></i>
            Contact Messages (<?php echo count($messages); ?>)
        </span>
        <a href="/NexSoft/?route=contact" target="_blank" class="btn-admin-secondary">
            <i class="bi bi-box-arrow-up-right"></i> View Contact Page
        </a>
    </div>
    <?php if (empty($messages)): ?>
    <div class="admin-card-body text-center py-5">
        <i class="bi bi-envelope-open" style="font-size:3rem;color:#ccc;margin-bottom:1rem;"></i>
        <p class="text-muted">No messages yet.</p>
    </div>
    <?php else: ?>
    <div class="table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Message</th>
                    <th>Date</th>
                    <th style="text-align: center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($messages as $msg_row): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($msg_row['name']); ?></strong></td>
                    <td><a href="mailto:<?php echo htmlspecialchars($msg_row['email']); ?>" style="color:var(--secondary);"><?php echo htmlspecialchars($msg_row['email']); ?></a></td>
                    <td style="max-width:350px;"><div style="background:var(--bg);border-radius:var(--radius-sm);padding:0.6rem;font-size:0.85rem;border-left:3px solid var(--secondary);"><?php echo nl2br(htmlspecialchars($msg_row['message'])); ?></div></td>
                    <td><span class="badge-orange"><?php echo date('M d, Y', strtotime($msg_row['created_at'])); ?></span></td>
                    <td style="text-align:center;">
                        <button class="btn-action" onclick="toggleReplyForm(<?php echo $msg_row['id']; ?>)" title="Reply"><i class="bi bi-reply-fill"></i></button>
                        <button class="btn-action" onclick="viewReplies(<?php echo $msg_row['id']; ?>)" title="View replies" style="color:#28a745;"><i class="bi bi-chat-dots"></i></button>
                        <form method="POST" style="display:inline;">
                            <?php echo adminCsrfField(); ?>
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?php echo $msg_row['id']; ?>">
                            <button type="submit" class="btn-action btn-delete confirm-delete" title="Delete"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                <tr id="replyForm<?php echo $msg_row['id']; ?>" style="display:none;background:#f0f9ff;">
                    <td colspan="5">
                        <form method="POST" style="padding:1.5rem;">
                            <?php echo adminCsrfField(); ?>
                            <input type="hidden" name="action" value="quick_reply">
                            <input type="hidden" name="message_id" value="<?php echo $msg_row['id']; ?>">
                            <div class="row mb-3">
                                <div class="col-md-6"><label class="form-label">Subject (Optional)</label><input type="text" class="form-control" name="reply_subject"></div>
                            </div>
                            <div class="mb-3"><label class="form-label">Your Reply</label><textarea class="form-control" name="reply_message" rows="4" required></textarea></div>
                            <button type="submit" class="btn-admin-primary btn-sm"><i class="bi bi-send-fill me-1"></i> Send</button>
                            <button type="button" class="btn-admin-secondary btn-sm" onclick="toggleReplyForm(<?php echo $msg_row['id']; ?>)">Cancel</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<!-- Modal -->
<div id="repliesModal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:1000;align-items:center;justify-content:center;">
    <div style="background:white;border-radius:8px;width:90%;max-width:600px;max-height:80vh;overflow-y:auto;box-shadow:0 10px 40px rgba(0,0,0,0.2);">
        <div style="display:flex;justify-content:space-between;align-items:center;padding:1.5rem;border-bottom:1px solid #ddd;background:var(--secondary);color:white;">
            <h5 style="margin:0;font-weight:600;">Replies Sent</h5>
            <button type="button" style="background:none;border:none;font-size:1.5rem;cursor:pointer;color:white;" onclick="document.getElementById('repliesModal').style.display='none';">&times;</button>
        </div>
        <div id="repliesBody" style="padding:1.5rem;"><p style="color:#999;">Loading...</p></div>
    </div>
</div>

<script>
function toggleReplyForm(id) {
    const form = document.getElementById('replyForm' + id);
    form.style.display = form.style.display === 'none' ? 'table-row' : 'none';
}
function viewReplies(id) {
    fetch('<?php echo adminUrl('ajax-get-replies'); ?>?message_id=' + id)
        .then(r => r.json())
        .then(data => {
            const modal = document.getElementById('repliesModal');
            const body = document.getElementById('repliesBody');
            if (data.success) {
                if (data.replies.length === 0) {
                    body.innerHTML = '<p style="color:#999;text-align:center;"><i class="bi bi-chat-dots" style="font-size:2rem;display:block;margin-bottom:0.5rem;"></i>No replies yet.</p>';
                } else {
                    let html = '';
                    data.replies.forEach(r => {
                        html += `<div style="background:#f5f5f5;padding:1rem;border-radius:4px;margin-bottom:1rem;border-left:3px solid var(--secondary);"><h6 style="margin:0 0 0.5rem 0;color:var(--secondary);">${r.reply_subject || 'No Subject'}</h6><div style="font-size:0.85rem;color:#666;margin-bottom:0.5rem;"><i class="bi bi-person"></i> ${r.sent_by_name} | ${r.created_at}</div><div>${r.reply_message.replace(/\n/g, '<br>')}</div></div>`;
                    });
                    body.innerHTML = html;
                }
                modal.style.display = 'flex';
            }
        });
}
document.getElementById('repliesModal')?.addEventListener('click', function(e) {
    if (e.target === this) this.style.display = 'none';
});
</script>

<?php require_once __DIR__ . '/layout-footer.php'; ?>


