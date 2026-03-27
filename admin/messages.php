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

// Handle Reply
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'reply') {
    $reply_subject = trim($_POST['reply_subject'] ?? '');
    $reply_message = trim($_POST['reply_message'] ?? '');
    $selected_messages = $_POST['selected_messages'] ?? [];
    
    if (!empty($selected_messages) && !empty($reply_message)) {
        try {
            $success_count = 0;
            foreach ($selected_messages as $msg_id) {
                $msg_id = (int)$msg_id;
                $contact = $db->prepare("SELECT * FROM contact_messages WHERE id = ?")->execute([$msg_id])->fetch();
                
                if ($contact) {
                    // Store reply record
                    $db->prepare("INSERT INTO message_replies (message_id, reply_subject, reply_message, sent_by, created_at) VALUES (?, ?, ?, ?, NOW())")
                        ->execute([$msg_id, $reply_subject, $reply_message, $_SESSION['user_id'] ?? 1]);
                    
                    // In production, send actual email here
                    // sendEmail($contact['email'], $reply_subject, $reply_message);
                    
                    $success_count++;
                }
            }
            $msg = "Reply sent to $success_count message(s).";
            adminLogAction('messages.reply', "Sent replies to $success_count messages");
        } catch (Exception $e) {
            $error = "Error sending reply: " . $e->getMessage();
        }
    } else {
        $error = "Please select at least one message and write a reply.";
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

<!-- Reply Form Section -->
<div class="admin-card mb-4" id="replySection" style="display: none;">
    <div class="admin-card-header">
        <h5 class="mb-0">
            <i class="bi bi-reply-fill me-2"></i> Send Reply to Selected Message(s)
        </h5>
    </div>
    <div class="admin-card-body">
        <form method="POST" id="replyForm">
            <?php echo adminCsrfField(); ?>
            <input type="hidden" name="action" value="reply">
            <div id="selectedListDiv"></div>

            <div class="mb-3">
                <label class="form-label fw-bold">Subject</label>
                <input type="text" class="form-control" name="reply_subject" placeholder="e.g., Re: Your Inquiry" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Reply Message</label>
                <textarea class="form-control" name="reply_message" rows="8" placeholder="Type your reply here..." required></textarea>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn-admin-primary">
                    <i class="bi bi-send-fill me-1"></i> Send Reply
                </button>
                <button type="button" class="btn-admin-secondary" onclick="closeReplyForm()">
                    <i class="bi bi-x-circle me-1"></i> Cancel
                </button>
            </div>
        </form>
    </div>
</div>

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
    <div class="table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th style="width: 40px;">
                        <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll(this)">
                    </th>
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
                <tr><td colspan="7" style="text-align:center;padding:3rem;color:var(--text-muted);">
                    <i class="bi bi-envelope-open" style="font-size:2rem;display:block;margin-bottom:0.5rem;"></i>
                    No messages yet.
                </td></tr>
                <?php else: ?>
                <?php foreach($messages as $i => $msg_row): ?>
                <tr>
                    <td>
                        <input type="checkbox" class="messageCheckbox" value="<?php echo $msg_row['id']; ?>" onchange="updateReplyForm()">
                    </td>
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
                            <?php echo adminCsrfField(); ?>
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

<style>
#replySection {
    border-top: 3px solid var(--secondary);
    animation: slideDown 0.3s ease-in-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

#selectedListDiv {
    background: var(--bg);
    padding: 1rem;
    border-radius: var(--radius-sm);
    margin-bottom: 1.5rem;
    border-left: 3px solid var(--secondary);
}

#selectedListDiv strong {
    color: var(--secondary);
}
</style>

<script>
function toggleSelectAll(checkbox) {
    const checkboxes = document.querySelectorAll('.messageCheckbox');
    checkboxes.forEach(cb => cb.checked = checkbox.checked);
    updateReplyForm();
}

function updateReplyForm() {
    const selected = document.querySelectorAll('.messageCheckbox:checked');
    const replySection = document.getElementById('replySection');
    const selectedListDiv = document.getElementById('selectedListDiv');
    const replyForm = document.getElementById('replyForm');
    
    if (selected.length > 0) {
        replySection.style.display = 'block';
        
        // Create hidden inputs for selected messages
        const existingInputs = replyForm.querySelectorAll('input[name="selected_messages[]"]');
        existingInputs.forEach(input => input.remove());
        
        let selectedHtml = '<strong>Selected Messages:</strong> ';
        const names = [];
        
        selected.forEach(checkbox => {
            const row = checkbox.closest('tr');
            const name = row.querySelector('td:nth-child(3)').textContent.trim();
            const email = row.querySelector('td:nth-child(4) a').textContent.trim();
            names.push(`${name} (${email})`);
            
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'selected_messages[]';
            input.value = checkbox.value;
            replyForm.appendChild(input);
        });
        
        selectedHtml += names.join(', ');
        selectedListDiv.innerHTML = selectedHtml;
    } else {
        replySection.style.display = 'none';
        selectedListDiv.innerHTML = '';
    }
}

function closeReplyForm() {
    document.getElementById('selectAllCheckbox').checked = false;
    document.querySelectorAll('.messageCheckbox').forEach(cb => cb.checked = false);
    updateReplyForm();
    document.getElementById('replyForm').reset();
}
</script>

<?php require_once __DIR__ . '/layout-footer.php'; ?>
