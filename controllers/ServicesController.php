<?php
require_once __DIR__ . '/../config/database.php';

class ServicesController {
    public function index(): void {
        $pageTitle = 'Our Services — NexSoft Hub';
        $metaDescription = 'Explore NexSoft Hub\'s comprehensive digital services: web development, app development, WordPress, UI/UX design, content writing, and video editing.';

        require_once __DIR__ . '/../components/header.php';
        require_once __DIR__ . '/../views/services.php';
        require_once __DIR__ . '/../components/footer.php';
    }
}
