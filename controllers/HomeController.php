<?php
require_once __DIR__ . '/../config/database.php';

class HomeController {
    public function index(): void {
        $db = getDB();

        // Fetch latest projects (up to 6)
        $stmt = $db->query("SELECT * FROM projects ORDER BY created_at DESC LIMIT 6");
        $projects = $stmt->fetchAll();

        // Fetch 3 latest blog posts
        $stmt = $db->query("SELECT id, title, slug, excerpt, featured_image, author, created_at FROM blog_posts ORDER BY created_at DESC LIMIT 3");
        $blogs = $stmt->fetchAll();

        $pageTitle = 'NexSoft Hub — Premium Software Consulting Agency';
        $metaDescription = 'NexSoft Hub delivers world-class web development, app development, UI/UX design, and digital solutions for businesses worldwide.';

        require_once __DIR__ . '/../components/header.php';
        require_once __DIR__ . '/../views/home.php';
        require_once __DIR__ . '/../components/footer.php';
    }
}
