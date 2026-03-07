<?php
require_once __DIR__ . '/auth.php';
adminCheck();
adminRequirePermission('projects');

$db     = getDB();
$action = $_GET['action'] ?? 'list';
$msg    = '';
$error  = '';

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $actionPost = $_POST['action'] ?? '';

    if ($actionPost === 'add' || $actionPost === 'edit') {
        $title = trim($_POST['title'] ?? '');
        $desc  = trim($_POST['description'] ?? '');
        $link  = trim($_POST['link'] ?? '');
        $imageName = null;

        // Handle image upload
        if (!empty($_FILES['image']['name'])) {
            $allowedTypes = ['image/jpeg','image/jpg','image/png','image/gif','image/webp'];
            if (in_array($_FILES['image']['type'], $allowedTypes) && $_FILES['image']['size'] < 5000000) {
                $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $imageName = uniqid('proj_') . '.' . $ext;
                $uploadDir = __DIR__ . '/../assets/uploads/projects/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $imageName);
            } else {
                $error = 'Invalid image. Use JPG/PNG/GIF/WebP under 5MB.';
            }
        }

        if (empty($title) || empty($desc)) {
            $error = 'Title and description are required.';
        } elseif (empty($error)) {
            if ($actionPost === 'add') {
                $stmt = $db->prepare("INSERT INTO projects (title, description, image, link) VALUES (?, ?, ?, ?)");
                $stmt->execute([$title, $desc, $imageName, $link]);
                $msg = 'Project added successfully!';
            } else {
                $id = (int)($_POST['id'] ?? 0);
                if ($imageName) {
                    $stmt = $db->prepare("UPDATE projects SET title=?, description=?, image=?, link=? WHERE id=?");
                    $stmt->execute([$title, $desc, $imageName, $link, $id]);
                } else {
                    $stmt = $db->prepare("UPDATE projects SET title=?, description=?, link=? WHERE id=?");
                    $stmt->execute([$title, $desc, $link, $id]);
                }
                $msg = 'Project updated successfully!';
            }
            $action = 'list';
        }
    } elseif ($actionPost === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $proj = $db->prepare("SELECT image FROM projects WHERE id = ?");
        $proj->execute([$id]);
        $p = $proj->fetch();
        if ($p && $p['image']) {
            $imgPath = __DIR__ . '/../assets/uploads/projects/' . $p['image'];
            if (file_exists($imgPath)) unlink($imgPath);
        }
        $db->prepare("DELETE FROM projects WHERE id = ?")->execute([$id]);
        $msg = 'Project deleted.';
    }
}

// Fetch project for edit
$editProject = null;
if ($action === 'edit') {
    $id = (int)($_GET['id'] ?? 0);
    $s = $db->prepare("SELECT * FROM projects WHERE id = ?");
    $s->execute([$id]);
    $editProject = $s->fetch();
    if (!$editProject) { $action = 'list'; }
}

// Fetch all projects for list
$projects = $db->query("SELECT * FROM projects ORDER BY created_at DESC")->fetchAll();

$pageTitle  = 'Manage Projects — NexSoft Hub Admin';
$activePage = 'projects';
require_once __DIR__ . '/layout-header.php';
?>

