<?php
require_once __DIR__ . '/auth.php';
adminCheck();
adminRequirePermission('applications');

$db = getDB();
$msg = $_GET['msg'] ?? '';
$error = $_GET['error'] ?? '';

// Handle Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $action = $_POST['action'] ?? '';

    if ($action === 'update_status' && $id) {
        $status = $_POST['status'] ?? 'pending';
        $stmt = $db->prepare("UPDATE course_registrations SET status = ? WHERE id = ?");
        if ($stmt->execute([$status, $id])) {
            header('Location: intern_applications.php?msg=Status updated');
            exit;
        }
    } elseif ($action === 'delete' && $id) {
        $stmt = $db->prepare("DELETE FROM course_registrations WHERE id = ?");
        if ($stmt->execute([$id])) {
            header('Location: intern_applications.php?msg=Application deleted');
            exit;
        }
    } elseif ($action === 'batch_accept' && !empty($_POST['ids'])) {
        $ids = $_POST['ids'];
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $db->prepare("UPDATE course_registrations SET status = 'accepted' WHERE id IN ($placeholders)")->execute($ids);
        header('Location: intern_applications.php?msg=Selected applications accepted');
        exit;
    } elseif ($action === 'batch_reject' && !empty($_POST['ids'])) {
        $ids = $_POST['ids'];
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $db->prepare("UPDATE course_registrations SET status = 'rejected' WHERE id IN ($placeholders)")->execute($ids);
        header('Location: intern_applications.php?msg=Selected applications rejected');
        exit;
    } elseif ($action === 'batch_delete' && !empty($_POST['ids'])) {
        $ids = $_POST['ids'];
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $db->prepare("DELETE FROM course_registrations WHERE id IN ($placeholders)")->execute($ids);
        header('Location: intern_applications.php?msg=Selected applications deleted');
        exit;
    } elseif ($action === 'batch_certificate' && !empty($_POST['ids'])) {
        $ids = implode(',', $_POST['ids']);
        $tid = (int)($_POST['template_id'] ?? 0);
        header("Location: generate_document.php?ids=$ids&type=certificate" . ($tid ? "&template_id=$tid" : ""));
        exit;
    } elseif ($action === 'batch_experience' && !empty($_POST['ids'])) {
        $ids = implode(',', $_POST['ids']);
        $tid = (int)($_POST['template_id'] ?? 0);
        header("Location: generate_document.php?ids=$ids&type=experience" . ($tid ? "&template_id=$tid" : ""));
        exit;
    }
}

// Fetch Registrations for Internships specifically
$internshipId = (int)($_GET['id'] ?? 0);
$whereClause = "WHERE (c.category = 'internship' OR c.id IN (SELECT id FROM hr_internships WHERE category='internship'))";
$params = [];

if ($internshipId > 0) {
    $whereClause .= " AND c.id = ?";
    $params[] = $internshipId;
}

