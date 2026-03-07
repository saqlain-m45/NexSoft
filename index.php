<?php
/**
 * NexSoft Hub - Main Router
 * All frontend requests pass through this file
 */

require_once __DIR__ . '/config/database.php';

$route = $_GET['route'] ?? 'home';
$route = trim($route, '/');

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
    'internships' => ['controllers/InternshipsController.php', 'InternshipsController'],
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