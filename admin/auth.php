<?php
/**
 * NexSoft Hub - Admin Auth Helper
 * Include this at the top of every protected admin page.
 */
require_once __DIR__ . '/../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function adminCheck(): void {
    if (!isset($_SESSION['admin_id'])) {
        header('Location: /NexSoft/admin/login.php');
        exit;
    }
}

function adminLogin(string $username, string $password): bool {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['admin_id']   = $user['id'];
        $_SESSION['admin_user'] = $user['username'];
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

// Admin base URL helper
function adminUrl(string $page = ''): string {
    return '/NexSoft/admin/' . ltrim($page, '/');
}

// Admin asset URL
function adminAsset(string $path): string {
    return '/NexSoft/assets/' . ltrim($path, '/');
}
