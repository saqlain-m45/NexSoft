<?php

class InternshipsController
{
    public function index()
    {
        $pageTitle = 'Internships — Coming Soon';
        require_once __DIR__ . '/../views/internships.php';
    }
}