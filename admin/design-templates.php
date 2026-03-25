<?php
require_once __DIR__ . '/auth.php';
adminCheck();
adminRequirePermission('templates');

$db = getDB();
$msg = '';
$error = '';
$action = $_GET['action'] ?? 'list';
$template_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $db->prepare("DELETE FROM design_templates WHERE id = ?")->execute([$id]);
        $msg = "Template deleted successfully.";
        adminLogAction('template.design_delete', "Deleted design template ID: $id");
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
    header("Location: design-templates.php?msg=" . urlencode($msg));
    exit;
}

// Handle Set Default
if (isset($_GET['set_default'])) {
    $id = (int)$_GET['set_default'];
    $type = $_GET['type'] ?? 'certificate';
    try {
        $db->beginTransaction();
        $db->prepare("UPDATE design_templates SET is_default = 0 WHERE type = ?")->execute([$type]);
        $db->prepare("UPDATE design_templates SET is_default = 1 WHERE id = ?")->execute([$id]);
        $db->commit();
        $msg = "Default template updated.";
    } catch (Exception $e) {
        $db->rollBack();
        $error = "Error: " . $e->getMessage();
    }
    header("Location: design-templates.php?msg=" . urlencode($msg));
    exit;
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'edit') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $name = trim($_POST['name']);
    $type = $_POST['type'];
    $description = trim($_POST['description']);
    $logo_position = $_POST['logo_position'] ?? 'top-center';
    $logo_width = (int)($_POST['logo_width'] ?? 150);
    $header_html = $_POST['header_html'];
    $body_html = $_POST['body_html'];
    $footer_html = $_POST['footer_html'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $is_default = isset($_POST['is_default']) ? 1 : 0;

    $logo_image = null;
    if (!empty($_FILES['logo_image']['name'])) {
        $logo_image = handleTemplateLogoUpload($_FILES['logo_image']);
        if (!$logo_image) {
            $error = "Failed to upload logo image.";
        }
    }

    if (!$error && $name && $type) {
        try {
            if ($id > 0) {
                $stmt = $db->prepare("
                    UPDATE design_templates SET 
                    name = ?, type = ?, description = ?, logo_position = ?, 
                    logo_width = ?, header_html = ?, body_html = ?, footer_html = ?, 
                    is_active = ?, is_default = ?
                    WHERE id = ?
                ");
                if ($logo_image) {
                    $db->prepare("UPDATE design_templates SET logo_image = ? WHERE id = ?")->execute([$logo_image, $id]);
                }
                $stmt->execute([$name, $type, $description, $logo_position, $logo_width, $header_html, $body_html, $footer_html, $is_active, $is_default, $id]);
                $msg = "Template updated successfully.";
            } else {
                $stmt = $db->prepare("
                    INSERT INTO design_templates 
                    (name, type, description, logo_image, logo_position, logo_width, header_html, body_html, footer_html, is_active, is_default, created_by) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([$name, $type, $description, $logo_image, $logo_position, $logo_width, $header_html, $body_html, $footer_html, $is_active, $is_default, $_SESSION['user_id'] ?? 1]);
                $msg = "Template created successfully.";
            }

            if ($is_default) {
                $last_id = $id > 0 ? $id : $db->lastInsertId();
                $db->prepare("UPDATE design_templates SET is_default = 0 WHERE type = ? AND id != ?")->execute([$type, $last_id]);
            }

            adminLogAction('template.design_save', $id > 0 ? "Updated template ID: $id" : "Created new design template");
            header("Location: design-templates.php?msg=" . urlencode($msg));
            exit;
        } catch (Exception $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}

// Fetch data
$templates = [];
$current_template = null;
$text_styles = $db->query("SELECT * FROM text_styles ORDER BY style_type, name")->fetchAll();
$logos = $db->query("SELECT * FROM template_logos WHERE is_active = 1 ORDER BY name")->fetchAll();

if ($action === 'list') {
    $templates = $db->query("SELECT * FROM design_templates ORDER BY type, name")->fetchAll();
} elseif ($action === 'edit' && $template_id) {
    $current_template = $db->prepare("SELECT * FROM design_templates WHERE id = ?")->execute([$template_id])->fetch();
}

$pageTitle = 'Design Templates — Letters & Certificates — NexSoft Hub Admin';
$activePage = 'design-templates';
require_once __DIR__ . '/layout-header.php';

function handleTemplateLogoUpload($file) {
    $upload_dir = __DIR__ . '/../assets/uploads/logos/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($file['type'], $allowed_types)) {
        return null;
    }
    
    $filename = 'logo_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
    $target = $upload_dir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $target)) {
        return 'assets/uploads/logos/' . $filename;
    }
    return null;
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 mb-0 text-white">Design Templates</h2>
        <p class="text-muted small mb-0">Create and manage letter and certificate templates.</p>
    </div>
    <?php if ($action === 'list'): ?>
    <a href="design-templates.php?action=edit" class="btn-admin-primary">
        <i class="bi bi-plus-circle me-1"></i> Create New Template
    </a>
    <?php else: ?>
    <a href="design-templates.php" class="btn-admin-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to Templates
    </a>
    <?php endif; ?>
</div>

<?php if ($msg): ?>
<div class="admin-alert-success mb-3"><i class="bi bi-check-circle-fill me-2"></i><?php echo htmlspecialchars($msg); ?></div>
<?php endif; ?>
<?php if ($error): ?>
<div class="admin-alert-error mb-3"><i class="bi bi-exclamation-circle-fill me-2"></i><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<?php if ($action === 'list'): ?>
<!-- Templates List View -->
<div class="admin-card">
    <div class="table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Template Name</th>
                    <th>Type</th>
                    <th>Logo</th>
                    <th>Status</th>
                    <th>Default</th>
                    <th>Created</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($templates)): ?>
                <tr><td colspan="7" class="text-center py-4">No templates created yet.</td></tr>
                <?php else: ?>
                <?php foreach ($templates as $t): ?>
                <tr>
                    <td>
                        <strong><?php echo htmlspecialchars($t['name']); ?></strong>
                        <?php if ($t['description']): ?>
                        <br><span class="text-muted small"><?php echo htmlspecialchars(substr($t['description'], 0, 50)); ?></span>
                        <?php endif; ?>
                    </td>
                    <td><span class="badge-blue"><?php echo ucfirst($t['type']); ?></span></td>
                    <td>
                        <?php if ($t['logo_image']): ?>
                        <i class="bi bi-image text-success"></i> <?php echo basename($t['logo_image']); ?>
                        <?php else: ?>
                        <span class="text-muted small">None</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="<?php echo $t['is_active'] ? 'badge-green' : 'badge-orange'; ?>">
                            <?php echo $t['is_active'] ? 'Active' : 'Inactive'; ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($t['is_default']): ?>
                        <i class="bi bi-star-fill" style="color: #ffc107;"></i>
                        <?php else: ?>
                        <a href="design-templates.php?set_default=<?php echo $t['id']; ?>&type=<?php echo $t['type']; ?>" title="Set as default" style="color: #999;">
                            <i class="bi bi-star"></i>
                        </a>
                        <?php endif; ?>
                    </td>
                    <td><?php echo date('M d, Y', strtotime($t['created_at'])); ?></td>
                    <td style="text-align: right;">
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="design-templates.php?action=edit&id=<?php echo $t['id']; ?>" class="btn-action btn-edit" title="Edit Template">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <a href="design-templates.php?action=preview&id=<?php echo $t['id']; ?>" class="btn-action" style="color: #0066cc;" title="Preview">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="design-templates.php?delete=<?php echo $t['id']; ?>" class="btn-action" style="color: #ea580c;" onclick="return confirm('Delete this template?');" title="Delete">
                                <i class="bi bi-trash"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php else: ?>
<!-- Template Edit Form -->
<div class="admin-card">
    <div class="admin-card-body">
        <form method="POST" enctype="multipart/form-data" id="templateForm">
            <?php echo adminCsrfField(); ?>
            <input type="hidden" name="id" value="<?php echo $current_template['id'] ?? ''; ?>">

            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Template Name</label>
                    <input type="text" class="form-control" name="name" required 
                           value="<?php echo htmlspecialchars($current_template['name'] ?? ''); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Template Type</label>
                    <select class="form-control" name="type" required>
                        <option value="certificate" <?php echo ($current_template['type'] ?? '') === 'certificate' ? 'selected' : ''; ?>>Certificate</option>
                        <option value="letter" <?php echo ($current_template['type'] ?? '') === 'letter' ? 'selected' : ''; ?>>Letter</option>
                        <option value="appreciation" <?php echo ($current_template['type'] ?? '') === 'appreciation' ? 'selected' : ''; ?>>Appreciation Card</option>
                        <option value="credentials" <?php echo ($current_template['type'] ?? '') === 'credentials' ? 'selected' : ''; ?>>Credentials</option>
                    </select>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">Description</label>
                <textarea class="form-control" name="description" rows="2"><?php echo htmlspecialchars($current_template['description'] ?? ''); ?></textarea>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Logo File</label>
                    <input type="file" class="form-control" name="logo_image" accept="image/*">
                    <?php if ($current_template && $current_template['logo_image']): ?>
                    <small class="text-muted d-block mt-2">Current: <strong><?php echo basename($current_template['logo_image']); ?></strong></small>
                    <?php endif; ?>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Logo Position</label>
                    <select class="form-control" name="logo_position">
                        <option value="top-center" <?php echo ($current_template['logo_position'] ?? 'top-center') === 'top-center' ? 'selected' : ''; ?>>Top Center</option>
                        <option value="top-left" <?php echo ($current_template['logo_position'] ?? '') === 'top-left' ? 'selected' : ''; ?>>Top Left</option>
                        <option value="top-right" <?php echo ($current_template['logo_position'] ?? '') === 'top-right' ? 'selected' : ''; ?>>Top Right</option>
                        <option value="center" <?php echo ($current_template['logo_position'] ?? '') === 'center' ? 'selected' : ''; ?>>Center</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Logo Width (px)</label>
                    <input type="number" class="form-control" name="logo_width" min="50" max="400" 
                           value="<?php echo $current_template['logo_width'] ?? 150; ?>">
                </div>
            </div>

            <!-- Header Section -->
            <div class="mb-4">
                <label class="form-label fw-bold d-flex align-items-center gap-2">
                    <i class="bi bi-type"></i> Header Content
                    <small class="text-muted">(Company name, title, etc.)</small>
                </label>
                <div class="editor-toolbar mb-2">
                    <button type="button" class="btn-editor" onclick="formatText('bold')" title="Bold"><i class="bi bi-type-bold"></i></button>
                    <button type="button" class="btn-editor" onclick="formatText('italic')" title="Italic"><i class="bi bi-type-italic"></i></button>
                    <button type="button" class="btn-editor" onclick="formatText('underline')" title="Underline"><i class="bi bi-type-underline"></i></button>
                    <div class="divider"></div>
                    <button type="button" class="btn-editor" onclick="formatText('formatBlock', 'h1')" title="Heading 1">H1</button>
                    <button type="button" class="btn-editor" onclick="formatText('formatBlock', 'h2')" title="Heading 2">H2</button>
                    <button type="button" class="btn-editor" onclick="formatText('formatBlock', 'p')" title="Paragraph">P</button>
                    <div class="divider"></div>
                    <button type="button" class="btn-editor" onclick="formatText('insertUnorderedList')" title="Bullet List"><i class="bi bi-list-ul"></i></button>
                    <button type="button" class="btn-editor" onclick="formatText('justifyLeft')" title="Align Left"><i class="bi bi-text-left"></i></button>
                    <button type="button" class="btn-editor" onclick="formatText('justifyCenter')" title="Align Center"><i class="bi bi-text-center"></i></button>
                    <button type="button" class="btn-editor" onclick="formatText('justifyRight')" title="Align Right"><i class="bi bi-text-right"></i></button>
                </div>
                <div class="editor-container" contenteditable="true" id="headerEditor" class="form-control" style="min-height: 100px; border: 2px solid #ddd; padding: 10px; overflow-y: auto; max-height: 200px;"><?php echo $current_template['header_html'] ?? '<p>Certificate of Achievement</p>'; ?></div>
                <textarea name="header_html" id="headerHidden" style="display:none;"></textarea>
            </div>

            <!-- Body Section -->
            <div class="mb-4">
                <label class="form-label fw-bold d-flex align-items-center gap-2">
                    <i class="bi bi-file-text"></i> Body Content
                    <small class="text-muted">(Main certificate text, recipient info, etc.)</small>
                </label>
                <div class="editor-toolbar mb-2">
                    <button type="button" class="btn-editor" onclick="formatText('bold')" title="Bold"><i class="bi bi-type-bold"></i></button>
                    <button type="button" class="btn-editor" onclick="formatText('italic')" title="Italic"><i class="bi bi-type-italic"></i></button>
                    <button type="button" class="btn-editor" onclick="formatText('underline')" title="Underline"><i class="bi bi-type-underline"></i></button>
                    <div class="divider"></div>
                    <button type="button" class="btn-editor" onclick="insertVariable()"  title="Insert Variable"><i class="bi bi-braces"></i></button>
                    <div class="divider"></div>
                    <input type="color" class="color-picker" onchange="formatText('foreColor', this.value)" title="Text Color">
                    <input type="color" class="color-picker" onchange="formatText('backColor', this.value)" title="Background Color">
                </div>
                <div class="editor-container" contenteditable="true" id="bodyEditor" class="form-control" style="min-height: 150px; border: 2px solid #ddd; padding: 10px; overflow-y: auto; max-height: 300px;"><?php echo $current_template['body_html'] ?? '<p><strong>[Recipient Name]</strong> has successfully completed the course...</p>'; ?></div>
                <small class="text-muted d-block mt-2">Variables: [Recipient Name], [Date], [Certificate ID], [Course Name], [Duration]</small>
                <textarea name="body_html" id="bodyHidden" style="display:none;"></textarea>
            </div>

            <!-- Footer Section -->
            <div class="mb-4">
                <label class="form-label fw-bold d-flex align-items-center gap-2">
                    <i class="bi bi-footer"></i> Footer Content
                    <small class="text-muted">(Signatures, dates, footer text)</small>
                </label>
                <div class="editor-toolbar mb-2">
                    <button type="button" class="btn-editor" onclick="formatText('bold')" title="Bold"><i class="bi bi-type-bold"></i></button>
                    <button type="button" class="btn-editor" onclick="formatText('italic')" title="Italic"><i class="bi bi-type-italic"></i></button>
                    <div class="divider"></div>
                    <button type="button" class="btn-editor" onclick="formatText('justifyLeft')" title="Align Left"><i class="bi bi-text-left"></i></button>
                    <button type="button" class="btn-editor" onclick="formatText('justifyCenter')" title="Align Center"><i class="bi bi-text-center"></i></button>
                    <button type="button" class="btn-editor" onclick="formatText('justifyRight')" title="Align Right"><i class="bi bi-text-right"></i></button>
                </div>
                <div class="editor-container" contenteditable="true" id="footerEditor" class="form-control" style="min-height: 80px; border: 2px solid #ddd; padding: 10px; overflow-y: auto; max-height: 200px;"><?php echo $current_template['footer_html'] ?? '<p>Director: __________ | Date: __________</p>'; ?></div>
                <textarea name="footer_html" id="footerHidden" style="display:none;"></textarea>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" 
                               <?php echo ($current_template['is_active'] ?? 1) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="is_active">
                            <strong>Active Template</strong>
                        </label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="is_default" name="is_default" 
                               <?php echo ($current_template['is_default'] ?? 0) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="is_default">
                            <strong>Set as Default</strong>
                        </label>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn-admin-primary">
                    <i class="bi bi-check-circle me-1"></i>
                    <?php echo ($current_template ? 'Update' : 'Create') ?> Template
                </button>
                <a href="design-templates.php" class="btn-admin-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<style>
.editor-toolbar {
    display: flex;
    gap: 5px;
    padding: 8px;
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    flex-wrap: wrap;
    align-items: center;
}

.btn-editor {
    padding: 6px 12px;
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    transition: all 0.2s;
}

.btn-editor:hover {
    background: #e9ecef;
    border-color: #adb5bd;
}

.btn-editor:active {
    background: #0066cc;
    color: white;
    border-color: #0066cc;
}

.divider {
    width: 1px;
    height: 24px;
    background: #dee2e6;
    margin: 0 5px;
}

.editor-container {
    background: white;
    border-radius: 4px;
    font-family: 'Courier New', monospace;
}

.editor-container:focus {
    outline: 2px solid #0066cc;
}

.color-picker {
    width: 40px;
    height: 36px;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    cursor: pointer;
    padding: 2px;
}
</style>

<script>
function formatText(command, value = null) {
    const editor = document.activeElement;
    if (editor.classList.contains('editor-container')) {
        document.execCommand(command, false, value);
        editor.focus();
    }
}

function insertVariable() {
    const variables = [
        '[Recipient Name]',
        '[Date]',
        '[Certificate ID]',
        '[Course Name]',
        '[Duration]',
        '[Grade]',
        '[Instructor Name]',
        '[Issue Date]'
    ];
    
    const selected = prompt('Select or type a variable:\n' + variables.join('\n'));
    if (selected) {
        document.execCommand('insertText', false, selected);
    }
}

// Sync editors with hidden fields on submit
document.getElementById('templateForm').addEventListener('submit', function() {
    document.getElementById('headerHidden').value = document.getElementById('headerEditor').innerHTML;
    document.getElementById('bodyHidden').value = document.getElementById('bodyEditor').innerHTML;
    document.getElementById('footerHidden').value = document.getElementById('footerEditor').innerHTML;
});
</script>

<?php endif; ?>

<?php require_once __DIR__ . '/layout-footer.php'; ?>
