<?php
require_once __DIR__ . '/../config/database.php';

class ContactController {
    public function index(): void {
        $pageTitle = 'Contact Us — NexSoft Hub';
        $metaDescription = 'Get in touch with NexSoft Hub. We would love to discuss your project.';
        $success = '';
        $error = '';

        require_once __DIR__ . '/../components/header.php';
        require_once __DIR__ . '/../views/contact.php';
        require_once __DIR__ . '/../components/footer.php';
    }

    public function store(): void {
        $pageTitle = 'Contact Us — NexSoft Hub';
        $metaDescription = 'Get in touch with NexSoft Hub.';
        $success = '';
        $error = '';

        $name    = sanitize($_POST['name'] ?? '');
        $email   = sanitize($_POST['email'] ?? '');
        $message = sanitize($_POST['message'] ?? '');

        if (empty($name) || strlen($name) < 2) {
            $error = 'Please enter your full name.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please enter a valid email address.';
        } elseif (empty($message) || strlen($message) < 10) {
            $error = 'Message must be at least 10 characters.';
        } else {
            try {
                $db = getDB();
                $stmt = $db->prepare("INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)");
                $stmt->execute([$name, $email, $message]);
                $success = 'Thank you! Your message has been sent. We will get back to you shortly.';
            } catch (PDOException $e) {
                $error = 'Something went wrong. Please try again later.';
            }
        }

        require_once __DIR__ . '/../components/header.php';
        require_once __DIR__ . '/../views/contact.php';
        require_once __DIR__ . '/../components/footer.php';
    }
}
