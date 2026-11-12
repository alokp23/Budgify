<?php
/**
 * Delete Transaction Handler
 * Accepts POST requests with CSRF token and checks user ownership.
 */

require_once __DIR__ . '/../includes/auth.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . url('transactions/index.php'));
    exit;
}

verifyCsrf();

$userId = (int)$_SESSION['user_id'];
$txId = (int)($_POST['id'] ?? 0);

if ($txId > 0) {
    $stmt = $pdo->prepare('DELETE FROM transactions WHERE id = ? AND user_id = ?');
    $stmt->execute([$txId, $userId]);

    if ($stmt->rowCount() > 0) {
        setFlash('success', 'Transaction deleted successfully.');
    } else {
        setFlash('danger', 'Transaction not found.');
    }
} else {
    setFlash('danger', 'Invalid transaction ID.');
}

header('Location: ' . url('transactions/index.php'));
exit;

