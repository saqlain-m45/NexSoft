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

// Handle Quick Reply
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
            $error = "Error sending reply: " . $e->getMessage();
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

<div class="admin-card">
    <div class="admin-card-header">
        <span class="admin-card-title">
            <i class="bi bi-chat-left-text-fill me-2" style="color:var(--secondary);"></i>
            All Contact Messages (<span id="messageCount"><?php echo count($messages); ?></span>)
        </span>
        <a href="/NexSoft/?route=contact" target="_blank" class="btn-admin-secondary">
            <i class="bi bi-box-arrow-up-right"></i> View Contact Page
        </a>
    </div>

    <?php if (empty($messages)): ?>
    <div class="admin-card-body text-center py-5">
        <i class="bi bi-envelope-open" style="font-size:3rem;color:#ccc;display:block;margin-bottom:1rem;"></i>
        <p class="text-muted">No messages yet.</p>
    </div>
    <?php else: ?>
    <div class="message-list">
        <?php foreach($messages as $i => $msg_row): ?>
        <div class="message-item">
            <div class="message-header">
                <div class="message-info">
                    <h6 class="mb-1">
                        <strong><?php echo htmlspecialchars($msg_row['name']); ?></strong>
                        <span class="badge-orange" style="font-size: 0.75rem;">
                            <?php echo date('M d, Y', strtotime($msg_row['created_at'])); ?>
                        </span>
                    </h6>
                    <a href="mailto:<?php echo htmlspecialchars($msg_row['email']); ?>" style="color:var(--secondary);font-size:0.9rem;">
                        <i class="bi bi-envelope me-1"></i><?php echo htmlspecialchars($msg_row['email']); ?>
                    </a>
                </div>
                <div class="message-actions">
                    <button class="btn-action btn-reply" onclick="toggleReplyForm(<?php echo $msg_row['id']; ?>)" title="Reply to this message">
                        <i class="bi bi-reply-fill"></i>
                    </button>
                    <button class="btn-action" onclick="viewReplies(<?php echo $msg_row['id']; ?>)" title="View replies sent" style="color:#28a745;">
                        <i class="bi bi-chat-dots"></i>
                    </button>
                    <form method="POST" style="display:inline;">
                        <?php echo adminCsrfField(); ?>
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?php echo $msg_row['id']; ?>">
                        <button type="submit" class="btn-action btn-delete confirm-delete" title="Delete message">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </div>
            </div>

            <div class="message-body">
                <p><?php echo nl2br(htmlspecialchars($msg_row['message'])); ?></p>
            </div>

            <!-- Quick Reply Form (Hidden by default) -->
            <form method="POST" id="replyForm<?php echo $msg_row['id']; ?>" class="reply-form" style="display: none;">
                <?php echo adminCsrfField(); ?>
                <input type="hidden" name="action" value="quick_reply">
                <input type="hidden" name="message_id" value="<?php echo $msg_row['id']; ?>">
                
                <div class="form-group">
                    <label class="form-label">Subject</label>
                    <input type="text" class="form-control" name="reply_subject" placeholder="Re: Your message" value="Re: <?php echo htmlspecialchars($msg_row['subject'] ?? 'Your Inquiry'); ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">Your Reply</label>
                    <textarea class="form-control" name="reply_message" rows="5" placeholder="Type your reply..." required></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-admin-primary btn-sm">
                        <i class="bi bi-send-fill me-1"></i> Send Reply
                    </button>
                    <button type="button" class="btn-admin-secondary btn-sm" onclick="toggleReplyForm(<?php echo $msg_row['id']; ?>)">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<!-- Replies Modal -->
<div id="repliesModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h5>Replies Sent</h5>
            <button type="button" class="modal-close" onclick="document.getElementById('repliesModal').style.display='none';">&times;</button>
        </div>
        <div id="repliesBody" class="modal-body">
            <p class="text-muted">Loading replies...</p>
        </div>
    </div>
</div>

<style>
.message-list {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
    padding: 1.5rem;
}

.message-item {
    border: 1px solid var(--border-color);
    border-radius: var(--radius-sm);
    padding: 1.5rem;
    background: white;
    transition: all 0.3s ease;
}

.message-item:hover {
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    border-color: var(--secondary);
}

.message-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
    gap: 1rem;
}

.message-info {
    flex: 1;
}

.message-info h6 {
    color: var(--text-dark);
    margin-bottom: 0.25rem;
}

