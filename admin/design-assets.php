<?php
require_once __DIR__ . '/auth.php';
adminCheck();
adminRequirePermission('templates');

$db = getDB();
$msg = '';
$error = '';
$section = $_GET['section'] ?? 'styles';

// Handle Delete Style
if (isset($_GET['delete_style'])) {
    $id = (int)$_GET['delete_style'];
    try {
        $db->prepare("DELETE FROM text_styles WHERE id = ? AND is_default = 0")->execute([$id]);
        $msg = "Style deleted successfully.";
        adminLogAction('template.style_delete', "Deleted style ID: $id");
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Handle Delete Logo
if (isset($_GET['delete_logo'])) {
    $id = (int)$_GET['delete_logo'];
    try {
        $logo = $db->prepare("SELECT file_path FROM template_logos WHERE id = ?")->execute([$id])->fetch();
        if ($logo && file_exists($logo['file_path'])) {
            unlink($logo['file_path']);
        }
        $db->prepare("DELETE FROM template_logos WHERE id = ?")->execute([$id]);
        $msg = "Logo deleted successfully.";
        adminLogAction('template.logo_delete', "Deleted logo ID: $id");
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Handle Style Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['style_action'])) {
    $id = isset($_POST['style_id']) ? (int)$_POST['style_id'] : 0;
    $name = trim($_POST['style_name']);
    $style_type = $_POST['style_type'];
    $font_family = $_POST['font_family'];
    $font_size = (int)$_POST['font_size'];
    $font_weight = $_POST['font_weight'];
    $font_color = $_POST['font_color'];
    $line_height = $_POST['line_height'];
    $text_align = $_POST['text_align'];

    if ($name && $style_type) {
        try {
            if ($id > 0) {
                $stmt = $db->prepare("
                    UPDATE text_styles SET 
                    name = ?, style_type = ?, font_family = ?, font_size = ?, 
                    font_weight = ?, font_color = ?, line_height = ?, text_align = ?
                    WHERE id = ?
                ");
                $stmt->execute([$name, $style_type, $font_family, $font_size, $font_weight, $font_color, $line_height, $text_align, $id]);
                $msg = "Style updated successfully.";
            } else {
                $stmt = $db->prepare("
                    INSERT INTO text_styles 
                    (name, style_type, font_family, font_size, font_weight, font_color, line_height, text_align)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([$name, $style_type, $font_family, $font_size, $font_weight, $font_color, $line_height, $text_align]);
                $msg = "Style created successfully.";
            }
            adminLogAction('template.style_save', $id > 0 ? "Updated style ID: $id" : "Created new style");
        } catch (Exception $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}

// Handle Logo Upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logo_action'])) {
    if (!empty($_FILES['logo_file']['name'])) {
        $logo_name = trim($_POST['logo_name']);
        $logo_file = handleLogoUpload($_FILES['logo_file']);
        
        if ($logo_file) {
            try {
                $stmt = $db->prepare("
                    INSERT INTO template_logos 
                    (name, file_path, uploaded_by, file_size)
                    VALUES (?, ?, ?, ?)
                ");
                $stmt->execute([$logo_name ?: basename($logo_file), $logo_file, $_SESSION['user_id'] ?? 1, $_FILES['logo_file']['size']]);
                $msg = "Logo uploaded successfully.";
                adminLogAction('template.logo_upload', "Uploaded logo: $logo_name");
            } catch (Exception $e) {
                $error = "Error: " . $e->getMessage();
            }
        } else {
            $error = "Failed to upload logo. Please check the file format.";
        }
    }
}

// Fetch data
$styles = $db->query("SELECT * FROM text_styles ORDER BY style_type, name")->fetchAll();
$logos = $db->query("SELECT * FROM template_logos ORDER BY name")->fetchAll();

$pageTitle = 'Design Assets — Text Styles & Logos — NexSoft Hub Admin';
$activePage = 'design-styles';
require_once __DIR__ . '/layout-header.php';

function handleLogoUpload($file) {
    $upload_dir = __DIR__ . '/../assets/uploads/logos/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($file['type'], $allowed_types)) {
        return null;
    }
    
    $max_size = 5 * 1024 * 1024; // 5MB
    if ($file['size'] > $max_size) {
        return null;
    }
    
    $filename = 'logo_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
    $target = $upload_dir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $target)) {
        return 'assets/uploads/logos/' . $filename;
    }
    return null;
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 mb-0 text-white">Design Assets</h2>
        <p class="text-muted small mb-0">Manage text styles and logo assets for templates.</p>
    </div>
</div>

<?php if ($msg): ?>
<div class="admin-alert-success mb-3"><i class="bi bi-check-circle-fill me-2"></i><?php echo htmlspecialchars($msg); ?></div>
<?php endif; ?>
<?php if ($error): ?>
<div class="admin-alert-error mb-3"><i class="bi bi-exclamation-circle-fill me-2"></i><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<!-- Tabs Navigation -->
<div class="mb-4">
    <div class="nav nav-tabs" role="tablist">
        <button class="nav-link <?php echo $section === 'styles' ? 'active' : ''; ?>" 
                onclick="setSection('styles'); window.location.href='design-assets.php?section=styles'" type="button">
            <i class="bi bi-palette me-2"></i>Text Styles
        </button>
        <button class="nav-link <?php echo $section === 'logos' ? 'active' : ''; ?>" 
                onclick="setSection('logos'); window.location.href='design-assets.php?section=logos'" type="button">
            <i class="bi bi-image me-2"></i>Logos & Images
        </button>
    </div>
</div>

<?php if ($section === 'styles'): ?>
<!-- Text Styles Section -->

<div class="row">
    <div class="col-md-8">
        <div class="admin-card">
            <div class="admin-card-header">
                <h5 class="mb-0">Available Text Styles</h5>
            </div>
            <div class="table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Style Name</th>
                            <th>Type</th>
                            <th>Font Family</th>
                            <th>Size</th>
                            <th>Weight</th>
                            <th>Color</th>
                            <th>Preview</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($styles as $s): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($s['name']); ?></strong></td>
                            <td><span class="badge-blue"><?php echo ucfirst($s['style_type']); ?></span></td>
                            <td><?php echo htmlspecialchars(explode(',', $s['font_family'])[0]); ?></td>
                            <td><?php echo $s['font_size']; ?>px</td>
                            <td><?php echo ucfirst($s['font_weight']); ?></td>
                            <td>
                                <span style="display: inline-block; width: 20px; height: 20px; background: <?php echo htmlspecialchars($s['font_color']); ?>; border: 1px solid #ddd; border-radius: 3px;"></span>
                                <?php echo htmlspecialchars($s['font_color']); ?>
                            </td>
                            <td>
                                <div style="<?php echo "font-family: {$s['font_family']}; font-size: {$s['font_size']}px; font-weight: {$s['font_weight']}; color: {$s['font_color']}; line-height: {$s['line_height']}; text-align: {$s['text_align']};"; ?>">
                                    Sample Text
                                </div>
                            </td>
                            <td>
                                <?php if (!$s['is_default']): ?>
                                <a href="design-assets.php?delete_style=<?php echo $s['id']; ?>" class="btn-action" style="color: #ea580c;" onclick="return confirm('Delete this style?');">
                                    <i class="bi bi-trash"></i>
                                </a>
                                <?php else: ?>
                                <span class="badge-green">Default</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="admin-card">
            <div class="admin-card-header">
                <h5 class="mb-0">New Text Style</h5>
            </div>
            <div class="admin-card-body">
                <form method="POST">
                    <?php echo adminCsrfField(); ?>
                    <input type="hidden" name="style_action" value="1">

                    <div class="mb-3">
                        <label class="form-label">Style Name</label>
                        <input type="text" class="form-control" name="style_name" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Style Type</label>
                        <select class="form-control" name="style_type" required>
                            <option value="heading">Heading</option>
                            <option value="body">Body Text</option>
                            <option value="footer">Footer</option>
                            <option value="accent">Accent</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Font Family</label>
                        <select class="form-control" name="font_family" required>
                            <option value="Arial, sans-serif">Arial</option>
                            <option value="Georgia, serif">Georgia</option>
                            <option value="'Times New Roman', serif">Times New Roman</option>
                            <option value="'Courier New', monospace">Courier New</option>
                            <option value="Verdana, sans-serif">Verdana</option>
                            <option value="'Comic Sans MS', cursive">Comic Sans MS</option>
                        </select>
                    </div>

                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label">Font Size (px)</label>
                            <input type="number" class="form-control" name="font_size" value="14" min="8" max="72" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Font Weight</label>
                            <select class="form-control" name="font_weight" required>
                                <option value="normal">Normal</option>
                                <option value="bold">Bold</option>
                                <option value="500">Medium</option>
                                <option value="600">Semi-Bold</option>
                                <option value="lighter">Lighter</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Font Color</label>
                        <input type="color" class="form-control" name="font_color" value="#000000" required>
                    </div>

                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label">Line Height</label>
                            <input type="text" class="form-control" name="line_height" value="1.5" placeholder="e.g., 1.5, 1.6">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Text Align</label>
                            <select class="form-control" name="text_align" required>
                                <option value="left">Left</option>
                                <option value="center">Center</option>
                                <option value="right">Right</option>
                                <option value="justify">Justify</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn-admin-primary w-100">
                        <i class="bi bi-check-circle me-1"></i> Add Style
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php else: ?>
<!-- Logos Section -->

<div class="row">
    <div class="col-md-8">
        <div class="admin-card">
            <div class="admin-card-header">
                <h5 class="mb-0">Uploaded Logos</h5>
            </div>
            <?php if (empty($logos)): ?>
            <div class="admin-card-body text-center py-5">
                <i class="bi bi-image" style="font-size: 3rem; color: #ccc;"></i>
                <p class="text-muted mt-3">No logos uploaded yet. Upload your company logos below.</p>
            </div>
            <?php else: ?>
            <div class="table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Logo Name</th>
                            <th>Preview</th>
                            <th>Size</th>
                            <th>Dimensions</th>
                            <th>Uploaded</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logos as $l): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($l['name']); ?></strong></td>
                            <td>
                                <img src="<?php echo htmlspecialchars($l['file_path']); ?>" alt="Logo" style="max-height: 40px; max-width: 100px;">
                            </td>
                            <td><?php echo number_format($l['file_size'] / 1024, 1); ?> KB</td>
                            <td><?php echo $l['width'] . ' x ' . $l['height']; ?> px (estimated)</td>
                            <td><?php echo date('M d, Y', strtotime($l['created_at'])); ?></td>
                            <td>
                                <a href="design-assets.php?delete_logo=<?php echo $l['id']; ?>&section=logos" class="btn-action" style="color: #ea580c;" onclick="return confirm('Delete this logo?');">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="col-md-4">
        <div class="admin-card">
            <div class="admin-card-header">
                <h5 class="mb-0">Upload Logo</h5>
            </div>
            <div class="admin-card-body">
                <form method="POST" enctype="multipart/form-data">
                    <?php echo adminCsrfField(); ?>
                    <input type="hidden" name="logo_action" value="1">

                    <div class="mb-3">
                        <label class="form-label">Logo Name</label>
                        <input type="text" class="form-control" name="logo_name" placeholder="e.g., Company Logo" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Logo File</label>
                        <div class="upload-area" onclick="document.getElementById('logoFile').click()">
                            <i class="bi bi-cloud-upload" style="font-size: 2rem; color: #0066cc;"></i>
                            <p class="mt-2 mb-1"><strong>Click to upload</strong></p>
                            <p class="text-muted small">PNG, JPG, GIF, or WebP (Max 5MB)</p>
                        </div>
                        <input type="file" id="logoFile" name="logo_file" accept="image/*" style="display: none;" onchange="updateFileName(this)">
                        <small id="fileName" class="text-muted d-block mt-2"></small>
                    </div>

                    <button type="submit" class="btn-admin-primary w-100">
                        <i class="bi bi-upload me-1"></i> Upload Logo
                    </button>
                </form>

                <hr>

                <div class="bg-light p-3 rounded mt-3">
                    <h6 class="mb-2">Recommended Specifications:</h6>
                    <ul class="small mb-0">
                        <li>Format: PNG (transparent background)</li>
                        <li>Resolution: 300 DPI</li>
                        <li>Size: 150-300 px wide</li>
                        <li>File Size: &lt; 500 KB</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.upload-area {
    border: 2px dashed #0066cc;
    border-radius: 8px;
    padding: 30px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s;
    background: rgba(0, 102, 204, 0.05);
}

.upload-area:hover {
    background: rgba(0, 102, 204, 0.1);
    border-color: #004ba8;
}
</style>

<script>
function updateFileName(input) {
    const fileName = document.getElementById('fileName');
    if (input.files && input.files[0]) {
        fileName.textContent = '✓ ' + input.files[0].name;
    }
}
</script>

<?php endif; ?>

<?php require_once __DIR__ . '/layout-footer.php'; ?>
