<?php
require_once __DIR__ . '/../config/database.php';

class CoursesController
{
    public function index()
    {
        $db = getDB();
        $courses = $db->query("SELECT * FROM courses WHERE registration_open = 1 ORDER BY created_at DESC")->fetchAll();

        $pageTitle = 'Professional Courses — NexSoft Hub';
        require_once __DIR__ . '/../views/courses.php';
    }

    public function store()
    {
        $db = getDB();
        $course_id = (int)($_POST['course_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $message = trim($_POST['message'] ?? '');

        if (!$course_id || !$name || !$email) {
            $_SESSION['error'] = 'Please fill in all required fields.';
            header('Location: ' . baseUrl('courses'));
            exit;
        }

        $stmt = $db->prepare("INSERT INTO course_registrations (course_id, name, email, phone, message) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$course_id, $name, $email, $phone, $message])) {
            $_SESSION['success'] = 'Your registration has been submitted successfully! We will contact you soon.';

            // Send Email Notifications
            require_once __DIR__ . '/../config/mailer.php';

            // Get Course Title for email
            $course = $db->prepare("SELECT title FROM courses WHERE id = ?");
            $course->execute([$course_id]);
            $courseInfo = $course->fetch();
            $courseTitle = $courseInfo['title'] ?? 'Selected Course';

            // 1. Notify Admin
            $adminEmail = getSetting('site_email', 'admin@nexsofthub.com');
            $adminBody = emailTemplateCourseRegistrationAdmin($_POST, $courseTitle);
            sendMail($adminEmail, 'Admin', 'New Course Registration: ' . $courseTitle, $adminBody);

            // 2. Notify Student
            $studentBody = emailTemplateCourseRegistrationStudent($name, $courseTitle);
            sendMail($email, $name, 'Registration Received: ' . $courseTitle, $studentBody);
        }
        else {
            $_SESSION['error'] = 'Failed to submit registration. Please try again.';
        }

        header('Location: ' . baseUrl('courses'));
        exit;
    }
}