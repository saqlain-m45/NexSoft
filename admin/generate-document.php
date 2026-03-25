<?php
require_once __DIR__ . '/auth.php';
adminCheck();
adminRequirePermission('certificates');

$db = getDB();
$template_id = isset($_GET['template_id']) ? (int)$_GET['template_id'] : 0;
$action = $_GET['action'] ?? 'select';
$msg = '';
$error = '';

// Handle Document Generation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate_document'])) {
    $template_id = (int)$_POST['template_id'];
    $recipient_name = trim($_POST['recipient_name']);
    $recipient_email = trim($_POST['recipient_email']);
    $issue_date = $_POST['issue_date'];
    
    // Collect all filled variables
    $variables = [];
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'var_') === 0) {
            $var_name = str_replace('var_', '', $key);
            $variables[$var_name] = $value;
        }
    }
    
    if ($recipient_name && $template_id) {
        try {
            $template = $db->prepare("SELECT * FROM design_templates WHERE id = ?")->execute([$template_id])->fetch();
            
            if (!$template) {
                throw new Exception("Template not found");
            }
            
            // Generate unique document ID
            $document_id = strtoupper(substr(md5(time()), 0, 8));
            
            // Replace variables in template HTML
            $body_html = $template['body_html'];
            $body_html = str_replace('[Recipient Name]', htmlspecialchars($recipient_name), $body_html);
            $body_html = str_replace('[Date]', htmlspecialchars($issue_date), $body_html);
            $body_html = str_replace('[Certificate ID]', htmlspecialchars($document_id), $body_html);
            
            foreach ($variables as $key => $value) {
                $body_html = str_replace('[' . $key . ']', htmlspecialchars($value), $body_html);
            }
            
            // Store the generated document
            $stmt = $db->prepare("
                INSERT INTO issued_documents 
                (document_id, recipient_name, recipient_email, type, body_content, issue_date, status)
                VALUES (?, ?, ?, ?, ?, ?, 'active')
            ");
            $stmt->execute([$document_id, $recipient_name, $recipient_email, $template['type'], $body_html, $issue_date]);
            
            $doc_id = $db->lastInsertId();
            
            // Log action
            adminLogAction('document.issue', "Issued {$template['type']} to $recipient_name (ID: $document_id)");
            
            // Redirect to preview
            header("Location: generate-document.php?action=preview&id=$doc_id");
            exit;
        } catch (Exception $e) {
            $error = "Error generating document: " . $e->getMessage();
        }
    } else {
        $error = "Please fill in all required fields.";
    }
}

// Fetch templates
$templates = $db->query("SELECT * FROM design_templates WHERE is_active = 1 ORDER BY type, name")->fetchAll();
$current_template = null;

if ($template_id > 0) {
    $current_template = $db->prepare("SELECT * FROM design_templates WHERE id = ?")->execute([$template_id])->fetch();
}

$pageTitle = 'Generate Document — NexSoft Hub Admin';
$activePage = 'certificates';
require_once __DIR__ . '/layout-header.php';

// Extract variables from template
function extractVariables($html) {
    preg_match_all('/\[([^\]]+)\]/', $html, $matches);
    return array_unique($matches[1] ?? []);
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 mb-0 text-white">Generate Document</h2>
        <p class="text-muted small mb-0">Create personalized certificates and letters from templates.</p>
    </div>
    <a href="certificates.php" class="btn-admin-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to Certificates
    </a>
</div>

<?php if ($msg): ?>
<div class="admin-alert-success mb-3"><i class="bi bi-check-circle-fill me-2"></i><?php echo htmlspecialchars($msg); ?></div>
<?php endif; ?>
<?php if ($error): ?>
<div class="admin-alert-error mb-3"><i class="bi bi-exclamation-circle-fill me-2"></i><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<?php if ($action === 'select' || !$current_template): ?>
<!-- Template Selection -->
<div class="admin-card">
    <div class="admin-card-header">
        <h5 class="mb-0">Select a Template</h5>
    </div>
    <div class="admin-card-body">
        <?php if (empty($templates)): ?>
        <div class="alert alert-warning">
            <i class="bi bi-info-circle me-2"></i>
            No templates available. <a href="design-templates.php?action=edit">Create a template first</a>.
        </div>
        <?php else: ?>
        <div class="row">
            <?php foreach ($templates as $t): ?>
            <div class="col-md-6 mb-3">
                <div class="template-card" onclick="window.location.href='generate-document.php?action=fill&template_id=<?php echo $t['id']; ?>'" 
                     style="border: 1px solid #dee2e6; border-radius: 8px; padding: 20px; cursor: pointer; transition: all 0.3s; height: 100%;">
                    <?php if ($t['logo_image']): ?>
                    <img src="<?php echo htmlspecialchars($t['logo_image']); ?>" alt="Logo" style="max-height: 60px; margin-bottom: 15px;">
                    <?php endif; ?>
                    <h5><?php echo htmlspecialchars($t['name']); ?></h5>
                    <p class="text-muted small mb-2"><?php echo htmlspecialchars($t['description']); ?></p>
                    <div class="mb-3">
                        <span class="badge-blue"><?php echo ucfirst($t['type']); ?></span>
                        <?php if ($t['is_default']): ?>
                        <span class="badge" style="background: #28a745; color: white;">Default</span>
                        <?php endif; ?>
                    </div>
                    <button class="btn-admin-primary w-100">
                        <i class="bi bi-arrow-right me-1"></i> Use This Template
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
.template-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    border-color: #0066cc;
}
</style>

