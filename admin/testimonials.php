<?php
require_once __DIR__ . '/auth.php';
adminCheck();
adminRequirePermission('testimonials');

$db     = getDB();
$action = $_GET['action'] ?? 'list';
$msg    = '';
$error  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $actionPost = $_POST['action'] ?? '';

    if ($actionPost === 'add' || $actionPost === 'edit') {
        $name        = trim($_POST['client_name'] ?? '');
        $designation = trim($_POST['designation'] ?? '');
        $feedback    = trim($_POST['feedback'] ?? '');
        $rating      = (int)($_POST['rating'] ?? 5);

        if (empty($name) || empty($feedback)) {
            $error = 'Client name and feedback are required.';
        } else {
            if ($actionPost === 'add') {
                $stmt = $db->prepare("INSERT INTO testimonials (client_name, designation, feedback, rating) VALUES (?, ?, ?, ?)");
                $stmt->execute([$name, $designation, $feedback, $rating]);
                $msg = 'Testimonial added!';
            } else {
                $id = (int)($_POST['id'] ?? 0);
                $stmt = $db->prepare("UPDATE testimonials SET client_name=?, designation=?, feedback=?, rating=? WHERE id=?");
                $stmt->execute([$name, $designation, $feedback, $rating, $id]);
                $msg = 'Testimonial updated!';
            }
            $action = 'list';
        }
    } elseif ($actionPost === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $db->prepare("DELETE FROM testimonials WHERE id = ?")->execute([$id]);
        $msg = 'Deleted successfully.';
    }
}

$editT = null;
if ($action === 'edit') {
    $id = (int)($_GET['id'] ?? 0);
    $s = $db->prepare("SELECT * FROM testimonials WHERE id = ?");
    $s->execute([$id]);
    $editT = $s->fetch();
    if (!$editT) $action = 'list';
}

$testimonials = $db->query("SELECT * FROM testimonials ORDER BY created_at DESC")->fetchAll();

$pageTitle  = 'Manage Testimonials — NexSoft Hub Admin';
$activePage = 'testimonials';
require_once __DIR__ . '/layout-header.php';
?>

<?php if ($msg): ?>
<div class="admin-alert-success mb-3"><?php echo $msg; ?></div>
<?php endif; ?>

<?php if ($action === 'add' || $action === 'edit'): ?>
<div class="admin-card">
    <div class="admin-card-header">
        <span class="admin-card-title"><?php echo $action==='add'?'Add Testimonial':'Edit Testimonial'; ?></span>
        <a href="testimonials.php" class="btn-action btn-view">Back</a>
    </div>
    <div class="admin-card-body">
        <form method="POST" class="admin-form">
            <input type="hidden" name="action" value="<?php echo $action; ?>">
            <?php if ($action==='edit'): ?>
            <input type="hidden" name="id" value="<?php echo $editT['id']; ?>">
            <?php endif; ?>
            <div class="row g-3">
                <div class="col-md-6">
                    <label>Client Name *</label>
                    <input type="text" name="client_name" class="form-control" required value="<?php echo h($editT['client_name'] ?? ''); ?>">
                </div>
                <div class="col-md-6">
                    <label>Designation (e.g. CEO, Tech Lead)</label>
                    <input type="text" name="designation" class="form-control" value="<?php echo h($editT['designation'] ?? ''); ?>">
                </div>
                <div class="col-12">
                    <label>Feedback *</label>
                    <textarea name="feedback" class="form-control" rows="4" required><?php echo h($editT['feedback'] ?? ''); ?></textarea>
                </div>
                <div class="col-md-4">
                    <label>Rating (1-5)</label>
                    <select name="rating" class="form-control">
                        <?php for($i=5;$i>=1;$i--): ?>
                        <option value="<?php echo $i; ?>" <?php echo ($editT['rating']??5)==$i?'selected':''; ?>><?php echo $i; ?> Stars</option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-12 pt-3">
                    <button type="submit" class="btn-admin-primary">Save Testimonial</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php else: ?>
<div class="admin-card">
    <div class="admin-card-header">
        <span class="admin-card-title">Client Testimonials</span>
        <a href="testimonials.php?action=add" class="btn-admin-primary">Add Testimonial</a>
    </div>
    <div class="table-wrap">
        <table class="admin-table">
            <thead><tr><th>Client</th><th>Feedback</th><th>Date</th><th>Actions</th></tr></thead>
            <tbody>
                <?php foreach($testimonials as $t): ?>
                <tr>
                    <td><strong><?php echo h($t['client_name']); ?></strong><br><small><?php echo h($t['designation']); ?></small></td>
                    <td><?php echo h(mb_strimwidth($t['feedback'], 0, 60, '...')); ?></td>
                    <td><?php echo date('M d, Y', strtotime($t['created_at'])); ?></td>
                    <td>
                        <a href="testimonials.php?action=edit&id=<?php echo $t['id']; ?>" class="btn-action btn-edit">Edit</a>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?php echo $t['id']; ?>">
                            <button type="submit" class="btn-action btn-delete confirm-delete">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/layout-footer.php'; ?>
