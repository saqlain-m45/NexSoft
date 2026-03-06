<?php
require_once __DIR__ . '/auth.php';
adminCheck();

// Redirect to dashboard
header('Location: /NexSoft/admin/dashboard.php');
exit;
