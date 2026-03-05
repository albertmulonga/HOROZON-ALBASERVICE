<?php
/**
 * API: Update Delivery Person Location
 * Allows delivery person to update their GPS location
 */

require_once '../../config/db.php';
require_once '../../config/functions.php';

header('Content-Type: application/json');

// Enable CORS for development
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Get input data
$data = json_decode(file_get_contents('php://input'), true);

$orderId = isset($data['order_id']) ? (int)$data['order_id'] : 0;
$latitude = isset($data['latitude']) ? (float)$data['latitude'] : null;
$longitude = isset($data['longitude']) ? (float)$data['longitude'] : null;
$accuracy = isset($data['accuracy']) ? (float)$data['accuracy'] : null;
$speed = isset($data['speed']) ? (float)$data['speed'] : null;
$heading = isset($data['heading']) ? (float)$data['heading'] : null;

if (!$orderId || !$latitude || !$longitude) {
    http_response_code(400);
    echo json_encode(['error' => 'Order ID, latitude and longitude are required']);
    exit;
}

// Verify user is logged in and is a livreur
$user = getCurrentUser();
if (!$user || $user['role'] !== 'livreur') {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

try {
    $db = getDB();
    
    // Verify the delivery person is assigned to this order
    $stmt = $db->prepare("SELECT delivery_person_id FROM orders WHERE id = ?");
    $stmt->execute([$orderId]);
    $order = $stmt->fetch();
    
    if (!$order || $order['delivery_person_id'] != $user['id']) {
        http_response_code(403);
        echo json_encode(['error' => 'Not authorized for this order']);
        exit;
    }
    
    // Insert the location
    $stmt = $db->prepare("INSERT INTO delivery_locations (order_id, delivery_person_id, latitude, longitude, accuracy, speed, heading) 
                          VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$orderId, $user['id'], $latitude, $longitude, $accuracy, $speed, $heading]);
    
    // Also update the user's stored location
    $stmt = $db->prepare("UPDATE users SET latitude = ?, longitude = ? WHERE id = ?");
    $stmt->execute([$latitude, $longitude, $user['id']]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Location updated',
        'data' => [
            'lat' => $latitude,
            'lng' => $longitude
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
