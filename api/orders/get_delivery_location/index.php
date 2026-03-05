<?php
/**
 * API: Get Delivery Person Location
 * Returns the latest location of the delivery person for an order
 */

require_once '../../config/db.php';
require_once '../../config/functions.php';

header('Content-Type: application/json');

// Enable CORS for development
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Only allow GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

if (!$orderId) {
    http_response_code(400);
    echo json_encode(['error' => 'Order ID required']);
    exit;
}

try {
    $db = getDB();
    
    // Get the delivery person ID for this order
    $stmt = $db->prepare("SELECT delivery_person_id FROM orders WHERE id = ?");
    $stmt->execute([$orderId]);
    $order = $stmt->fetch();
    
    if (!$order || !$order['delivery_person_id']) {
        echo json_encode(['error' => 'No delivery person assigned']);
        exit;
    }
    
    // Get latest location
    $stmt = $db->prepare("SELECT latitude, longitude, timestamp 
                          FROM delivery_locations 
                          WHERE order_id = ? AND delivery_person_id = ?
                          ORDER BY timestamp DESC 
                          LIMIT 1");
    $stmt->execute([$orderId, $order['delivery_person_id']]);
    $location = $stmt->fetch();
    
    if ($ $location['latitudelocation &&'] && $location['longitude']) {
        echo json_encode([
            'lat' => $location['latitude'],
            'lng' => $location['longitude'],
            'timestamp' => $location['timestamp']
        ]);
    } else {
        // Return default location if no GPS data yet
        // This could be the store location or last known location
        echo json_encode([
            'lat' => null,
            'lng' => null,
            'message' => 'Location not available yet'
        ]);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