.message-body {
    background: var(--bg);
    padding: 1rem;
    border-radius: var(--radius-sm);
    border-left: 3px solid var(--secondary);
    margin-bottom: 0;
    line-height: 1.6;
    color: var(--text-muted);
}

.message-body p {
    margin: 0;
    word-wrap: break-word;
}

.message-actions {
    display: flex;
    gap: 0.5rem;
}

.btn-reply {
    color: var(--secondary);
}

.btn-reply:hover {
    background: rgba(0, 102, 204, 0.1);
}

.reply-form {
    background: #f0f9ff;
    padding: 1.5rem;
    border-radius: var(--radius-sm);
    margin-top: 1rem;
    border: 1px solid var(--secondary);
    animation: slideDown 0.3s ease-in-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.form-group {
    margin-bottom: 1rem;
}

.form-label {
    display: block;
    font-weight: 600;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
    color: var(--text-dark);
}

.form-control {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid var(--border-color);
    border-radius: var(--radius-sm);
    font-family: inherit;
    font-size: 0.9rem;
}

.form-control:focus {
    outline: none;
    border-color: var(--secondary);
    box-shadow: 0 0 0 3px rgba(0, 102, 204, 0.1);
}

.form-actions {
    display: flex;
    gap: 0.75rem;
    margin-top: 1rem;
}

.btn-sm {
    padding: 0.5rem 1rem !important;
    font-size: 0.9rem !important;
}

/* Modal Styles */
.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}

.modal-content {
    background: white;
    border-radius: var(--radius-sm);
    width: 90%;
    max-width: 600px;
    max-height: 80vh;
    overflow-y: auto;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem;
    border-bottom: 1px solid var(--border-color);
    background: var(--secondary);
    color: white;
}

.modal-header h5 {
    margin: 0;
    font-weight: 600;
}

.modal-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: white;
}

.modal-body {
    padding: 1.5rem;
}

.reply-item {
    background: var(--bg);
    padding: 1rem;
    border-radius: var(--radius-sm);
    margin-bottom: 1rem;
    border-left: 3px solid var(--secondary);
}

.reply-item h6 {
    margin: 0 0 0.5rem 0;
    color: var(--secondary);
    font-weight: 600;
}

.reply-item .reply-meta {
    font-size: 0.85rem;
    color: var(--text-muted);
    margin-bottom: 0.75rem;
}

.reply-item .reply-text {
    color: var(--text-dark);
    line-height: 1.5;
}

.no-replies {
    text-align: center;
    padding: 2rem;
    color: var(--text-muted);
}

.no-replies i {
    font-size: 2rem;
    display: block;
    margin-bottom: 0.5rem;
}
</style>

<script>
function toggleReplyForm(messageId) {
    const form = document.getElementById('replyForm' + messageId);
    if (form.style.display === 'none') {
        form.style.display = 'block';
        form.querySelector('textarea').focus();
    } else {
        form.style.display = 'none';
    }
}

function viewReplies(messageId) {
    const modal = document.getElementById('repliesModal');
    const body = document.getElementById('repliesBody');
    
    // Fetch replies via AJAX
    fetch('<?php echo adminUrl('ajax-get-replies.php'); ?>?message_id=' + messageId)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.replies.length === 0) {
                    body.innerHTML = '<div class="no-replies"><i class="bi bi-chat-dots"></i><p>No replies sent for this message yet.</p></div>';
                } else {
                    let html = '';
                    data.replies.forEach(reply => {
                        html += `
                            <div class="reply-item">
                                <h6>${reply.reply_subject || 'No Subject'}</h6>
                                <div class="reply-meta">
                                    <i class="bi bi-person-circle me-1"></i> ${reply.sent_by_name || 'Admin'}
                                    <span class="ms-3"><i class="bi bi-calendar me-1"></i> ${reply.created_at}</span>
                                </div>
                                <div class="reply-text">${reply.reply_message.replace(/\n/g, '<br>')}</div>
                            </div>
                        `;
                    });
                    body.innerHTML = html;
                }
                modal.style.display = 'flex';
            }
        })
        .catch(error => {
            body.innerHTML = '<p class="text-danger">Error loading replies.</p>';
            modal.style.display = 'flex';
        });
}

// Close modal when clicking outside
document.getElementById('repliesModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        this.style.display = 'none';
    }
});
</script>

<?php require_once __DIR__ . '/layout-footer.php'; ?>
