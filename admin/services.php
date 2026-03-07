<?php
require_once __DIR__ . '/auth.php';
adminCheck();
adminRequirePermission('services');

$db     = getDB();
$action = $_GET['action'] ?? 'list';
$msg    = '';
$error  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $actionPost = $_POST['action'] ?? '';

    if ($actionPost === 'add' || $actionPost === 'edit') {
        $title       = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $features    = trim($_POST['features'] ?? '');
        $tags        = trim($_POST['tags'] ?? '');
        $icon        = trim($_POST['icon'] ?? 'bi-gear');
        $order_no    = (int)($_POST['order_no'] ?? 0);

        if (empty($title) || empty($description)) {
            $error = 'Title and description are required.';
        } else {
            if ($actionPost === 'add') {
                $stmt = $db->prepare("INSERT INTO services (title, description, features, tags, icon, order_no) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$title, $description, $features, $tags, $icon, $order_no]);
                adminLogAction('services.create', 'Created service: ' . $title);
                $msg = 'Service added successfully!';
            } else {
                $id = (int)($_POST['id'] ?? 0);
                $stmt = $db->prepare("UPDATE services SET title=?, description=?, features=?, tags=?, icon=?, order_no=? WHERE id=?");
                $stmt->execute([$title, $description, $features, $tags, $icon, $order_no, $id]);
                adminLogAction('services.update', 'Updated service id=' . $id . ' title=' . $title);
                $msg = 'Service updated successfully!';
            }
            $action = 'list';
        }
    } elseif ($actionPost === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $db->prepare("DELETE FROM services WHERE id = ?")->execute([$id]);
        adminLogAction('services.delete', 'Deleted service id=' . $id);
        $msg = 'Service deleted successfully.';
    }
}

$editService = null;
if ($action === 'edit') {
    $id = (int)($_GET['id'] ?? 0);
    $s = $db->prepare("SELECT * FROM services WHERE id = ?");
    $s->execute([$id]);
    $editService = $s->fetch();
    if (!$editService) $action = 'list';
}

$services = $db->query("SELECT * FROM services ORDER BY order_no ASC, created_at DESC")->fetchAll();

$pageTitle  = 'Manage Services — NexSoft Hub Admin';
$activePage = 'services';
require_once __DIR__ . '/layout-header.php';
?>

<?php if ($msg): ?>
<div class="admin-alert-success mb-3"><?php echo $msg; ?></div>
<?php endif; ?>
<?php if ($error): ?>
<div class="admin-alert-error mb-3"><?php echo $error; ?></div>
<?php endif; ?>

<?php if ($action === 'add' || $action === 'edit'): ?>
<div class="admin-card">
    <div class="admin-card-header">
        <span class="admin-card-title"><?php echo $action==='add'?'Add New Service':'Edit Service'; ?></span>
        <a href="services.php" class="btn-action btn-view">Back</a>
    </div>
    <div class="admin-card-body">
        <form method="POST" class="admin-form">
            <?php echo adminCsrfField(); ?>
            <input type="hidden" name="action" value="<?php echo $action; ?>">
            <?php if ($action==='edit'): ?>
            <input type="hidden" name="id" value="<?php echo $editService['id']; ?>">
            <?php endif; ?>
            <div class="row g-3">
                <div class="col-md-8">
                    <label>Service Title *</label>
                    <input type="text" name="title" class="form-control" required
                           value="<?php echo htmlspecialchars($editService['title'] ?? ''); ?>">
                </div>
                <div class="col-md-4">
                    <label>Bootstrap Icon Class (e.g. bi-laptop)</label>
                    <input type="text" name="icon" class="form-control"
                           value="<?php echo htmlspecialchars($editService['icon'] ?? 'bi-gear'); ?>">
                    <small><a href="https://icons.getbootstrap.com/" target="_blank">Browse Icons</a></small>
                </div>
                <div class="col-12">
                    <label>Description *</label>
                    <textarea name="description" class="form-control" rows="4" required><?php echo htmlspecialchars($editService['description'] ?? ''); ?></textarea>
                </div>
                <div class="col-md-4">
                    <label>Order Number (Lower appears first)</label>
                    <input type="number" name="order_no" class="form-control"
                           value="<?php echo htmlspecialchars($editService['order_no'] ?? '0'); ?>">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn-admin-primary"><?php echo $action==='add'?'Save Service':'Update Service'; ?></button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php else: ?>
<div class="admin-card">
    <div class="admin-card-header">
        <span class="admin-card-title">All Services</span>
        <a href="services.php?action=add" class="btn-admin-primary">Add Service</a>
    </div>
    <div class="table-wrap">
        <table class="admin-table">
            <thead><tr><th>Order</th><th>Icon</th><th>Title</th><th>Actions</th></tr></thead>
            <tbody>
                <?php foreach($services as $s): ?>
                <tr>
                    <td><?php echo $s['order_no']; ?></td>
                    <td><i class="bi <?php echo h($s['icon']); ?> fs-4"></i></td>
                    <td><strong><?php echo h($s['title']); ?></strong></td>
                    <td>
                        <a href="services.php?action=edit&id=<?php echo $s['id']; ?>" class="btn-action btn-edit">Edit</a>
                        <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this service?');">
                            <?php echo adminCsrfField(); ?>
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?php echo $s['id']; ?>">
                            <button type="submit" class="btn-action btn-delete">Delete</button>
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