<?php else: ?>
<!-- Document Generation Form -->
<form method="POST" id="generateForm">
    <?php echo adminCsrfField(); ?>
    <input type="hidden" name="generate_document" value="1">
    <input type="hidden" name="template_id" value="<?php echo $current_template['id']; ?>">

    <div class="row">
        <!-- Left Column - Form -->
        <div class="col-md-6">
            <div class="admin-card">
                <div class="admin-card-header">
                    <h5 class="mb-0">Recipient Information</h5>
                </div>
                <div class="admin-card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Recipient Name *</label>
                        <input type="text" class="form-control" name="recipient_name" required placeholder="e.g., John Doe">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Recipient Email</label>
                        <input type="email" class="form-control" name="recipient_email" placeholder="email@example.com">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Issue Date *</label>
                        <input type="date" class="form-control" name="issue_date" required value="<?php echo date('Y-m-d'); ?>">
                    </div>

                    <?php
                    // Extract and display variables from template body
                    $body_variables = extractVariables($current_template['body_html']);
                    if (!empty($body_variables)):
                    ?>
                    <hr>
                    <h6 class="mb-3">Template Variables</h6>
                    <?php foreach ($body_variables as $var): ?>
                        <?php if (!in_array($var, ['Recipient Name', 'Date', 'Certificate ID'])): ?>
                        <div class="mb-3">
                            <label class="form-label"><?php echo htmlspecialchars($var); ?></label>
                            <input type="text" class="form-control" name="var_<?php echo htmlspecialchars($var); ?>" 
                                   placeholder="Enter <?php echo htmlspecialchars($var); ?>">
                        </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Right Column - Preview -->
        <div class="col-md-6">
            <div class="admin-card">
                <div class="admin-card-header">
                    <h5 class="mb-0">Preview</h5>
                </div>
                <div class="admin-card-body" style="background: #f8f9fa; min-height: 400px; border-radius: 0 0 4px 4px; padding: 20px; overflow-y: auto; max-height: 500px; border: 1px solid #dee2e6;">
                    <?php if ($current_template['logo_image']): ?>
                    <div style="text-align: <?php echo strpos($current_template['logo_position'], 'left') !== false ? 'left' : (strpos($current_template['logo_position'], 'right') !== false ? 'right' : 'center'); ?>; margin-bottom: 20px;">
                        <img src="<?php echo htmlspecialchars($current_template['logo_image']); ?>" alt="Logo" 
                             style="max-width: <?php echo $current_template['logo_width']; ?>px; height: auto;">
                    </div>
                    <?php endif; ?>
                    
                    <div style="text-align: center; margin-bottom: 20px;">
                        <?php echo $current_template['header_html']; ?>
                    </div>

                    <div style="line-height: 1.6; margin-bottom: 20px;">
                        <?php echo $current_template['body_html']; ?>
                    </div>

                    <div style="text-align: center; margin-top: 40px; font-size: 12px; color: #666;">
                        <?php echo $current_template['footer_html']; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2 mt-4">
        <button type="submit" class="btn-admin-primary">
            <i class="bi bi-check-circle me-1"></i> Generate Document
        </button>
        <a href="generate-document.php" class="btn-admin-secondary">Select Different Template</a>
    </div>
</form>

<?php endif; ?>

<?php require_once __DIR__ . '/layout-footer.php'; ?>
