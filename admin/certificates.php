<?php
require_once __DIR__ . '/auth.php';
adminCheck();
adminRequirePermission('certificates');

$db = getDB();
$msg = $_GET['msg'] ?? '';
$error = $_GET['error'] ?? '';

// Handle Actions (Revoke)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $action = $_POST['action'] ?? '';

    if ($action === 'revoke' && $id) {
        $stmt = $db->prepare("UPDATE issued_documents SET status = 'revoked' WHERE id = ?");
        if ($stmt->execute([$id])) {
            adminLogAction('hr.document_revoke', 'Revoked document ID: ' . $id);
            header('Location: certificates.php?msg=Document revoked');
            exit;
        }
    } elseif ($action === 'activate' && $id) {
        $stmt = $db->prepare("UPDATE issued_documents SET status = 'active' WHERE id = ?");
        if ($stmt->execute([$id])) {
            adminLogAction('hr.document_activate', 'Activated document ID: ' . $id);
            header('Location: certificates.php?msg=Document activated');
            exit;
        }
    }
}

// Fetch Issued Documents
$docs = $db->query("
    SELECT d.*, i.title as internship_title
    FROM issued_documents d
    LEFT JOIN hr_internships i ON d.internship_id = i.id
    ORDER BY d.created_at DESC
")->fetchAll();

$pageTitle = 'Issued Documents — NexSoft Hub Admin';
$activePage = 'certificates';
require_once __DIR__ . '/layout-header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h4 mb-0">Issued Documents</h2>
        <p class="text-muted small mb-0">Certificates and Experience Letters issued to interns/students.</p>
    </div>
    <a href="intern_applications.php" class="btn-admin-primary"><i class="bi bi-plus-circle me-1"></i> Issue New</a>
</div>

<?php if ($msg): ?>
<div class="admin-alert-success mb-3"><i class="bi bi-check-circle-fill me-2"></i><?php echo htmlspecialchars($msg); ?></div>
<?php endif; ?>

<div class="admin-card">
    <div class="table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Doc ID</th>
                    <th>Recipient</th>
                    <th>Type</th>
                    <th>Internship/Course</th>
                    <th>Issue Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($docs)): ?>
                <tr><td colspan="7" class="text-center py-4">No documents issued yet.</td></tr>
                <?php else: ?>
                <?php foreach ($docs as $doc): ?>
                <tr>
                    <td><span class="font-monospace fw-bold" style="font-size: 0.85rem;"><?php echo htmlspecialchars($doc['document_id']); ?></span></td>
                    <td>
                        <div class="fw-bold"><?php echo htmlspecialchars($doc['recipient_name']); ?></div>
                        <div class="small text-muted"><?php echo htmlspecialchars($doc['recipient_email']); ?></div>
                    </td>
                    <td>
                        <span class="badge-blue"><?php echo ucfirst(str_replace('_', ' ', $doc['type'])); ?></span>
                    </td>
                    <td><?php echo htmlspecialchars($doc['internship_title'] ?: '—'); ?></td>
                    <td><?php echo date('M d, Y', strtotime($doc['issue_date'])); ?></td>
                    <td>
                        <span class="<?php echo $doc['status'] === 'active' ? 'badge-green' : 'badge-orange'; ?>">
                            <?php echo ucfirst($doc['status']); ?>
                        </span>
                    </td>
                    <td>
                        <div class="d-flex gap-2">
                             <a href="generate_document.php?id=<?php echo $doc['id']; ?>&preview=1" class="btn-action btn-view" title="View Document" target="_blank"><i class="bi bi-eye"></i></a>
                             <?php if ($doc['status'] === 'active'): ?>
                             <form method="POST" style="display:inline;" onsubmit="return confirm('Revoke this document? It will not be verifiable anymore.');">
                                <?php echo adminCsrfField(); ?>
                                <input type="hidden" name="action" value="revoke">
                                <input type="hidden" name="id" value="<?php echo $doc['id']; ?>">
                                <button type="submit" class="btn-action" style="color: #ea580c; background: rgba(234,88,12,0.1);"><i class="bi bi-slash-circle"></i></button>
                             </form>
                             <?php else: ?>
                             <form method="POST" style="display:inline;">
                                <?php echo adminCsrfField(); ?>
                                <input type="hidden" name="action" value="activate">
                                <input type="hidden" name="id" value="<?php echo $doc['id']; ?>">
                                <button type="submit" class="btn-action" style="color: #16a34a; background: rgba(22,163,74,0.1);"><i class="bi bi-check-circle"></i></button>
                             </form>
                             <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/layout-footer.php'; ?>
