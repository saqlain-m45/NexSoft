<?php
require_once __DIR__ . '/auth.php';
adminCheck();
adminRequirePermission('internships');

$db = getDB();
$action = $_GET['action'] ?? 'list';
$msg = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['action'] === 'add' || $_POST['action'] === 'edit') {
        $id          = (int)($_POST['id'] ?? 0);
        $title       = trim($_POST['title'] ?? '');
        $category    = $_POST['category'] ?? 'internship';
        $description = trim($_POST['description'] ?? '');
        $duration    = trim($_POST['duration'] ?? '');
        $status      = $_POST['status'] ?? 'active';

        if ($title === '') {
            $error = 'Title is required.';
        } else {
            if ($id > 0) {
                $stmt = $db->prepare("UPDATE hr_internships SET title=?, category=?, description=?, duration=?, status=? WHERE id=?");
                $stmt->execute([$title, $category, $description, $duration, $status, $id]);
                adminLogAction('hr.internship_update', 'Updated internship ID: ' . $id);
                $msg = 'Internship updated successfully.';
            } else {
                $stmt = $db->prepare("INSERT INTO hr_internships (title, category, description, duration, status) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$title, $category, $description, $duration, $status]);
                adminLogAction('hr.internship_add', 'Added new internship: ' . $title);
                $msg = 'Internship added successfully.';
            }
            $action = 'list';
        }
    } elseif ($_POST['action'] === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $db->prepare("DELETE FROM hr_internships WHERE id=?")->execute([$id]);
        adminLogAction('hr.internship_delete', 'Deleted internship ID: ' . $id);
        $msg = 'Internship deleted.';
    }
}

$editData = null;
if ($action === 'edit') {
    $id = (int)($_GET['id'] ?? 0);
    $stmt = $db->prepare("SELECT * FROM hr_internships WHERE id=?");
    $stmt->execute([$id]);
    $editData = $stmt->fetch();
    if (!$editData) $action = 'list';
}

$items = $db->query("SELECT * FROM hr_internships ORDER BY created_at DESC")->fetchAll();

$pageTitle = 'Internships/Courses — NexSoft Hub Admin';
$activePage = 'internships';
require_once __DIR__ . '/layout-header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h4 mb-0">Internships & Courses</h2>
    <?php if ($action === 'list'): ?>
    <a href="?action=add" class="btn-admin-primary"><i class="bi bi-plus-circle me-1"></i> Add New</a>
    <?php else: ?>
    <a href="?action=list" class="btn-admin-secondary"><i class="bi bi-arrow-left me-1"></i> Back to List</a>
    <?php endif; ?>
</div>

<?php if ($msg): ?>
<div class="admin-alert-success mb-3"><i class="bi bi-check-circle-fill me-2"></i><?php echo $msg; ?></div>
<?php endif; ?>
<?php if ($error): ?>
<div class="admin-alert-error mb-3"><i class="bi bi-exclamation-circle-fill me-2"></i><?php echo $error; ?></div>
<?php endif; ?>

<?php if ($action === 'add' || $action === 'edit'): ?>
<div class="admin-card">
    <div class="admin-card-header">
        <span class="admin-card-title"><?php echo $action === 'edit' ? 'Edit' : 'Add New'; ?> Internship/Course</span>
    </div>
    <div class="admin-card-body">
        <form method="POST" class="admin-form">
            <?php echo adminCsrfField(); ?>
            <input type="hidden" name="action" value="<?php echo $action; ?>">
            <input type="hidden" name="id" value="<?php echo $editData['id'] ?? ''; ?>">
            
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($editData['title'] ?? ''); ?>" required placeholder="e.g. Flutter Development Internship">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-control">
                        <option value="internship" <?php echo ($editData['category'] ?? '') === 'internship' ? 'selected' : ''; ?>>Internship</option>
                        <option value="course" <?php echo ($editData['category'] ?? '') === 'course' ? 'selected' : ''; ?>>Course</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Duration</label>
                    <input type="text" name="duration" class="form-control" value="<?php echo htmlspecialchars($editData['duration'] ?? ''); ?>" placeholder="e.g. 3 Months / 12 Weeks">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="active" <?php echo ($editData['status'] ?? '') === 'active' ? 'selected' : ''; ?>>Active (Registration Open)</option>
                        <option value="closed" <?php echo ($editData['status'] ?? '') === 'closed' ? 'selected' : ''; ?>>Closed (Registration Finished)</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="4"><?php echo htmlspecialchars($editData['description'] ?? ''); ?></textarea>
                </div>
                <div class="col-12 pt-2">
                    <button type="submit" class="btn-admin-primary">
                        <i class="bi bi-save me-1"></i> <?php echo $action === 'edit' ? 'Update' : 'Save'; ?> Internship
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php else: ?>
<div class="admin-card">
    <div class="table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Duration</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($items)): ?>
                <tr><td colspan="6" class="text-center py-4">No internships or courses found.</td></tr>
                <?php else: ?>
                <?php foreach ($items as $item): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($item['title']); ?></strong></td>
                    <td><span class="badge-blue"><?php echo ucfirst($item['category']); ?></span></td>
                    <td><?php echo htmlspecialchars($item['duration'] ?: '—'); ?></td>
                    <td>
                        <span class="<?php echo $item['status'] === 'active' ? 'badge-green' : 'badge-orange'; ?>">
                            <?php echo ucfirst($item['status']); ?>
                        </span>
                    </td>
                    <td><?php echo date('M d, Y', strtotime($item['created_at'])); ?></td>
                    <td>
                        <div class="d-flex gap-2">
                            <a href="intern_applications.php?id=<?php echo $item['id']; ?>" class="btn-action btn-view" title="View Applications"><i class="bi bi-people"></i></a>
                            <a href="?action=edit&id=<?php echo $item['id']; ?>" class="btn-action btn-view"><i class="bi bi-pencil"></i></a>
                            <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this internship?');">
                                <?php echo adminCsrfField(); ?>
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                <button type="submit" class="btn-action btn-delete"><i class="bi bi-trash"></i></button>
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
