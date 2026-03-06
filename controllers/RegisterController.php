<?php
require_once __DIR__ . '/../config/database.php';

class RegisterController {
    public function index(): void {
        $pageTitle = 'Join Our Team — NexSoft Hub';
        $metaDescription = 'Join NexSoft Hub as a freelancer or team member. Submit your application today.';
        $success = '';
        $error = '';

        require_once __DIR__ . '/../components/header.php';
        require_once __DIR__ . '/../views/register.php';
        require_once __DIR__ . '/../components/footer.php';
    }

    public function store(): void {
        $pageTitle = 'Join Our Team — NexSoft Hub';
        $metaDescription = 'Join NexSoft Hub as a freelancer or team member.';
        $success = '';
        $error = '';

        $name      = sanitize($_POST['name'] ?? '');
        $email     = sanitize($_POST['email'] ?? '');
        $phone     = sanitize($_POST['phone'] ?? '');
        $skills    = sanitize($_POST['skills'] ?? '');
        $portfolio = sanitize($_POST['portfolio'] ?? '');
        $message   = sanitize($_POST['message'] ?? '');

        if (empty($name) || strlen($name) < 2) {
            $error = 'Please enter your full name.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please enter a valid email address.';
        } elseif (empty($phone)) {
            $error = 'Please enter your phone number.';
        } elseif (empty($skills)) {
            $error = 'Please describe your skills/expertise.';
        } elseif (empty($message) || strlen($message) < 10) {
            $error = 'Please write a message of at least 10 characters.';
        } else {
            try {
                $db = getDB();
                $stmt = $db->prepare("INSERT INTO registrations (name, email, phone, skills, portfolio_link, message) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$name, $email, $phone, $skills, $portfolio, $message]);
                $success = 'Your application has been submitted successfully! We will review it and get back to you.';
            } catch (PDOException $e) {
                $error = 'Something went wrong. Please try again later.';
            }
        }

        require_once __DIR__ . '/../components/header.php';
        require_once __DIR__ . '/../views/register.php';
        require_once __DIR__ . '/../components/footer.php';
    }
}
