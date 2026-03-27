<?php
require_once __DIR__ . '/auth.php';
adminCheck();
adminRequirePermission('settings');

$db = getDB();
$msg = '';
$error = '';

// Predefined placeholders available in templates
$placeholders = [
    ['name' => '{name}', 'description' => 'Recipient\'s name'],
    ['name' => '{email}', 'description' => 'Recipient\'s email address'],
    ['name' => '{phone}', 'description' => 'Recipient\'s phone number'],
    ['name' => '{company}', 'description' => 'Company/Organization name'],
    ['name' => '{date}', 'description' => 'Current date'],
    ['name' => '{site_name}', 'description' => 'Your website name'],
];

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $stmt = $db->prepare("DELETE FROM email_templates WHERE id = ?");
        $stmt->execute([$id]);
        $msg = "Template deleted successfully.";
        adminLogAction('email.template_delete', "Deleted email template ID: $id");
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Handle Form Submission (Add/Edit)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $name = trim($_POST['name'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $body = $_POST['body'] ?? '';
    $description = trim($_POST['description'] ?? '');
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $user_id = $_SESSION['user_id'] ?? null;

    if (empty($name) || empty($subject) || empty($body)) {
        $error = "Name, subject, and body are required.";
    } else {
        try {
            $placeholders_json = json_encode(array_column($placeholders, 'name'));
            
            if ($id > 0) {
                $stmt = $db->prepare("UPDATE email_templates SET name = ?, subject = ?, body = ?, description = ?, is_active = ?, available_placeholders = ? WHERE id = ?");
                $stmt->execute([$name, $subject, $body, $description, $is_active, $placeholders_json, $id]);
                $msg = "Template updated successfully.";
                adminLogAction('email.template_update', "Updated template: $name");
            } else {
                $stmt = $db->prepare("INSERT INTO email_templates (name, subject, body, description, is_active, available_placeholders, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$name, $subject, $body, $description, $is_active, $placeholders_json, $user_id]);
                $msg = "Template created successfully.";
                adminLogAction('email.template_add', "Created template: $name");
            }
        } catch (Exception $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}

// Fetch templates
try {
    $templates = $db->query("SELECT id, name, subject, description, is_active, created_at FROM email_templates ORDER BY created_at DESC")->fetchAll();
} catch (Exception $e) {
    $templates = [];
}

// Get single template for editing
$editData = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $db->prepare("SELECT * FROM email_templates WHERE id = ?");
    $stmt->execute([$id]);
    $editData = $stmt->fetch();
}

$pageTitle = 'Email Templates — NexSoft Hub Admin';
$activePage = 'email_templates';
require_once __DIR__ . '/layout-header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h3 mb-0 text-white">Email Templates</h2>
    <?php if (!isset($_GET['edit'])): ?>
    <button class="btn-admin-primary" data-bs-toggle="modal" data-bs-target="#templateModal">
        <i class="bi bi-plus-circle me-1"></i> Create New Template
    </button>
    <?php else: ?>
    <a href="email_templates.php" class="btn-admin-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to List
    </a>
    <?php endif; ?>
</div>

<?php if ($msg): ?><div class="admin-alert-success mb-3"><?php echo htmlspecialchars($msg); ?></div><?php endif; ?>
<?php if ($error): ?><div class="admin-alert-error mb-3"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

<?php if (!isset($_GET['edit'])): ?>
<!-- Templates List -->
<div class="admin-card">
    <div class="admin-card-body p-0">
        <div class="table-responsive">
            <table class="table admin-table mb-0">
                <thead>
                    <tr>
                        <th>Template Name</th>
                        <th>Subject</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($templates as $t): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($t['name']); ?></strong></td>
                        <td><?php echo htmlspecialchars(substr($t['subject'], 0, 50)); ?></td>
                        <td>
                            <?php if ($t['is_active']): ?>
                            <span class="badge bg-success">Active</span>
                            <?php else: ?>
                            <span class="badge bg-secondary">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <a href="?edit=<?php echo $t['id']; ?>" class="btn btn-sm btn-warning me-1">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <a href="send_email.php?template=<?php echo $t['id']; ?>" class="btn btn-sm btn-info me-1">
                                <i class="bi bi-envelope"></i> Send
                            </a>
                            <a href="?delete=<?php echo $t['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this template?')">
                                <i class="bi bi-trash"></i> Delete
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php else: ?>
<!-- Edit Template -->
<div class="admin-card">
    <div class="admin-card-header">
        <span class="admin-card-title text-white">Edit Email Template</span>
    </div>
    <div class="admin-card-body">
        <form method="POST" action="">
            <input type="hidden" name="id" value="<?php echo $editData['id']; ?>">

            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="form-label text-white">Template Name</label>
                        <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($editData['name']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-white">Email Subject</label>
                        <input type="text" name="subject" class="form-control" value="<?php echo htmlspecialchars($editData['subject']); ?>" placeholder="Email subject (can include {name}, {email}, etc.)" required>
                        <small class="text-muted">Use placeholders like {name}, {email} to personalize</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-white">Email Body</label>
                        <textarea name="body" class="form-control" rows="12" required><?php echo htmlspecialchars($editData['body']); ?></textarea>
                        <small class="text-muted">Use HTML formatting and placeholders like {name}, {email}, etc.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-white">Description</label>
                        <textarea name="description" class="form-control" rows="2"><?php echo htmlspecialchars($editData['description']); ?></textarea>
                    </div>

                    <div class="form-check mb-3">
                        <input type="checkbox" name="is_active" class="form-check-input" <?php echo $editData['is_active'] ? 'checked' : ''; ?>>
                        <label class="form-check-label text-white">Active</label>
                    </div>

                    <div class="mb-3">
                        <button type="submit" class="btn btn-success">Save Template</button>
                        <a href="email_templates.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="admin-card-secondary">
                        <h5 class="text-white mb-3">Available Placeholders</h5>
                        <div class="placeholder-list">
                            <?php foreach ($placeholders as $p): ?>
                            <div class="mb-2">
                                <code class="text-info"><?php echo $p['name']; ?></code>
                                <br><small class="text-muted"><?php echo $p['description']; ?></small>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<?php endif; ?>

<!-- Modal for Add New Template -->
<div class="modal fade" id="templateModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content bg-dark">
            <div class="modal-header border-secondary">
                <h5 class="modal-title text-white">Create New Email Template</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-white">Template Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g., Welcome Email" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-white">Email Subject</label>
                        <input type="text" name="subject" class="form-control" placeholder="Welcome {name}!" required>
                        <small class="text-muted">Use placeholders: {name}, {email}, {phone}, {company}, {date}, {site_name}</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-white">Email Body</label>
                        <textarea name="body" class="form-control" rows="8" placeholder="Hello {name},%0A%0AWelcome to our platform..." required></textarea>
                        <small class="text-muted">Use HTML for formatting and placeholders for personalization</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-white">Description</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="When to use this template..."></textarea>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" name="is_active" class="form-check-input" checked>
                        <label class="form-check-label text-white">Make Active</label>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Template</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.admin-card-secondary {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    padding: 20px;
}

.placeholder-list code {
    background: rgba(52, 211, 153, 0.2);
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 13px;
}
</style>

<?php require_once __DIR__ . '/layout-footer.php'; ?>
