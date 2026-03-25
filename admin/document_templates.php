<?php
require_once __DIR__ . '/auth.php';
adminCheck();
adminRequirePermission('templates');

$db = getDB();
$msg = '';
$error = '';

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $stmt = $db->prepare("DELETE FROM hr_document_templates WHERE id = ? AND is_default = 0");
        $stmt->execute([$id]);
        $msg = "Template deleted successfully.";
        adminLogAction('hr.template_delete', "Deleted template ID: $id");
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Handle Set Default
if (isset($_GET['set_default'])) {
    $id = (int)$_GET['set_default'];
    $type = $_GET['type'] ?? 'certificate';
    try {
        $db->beginTransaction();
        $db->prepare("UPDATE hr_document_templates SET is_default = 0 WHERE type = ?")->execute([$type]);
        $db->prepare("UPDATE hr_document_templates SET is_default = 1 WHERE id = ?")->execute([$id]);
        $db->commit();
        $msg = "Default template updated.";
    } catch (Exception $e) {
        $db->rollBack();
        $error = "Error: " . $e->getMessage();
    }
}

// Handle Form Submission (Add/Edit)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $name = trim($_POST['name']);
    $type = $_POST['type'];
    $category = $_POST['category'];
    $body_text = $_POST['body_text'];
    $styles = $_POST['styles'];
    $is_default = isset($_POST['is_default']) ? 1 : 0;

    try {
        if ($id > 0) {
            $stmt = $db->prepare("UPDATE hr_document_templates SET name = ?, type = ?, category = ?, body_text = ?, styles = ?, is_default = ? WHERE id = ?");
            $stmt->execute([$name, $type, $category, $body_text, $styles, $is_default, $id]);
            $msg = "Template updated successfully.";
        } else {
            $stmt = $db->prepare("INSERT INTO hr_document_templates (name, type, category, body_text, styles, is_default) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $type, $category, $body_text, $styles, $is_default]);
            $msg = "Template created successfully.";
        }

        if ($is_default) {
            $last_id = $id > 0 ? $id : $db->lastInsertId();
            $db->prepare("UPDATE hr_document_templates SET is_default = 0 WHERE type = ? AND id != ?")->execute([$type, $last_id]);
        }
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

$templates = $db->query("SELECT * FROM hr_document_templates ORDER BY type, name")->fetchAll();

$pageTitle = 'Document Templates — NexSoft Hub Admin';
$activePage = 'templates';
require_once __DIR__ . '/layout-header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h3 mb-0 text-white">Document Templates</h2>
    <button class="btn-admin-primary" data-bs-toggle="modal" data-bs-target="#templateModal" onclick="prepareAdd()">
        <i class="bi bi-plus-circle me-1"></i> Create New Template
    </button>
</div>

<?php if ($msg): ?><div class="admin-alert-success mb-3"><?php echo $msg; ?></div><?php endif; ?>
<?php if ($error): ?><div class="admin-alert-error mb-3"><?php echo $error; ?></div><?php endif; ?>

<div class="admin-card">
    <div class="admin-card-body p-0">
        <div class="table-responsive">
            <table class="table admin-table mb-0">
                <thead>
                    <tr>
                        <th>Template Name</th>
                        <th>Type</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($templates as $t): ?>
                    <tr>
                        <td>
                            <div class="fw-bold text-white"><?php echo htmlspecialchars($t['name']); ?></div>
                            <small class="text-muted"><?php echo substr(strip_tags($t['body_text']), 0, 50); ?>...</small>
                        </td>
                        <td><span class="badge bg-secondary"><?php echo ucfirst($t['type']); ?></span></td>
                        <td><span class="badge bg-info"><?php echo ucfirst($t['category']); ?></span></td>
                        <td>
                            <?php if ($t['is_default']): ?>
                                <span class="badge bg-success">Default</span>
                            <?php else: ?>
                                <a href="?set_default=<?php echo $t['id']; ?>&type=<?php echo $t['type']; ?>" class="btn btn-sm btn-outline-primary">Set Default</a>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-outline-info me-1" onclick='prepareEdit(<?php echo json_encode($t); ?>)'>
                                <i class="bi bi-pencil"></i>
                            </button>
                            <?php if (!$t['is_default']): ?>
                            <a href="?delete=<?php echo $t['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Protect this template?')">
                                <i class="bi bi-trash"></i>
                            </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="templateModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="POST" class="modal-content admin-card">
            <div class="modal-header border-bottom border-secondary">
                <h5 class="modal-title text-white" id="modalTitle">Create Template</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <?php echo adminCsrfField(); ?>
                <input type="hidden" name="id" id="edit_id">
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label text-muted">Template Name</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required placeholder="e.g. Modern Internship Certificate">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted">Document Type</label>
                        <select name="type" id="edit_type" class="form-control">
                            <option value="certificate">Certificate</option>
                            <option value="experience_letter">Experience Letter</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted">Applicable Category</label>
                        <select name="category" id="edit_category" class="form-control">
                            <option value="internship">Internship Only</option>
                            <option value="course">Course Only</option>
                            <option value="both">Both</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label text-muted">Body Text (HTML Supported)</label>
                        <div class="mb-2 small text-info">Placeholders: {{name}}, {{title}}, {{duration}}, {{date}}, {{docId}}, {{vCode}}</div>
                        <textarea name="body_text" id="edit_body" class="form-control" rows="5" required></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label text-muted">Custom CSS (Optional)</label>
                        <textarea name="styles" id="edit_styles" class="form-control" rows="3" placeholder=".cert-name { color: gold !important; }"></textarea>
                    </div>
                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_default" id="edit_is_default" value="1">
                            <label class="form-check-label text-white">Set as Default for this type</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top border-secondary">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn-admin-primary">Save Template</button>
            </div>
        </form>
    </div>
</div>

<script>
function prepareAdd() {
    document.getElementById('modalTitle').innerText = 'Create Template';
    document.getElementById('edit_id').value = '';
    document.getElementById('edit_name').value = '';
    document.getElementById('edit_type').value = 'certificate';
    document.getElementById('edit_category').value = 'both';
    document.getElementById('edit_body').value = '';
    document.getElementById('edit_styles').value = '';
    document.getElementById('edit_is_default').checked = false;
}

function prepareEdit(template) {
    document.getElementById('modalTitle').innerText = 'Edit Template';
    document.getElementById('edit_id').value = template.id;
    document.getElementById('edit_name').value = template.name;
    document.getElementById('edit_type').value = template.type;
    document.getElementById('edit_category').value = template.category;
    document.getElementById('edit_body').value = template.body_text;
    document.getElementById('edit_styles').value = template.styles || '';
    document.getElementById('edit_is_default').checked = template.is_default == 1;
    
    // Open modal
    new bootstrap.Modal(document.getElementById('templateModal')).show();
}
</script>

<?php require_once __DIR__ . '/layout-footer.php'; ?>
