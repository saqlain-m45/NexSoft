<?php
/**
 * NexSoft Hub - Database Configuration
 * PDO connection with error handling
 */

// Absolute path root — used for reliable file_exists() checks across any included file
define('ROOT_PATH', realpath(__DIR__ . '/..'));

define('DB_HOST', 'localhost');
define('DB_NAME', 'nexsoft_hub');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die('<div style="font-family:sans-serif;background:#0B1F3B;color:#fff;padding:40px;text-align:center;">
                <h2>Database Connection Error</h2>
                <p>Please ensure MySQL is running and the database <strong>nexsoft_hub</strong> exists.</p>
                <p style="color:#0EA5A4;font-size:12px;">' . htmlspecialchars($e->getMessage()) . '</p>
                <p>Import <strong>/database/nexsoft_hub.sql</strong> in phpMyAdmin to get started.</p>
            </div>');
        }
    }
    return $pdo;
}

// Base URL helper
function baseUrl(string $path = ''): string {
    $base = '/NexSoft';
    return $base . ($path ? '/' . ltrim($path, '/') : '');
}

// Asset URL helper
function asset(string $path): string {
    return baseUrl('assets/' . ltrim($path, '/'));
}

// Redirect helper
function redirect(string $route): void {
    header('Location: ' . baseUrl('?route=' . $route));
    exit;
}

// Sanitize input
function sanitize(string $input): string {
    return htmlspecialchars(strip_tags(trim($input)));
}

// Escape HTML shortcut
function h($string) {
    return htmlspecialchars($string ?? '');
}

/**
 * Fetch a single site setting by key
 */
function getSetting(string $key, string $default = ''): string {
    static $settings = null;
    if ($settings === null) {
        $db = getDB();
        $stmt = $db->query("SELECT setting_key, setting_value FROM site_settings");
        $settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }
    return $settings[$key] ?? $default;
}

/**
 * Fetch all site settings
 */
function getSettings(): array {
    $db = getDB();
    $stmt = $db->query("SELECT setting_key, setting_value FROM site_settings");
    return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
}

// Session start
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Maintenance Mode Check
 */
if (getSetting('maintenance_mode', '0') === '1') {
    // Allow admins to skip maintenance mode
    if (!isset($_SESSION['admin_id'])) {
        die('
        <div style="font-family:sans-serif;height:100vh;display:flex;flex-direction:column;justify-content:center;align-items:center;background:#0B1F3B;color:#fff;text-align:center;padding:20px;">
            <h1 style="font-size:3rem;margin-bottom:1rem;color:#0EA5A4;">Under Maintenance</h1>
            <p style="font-size:1.2rem;max-width:600px;opacity:0.8;">We are currently performing some scheduled updates to improve your experience. We\'ll be back online shortly!</p>
            <div style="margin-top:2rem;width:50px;height:5px;background:#0EA5A4;"></div>
        </div>');
    }
}
