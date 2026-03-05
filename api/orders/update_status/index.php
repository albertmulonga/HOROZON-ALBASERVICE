<?php
/**
 * API - Mettre à jour le statut d'une commande
 */
require_once '../../config/db.php';
require_once '../../config/functions.php';

// Set headers for JSON response
header('Content-Type: application/json');

// Require login
if (!isLoggedIn()) {
    echo json_encode(['error' => 'Vous devez être connecté']);
    exit;
}

$user = getCurrentUser();

// Get POST data
$orderId = $_POST['order_id'] ?? null;
$status = $_POST['status'] ?? null;

if (!$orderId || !$status) {
    echo json_encode(['error' => 'Données invalides']);
    exit;
}

// Check permissions
if ($user['role'] === 'livreur') {
    // Livreur can only update their own assigned orders
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM orders WHERE id = ? AND delivery_person_id = ?");
    $stmt->execute([$orderId, $user['id']]);
    $order = $stmt->fetch();
    
    if (!$order) {
        echo json_encode(['error' => 'Vous n\'êtes pas assigné à cette commande']);
        exit;
    }
} elseif ($user['role'] !== 'admin') {
    echo json_encode(['error' => 'Accès refusé']);
    exit;
}

// Update status
$result = updateOrderStatus($orderId, $status);

if (isset($result['success'])) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => $result['error'] ?? 'Erreur lors de la mise à jour']);
}
