<?php
require_once __DIR__ . '/auth.php';
adminCheck();

$db = getDB();
$ids = isset($_GET['ids']) ? explode(',', $_GET['ids']) : (isset($_GET['id']) ? [$_GET['id']] : []);
$type = $_GET['type'] ?? 'certificate'; // 'certificate' or 'experience'
$preview = (bool)($_GET['preview'] ?? false);

if (empty($ids)) die('No IDs provided.');

$documents = [];

foreach ($ids as $id) {
    $id = (int)$id;
    $doc = null;

    if ($preview) {
        $stmt = $db->prepare("
            SELECT d.*, i.title as internship_title, i.duration as internship_duration, i.category as item_category
            FROM issued_documents d
            LEFT JOIN hr_internships i ON d.internship_id = i.id
            WHERE d.id = ?
        ");
        $stmt->execute([$id]);
        $doc = $stmt->fetch();
    } else {
        $stmt = $db->prepare("
            SELECT r.*, c.title as course_title, c.category as item_category
            FROM course_registrations r
            JOIN courses c ON r.course_id = c.id
            WHERE r.id = ?
        ");
        $stmt->execute([$id]);
        $reg = $stmt->fetch();
        if (!$reg) continue;

        $type_val = ($type === 'experience') ? 'experience_letter' : 'certificate';
        $stmt_check = $db->prepare("SELECT * FROM issued_documents WHERE recipient_email = ? AND internship_id = ? AND type = ?");
        $stmt_check->execute([$reg['email'], $reg['course_id'], $type_val]);
        $existing = $stmt_check->fetch();

        if ($existing) {
            $doc = $existing;
            $stmt_i = $db->prepare("SELECT title as internship_title, duration as internship_duration, category as item_category FROM hr_internships WHERE id = ?");
            $stmt_i->execute([$doc['internship_id']]);
            $i_info = $stmt_i->fetch();
            if ($i_info) {
                $doc['internship_title'] = $i_info['internship_title'];
                $doc['internship_duration'] = $i_info['internship_duration'];
                $doc['item_category'] = $i_info['item_category'];
            }
        } else {
            $doc_id = 'NEX-' . strtoupper(substr($type, 0, 1)) . '-' . date('Ymd') . '-' . sprintf('%04d', rand(1, 9999));
            $v_code = bin2hex(random_bytes(8));
            $stmt_ins = $db->prepare("
                INSERT INTO issued_documents (document_id, type, recipient_name, recipient_email, internship_id, issue_date, verification_code)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt_ins->execute([$doc_id, $type_val, $reg['name'], $reg['email'], $reg['course_id'], date('Y-m-d'), $v_code]);
            $new_id = $db->lastInsertId();
            
            $stmt_new = $db->prepare("
                SELECT d.*, i.title as internship_title, i.duration as internship_duration, i.category as item_category
                FROM issued_documents d
                LEFT JOIN hr_internships i ON d.internship_id = i.id
                WHERE d.id = ?
            ");
            $stmt_new->execute([$new_id]);
            $doc = $stmt_new->fetch();
        }
    }
    if ($doc) $documents[] = $doc;
}

if (empty($documents)) die('No valid documents to display.');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>NexSoft HR - Document Generation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;800&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <style>
        body { background: #f0f2f5; font-family: 'Montserrat', sans-serif; display: flex; flex-direction: column; align-items: center; padding: 40px 0; }
        .cert-container { 
            width: 1000px; height: 700px; background: white; position: relative; padding: 60px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.1); border: 20px solid #0B1F3B;
            display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center;
            margin-bottom: 40px; page-break-after: always;
        }
        .cert-inner-border { position: absolute; top: 10px; left: 10px; right: 10px; bottom: 10px; border: 2px solid #0EA5A4; pointer-events: none; }
        .cert-logo { margin-bottom: 20px; font-size: 2.5rem; font-weight: 800; color: #0B1F3B; }
        .cert-logo span { color: #0EA5A4; }
        .cert-title { font-family: 'Playfair Display', serif; font-size: 3.5rem; color: #0B1F3B; margin-bottom: 30px; letter-spacing: 2px; }
        .cert-subtitle { font-size: 1.2rem; color: #475569; letter-spacing: 4px; text-transform: uppercase; margin-bottom: 15px; }
        .cert-name { font-family: 'Playfair Display', serif; font-size: 3.2rem; color: #0EA5A4; margin-bottom: 20px; text-decoration: underline; }
        .cert-text { font-size: 1.1rem; line-height: 1.8; color: #1e293b; max-width: 800px; margin-bottom: 40px; }
        .cert-footer { width: 100%; display: flex; justify-content: space-between; align-items: flex-end; margin-top: auto; }
        .signature { border-top: 2px solid #0B1F3B; width: 200px; padding-top: 10px; font-weight: 700; color: #0B1F3B; }
        .doc-meta { position: absolute; bottom: 25px; font-size: 0.75rem; color: #94a3b8; font-family: monospace; }
        
        .no-print { margin-bottom: 20px; position: sticky; top: 20px; z-index: 1000; }
        @media print { .no-print { display: none; } body { padding: 0; background: white; } .cert-container { box-shadow: none; border-width: 15px; margin-bottom: 0; } }
    </style>
</head>
<body>

    <div class="no-print d-flex gap-2">
        <button onclick="window.print()" class="btn btn-primary btn-lg px-5 shadow-sm"><i class="bi bi-printer me-2"></i> Print All (<?php echo count($documents); ?>)</button>
        <a href="intern_applications.php" class="btn btn-outline-secondary btn-lg">Back to Admin</a>
    </div>

    <?php foreach ($documents as $doc): 
        $name = htmlspecialchars($doc['recipient_name']);
        $date = date('F d, Y', strtotime($doc['issue_date']));
        $title = htmlspecialchars($doc['internship_title'] ?: 'Internship/Course');
        $duration = htmlspecialchars($doc['internship_duration'] ?? 'Completed Duration');
        $docId = htmlspecialchars($doc['document_id']);
        $vCode = htmlspecialchars($doc['verification_code']);
        $docTypeLabel = ($doc['type'] === 'experience_letter') ? 'EXPERIENCE LETTER' : 'CERTIFICATE OF COMPLETION';
        
        // Template Selection
        $templateId = (int)($_GET['template_id'] ?? 0);
        $template = null;
        if ($templateId > 0) {
            $st = $db->prepare("SELECT * FROM hr_document_templates WHERE id = ?");
            $st->execute([$templateId]);
            $template = $st->fetch();
        }
        
        if (!$template) {
            $cat = $doc['item_category'] ?? 'internship';
            $st = $db->prepare("SELECT * FROM hr_document_templates WHERE type = ? AND (category = ? OR category = 'both') AND is_default = 1 LIMIT 1");
            $st->execute([$doc['type'], $cat]);
            $template = $st->fetch();
        }
        
        // Fallback to basic text if no template found
        $body = $template['body_text'] ?? "This is to certify that {{name}} has completed {{title}}.";
        $styles = $template['styles'] ?? "";
        
        $placeholders = [
            '{{name}}' => "<strong>$name</strong>",
            '{{title}}' => "<strong>$title</strong>",
            '{{duration}}' => "<strong>$duration</strong>",
            '{{date}}' => $date,
            '{{docId}}' => "<strong>$docId</strong>",
            '{{vCode}}' => "<strong>$vCode</strong>"
        ];
        $body = str_replace(array_keys($placeholders), array_values($placeholders), $body);
    ?>
    <style><?php echo $styles; ?></style>
    <div class="cert-container">
        <div class="cert-inner-border"></div>
        <div class="cert-logo"><?php echo getSetting('hr_doc_logo_text', 'NexSoft <span>Hub</span>'); ?></div>
        <div class="cert-subtitle"><?php echo ($doc['type'] === 'certificate') ? 'PRESENTED TO' : 'TO WHOM IT MAY CONCERN'; ?></div>
        <?php if ($doc['type'] === 'certificate'): ?>
        <div class="cert-name"><?php echo $name; ?></div>
        <?php endif; ?>
        <div class="cert-title"><?php echo $docTypeLabel; ?></div>
        <div class="cert-text">
            <?php echo $body; ?>
        </div>
        <div class="cert-footer">
            <div class="text-start">
                <?php if ($doc['type'] === 'certificate'): ?>
                    <div class="signature"><?php echo getSetting('hr_doc_certificate_signer', 'SAQLAIN MUZAFFAR'); ?></div>
                    <div class="small text-muted"><?php echo getSetting('hr_doc_certificate_designation', 'CEO & Founder, NexSoft Hub'); ?></div>
                <?php else: ?>
                    <div class="signature"><?php echo getSetting('hr_doc_experience_signer', 'HR MANAGER'); ?></div>
                    <div class="small text-muted"><?php echo getSetting('hr_doc_experience_designation', 'NexSoft HR Department'); ?></div>
                <?php endif; ?>
            </div>
            <div class="text-center">
                <div class="v-code font-monospace small mb-1" style="color: #64748b;">Verification Code: <?php echo $vCode; ?></div>
                <div class="small text-muted"><?php echo getSetting('hr_doc_footer_text', 'Verify at nexsofthub.com/verify'); ?></div>
            </div>
            <div class="text-end">
                <div class="signature"><?php echo $date; ?></div>
                <div class="small text-muted">Date of Issuance</div>
            </div>
        </div>
        <div class="doc-meta">Document ID: <?php echo $docId; ?> | System Generated by NexSoft HR Module</div>
    </div>
    <?php endforeach; ?>

</body>
</html>
