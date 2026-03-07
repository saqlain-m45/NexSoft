<?php
require_once __DIR__ . '/auth.php';
adminCheck();
adminRequirePermission('team');

$db     = getDB();
$action = $_GET['action'] ?? 'list';
$msg    = '';
$error  = '';

// Auto-create the table if it doesn't exist yet
$db->exec("CREATE TABLE IF NOT EXISTS `team_members` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `designation` varchar(255) NOT NULL,
  `bio` text DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $actionPost = $_POST['action'] ?? '';

    if ($actionPost === 'add' || $actionPost === 'edit') {
        $name        = trim($_POST['name'] ?? '');
        $designation = trim($_POST['designation'] ?? '');
        $bio         = trim($_POST['bio'] ?? '');
        $sortOrder   = (int)($_POST['sort_order'] ?? 0);
        $photoName   = null;

        // Handle photo upload
        if (!empty($_FILES['photo']['name'])) {
            $allowed = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
            if (in_array($_FILES['photo']['type'], $allowed) && $_FILES['photo']['size'] < 5000000) {
                $ext       = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
                $photoName = uniqid('team_') . '.' . $ext;
                $uploadDir = __DIR__ . '/../assets/uploads/team/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                move_uploaded_file($_FILES['photo']['tmp_name'], $uploadDir . $photoName);
            } else {
                $error = 'Invalid image. Use JPG/PNG/WebP under 5 MB.';
            }
        }

        if (empty($name) || empty($designation)) {
            $error = 'Name and designation are required.';
        } elseif (empty($error)) {
            if ($actionPost === 'add') {
                $stmt = $db->prepare(
                    "INSERT INTO team_members (name, designation, bio, photo, sort_order) VALUES (?, ?, ?, ?, ?)"
                );
                $stmt->execute([$name, $designation, $bio, $photoName, $sortOrder]);
                $msg = 'Team member added successfully!';
            } else {
                $id = (int)($_POST['id'] ?? 0);
                if ($photoName) {
                    // Delete old photo
                    $old = $db->prepare("SELECT photo FROM team_members WHERE id = ?");
                    $old->execute([$id]);
                    $oldPhoto = $old->fetchColumn();
                    if ($oldPhoto) {
                        $oldPath = __DIR__ . '/../assets/uploads/team/' . $oldPhoto;
                        if (file_exists($oldPath)) unlink($oldPath);
                    }
                    $stmt = $db->prepare(
                        "UPDATE team_members SET name=?, designation=?, bio=?, photo=?, sort_order=? WHERE id=?"
                    );
                    $stmt->execute([$name, $designation, $bio, $photoName, $sortOrder, $id]);
                } else {
                    $stmt = $db->prepare(
                        "UPDATE team_members SET name=?, designation=?, bio=?, sort_order=? WHERE id=?"
                    );
                    $stmt->execute([$name, $designation, $bio, $sortOrder, $id]);
                }
                $msg = 'Team member updated!';
            }
            $action = 'list';
        }

    } elseif ($actionPost === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $s  = $db->prepare("SELECT photo FROM team_members WHERE id = ?");
        $s->execute([$id]);
        $photoFile = $s->fetchColumn();
        if ($photoFile) {
            $p = __DIR__ . '/../assets/uploads/team/' . $photoFile;
            if (file_exists($p)) unlink($p);
        }
        $db->prepare("DELETE FROM team_members WHERE id = ?")->execute([$id]);
        $msg = 'Team member deleted.';
    }
}

// Always re-fetch members (after any POST mutations)
$members = $db->query("SELECT * FROM team_members ORDER BY sort_order ASC, id ASC")->fetchAll();

// Fetch member for edit
$editMember = null;
if ($action === 'edit') {
    $id = (int)($_GET['id'] ?? 0);
    $s  = $db->prepare("SELECT * FROM team_members WHERE id = ?");
    $s->execute([$id]);
    $editMember = $s->fetch();
    if (!$editMember) $action = 'list';
}


$pageTitle  = 'Manage Team — NexSoft Hub Admin';
$activePage = 'team';
require_once __DIR__ . '/layout-header.php';
?>