$stmt = $db->prepare("
    SELECT r.*, c.title as course_title, c.category as course_category
    FROM course_registrations r
    JOIN courses c ON r.course_id = c.id
    $whereClause
    ORDER BY r.created_at DESC
");
$stmt->execute($params);
$regs = $stmt->fetchAll();

// Fetch Templates for selection
$all_templates = $db->query("SELECT id, name, type, category FROM hr_document_templates ORDER BY name")->fetchAll();

// For simplicity in this specialized HR view, we'll combine or just use the core one if it's unified.
// Given the prompt, let's keep it clean.

$pageTitle = 'Intern Applications — NexSoft Hub Admin';
$activePage = 'applications';
require_once __DIR__ . '/layout-header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h4 mb-0">Intern Applications</h2>
        <p class="text-muted small mb-0">Manage applications for internships and courses.</p>
    </div>
</div>

<?php if ($msg): ?>
<div class="admin-alert-success mb-3"><i class="bi bi-check-circle-fill me-2"></i><?php echo htmlspecialchars($msg); ?></div>
<?php endif; ?>

<form id="batchForm" method="POST">
    <?php echo adminCsrfField(); ?>
    <div class="admin-card">
        <div class="admin-card-header d-flex justify-content-between align-items-center">
            <span class="admin-card-title">Recent Applications</span>
            <div class="d-flex gap-2">
                <select name="template_id" class="form-select form-select-sm" style="width: 180px; border-radius: 50px;">
                    <option value="">Default Template</option>
                    <?php foreach($all_templates as $tmpl): ?>
                    <option value="<?php echo $tmpl['id']; ?>"><?php echo htmlspecialchars($tmpl['name']); ?> (<?php echo ucfirst($tmpl['type']); ?>)</option>
                    <?php endforeach; ?>
                </select>
                <select name="action" class="form-select form-select-sm" style="width: 150px; border-radius: 50px;">
                    <option value="">Batch Action</option>
                    <option value="batch_accept">Accept Selected</option>
                    <option value="batch_reject">Reject Selected</option>
                    <option value="batch_certificate">Generate Certificates</option>
                    <option value="batch_experience">Generate Exp. Letters</option>
                    <option value="batch_delete">Delete Selected</option>
                </select>
                <button type="submit" class="btn-admin-primary btn-sm" onclick="return confirm('Apply this action to all selected items?')">Apply</button>
            </div>
        </div>
        <div class="table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 40px;"><input type="checkbox" onclick="toggleAll(this)"></th>
                        <th>Applicant</th>
                        <th>Interest</th>
                        <th>Status</th>
                        <th>Applied Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($regs)): ?>
                    <tr><td colspan="6" class="text-center py-4">No intern applications found.</td></tr>
                    <?php else: ?>
                    <?php foreach ($regs as $reg): ?>
                    <tr>
                        <td><input type="checkbox" name="ids[]" value="<?php echo $reg['id']; ?>"></td>
                        <td>
                            <div class="fw-bold"><?php echo htmlspecialchars($reg['name']); ?></div>
                            <div class="small text-muted"><?php echo htmlspecialchars($reg['email']); ?></div>
                            <div class="small text-muted"><?php echo htmlspecialchars($reg['phone']); ?></div>
                        </td>
                    <td>
                        <span class="badge-teal"><?php echo htmlspecialchars($reg['course_title']); ?></span>
                        <div class="small text-muted mt-1"><?php echo strtoupper($reg['course_category']); ?></div>
                    </td>
                    <td>
                        <form method="POST">
                            <?php echo adminCsrfField(); ?>
                            <input type="hidden" name="action" value="update_status">
                            <input type="hidden" name="id" value="<?php echo $reg['id']; ?>">
                            <select name="status" onchange="this.form.submit()" class="form-select form-select-sm" style="width: 130px; border-radius: 50px; font-size: 0.8rem;">
                                <option value="pending" <?php echo $reg['status']==='pending'?'selected':''; ?>>Pending</option>
                                <option value="called" <?php echo $reg['status']==='called'?'selected':''; ?>>Called</option>
                                <option value="accepted" <?php echo $reg['status']==='accepted'?'selected':''; ?>>Accepted</option>
                                <option value="rejected" <?php echo $reg['status']==='rejected'?'selected':''; ?>>Rejected</option>
                            </select>
                        </form>
                    </td>
                    <td><?php echo date('M d, Y', strtotime($reg['created_at'])); ?></td>
                    <td>
                        <div class="d-flex gap-2">
                             <a href="generate_document.php?id=<?php echo $reg['id']; ?>&type=certificate" class="btn-action btn-view" title="Generate Certificate"><i class="bi bi-patch-check"></i></a>
                             <a href="generate_document.php?id=<?php echo $reg['id']; ?>&type=experience" class="btn-action btn-view" title="Generate Experience Letter"><i class="bi bi-file-earmark-person"></i></a>
                             <form method="POST" onsubmit="return confirm('Delete this application?');">
                                <?php echo adminCsrfField(); ?>
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo $reg['id']; ?>">
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
</form>

<script>
function toggleAll(source) {
    checkboxes = document.getElementsByName('ids[]');
    for(var i=0, n=checkboxes.length;i<n;i++) {
        checkboxes[i].checked = source.checked;
    }
}
</script>

<?php require_once __DIR__ . '/layout-footer.php'; ?>
