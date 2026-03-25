<?php
require_once __DIR__ . '/../config/database.php';

class InternshipsController
{
    public function index()
    {
        $db = getDB();
        $internships = $db->query("SELECT * FROM hr_internships WHERE status = 'active' ORDER BY created_at DESC")->fetchAll();

        $pageTitle = 'Internship Opportunities — NexSoft Hub';
        require_once __DIR__ . '/../views/internships.php';
    }
}