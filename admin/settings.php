<?php
require_once __DIR__ . '/auth.php';
adminCheck();

$db    = getDB();
$msg   = '';
$error = '';

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $settings = [
        'site_name'             => $_POST['site_name'] ?? '',
        'meta_title'            => $_POST['meta_title'] ?? '',
        'meta_description'      => $_POST['meta_description'] ?? '',
        'meta_keywords'         => $_POST['meta_keywords'] ?? '',
        'site_email'            => $_POST['site_email'] ?? '',
        'site_phone'            => $_POST['site_phone'] ?? '',
        'site_address'          => $_POST['site_address'] ?? '',
        'facebook_link'         => $_POST['facebook_link'] ?? '',
        'twitter_link'          => $_POST['twitter_link'] ?? '',
        'linkedin_link'         => $_POST['linkedin_link'] ?? '',
        'instagram_link'        => $_POST['instagram_link'] ?? '',
        'github_link'           => $_POST['github_link'] ?? '',
        'smtp_host'             => $_POST['smtp_host'] ?? '',
        'smtp_port'             => $_POST['smtp_port'] ?? '',
        'smtp_user'             => $_POST['smtp_user'] ?? '',
        'smtp_pass'             => $_POST['smtp_pass'] ?? '',
        'smtp_encryption'       => $_POST['smtp_encryption'] ?? '',
        'google_analytics_id'   => $_POST['google_analytics_id'] ?? '',
        'custom_head_scripts'   => $_POST['custom_head_scripts'] ?? '',
        'custom_footer_scripts' => $_POST['custom_footer_scripts'] ?? '',
        'custom_cursor_enabled' => $_POST['custom_cursor_enabled'] ?? '0',
        'maintenance_mode'      => $_POST['maintenance_mode'] ?? '0'
    ];

    try {
        $db->beginTransaction();
        $stmt = $db->prepare("INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?) 
                              ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
        foreach ($settings as $key => $value) {
            $stmt->execute([$key, $value]);
        }
        $db->commit();
        $msg = 'Site settings updated successfully!';
    } catch (Exception $e) {
        $db->rollBack();
        $error = 'Failed to update settings: ' . $e->getMessage();
    }
}

// Fetch current settings
$stmt = $db->query("SELECT setting_key, setting_value FROM site_settings");
$currentSettings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

$pageTitle  = 'Site Settings — NexSoft Hub Admin';
$activePage = 'settings';
require_once __DIR__ . '/layout-header.php';
?>

