<?php
require_once __DIR__ . '/auth.php';
// No adminCheck() here to allow AJAX but ensure session is valid
if (!isset($_SESSION['admin_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$db = getDB();

// 1. Fetch new registrations
$stmt = $db->query("
    SELECT r.id, r.name, c.title as course_title 
    FROM course_registrations r
    JOIN courses c ON r.course_id = c.id
    WHERE r.is_notified = 0
    ORDER BY r.created_at ASC
");
$newRegs = $stmt->fetchAll();

if (!empty($newRegs)) {
    // 2. Mark as notified immediately
    $ids = array_column($newRegs, 'id');
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $db->prepare("UPDATE course_registrations SET is_notified = 1 WHERE id IN ($placeholders)")->execute($ids);
}

header('Content-Type: application/json');
echo json_encode(['new_registrations' => $newRegs]);
exit;