<?php if (!empty($msg)): ?>
<div class="admin-alert-success mb-3"><i class="bi bi-check-circle me-2"></i><?php echo htmlspecialchars($msg); ?></div>
<?php endif; ?>
<?php if (!empty($error)): ?>
<div class="admin-alert-error mb-3"><i class="bi bi-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<?php if ($action === 'add' || $action === 'edit'): ?>
<!-- ===== ADD / EDIT FORM ===== -->
<div class="admin-card">
    <div class="admin-card-header">
        <span class="admin-card-title">
            <i class="bi bi-<?php echo $action === 'add' ? 'person-plus' : 'pencil'; ?> me-2" style="color:var(--secondary);"></i>
            <?php echo $action === 'add' ? 'Add Team Member' : 'Edit Team Member'; ?>
        </span>
        <a href="<?php echo adminUrl('team.php'); ?>" class="btn-action btn-view">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>
    <div class="admin-card-body">
        <form method="POST" enctype="multipart/form-data" class="admin-form">
            <input type="hidden" name="action" value="<?php echo $action; ?>">
            <?php if ($action === 'edit'): ?>
            <input type="hidden" name="id" value="<?php echo $editMember['id']; ?>">
            <?php endif; ?>

            <div class="row g-3">
                <div class="col-md-6">
                    <label>Full Name <span style="color:var(--secondary);">*</span></label>
                    <input type="text" name="name" class="form-control" required
                           placeholder="e.g. Ahmad Raza"
                           value="<?php echo htmlspecialchars($editMember['name'] ?? $_POST['name'] ?? ''); ?>">
                </div>
                <div class="col-md-6">
                    <label>Designation <span style="color:var(--secondary);">*</span></label>
                    <input type="text" name="designation" class="form-control" required
                           placeholder="e.g. Lead Developer"
                           value="<?php echo htmlspecialchars($editMember['designation'] ?? $_POST['designation'] ?? ''); ?>">
                </div>
                <div class="col-md-6">
                    <label>Photo (JPG/PNG/WebP, max 5 MB)</label>
                    <?php if (!empty($editMember['photo'])): ?>
                    <div class="mb-2" style="display:flex;align-items:center;gap:10px;">
                        <img src="/NexSoft/assets/uploads/team/<?php echo htmlspecialchars($editMember['photo']); ?>"
                             style="width:60px;height:60px;object-fit:cover;border-radius:50%;border:2px solid var(--border);"
                             alt="Current photo">
                        <small style="color:var(--text-muted);">Current photo — upload a new one to replace</small>
                    </div>
                    <?php endif; ?>
                    <input type="file" name="photo" class="form-control" accept="image/*">
                </div>
                <div class="col-md-6">
                    <label>Display Order <small style="color:var(--text-muted);font-weight:400;">(lower = earlier)</small></label>
                    <input type="number" name="sort_order" class="form-control" min="0"
                           value="<?php echo (int)($editMember['sort_order'] ?? $_POST['sort_order'] ?? 0); ?>"
                           placeholder="0">
                </div>
                <div class="col-12">
                    <label>Short Bio <small style="color:var(--text-muted);font-weight:400;">(optional, shown on hover)</small></label>
                    <textarea name="bio" class="form-control" rows="3"
                              placeholder="2–3 sentences about this team member's expertise..."><?php echo htmlspecialchars($editMember['bio'] ?? $_POST['bio'] ?? ''); ?></textarea>
                </div>
                <div class="col-12 pt-2 d-flex gap-2">
                    <button type="submit" class="btn-admin-primary">
                        <i class="bi bi-<?php echo $action === 'add' ? 'plus' : 'save'; ?>"></i>
                        <?php echo $action === 'add' ? 'Add Member' : 'Save Changes'; ?>
                    </button>
                    <a href="<?php echo adminUrl('team.php'); ?>" class="btn-admin-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>

<?php else: ?>
<!-- ===== MEMBERS LIST ===== -->
<div class="admin-card">
    <div class="admin-card-header">
        <span class="admin-card-title">
            <i class="bi bi-people-fill me-2" style="color:var(--secondary);"></i>
            Team Members (<?php echo count($members); ?>)
        </span>
        <a href="<?php echo adminUrl('team.php?action=add'); ?>" class="btn-admin-primary">
            <i class="bi bi-plus"></i> Add Member
        </a>
    </div>
    <div class="table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Order</th>
                    <th>Photo</th>
                    <th>Name</th>
                    <th>Designation</th>
                    <th>Bio</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($members)): ?>
                <tr>
                    <td colspan="6" style="text-align:center;padding:3rem;color:var(--text-muted);">
                        <i class="bi bi-people" style="font-size:2rem;display:block;margin-bottom:0.5rem;"></i>
                        No team members yet.
                        <a href="<?php echo adminUrl('team.php?action=add'); ?>" style="color:var(--secondary);">Add your first member.</a>
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($members as $m): ?>
                <tr>
                    <td style="color:var(--text-muted);text-align:center;font-weight:700;"><?php echo (int)$m['sort_order']; ?></td>
                    <td>
                        <?php if (!empty($m['photo']) && file_exists(__DIR__ . '/../assets/uploads/team/' . $m['photo'])): ?>
                        <img src="/NexSoft/assets/uploads/team/<?php echo htmlspecialchars($m['photo']); ?>"
                             style="width:48px;height:48px;object-fit:cover;border-radius:50%;border:2px solid var(--border);"
                             alt="<?php echo htmlspecialchars($m['name']); ?>">
                        <?php else: ?>
                        <div style="width:48px;height:48px;border-radius:50%;background:linear-gradient(135deg,var(--secondary),var(--secondary-dark));display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:1.1rem;">
                            <?php echo strtoupper(substr($m['name'], 0, 1)); ?>
                        </div>
                        <?php endif; ?>
                    </td>
                    <td><strong><?php echo htmlspecialchars($m['name']); ?></strong></td>
                    <td><span class="badge-teal"><?php echo htmlspecialchars($m['designation']); ?></span></td>
                    <td style="color:var(--text-muted);max-width:220px;">
                        <?php echo htmlspecialchars(mb_strimwidth($m['bio'] ?? '—', 0, 70, '...')); ?>
                    </td>
                    <td>
                        <div style="display:flex;gap:6px;">
                            <a href="<?php echo adminUrl('team.php?action=edit&id=' . $m['id']); ?>"
                               class="btn-action btn-edit"><i class="bi bi-pencil"></i> Edit</a>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo $m['id']; ?>">
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
