<?php
/**
 * AJAX: Get Replies for a Message
 * Returns JSON with all replies sent to a specific message
 */

require_once __DIR__ . '/auth.php';
adminCheck();

header('Content-Type: application/json');

$message_id = (int)($_GET['message_id'] ?? 0);

if (!$message_id) {
    echo json_encode(['success' => false, 'error' => 'Message ID required']);
    exit;
}

$db = getDB();

try {
    // Get message details
    $message = $db->prepare("SELECT * FROM contact_messages WHERE id = ?")
        ->execute([$message_id])->fetch();
    
    if (!$message) {
        echo json_encode(['success' => false, 'error' => 'Message not found']);
        exit;
    }
    
    // Get all replies for this message
    $replies = $db->prepare("
        SELECT mr.*, u.username as sent_by_name
        FROM message_replies mr
        LEFT JOIN users u ON u.id = mr.sent_by
        WHERE mr.message_id = ?
        ORDER BY mr.created_at DESC
    ")->execute([$message_id])->fetchAll();
    
    // Format replies for JSON
    $formatted_replies = [];
    foreach ($replies as $reply) {
        $formatted_replies[] = [
            'id' => $reply['id'],
            'reply_subject' => $reply['reply_subject'],
            'reply_message' => $reply['reply_message'],
            'sent_by_name' => $reply['sent_by_name'] ?? 'System Admin',
            'created_at' => date('M d, Y H:i', strtotime($reply['created_at']))
        ];
    }
    
    echo json_encode([
        'success' => true,
        'message' => [
            'name' => $message['name'],
            'email' => $message['email']
        ],
        'replies' => $formatted_replies
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
