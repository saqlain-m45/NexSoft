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

function getDB(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        }
        catch (PDOException $e) {
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
function baseUrl(string $path = ''): string
{
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    $dir = str_replace('\\', '/', dirname($scriptName));
    $base = ($dir === '/' || $dir === '.') ? '' : rtrim($dir, '/');

    if ($path === '') {
        return $base === '' ? '/' : $base;
    }

    return ($base === '' ? '' : $base) . '/' . ltrim($path, '/');
}

// Asset URL helper
function asset(string $path): string
{
    return baseUrl('assets/' . ltrim($path, '/'));
}

// Redirect helper
function redirect(string $route): void
{
    header('Location: ' . baseUrl('?route=' . $route));
    exit;
}

// Sanitize input
function sanitize(string $input): string
{
    return htmlspecialchars(strip_tags(trim($input)));
}

// Escape HTML shortcut
function h($string)
{
    return htmlspecialchars($string ?? '');
}

/**
 * Fetch a single site setting by key
 */
function getSetting(string $key, string $default = ''): string
{
    static $settings = null;
    if ($settings === null) {
        try {
            $db = getDB();
            $stmt = $db->query("SELECT setting_key, setting_value FROM site_settings");
            $settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        }
        catch (PDOException $e) {
            return $default;
        }
    }
    return $settings[$key] ?? $default;
}

/**
 * Fetch all site settings
 */
function getSettings(): array
{
    try {
        $db = getDB();
        $stmt = $db->query("SELECT setting_key, setting_value FROM site_settings");
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }
    catch (PDOException $e) {
        return [];
    }
}

// Session start
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Maintenance Mode Check
 */
try {
    if (getSetting('maintenance_mode', '0') === '1') {
        $requestUri = $_SERVER['REQUEST_URI'] ?? '';
        $isAdminArea = strpos($requestUri, '/admin/') !== false;

        // Keep admin panel reachable while frontend stays under maintenance.
        if (!$isAdminArea && !isset($_SESSION['admin_id'])) {
            http_response_code(503);
            header('Retry-After: 3600');
            // Try to use maintenance view if it exists
            if (file_exists(ROOT_PATH . '/views/maintenance.php')) {
                require ROOT_PATH . '/views/maintenance.php';
            }
            else {
                die('<h1>Under Maintenance</h1><p>We\'ll be back shortly!</p>');
            }
            exit;
        }
    }
}
catch (Exception $e) {
// Gracefully ignore errors during maintenance check (e.g. missing table)
}