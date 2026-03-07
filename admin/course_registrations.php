<?php
require_once __DIR__ . '/auth.php';
adminCheck();
adminRequirePermission('registrations');

$db = getDB();
$msg = $_GET['msg'] ?? '';
$error = $_GET['error'] ?? '';

// Handle Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $action = $_POST['action'] ?? '';

    if ($action === 'update_status' && $id) {
        $status = $_POST['status'] ?? 'pending';
        
        // Get student info for email notification before update
        $stmt_info = $db->prepare("
            SELECT r.*, c.title as course_title 
            FROM course_registrations r 
            JOIN courses c ON r.course_id = c.id 
            WHERE r.id = ?
        ");
        $stmt_info->execute([$id]);
        $student = $stmt_info->fetch();

        $stmt = $db->prepare("UPDATE course_registrations SET status = ? WHERE id = ?");
        if ($stmt->execute([$status, $id])) {
            // Send Email Notification to Student
            if ($student && $status !== 'pending') {
                require_once __DIR__ . '/../config/mailer.php';
                $body = emailTemplateCourseStatusUpdate($student['name'], $student['course_title'], $status);
                sendMail($student['email'], $student['name'], "Course Application Update: " . $student['course_title'], $body);
            }
            header('Location: course_registrations.php?msg=Status updated successfully');
            exit;
        }
    } elseif ($action === 'delete' && $id) {
        $stmt = $db->prepare("DELETE FROM course_registrations WHERE id = ?");
        if ($stmt->execute([$id])) {
            header('Location: course_registrations.php?msg=Application deleted');
            exit;
        }
    }
}

// Fetch Registrations
$regs = $db->query("
    SELECT r.*, c.title as course_title, c.category as course_category
    FROM course_registrations r
    JOIN courses c ON r.course_id = c.id
    ORDER BY r.created_at DESC
")->fetchAll();

$pageTitle = 'Course Applications — NexSoft Hub Admin';
$activePage = 'course_registrations';
require_once __DIR__ . '/layout-header.php';
?>

<?php if ($msg): ?>
<div class="alert alert-success border-0 shadow-sm mb-4" style="border-radius: 12px;"><i
        class="bi bi-check-circle-fill me-2"></i>
    <?php echo htmlspecialchars($msg); ?>
</div>
<?php endif; ?>

<div class="admin-card border-0 shadow-sm" style="border-radius: 15px; overflow: hidden; background: white;">
    <div class="admin-card-header bg-white border-0 py-4 px-4 d-flex justify-content-between align-items-center">
        <div>
            <span class="admin-card-title h5 mb-1 d-block" style="font-weight: 800; color: #0B1F3B;">
                <i class="bi bi-person-badge-fill text-primary me-2"></i>Course Applications
            </span>
            <p class="text-muted small mb-0">Review and manage student registrations for active courses.</p>
        </div>
        <span class="badge bg-light text-dark shadow-sm py-2 px-3" style="border-radius: 50px;">
            <?php echo count($regs); ?> Total Applications
        </span>
    </div>

    <div class="table-wrap px-2 pb-2">
        <table class="admin-table align-middle">
            <thead>
                <tr class="text-muted small fw-bold">
                    <th class="ps-4">APPLICANT</th>
                    <th>COURSE</th>
                    <th>MESSAGE / NOTES</th>
                    <th class="text-center">STATUS</th>
                    <th class="text-end pe-4">ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($regs as $reg): ?>
                <tr style="border-bottom: 1px solid #f8fafc;">
                    <td class="ps-4 py-4">
                        <div class="fw-bold fs-6 mb-1" style="color: #0B1F3B;">
                            <?php echo htmlspecialchars($reg['name']); ?>
                        </div>
                        <div class="small text-muted d-flex align-items-center gap-2 mb-1">
                            <i class="bi bi-envelope text-primary"></i>
                            <?php echo htmlspecialchars($reg['email']); ?>
                        </div>
                        <div class="small text-muted d-flex align-items-center gap-2">
                            <i class="bi bi-telephone text-success"></i>
                            <?php echo htmlspecialchars($reg['phone']); ?>
                        </div>
                        <div class="mt-2 small text-muted font-monospace" style="font-size: 0.7rem;">
                            <i class="bi bi-clock"></i>
                            <?php echo date('M j, Y — g:i A', strtotime($reg['created_at'])); ?>
                        </div>
                    </td>
                    <td>
                        <div class="badge-teal text-truncate d-inline-block"
                            style="max-width: 150px; font-size: 0.7rem; padding: 6px 12px; border-radius: 50px; font-weight: 700;">
                            <?php echo htmlspecialchars($reg['course_title']); ?>
                        </div>
                        <div class="small text-muted mt-1 ps-1" style="font-size: 0.7rem;">
                            <?php echo htmlspecialchars(strtoupper($reg['course_category'])); ?>
                        </div>
                    </td>
                    <td style="max-width: 250px;">
                        <div class="text-muted small border-start ps-3"
                            style="font-size: 0.85rem; line-height: 1.5; font-style: italic;">
                            <?php echo !empty($reg['message']) ? nl2br(htmlspecialchars($reg['message'])) : 'No notes provided.'; ?>
                        </div>
                    </td>
                    <td class="text-center">
                        <form method="POST">
                            <input type="hidden" name="action" value="update_status">
                            <input type="hidden" name="id" value="<?php echo $reg['id']; ?>">
                            <select name="status" onchange="this.form.submit()"
                                class="form-select form-select-sm border-0 shadow-sm" style="border-radius: 50px; font-size: 0.75rem; width: 120px; margin: 0 auto; <?php 
                                echo match($reg['status']) {
                                    'accepted' => 'background-color: #dcfce7; color: #166534;',
                                    'rejected' => 'background-color: #fee2e2; color: #991b1b;',
                                    'called' => 'background-color: #fef9c3; color: #854d0e;',
                                    default => 'background-color: #f1f5f9; color: #475569;'
                                };
                            ?>">
                                <option value="pending" <?php echo $reg['status']==='pending' ?'selected':''; ?>>Pending
                                </option>
                                <option value="called" <?php echo $reg['status']==='called' ?'selected':''; ?>>Called
                                </option>
                                <option value="accepted" <?php echo $reg['status']==='accepted' ?'selected':''; ?>
                                    >Accept</option>
                                <option value="rejected" <?php echo $reg['status']==='rejected' ?'selected':''; ?>
                                    >Reject</option>
                            </select>
                        </form>
                    </td>
                    <td class="text-end pe-4">
                        <form method="POST" onsubmit="return confirm('Permanently delete this application?')">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?php echo $reg['id']; ?>">
                            <button type="submit" class="btn btn-sm btn-light p-2 shadow-sm"
                                style="border-radius: 8px; width: 34px; height: 34px;">
                                <i class="bi bi-trash-fill text-danger"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($regs)): ?>
                <tr>
                    <td colspan="5" class="text-center py-5">
                        <div class="text-muted">
                            <i class="bi bi-person-slash h1 d-block mb-3 opacity-25"></i>
                            No applications found.
                        </div>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
    .admin-table tbody tr:hover {
        background-color: #fcfdfe;
    }

    .form-select:focus {
        box-shadow: none;
        border: 1px solid #0EA5A4 !important;
    }
</style>

<?php require_once __DIR__ . '/layout-footer.php'; ?>