<?php if (!empty($msg)): ?>
<div class="admin-alert-success mb-3"><i class="bi bi-check-circle me-2"></i><?php echo htmlspecialchars($msg); ?></div>
<?php endif; ?>
<?php if (!empty($error)): ?>
<div class="admin-alert-error mb-3"><i class="bi bi-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<?php if ($action === 'add' || $action === 'edit'): ?>
<!-- ADD / EDIT FORM -->
<div class="admin-card">
    <div class="admin-card-header">
        <span class="admin-card-title">
            <i class="bi bi-<?php echo $action==='add'?'plus-circle':'pencil'; ?> me-2" style="color:var(--secondary);"></i>
            <?php echo $action==='add' ? 'Add New Project' : 'Edit Project'; ?>
        </span>
        <a href="<?php echo adminUrl('projects.php'); ?>" class="btn-action btn-view">
            <i class="bi bi-arrow-left"></i> Back to List
        </a>
    </div>
    <div class="admin-card-body">
        <form method="POST" enctype="multipart/form-data" class="admin-form">
            <input type="hidden" name="action" value="<?php echo $action; ?>">
            <?php if ($action === 'edit'): ?>
            <input type="hidden" name="id" value="<?php echo $editProject['id']; ?>">
            <?php endif; ?>

            <div class="row g-3">
                <div class="col-12">
                    <label>Project Title *</label>
                    <input type="text" name="title" class="form-control" required
                           value="<?php echo htmlspecialchars($editProject['title'] ?? $_POST['title'] ?? ''); ?>"
                           placeholder="e.g. E-Commerce Platform">
                </div>
                <div class="col-12">
                    <label>Description *</label>
                    <textarea name="description" class="form-control" rows="4" required
                              placeholder="Describe the project, technologies used, and outcomes..."><?php echo htmlspecialchars($editProject['description'] ?? $_POST['description'] ?? ''); ?></textarea>
                </div>
                <div class="col-md-6">
                    <label>Project Image (JPG/PNG/WebP, max 5MB)</label>
                    <?php if (!empty($editProject['image'])): ?>
                    <div class="mb-2">
                        <img src="/NexSoft/assets/uploads/projects/<?php echo htmlspecialchars($editProject['image']); ?>"
                             class="img-preview" alt="Current image">
                        <small style="color:var(--text-muted);display:block;margin-top:4px;">Current image (leave empty to keep)</small>
                    </div>
                    <?php endif; ?>
                    <input type="file" name="image" class="form-control" accept="image/*">
                </div>
                <div class="col-md-6">
                    <label>Project URL (optional)</label>
                    <input type="url" name="link" class="form-control"
                           value="<?php echo htmlspecialchars($editProject['link'] ?? $_POST['link'] ?? ''); ?>"
                           placeholder="https://...">
                </div>
                <div class="col-12 pt-2">
                    <button type="submit" class="btn-admin-primary">
                        <i class="bi bi-<?php echo $action==='add'?'plus':'save'; ?>"></i>
                        <?php echo $action==='add' ? 'Add Project' : 'Save Changes'; ?>
                    </button>
                    <a href="<?php echo adminUrl('projects.php'); ?>" class="btn-admin-secondary ms-2">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>

<?php else: ?>
<!-- PROJECTS LIST -->
<div class="admin-card">
    <div class="admin-card-header">
        <span class="admin-card-title"><i class="bi bi-folder me-2" style="color:var(--secondary);"></i>All Projects (<?php echo count($projects); ?>)</span>
        <a href="<?php echo adminUrl('projects.php?action=add'); ?>" class="btn-admin-primary">
            <i class="bi bi-plus"></i> Add Project
        </a>
    </div>
    <div class="table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Image</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Link</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($projects)): ?>
                <tr><td colspan="7" style="text-align:center;padding:3rem;color:var(--text-muted);">
                    <i class="bi bi-folder2-open" style="font-size:2rem;display:block;margin-bottom:0.5rem;"></i>
                    No projects yet. <a href="<?php echo adminUrl('projects.php?action=add'); ?>" style="color:var(--secondary);">Add your first project</a>.
                </td></tr>
                <?php else: ?>
                <?php foreach($projects as $i => $p): ?>
                <tr>
                    <td style="color:var(--text-muted);"><?php echo $i+1; ?></td>
                    <td>
                        <?php if (!empty($p['image']) && file_exists(__DIR__ . '/../assets/uploads/projects/' . $p['image'])): ?>
                        <img src="/NexSoft/assets/uploads/projects/<?php echo htmlspecialchars($p['image']); ?>"
                             class="img-preview" alt="<?php echo htmlspecialchars($p['title']); ?>">
                        <?php else: ?>
                        <div class="img-placeholder-thumb"><i class="bi bi-image"></i></div>
                        <?php endif; ?>
                    </td>
                    <td><strong><?php echo htmlspecialchars($p['title']); ?></strong></td>
                    <td style="color:var(--text-muted);max-width:250px;"><?php echo htmlspecialchars(mb_strimwidth($p['description'], 0, 80, '...')); ?></td>
                    <td>
                        <?php if(!empty($p['link'])): ?>
                        <a href="<?php echo htmlspecialchars($p['link']); ?>" target="_blank"
                           style="color:var(--secondary);font-size:0.82rem;display:flex;align-items:center;gap:3px;">
                            <i class="bi bi-box-arrow-up-right"></i> View
                        </a>
                        <?php else: ?>
                        <span style="color:var(--text-light);">—</span>
                        <?php endif; ?>
                    </td>
                    <td><span class="badge-blue"><?php echo date('M d, Y', strtotime($p['created_at'])); ?></span></td>
                    <td>
                        <div style="display:flex;gap:6px;">
                            <a href="<?php echo adminUrl('projects.php?action=edit&id=' . $p['id']); ?>" class="btn-action btn-edit">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                                <button type="submit" class="btn-action btn-delete confirm-delete">
                                    <i class="bi bi-trash"></i> Delete
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
