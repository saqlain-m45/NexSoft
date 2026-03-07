<?php
/**
 * NexSoft Hub - Admin Auth Helper
 * Include this at the top of every protected admin page.
 */
require_once __DIR__ . '/../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function ensureUsersRoleSchema(PDO $db): void {
    static $checked = false;
    if ($checked) {
        return;
    }

    // Keep existing installations compatible by adding role support lazily.
    try {
        $db->exec("ALTER TABLE users ADD COLUMN role VARCHAR(50) NOT NULL DEFAULT 'viewer' AFTER password");
    } catch (PDOException $e) {
        // Column likely already exists.
    }

    try {
        $db->exec("UPDATE users SET role = 'super_admin' WHERE username = 'admin' AND (role IS NULL OR role = '' OR role = 'viewer')");
    } catch (PDOException $e) {
        // Best-effort only.
    }

    $checked = true;
}

function adminRoleConfig(): array {
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
            'permissions' => ['dashboard', 'registrations', 'messages'],
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

function normalizeAdminRole(?string $role): string {
    $role = strtolower(trim((string)$role));
    return array_key_exists($role, adminRoleConfig()) ? $role : 'viewer';
}

function adminCheck(): void {
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
}

function adminLogin(string $username, string $password): bool {
    $db = getDB();
    ensureUsersRoleSchema($db);

    $stmt = $db->prepare("SELECT id, username, password, role FROM users WHERE username = ? LIMIT 1");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['admin_id']   = $user['id'];
        $_SESSION['admin_user'] = $user['username'];
        $_SESSION['admin_role'] = normalizeAdminRole($user['role'] ?? 'viewer');
        return true;
    }

    return false;
}

function adminLogout(): void {
    session_destroy();
    header('Location: /NexSoft/admin/login.php');
    exit;
}

function adminUsername(): string {
    return htmlspecialchars($_SESSION['admin_user'] ?? 'Admin');
}

function adminRole(): string {
    return normalizeAdminRole($_SESSION['admin_role'] ?? 'viewer');
}

function adminRoleLabel(): string {
    $role = adminRole();
    $config = adminRoleConfig();
    return $config[$role]['label'] ?? 'Viewer';
}

function adminHasPermission(string $permission): bool {
    if ($permission === 'profile') {
        return true;
    }

    $role = adminRole();
    $config = adminRoleConfig();
    $permissions = $config[$role]['permissions'] ?? [];

    return in_array('*', $permissions, true) || in_array($permission, $permissions, true);
}

function adminRequirePermission(string $permission): void {
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
function adminUrl(string $page = ''): string {
    return '/NexSoft/admin/' . ltrim($page, '/');
}

// Admin asset URL
function adminAsset(string $path): string {
    return '/NexSoft/assets/' . ltrim($path, '/');
}
