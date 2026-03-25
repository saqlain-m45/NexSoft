<?php

class VerifyController
{
    public function index()
    {
        $db = getDB();
        $docId = trim($_GET['doc_id'] ?? '');
        $result = null;
        $error = '';

        if ($docId !== '') {
            $stmt = $db->prepare("
                SELECT d.*, i.title as internship_title, i.duration as internship_duration
                FROM issued_documents d
                LEFT JOIN hr_internships i ON d.internship_id = i.id
                WHERE d.document_id = ? OR d.verification_code = ?
                LIMIT 1
            ");
            $stmt->execute([$docId, $docId]);
            $result = $stmt->fetch();

            if (!$result) {
                $error = "No document found with the provided ID. Please double-check and try again.";
            }
        }

        $pageTitle = 'Verify Document — NexSoft Hub';
        require_once __DIR__ . '/../views/verify.php';
    }
}
