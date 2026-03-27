<?php
/**
 * NexSoft Hub - Main Router
 * All frontend requests pass through this file
 */

require_once __DIR__ . '/config/database.php';

// Canonicalize legacy query routes like /?route=about -> /about.
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['route'])) {
    $legacyRoute = trim((string)$_GET['route'], '/');
    $extraQueryKeys = array_diff(array_keys($_GET), ['route']);
    if (empty($extraQueryKeys)) {
        if ($legacyRoute === 'home' || $legacyRoute === '') {
            header('Location: ' . baseUrl(), true, 301);
            exit;
        }
        header('Location: ' . baseUrl($legacyRoute), true, 301);
        exit;
    }
}

$route = $_GET['route'] ?? 'home';
$route = trim($route, '/');

// Canonical URL: keep old /internships route working but redirect to /internship.
if ($route === 'internships') {
    header('Location: ' . baseUrl('internship'), true, 301);
    exit;
}

// Define valid routes and their controllers
$routes = [
    'home' => ['controllers/HomeController.php', 'HomeController'],
    'about' => ['controllers/AboutController.php', 'AboutController'],
    'services' => ['controllers/ServicesController.php', 'ServicesController'],
    'blog' => ['controllers/BlogController.php', 'BlogController'],
    'blog-single' => ['controllers/BlogController.php', 'BlogController'],
    'pricing' => ['controllers/PricingController.php', 'PricingController'],
    'contact' => ['controllers/ContactController.php', 'ContactController'],
    'register' => ['controllers/RegisterController.php', 'RegisterController'],
    'courses' => ['controllers/CoursesController.php', 'CoursesController'],
    'internship' => ['controllers/InternshipsController.php', 'InternshipsController'],
    'internships' => ['controllers/InternshipsController.php', 'InternshipsController'],
    'verify' => ['controllers/VerifyController.php', 'VerifyController'],
];

if (isset($routes[$route])) {
    [$file, $class] = $routes[$route];
    require_once __DIR__ . '/' . $file;
    $controller = new $class();

    if ($route === 'contact' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller->store();
    }
    elseif ($route === 'register' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller->store();
    }
    elseif ($route === 'courses' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller->store();
    }
    elseif ($route === 'blog-single') {
        $controller->single();
    }
    else {
        $controller->index();
    }
}
else {
    http_response_code(404);
    require_once __DIR__ . '/views/404.php';
}