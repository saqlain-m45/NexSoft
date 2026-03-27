<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../config/mailer.php';
adminCheck();
adminRequirePermission('settings');

$db = getDB();
$msg = '';
$error = '';

$template_id = isset($_GET['template']) ? (int)$_GET['template'] : 0;
if (!$template_id) {
    header('Location: email_templates?error=No template selected');
    exit;
}

// Get template
$stmt = $db->prepare("SELECT * FROM email_templates WHERE id = ? AND is_active = 1");
$stmt->execute([$template_id]);
$template = $stmt->fetch();

if (!$template) {
    header('Location: email_templates?error=Template not found');
    exit;
}

// Check if a specific recipient is pre-selected
$pre_selected_recipient = null;
$recipient_type = isset($_GET['recipient_type']) ? $_GET['recipient_type'] : '';
$recipient_id = isset($_GET['recipient_id']) ? (int)$_GET['recipient_id'] : 0;

if ($recipient_type && $recipient_id > 0) {
    if ($recipient_type === 'internship') {
        $stmt = $db->prepare("SELECT cr.id, u.full_name, u.email, u.phone FROM course_registrations cr 
            JOIN users u ON cr.user_id = u.id 
            WHERE cr.id = ? AND cr.course_id IS NULL");
        $stmt->execute([$recipient_id]);
        $pre_selected_recipient = $stmt->fetch();
        $source = 'internships';
    }
}

// Get available recipients from different sources
$recipients = [];
$source = 'users'; // users, courses, internships, contact_messages

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $source = $_POST['source'] ?? 'users';
    $selected_recipients = $_POST['recipients'] ?? [];
    $custom_field = $_POST['custom_field'] ?? 'name';
    $send_now = isset($_POST['send_now']);

    if (empty($selected_recipients)) {
        $error = "Please select at least one recipient.";
    } else {
        $sent_count = 0;
        $failed_count = 0;

        foreach ($selected_recipients as $recipient_id) {
            $recipient = null;
            $placeholder_data = [];

            // Get recipient data based on source
            if ($source === 'users') {
                $stmt = $db->prepare("SELECT id, full_name, email, phone FROM users WHERE id = ?");
                $stmt->execute([$recipient_id]);
                $recipient = $stmt->fetch();

                if ($recipient) {
                    $placeholder_data = [
                        '{name}' => $recipient['full_name'],
                        '{email}' => $recipient['email'],
                        '{phone}' => $recipient['phone'] ?? 'N/A',
                        '{company}' => 'N/A',
                    ];
                }
            } elseif ($source === 'courses') {
                $stmt = $db->prepare("SELECT cr.id, u.full_name, u.email, u.phone, c.title FROM course_registrations cr 
                    JOIN users u ON cr.user_id = u.id 
                    JOIN courses c ON cr.course_id = c.id 
                    WHERE cr.id = ?");
                $stmt->execute([$recipient_id]);
                $recipient = $stmt->fetch();

                if ($recipient) {
                    $placeholder_data = [
                        '{name}' => $recipient['full_name'],
                        '{email}' => $recipient['email'],
                        '{phone}' => $recipient['phone'] ?? 'N/A',
                        '{company}' => $recipient['title'] ?? 'N/A',
                    ];
                }
            } elseif ($source === 'internships') {
                $stmt = $db->prepare("SELECT cr.id, u.full_name, u.email, u.phone FROM course_registrations cr 
                    JOIN users u ON cr.user_id = u.id 
                    WHERE cr.id = ? AND cr.course_id IS NULL");
                $stmt->execute([$recipient_id]);
                $recipient = $stmt->fetch();

                if ($recipient) {
                    $placeholder_data = [
                        '{name}' => $recipient['full_name'],
                        '{email}' => $recipient['email'],
                        '{phone}' => $recipient['phone'] ?? 'N/A',
                        '{company}' => 'N/A',
                    ];
                }
            } elseif ($source === 'contact_messages') {
                $stmt = $db->prepare("SELECT id, name, email FROM contact_messages WHERE id = ?");
                $stmt->execute([$recipient_id]);
                $recipient = $stmt->fetch();

                if ($recipient) {
                    $placeholder_data = [
                        '{name}' => $recipient['name'],
                        '{email}' => $recipient['email'],
                        '{phone}' => 'N/A',
                        '{company}' => 'N/A',
                    ];
                }
            }

            if ($recipient) {
                // Replace placeholders in subject and body
                $subject = $template['subject'];
                $body = $template['body'];

                $placeholder_data['{date}'] = date('F j, Y');
                $placeholder_data['{site_name}'] = getSetting('site_name', 'NexSoft Hub');

                foreach ($placeholder_data as $placeholder => $value) {
                    $subject = str_replace($placeholder, $value, $subject);
                    $body = str_replace($placeholder, $value, $body);
                }

                // Send email
                $success = sendMail($recipient['email'], $recipient['name'] ?? $recipient['full_name'] ?? '', $subject, $body);

                // Log the send attempt
                try {
                    $log_stmt = $db->prepare("INSERT INTO email_logs (template_id, recipient_email, recipient_name, subject, status, sent_at) VALUES (?, ?, ?, ?, ?, NOW())");
                    $log_stmt->execute([
                        $template_id,
                        $recipient['email'],
                        $recipient['name'] ?? $recipient['full_name'] ?? '',
                        $subject,
                        $success ? 'sent' : 'failed'
                    ]);
                } catch (Exception $e) {
                    // Log error
                }

                if ($success) {
                    $sent_count++;
                } else {
                    $failed_count++;
                }
            }
        }

        $msg = "Emails sent! Successfully sent: <strong>$sent_count</strong>";
        if ($failed_count > 0) {
            $msg .= ", Failed: <strong>$failed_count</strong>";
        }
        $msg .= ".";
    }
}

// Get recipients based on source
if ($source === 'users') {
    $recipients = $db->query("SELECT id, full_name, email FROM users WHERE role != 'admin' ORDER BY full_name")->fetchAll();
} elseif ($source === 'courses') {
    // Get students registered in courses
    $recipients = $db->query("SELECT cr.id, u.full_name, u.email FROM course_registrations cr 
        JOIN users u ON cr.user_id = u.id 
        JOIN courses c ON cr.course_id = c.id 
        WHERE cr.course_id IS NOT NULL
        ORDER BY u.full_name")->fetchAll();
} elseif ($source === 'internships') {
    // Get students registered in internship programs
    $recipients = $db->query("SELECT cr.id, u.full_name, u.email FROM course_registrations cr 
        JOIN users u ON cr.user_id = u.id 
        WHERE cr.course_id IS NULL
        ORDER BY u.full_name")->fetchAll();
} elseif ($source === 'contact_messages') {
    $recipients = $db->query("SELECT id, name, email FROM contact_messages ORDER BY name")->fetchAll();
}

$pageTitle = 'Send Email — NexSoft Hub Admin';
$activePage = 'send_email';
require_once __DIR__ . '/layout-header.php';
?>

<div class="mb-4">
    <?php if ($pre_selected_recipient): ?>
    <a href="intern_applications" class="btn btn-secondary mb-3">
        <i class="bi bi-arrow-left me-1"></i> Back to Applications
    </a>
    <?php else: ?>
    <a href="email_templates" class="btn btn-secondary mb-3">
        <i class="bi bi-arrow-left me-1"></i> Back to Templates
    </a>
    <?php endif; ?>
    <h2 class="h3 text-white mb-0">Send Email: <?php echo htmlspecialchars($template['name']); ?></h2>
</div>

<?php if ($msg): ?><div class="admin-alert-success mb-3"><?php echo $msg; ?></div><?php endif; ?>
<?php if ($error): ?><div class="admin-alert-error mb-3"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

<div class="row">
    <div class="col-md-4">
        <div class="admin-card mb-4">
            <div class="admin-card-header">
                <span class="admin-card-title text-white">Template Preview</span>
            </div>
            <div class="admin-card-body">
                <p class="mb-3">
                    <strong class="text-white">Subject:</strong><br>
                    <small class="text-muted"><?php echo htmlspecialchars($template['subject']); ?></small>
                </p>
                <p class="mb-3">
                    <strong class="text-white">Description:</strong><br>
                    <small class="text-muted"><?php echo htmlspecialchars($template['description'] ?? 'N/A'); ?></small>
                </p>
                <div class="mt-3 pt-3 border-top border-secondary">
                    <strong class="text-white d-block mb-2">Preview Body:</strong>
                    <div style="background: rgba(255, 255, 255, 0.03); padding: 12px; border-radius: 6px; font-size: 12px; max-height: 300px; overflow-y: auto;">
                        <?php echo substr(strip_tags($template['body']), 0, 300) . '...'; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="admin-card">
            <div class="admin-card-header">
                <span class="admin-card-title text-white">Select Recipients</span>
            </div>
            <div class="admin-card-body">
                <form method="POST" action="">
                    <?php if (!$pre_selected_recipient): ?>
                    <div class="mb-4">
                        <label class="form-label text-white">Recipient Source</label>
                        <select name="source" class="form-control" onchange="this.form.submit()">
                            <option value="users" <?php echo $source === 'users' ? 'selected' : ''; ?>>All Users</option>
                            <option value="courses" <?php echo $source === 'courses' ? 'selected' : ''; ?>>Course Registrations</option>
                            <option value="internships" <?php echo $source === 'internships' ? 'selected' : ''; ?>>Internship Applications</option>
                            <option value="contact_messages" <?php echo $source === 'contact_messages' ? 'selected' : ''; ?>>Contact Messages</option>
                        </select>
                    </div>
                    <?php else: ?>
                    <div class="alert alert-info mb-4">
                        <i class="bi bi-info-circle me-2"></i>Sending to: <strong><?php echo htmlspecialchars($pre_selected_recipient['full_name']); ?></strong> (<?php echo htmlspecialchars($pre_selected_recipient['email']); ?>)
                    </div>
                    <?php endif; ?>

                    <div class="mb-4">
                        <label class="form-label text-white d-block mb-3">
                            <input type="checkbox" id="selectAll"> Select All Recipients
                        </label>
                        <div style="max-height: 400px; overflow-y: auto; border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 6px;">
                            <?php if ($pre_selected_recipient): ?>
                                <!-- Pre-selected single recipient -->
                                <div class="p-2 border-bottom border-secondary bg-success bg-opacity-10">
                                    <input type="checkbox" name="recipients[]" class="recipient-check" value="<?php echo $pre_selected_recipient['id']; ?>" checked>
                                    <label class="text-white ms-2">
                                        <?php echo htmlspecialchars($pre_selected_recipient['full_name']); ?>
                                        <br><small class="text-muted"><?php echo htmlspecialchars($pre_selected_recipient['email']); ?></small>
                                    </label>
                                </div>
                            <?php else: ?>
                                <!-- Show all recipients -->
                                <?php foreach ($recipients as $r): ?>
                                <div class="p-2 border-bottom border-secondary">
                                    <input type="checkbox" name="recipients[]" class="recipient-check" value="<?php echo $r['id']; ?>">
                                    <label class="text-white ms-2">
                                        <?php echo htmlspecialchars($r['name'] ?? $r['full_name']); ?>
                                        <br><small class="text-muted"><?php echo htmlspecialchars($r['email']); ?></small>
                                    </label>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" name="send_now" class="btn btn-success btn-lg">
                            <i class="bi bi-envelope-at me-2"></i> Send Email to Selected Recipients
                        </button>
                        <?php if ($pre_selected_recipient): ?>
                        <a href="intern_applications" class="btn btn-secondary">Cancel</a>
                        <?php else: ?>
                        <a href="email_templates" class="btn btn-secondary">Cancel</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.recipient-check');
    checkboxes.forEach(cb => cb.checked = this.checked);
});

// Check if all are selected
document.querySelectorAll('.recipient-check').forEach(cb => {
    cb.addEventListener('change', function() {
        const total = document.querySelectorAll('.recipient-check').length;
        const checked = document.querySelectorAll('.recipient-check:checked').length;
        document.getElementById('selectAll').checked = total === checked;
    });
});
</script>

<?php require_once __DIR__ . '/layout-footer.php'; ?>
