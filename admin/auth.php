<?php
/**
 * NexSoft Hub - Admin Auth Helper
 * Include this at the top of every protected admin page.
 */
require_once __DIR__ . '/../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function ensureUsersRoleSchema(PDO $db): void
{
    static $checked = false;
    if ($checked) {
        return;
    }

    // Keep existing installations compatible by adding role support lazily.
    try {
        $db->exec("ALTER TABLE users ADD COLUMN role VARCHAR(50) NOT NULL DEFAULT 'viewer' AFTER password");
    }
    catch (PDOException $e) {
    // Column likely already exists.
    }

    try {
        $db->exec("UPDATE users SET role = 'super_admin' WHERE username = 'admin' AND (role IS NULL OR role = '' OR role = 'viewer')");
    }
    catch (PDOException $e) {
    // Best-effort only.
    }

    $checked = true;
}

function adminRoleConfig(): array
{
    return [
        'super_admin' => [
            'label' => 'Super Admin',
            'permissions' => ['*'],
        ],
        'content_manager' => [
            'label' => 'Content Manager',
            'permissions' => ['dashboard', 'projects', 'blogs', 'services', 'testimonials'],
        ],
        'team_manager' => [
            'label' => 'Team Manager',
            'permissions' => ['dashboard', 'team'],
        ],
        'crm_manager' => [
            'label' => 'CRM Manager',
            'permissions' => ['dashboard', 'registrations', 'messages', 'courses'],
        ],
        'settings_manager' => [
            'label' => 'Settings Manager',
            'permissions' => ['dashboard', 'settings'],
        ],
        'viewer' => [
            'label' => 'Viewer',
            'permissions' => ['dashboard'],
        ],
    ];
}

function normalizeAdminRole(?string $role): string
{
    $role = strtolower(trim((string)$role));
    return array_key_exists($role, adminRoleConfig()) ? $role : 'viewer';
}

function ensureAuditLogSchema(PDO $db): void
{
    static $checked = false;
    if ($checked) {
        return;
    }

    $db->exec("CREATE TABLE IF NOT EXISTS admin_activity_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        admin_id INT NULL,
        admin_username VARCHAR(120) NULL,
        action VARCHAR(120) NOT NULL,
        details TEXT NULL,
        ip_address VARCHAR(45) NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_admin_id (admin_id),
        INDEX idx_action (action),
        INDEX idx_created_at (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    $checked = true;
}

function adminLogAction(string $action, string $details = ''): void
{
    try {
        $db = getDB();
        ensureAuditLogSchema($db);
        $stmt = $db->prepare('INSERT INTO admin_activity_logs (admin_id, admin_username, action, details, ip_address) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([
            (int)($_SESSION['admin_id'] ?? 0) ?: null,
            (string)($_SESSION['admin_user'] ?? 'guest'),
            $action,
            $details,
            (string)($_SERVER['REMOTE_ADDR'] ?? ''),
        ]);
    }
    catch (Throwable $e) {
    // Do not break admin UX if audit logging fails.
    }
}

function adminCsrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return (string)$_SESSION['csrf_token'];
}

function adminCsrfField(): string
{
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(adminCsrfToken(), ENT_QUOTES, 'UTF-8') . '">';
}

function adminValidateCsrfFromRequest(): bool
{
    $token = (string)($_POST['csrf_token'] ?? '');
    $sessionToken = (string)($_SESSION['csrf_token'] ?? '');
    return $token !== '' && $sessionToken !== '' && hash_equals($sessionToken, $token);
}

function adminEnforceCsrfForPost(): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
        return;
    }

    if (!adminValidateCsrfFromRequest()) {
        http_response_code(419);
        echo '<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Security Check Failed</title></head><body style="font-family:Arial,sans-serif;background:#f8f9fb;padding:40px;">';
        echo '<div style="max-width:640px;margin:0 auto;background:#fff;border:1px solid #e6e8f0;border-radius:12px;padding:24px;">';
        echo '<h2 style="margin-top:0">Security token mismatch</h2><p>Your session token is missing or expired. Please refresh and try again.</p>';
        echo '<p><a href="' . htmlspecialchars($_SERVER['REQUEST_URI'] ?? '/NexSoft/admin/dashboard.php') . '">Reload page</a></p></div></body></html>';
        exit;
    }
}

function adminIsStrongPassword(string $password): bool
{
    // 8+ chars, upper, lower, number and special char.
    return (bool)preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z\d]).{8,}$/', $password);
}

function adminStrongPasswordHint(): string
{
    return 'Use at least 8 characters with uppercase, lowercase, number, and symbol.';
}

