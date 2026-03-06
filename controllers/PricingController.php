<?php
require_once __DIR__ . '/../config/database.php';

class PricingController {
    public function index(): void {
        $pageTitle = 'Pricing Plans — NexSoft Hub';
        $metaDescription = 'Choose the right plan for your project. NexSoft Hub offers flexible pricing for startups, growing businesses, and enterprises.';

        require_once __DIR__ . '/../components/header.php';
        require_once __DIR__ . '/../views/pricing.php';
        require_once __DIR__ . '/../components/footer.php';
    }
}