<?php if (!empty($msg)): ?>
<div class="admin-alert-success mb-3"><i class="bi bi-check-circle me-2"></i><?php echo htmlspecialchars($msg); ?></div>
<?php endif; ?>
<?php if (!empty($error)): ?>
<div class="admin-alert-error mb-3"><i class="bi bi-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<div class="admin-card">
    <div class="admin-card-header">
        <span class="admin-card-title"><i class="bi bi-gear-fill me-2" style="color:var(--secondary);"></i>Global Site Settings</span>
    </div>
    <div class="admin-card-body">
        <form method="POST" class="admin-form">
            <div class="row g-4">
                <!-- General Settings -->
                <div class="col-12">
                    <h5 class="mb-3 border-bottom pb-2 text-primary">General Configuration</h5>
                </div>
                <div class="col-md-6">
                    <label>Site Name</label>
                    <input type="text" name="site_name" class="form-control" 
                           value="<?php echo htmlspecialchars($currentSettings['site_name'] ?? ''); ?>" placeholder="e.g. NexSoft Hub">
                </div>

                <!-- SEO Settings -->
                <div class="col-12 mt-4">
                    <h5 class="mb-3 border-bottom pb-2 text-primary">SEO & Meta Tags</h5>
                </div>
                <div class="col-12">
                    <label>Meta Title (SEO Title)</label>
                    <input type="text" name="meta_title" class="form-control" 
                           value="<?php echo htmlspecialchars($currentSettings['meta_title'] ?? ''); ?>" placeholder="NexSoft Hub — Software Development Agency">
                </div>
                <div class="col-12">
                    <label>Meta Description</label>
                    <textarea name="meta_description" class="form-control" rows="3"><?php echo htmlspecialchars($currentSettings['meta_description'] ?? ''); ?></textarea>
                </div>
                <div class="col-12">
                    <label>Meta Keywords (comma separated)</label>
                    <input type="text" name="meta_keywords" class="form-control" 
                           value="<?php echo htmlspecialchars($currentSettings['meta_keywords'] ?? ''); ?>" placeholder="web, app, digital, etc.">
                </div>

                <!-- Contact Settings -->
                <div class="col-12 mt-4">
                    <h5 class="mb-3 border-bottom pb-2 text-primary">Contact Information</h5>
                </div>
                <div class="col-md-6">
                    <label>Site Email</label>
                    <input type="email" name="site_email" class="form-control" 
                           value="<?php echo htmlspecialchars($currentSettings['site_email'] ?? ''); ?>">
                </div>
                <div class="col-md-6">
                    <label>Site Phone</label>
                    <input type="text" name="site_phone" class="form-control" 
                           value="<?php echo htmlspecialchars($currentSettings['site_phone'] ?? ''); ?>">
                </div>
                <div class="col-12">
                    <label>Office Address</label>
                    <input type="text" name="site_address" class="form-control" 
                           value="<?php echo htmlspecialchars($currentSettings['site_address'] ?? ''); ?>">
                </div>

                <!-- Social Media -->
                <div class="col-12 mt-4">
                    <h5 class="mb-3 border-bottom pb-2 text-primary">Social Media Links</h5>
                </div>
                <div class="col-md-6">
                    <label><i class="bi bi-facebook me-1"></i> Facebook</label>
                    <input type="url" name="facebook_link" class="form-control" 
                           value="<?php echo htmlspecialchars($currentSettings['facebook_link'] ?? ''); ?>">
                </div>
                <div class="col-md-6">
                    <label><i class="bi bi-twitter-x me-1"></i> Twitter / X</label>
                    <input type="url" name="twitter_link" class="form-control" 
                           value="<?php echo htmlspecialchars($currentSettings['twitter_link'] ?? ''); ?>">
                </div>
                <div class="col-md-6">
                    <label><i class="bi bi-linkedin me-1"></i> LinkedIn</label>
                    <input type="url" name="linkedin_link" class="form-control" 
                           value="<?php echo htmlspecialchars($currentSettings['linkedin_link'] ?? ''); ?>">
                </div>
                <div class="col-md-6">
                    <label><i class="bi bi-instagram me-1"></i> Instagram</label>
                    <input type="url" name="instagram_link" class="form-control" 
                           value="<?php echo htmlspecialchars($currentSettings['instagram_link'] ?? ''); ?>">
                </div>
                <div class="col-md-6">
                    <label><i class="bi bi-github me-1"></i> GitHub</label>
                    <input type="url" name="github_link" class="form-control" 
                           value="<?php echo htmlspecialchars($currentSettings['github_link'] ?? ''); ?>">
                </div>

                <!-- SMTP Settings -->
                <div class="col-12 mt-4">
                    <h5 class="mb-3 border-bottom pb-2 text-primary">SMTP Email Configuration</h5>
                </div>
                <div class="col-md-6">
                    <label>SMTP Host</label>
                    <input type="text" name="smtp_host" class="form-control" 
                           value="<?php echo htmlspecialchars($currentSettings['smtp_host'] ?? ''); ?>" placeholder="smtp.gmail.com">
                </div>
                <div class="col-md-3">
                    <label>SMTP Port</label>
                    <input type="text" name="smtp_port" class="form-control" 
                           value="<?php echo htmlspecialchars($currentSettings['smtp_port'] ?? ''); ?>" placeholder="587">
                </div>
                <div class="col-md-3">
                    <label>Encryption</label>
                    <select name="smtp_encryption" class="form-control">
                        <option value="tls" <?php echo ($currentSettings['smtp_encryption'] ?? '') === 'tls' ? 'selected' : ''; ?>>TLS</option>
                        <option value="ssl" <?php echo ($currentSettings['smtp_encryption'] ?? '') === 'ssl' ? 'selected' : ''; ?>>SSL</option>
                        <option value="none" <?php echo ($currentSettings['smtp_encryption'] ?? '') === 'none' ? 'selected' : ''; ?>>None</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label>SMTP Username</label>
                    <input type="text" name="smtp_user" class="form-control" 
                           value="<?php echo htmlspecialchars($currentSettings['smtp_user'] ?? ''); ?>">
                </div>
                <div class="col-md-6">
                    <label>SMTP Password</label>
                    <input type="password" name="smtp_pass" class="form-control" 
                           value="<?php echo htmlspecialchars($currentSettings['smtp_pass'] ?? ''); ?>">
                </div>

                <!-- Advanced Settings -->
                <div class="col-12 mt-4">
                    <h5 class="mb-3 border-bottom pb-2 text-primary">Advanced & Scripts</h5>
                </div>
                <div class="col-md-6">
                    <label>Google Analytics ID (G-XXXXXXX)</label>
                    <input type="text" name="google_analytics_id" class="form-control" 
                           value="<?php echo htmlspecialchars($currentSettings['google_analytics_id'] ?? ''); ?>">
                </div>
                <div class="col-md-6 d-flex align-items-center pt-4">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="maintenance_mode" value="1" id="mtToggle" 
                               <?php echo ($currentSettings['maintenance_mode'] ?? '0') == '1' ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="mtToggle">Enable Maintenance Mode</label>
                    </div>
                </div>
                <div class="col-md-6 d-flex align-items-center pt-4">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="custom_cursor_enabled" value="1" id="cursorToggle" 
                               <?php echo ($currentSettings['custom_cursor_enabled'] ?? '1') == '1' ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="cursorToggle">Enable Custom Glowing Cursor</label>
                    </div>
                </div>
                <div class="col-12">
                    <label>Custom Head Scripts (e.g. meta tags, tracking pixels)</label>
                    <textarea name="custom_head_scripts" class="form-control" rows="4"><?php echo htmlspecialchars($currentSettings['custom_head_scripts'] ?? ''); ?></textarea>
                </div>
                <div class="col-12">
                    <label>Custom Footer Scripts (JS before &lt;/body&gt;)</label>
                    <textarea name="custom_footer_scripts" class="form-control" rows="4"><?php echo htmlspecialchars($currentSettings['custom_footer_scripts'] ?? ''); ?></textarea>
                </div>

                <div class="col-12 pt-3 border-top mt-4">
                    <button type="submit" class="btn-admin-primary">
                        <i class="bi bi-save me-1"></i> Save All Settings
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/layout-footer.php'; ?>
