<?php
require_once __DIR__ . '/../config/database.php';

class AboutController {
    public function index(): void {
        $pageTitle       = 'About Us — NexSoft Hub';
        $metaDescription = 'Learn about NexSoft Hub — our mission, vision, story, and why businesses trust us for their digital transformation.';

        $db = getDB();
        // Gracefully handle the case where the table doesn't exist yet
        try {
            $teamMembers = $db->query("SELECT * FROM team_members ORDER BY sort_order ASC, id ASC")->fetchAll();
        } catch (\Exception $e) {
            $teamMembers = [];
        }

        require_once __DIR__ . '/../components/header.php';
        require_once __DIR__ . '/../views/about.php';
        require_once __DIR__ . '/../components/footer.php';
    }
}