function adminOptimizeAndSaveImage(string $tmpPath, string $destDir, string $prefix, int $maxWidth = 1600, int $jpegQuality = 82): string
{
    $imgInfo = @getimagesize($tmpPath);
    if (!$imgInfo || empty($imgInfo['mime'])) {
        throw new RuntimeException('Invalid image file.');
    }

    $mime = strtolower($imgInfo['mime']);
    $extMap = [
        'image/jpeg' => 'jpg',
        'image/jpg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
    ];

    if (!isset($extMap[$mime])) {
        throw new RuntimeException('Unsupported image type.');
    }

    if (!is_dir($destDir) && !mkdir($destDir, 0755, true) && !is_dir($destDir)) {
        throw new RuntimeException('Upload directory is not writable.');
    }

    $filename = uniqid($prefix . '_', true) . '.' . $extMap[$mime];
    $targetPath = rtrim($destDir, '/\\') . DIRECTORY_SEPARATOR . $filename;

    if (!function_exists('imagecreatetruecolor')) {
        if (!move_uploaded_file($tmpPath, $targetPath)) {
            throw new RuntimeException('Unable to save image.');
        }
        return $filename;
    }

    $width = (int)$imgInfo[0];
    $height = (int)$imgInfo[1];

    $source = match ($mime) {
            'image/jpeg', 'image/jpg' => @imagecreatefromjpeg($tmpPath),
            'image/png' => @imagecreatefrompng($tmpPath),
            'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($tmpPath) : false,
            'image/gif' => @imagecreatefromgif($tmpPath),
            default => false,
        };

    if (!$source) {
        if (!move_uploaded_file($tmpPath, $targetPath)) {
            throw new RuntimeException('Unable to process image.');
        }
        return $filename;
    }

    $newWidth = $width;
    $newHeight = $height;
    if ($width > $maxWidth) {
        $ratio = $maxWidth / $width;
        $newWidth = $maxWidth;
        $newHeight = (int)round($height * $ratio);
    }

    $canvas = imagecreatetruecolor($newWidth, $newHeight);
    if ($mime === 'image/png' || $mime === 'image/gif' || $mime === 'image/webp') {
        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);
        $transparent = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
        imagefilledrectangle($canvas, 0, 0, $newWidth, $newHeight, $transparent);
    }

    imagecopyresampled($canvas, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

    $saved = match ($mime) {
            'image/jpeg', 'image/jpg' => imagejpeg($canvas, $targetPath, $jpegQuality),
            'image/png' => imagepng($canvas, $targetPath, 6),
            'image/gif' => imagegif($canvas, $targetPath),
            'image/webp' => function_exists('imagewebp') ? imagewebp($canvas, $targetPath, $jpegQuality) : imagepng($canvas, $targetPath, 6),
            default => false,
        };

    imagedestroy($source);
    imagedestroy($canvas);

    if (!$saved) {
        throw new RuntimeException('Unable to save optimized image.');
    }

    return $filename;
}

function adminCheck(): void
{
    if (!isset($_SESSION['admin_id'])) {
        header('Location: /NexSoft/admin/login.php');
        exit;
    }

    if (!isset($_SESSION['admin_role'])) {
        $db = getDB();
        ensureUsersRoleSchema($db);
        $stmt = $db->prepare('SELECT role FROM users WHERE id = ? LIMIT 1');
        $stmt->execute([$_SESSION['admin_id']]);
        $_SESSION['admin_role'] = normalizeAdminRole($stmt->fetchColumn() ?: 'viewer');
    }

    adminCsrfToken();
    adminEnforceCsrfForPost();
}

function adminLogin(string $username, string $password): bool
{
    $db = getDB();
    ensureUsersRoleSchema($db);

    $stmt = $db->prepare("SELECT id, username, password, role FROM users WHERE username = ? LIMIT 1");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['admin_user'] = $user['username'];
        $_SESSION['admin_role'] = normalizeAdminRole($user['role'] ?? 'viewer');
        adminCsrfToken();
        adminLogAction('auth.login', 'Successful login');
        return true;
    }

    adminLogAction('auth.login_failed', 'Failed login attempt for username: ' . $username);

    return false;
}

function adminLogout(): void
{
    adminLogAction('auth.logout', 'User logged out');
    session_destroy();
    header('Location: /NexSoft/admin/login.php');
    exit;
}

function adminUsername(): string
{
    return htmlspecialchars($_SESSION['admin_user'] ?? 'Admin');
}

function adminRole(): string
{
    return normalizeAdminRole($_SESSION['admin_role'] ?? 'viewer');
}

function adminRoleLabel(): string
{
    $role = adminRole();
    $config = adminRoleConfig();
    return $config[$role]['label'] ?? 'Viewer';
}

function adminHasPermission(string $permission): bool
{
    if ($permission === 'profile') {
        return true;
    }

    $role = adminRole();
    $config = adminRoleConfig();
    $permissions = $config[$role]['permissions'] ?? [];

    return in_array('*', $permissions, true) || in_array($permission, $permissions, true);
}

function adminRequirePermission(string $permission): void
{
    adminCheck();

    if (adminHasPermission($permission)) {
        return;
    }

    http_response_code(403);
    echo '<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Access Denied</title>';
    echo '<style>body{font-family:Arial,sans-serif;background:#f8f9fb;padding:40px;color:#111} .box{max-width:680px;background:#fff;border:1px solid #e6e8f0;border-radius:12px;padding:24px;margin:40px auto} a{color:#0d6efd;text-decoration:none}</style>';
    echo '</head><body><div class="box"><h1 style="margin-top:0">Access denied</h1>';
    echo '<p>Your role <strong>' . htmlspecialchars(adminRoleLabel()) . '</strong> cannot access this area.</p>';
    echo '<p><a href="' . htmlspecialchars(adminUrl('dashboard.php')) . '">Back to dashboard</a></p>';
    echo '</div></body></html>';
    exit;
}

// Admin base URL helper
function adminUrl(string $page = ''): string
{
    return '/NexSoft/admin/' . ltrim($page, '/');
}

// Admin asset URL
function adminAsset(string $path): string
{
    return '/NexSoft/assets/' . ltrim($path, '/');
}