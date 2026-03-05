<?php
/**
 * API - Créer une commande
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
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['error' => 'Données invalides']);
    exit;
}

$customerName = $input['customer_name'] ?? '';
$customerPhone = $input['customer_phone'] ?? '';
$customerAddress = $input['customer_address'] ?? '';
$customerCity = $input['customer_city'] ?? '';
$transactionNumber = $input['transaction_number'] ?? '';
$paymentPhone = $input['payment_phone'] ?? '';
$items = $input['items'] ?? [];

// Validate
if (empty($customerName) || empty($customerPhone) || empty($customerAddress) || empty($customerCity)) {
    echo json_encode(['error' => 'Veuillez remplir tous les champs']);
    exit;
}

if (empty($items)) {
    echo json_encode(['error' => 'Le panier est vide']);
    exit;
}

// Create order
$result = createOrder(
    $user['id'],
    $customerName,
    $customerPhone,
    $customerAddress,
    $customerCity,
    $items
);

if (isset($result['success'])) {
    // Create payment record
    $totalAmount = array_sum(array_map(function($item) {
        return $item['price'] * $item['quantity'];
    }, $items));
    
    createPayment($result['order_id'], $totalAmount, $transactionNumber, $paymentPhone);
    
    echo json_encode([
        'success' => true,
        'order_id' => $result['order_id']
    ]);
} else {
    echo json_encode([
        'error' => $result['error'] ?? 'Erreur lors de la création de la commande'
    ]);
}
