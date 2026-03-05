<?php
require_once '../config/db.php';
require_once '../config/functions.php';

$pageTitle = 'Mes Commandes - HOROZON ALBASERVICE';

// Require client role
$user = requireRole(['client']);

// Get order ID
$orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get order details
$order = getOrderById($orderId);

// Verify order belongs to user
if (!$order || $order['user_id'] != $user['id']) {
    header('Location: /client/index.php');
    exit;
}

// Get delivery person info if assigned
$deliveryPerson = null;
if ($order['delivery_person_id']) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$order['delivery_person_id']]);
    $deliveryPerson = $stmt->fetch();
}

// Get delivery locations if in delivery
$deliveryLocations = [];
if ($order['delivery_person_id'] && in_array($order['status'], ['en_livraison'])) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM delivery_locations WHERE order_id = ? ORDER BY timestamp DESC LIMIT 100");
    $stmt->execute([$orderId]);
    $deliveryLocations = $stmt->fetchAll();
}

$statusLabels = [
    'en_attente' => 'En attente de paiement',
    'paye' => 'Payé - En préparation',
    'en_preparation' => 'En préparation',
    'en_livraison' => 'En livraison',
    'livre' => 'Livré',
    'annule' => 'Annulé',
];

$statusColors = [
    'en_attente' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
    'paye' => 'bg-blue-100 text-blue-800 border-blue-200',
    'en_preparation' => 'bg-purple-100 text-purple-800 border-purple-200',
    'en_livraison' => 'bg-orange-100 text-orange-800 border-orange-200',
    'livre' => 'bg-green-100 text-green-800 border-green-200',
    'annule' => 'bg-red-100 text-red-800 border-red-200',
];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=geometry" defer></script>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="flex items-center justify-between" style="height: 4rem;">
                <div class="flex items-center gap-4">
                    <a href="../index.php" class="flex items-center gap-2">
                        <div class="w-10 h-10 bg-gradient-to-r from-blue-600 to-blue-800 rounded-lg flex items-center justify-center text-white font-bold text-xl">HK</div>
                        <span class="text-xl font-bold text-gray-900">HOROZON ALBASERVICE</span>
                    </a>
                </div>
                
                <nav class="hidden md:flex items-center gap-6">
                    <a href="../index.php" class="header-nav-link">Accueil</a>
                    <a href="../produits.php" class="header-nav-link">Produits</a>
                    <a href="../index.php" class="header-nav-link active">Mon compte</a>
                </nav>
                
                <div class="flex items-center gap-3">
                    <a href="../produits.php" class="btn btn-primary btn-sm">Continuer mes achats</a>
                    <div class="dropdown">
                        <button onclick="toggleDropdown()" class="flex items-center gap-2 cursor-pointer">
                            <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white font-semibold">
                                <?= strtoupper(substr($user['name'], 0, 1)) ?>
                            </div>
                        </button>
                        <div id="userDropdown" class="dropdown-menu">
                            <div class="p-3 border-b">
                                <p class="font-semibold"><?= htmlspecialchars($user['name']) ?></p>
                                <p class="text-sm text-gray-500"><?= htmlspecialchars($user['email']) ?></p>
                            </div>
                            <a href="../index.php" class="dropdown-item">Mon compte</a>
                            <a href="../logout.php" class="dropdown-item text-red-600">Déconnexion</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="min-h-screen bg-gray-50">
        <!-- Page Header -->
        <div class="bg-white shadow-sm border-b border-gray-200">
            <div class="container py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <a href="../index.php" class="text-blue-600 hover:underline text-sm mb-2 inline-block">&larr; Retour à mes commandes</a>
                        <h1 class="text-3xl font-bold text-gradient">Commande #<?= $order['id'] ?></h1>
                    </div>
                    <span class="status-badge <?= $statusColors[$order['status']] ?> px-4 py-2">
                        <?= $statusLabels[$order['status']] ?>
                    </span>
                </div>
            </div>
        </div>

        <div class="container py-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Order Details -->
                <div class="lg:col-span-2">
                    <!-- Order Items -->
                    <div class="card mb-6">
                        <div class="card-header">
                            <h2 class="text-lg font-semibold">Produits commandés</h2>
                        </div>
                        <div class="divide-y divide-gray-200">
                            <?php foreach ($order['details'] as $item): ?>
                                <div class="p-4 flex items-center gap-4">
                                    <img src="<?= $item['product_image'] ?? 'https://via.placeholder.com/80x80?text=Produit' ?>" 
                                         alt="<?= htmlspecialchars($item['product_name']) ?>" 
                                         class="w-20 h-20 object-cover rounded-lg">
                                    <div class="flex-1">
                                        <h3 class="font-semibold"><?= htmlspecialchars($item['product_name']) ?></h3>
                                        <p class="text-sm text-gray-500"><?= number_format($item['product_price'], 2) ?> $ x <?= $item['quantity'] ?></p>
                                    </div>
                                    <p class="font-semibold"><?= number_format($item['subtotal'], 2) ?> $</p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="p-4 bg-gray-50 border-t">
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-semibold">Total</span>
                                <span class="text-2xl font-bold text-blue-600"><?= number_format($order['total_amount'], 2) ?> $</span>
                            </div>
                        </div>
                    </div>

                    <!-- Delivery Address -->
                    <div class="card">
                        <div class="card-header">
                            <h2 class="text-lg font-semibold">Adresse de livraison</h2>
                        </div>
                        <div class="p-4">
                            <p class="font-semibold"><?= htmlspecialchars($order['customer_name']) ?></p>
                            <p class="text-gray-600"><?= htmlspecialchars($order['customer_address']) ?></p>
                            <p class="text-gray-600"><?= htmlspecialchars($order['customer_city']) ?></p>
                            <p class="text-gray-600">Téléphone: <?= htmlspecialchars($order['customer_phone']) ?></p>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div>
                    <!-- Order Status Timeline -->
                    <div class="card mb-6">
                        <div class="card-header">
                            <h2 class="text-lg font-semibold">Suivi de la commande</h2>
                        </div>
                        <div class="p-4">
                            <div class="timeline">
                                <div class="timeline-item <?= in_array($order['status'], ['en_attente', 'paye', 'en_preparation', 'en_livraison', 'livre']) ? 'active' : '' ?>">
                                    <div class="timeline-dot"></div>
                                    <div class="timeline-content">
                                        <h4>Commande passée</h4>
                                        <p class="text-sm text-gray-500"><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></p>
                                    </div>
                                </div>
                                <div class="timeline-item <?= in_array($order['status'], ['paye', 'en_preparation', 'en_livraison', 'livre']) ? 'active' : '' ?>">
                                    <div class="timeline-dot"></div>
                                    <div class="timeline-content">
                                        <h4>Paiement confirmé</h4>
                                        <?php if (in_array($order['status'], ['paye', 'en_preparation', 'en_livraison', 'livre'])): ?>
                                            <p class="text-sm text-gray-500">Paiement validé</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="timeline-item <?= in_array($order['status'], ['en_preparation', 'en_livraison', 'livre']) ? 'active' : '' ?>">
                                    <div class="timeline-dot"></div>
                                    <div class="timeline-content">
                                        <h4>En préparation</h4>
                                        <?php if (in_array($order['status'], ['en_preparation', 'en_livraison', 'livre'])): ?>
                                            <p class="text-sm text-gray-500">Votre commande est en cours de préparation</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="timeline-item <?= in_array($order['status'], ['en_livraison', 'livre']) ? 'active' : '' ?>">
                                    <div class="timeline-dot"></div>
                                    <div class="timeline-content">
                                        <h4>En livraison</h4>
                                        <?php if (in_array($order['status'], ['en_livraison', 'livre'])): ?>
                                            <p class="text-sm text-gray-500">Votre livreur est en route</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="timeline-item <?= $order['status'] === 'livre' ? 'active' : '' ?>">
                                    <div class="timeline-dot"></div>
                                    <div class="timeline-content">
                                        <h4>Livré</h4>
                                        <?php if ($order['status'] === 'livre'): ?>
                                            <p class="text-sm text-gray-500">Commande livrée</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Delivery Person Info -->
                    <?php if ($deliveryPerson && in_array($order['status'], ['en_livraison', 'livre'])): ?>
                        <div class="card mb-6">
                            <div class="card-header">
                                <h2 class="text-lg font-semibold">Votre livreur</h2>
                            </div>
                            <div class="p-4">
                                <div class="flex items-center gap-4 mb-4">
                                    <?php if (!empty($deliveryPerson['profile_image'])): ?>
                                        <img src="<?= htmlspecialchars($deliveryPerson['profile_image']) ?>" 
                                             alt="<?= htmlspecialchars($deliveryPerson['name']) ?>" 
                                             class="w-12 h-12 rounded-full object-cover">
                                    <?php else: ?>
                                        <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-xl">
                                            <?= strtoupper(substr($deliveryPerson['name'], 0, 1)) ?>
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <h3 class="font-semibold"><?= htmlspecialchars($deliveryPerson['name']) ?></h3>
                                        <p class="text-sm text-gray-500"><?= htmlspecialchars($deliveryPerson['phone'] ?? '') ?></p>
                                    </div>
                                </div>
                                <button onclick="toggleMap()" class="btn btn-primary w-full">
                                    Suivre le livreur sur la carte
                                </button>
                            </div>
                        </div>

                        <!-- Map Container -->
                        <div id="mapContainer" class="card mb-6" style="display: none;">
                            <div class="card-header">
                                <h2 class="text-lg font-semibold">Position du livreur</h2>
                            </div>
                            <div class="p-4">
                                <div id="map" style="height: 400px; width: 100%; border-radius: 8px;"></div>
                                <div class="mt-4 text-center">
                                    <p id="distanceInfo" class="text-lg font-semibold">Calcul de la distance...</p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Payment Info -->
                    <?php if ($order['status'] === 'en_attente'): ?>
                        <div class="card">
                            <div class="card-header">
                                <h2 class="text-lg font-semibold">Paiement</h2>
                            </div>
                            <div class="p-4">
                                <p class="text-gray-600 mb-4">Veuillez finaliser votre paiement pour traiter votre commande.</p>
                                <a href="../checkout.php?order_id=<?= $order['id'] ?>" class="btn btn-primary w-full">
                                    Payer maintenant
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <script src="../js/main.js"></script>
    <script>
        let map;
        let deliveryMarker;
        let customerMarker;
        let deliveryLocation = null;
        let customerLocation = null;

        function toggleMap() {
            const container = document.getElementById('mapContainer');
            container.style.display = container.style.display === 'none' ? 'block' : 'none';
            
            if (container.style.display === 'block' && !map) {
                initMap();
            }
        }

        function initMap() {
            // Customer location from order
            customerLocation = {
                lat: parseFloat(<?= $order['customer_latitude'] ?? '0' ?>),
                lng: parseFloat(<?= $order['customer_longitude'] ?? '0' ?>)
            };

            // If no coordinates, try to get from browser
            if (!customerLocation.lat || !customerLocation.lng) {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function(position) {
                        customerLocation = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude
                        };
                        loadMap();
                    }, function() {
                        loadMap();
                    });
                } else {
                    loadMap();
                }
            } else {
                loadMap();
            }
        }

        function loadMap() {
            // Default location (Kindu, Congo)
            const defaultLocation = { lat: -3.4667, lng: 25.8667 };
            
            map = new google.maps.Map(document.getElementById('map'), {
                zoom: 14,
                center: customerLocation || defaultLocation
            });

            // Customer marker
            if (customerLocation && customerLocation.lat) {
                customerMarker = new google.maps.Marker({
                    position: customerLocation,
                    map: map,
                    title: 'Votre position',
                    icon: {
                        path: google.maps.SymbolPath.CIRCLE,
                        scale: 10,
                        fillColor: '#22c55e',
                        fillOpacity: 1,
                        strokeColor: '#ffffff',
                        strokeWeight: 2
                    }
                });
            }

            // Try to get delivery person location
            updateDeliveryLocation();
            
            // Update every 10 seconds
            setInterval(updateDeliveryLocation, 10000);
        }

        function updateDeliveryLocation() {
            fetch(`/api/orders/get_delivery_location.php?order_id=<?= $order['id'] ?>`)
                .then(response => response.json())
                .then(data => {
                    if (data.lat && data.lng) {
                        deliveryLocation = { lat: parseFloat(data.lat), lng: parseFloat(data.lng) };
                        
                        if (!deliveryMarker) {
                            deliveryMarker = new google.maps.Marker({
                                position: deliveryLocation,
                                map: map,
                                title: 'Livreur',
                                icon: {
                                    path: google.maps.SymbolPath.CIRCLE,
                                    scale: 12,
                                    fillColor: '#3b82f6',
                                    fillOpacity: 1,
                                    strokeColor: '#ffffff',
                                    strokeWeight: 3
                                }
                            });
                        } else {
                            deliveryMarker.setPosition(deliveryLocation);
                        }

                        // Calculate distance
                        if (customerLocation && customerLocation.lat) {
                            const distance = google.maps.geometry.spherical.computeDistanceBetween(
                                new google.maps.LatLng(deliveryLocation),
                                new google.maps.LatLng(customerLocation)
                            ) / 1000; // Convert to km
                            
                            document.getElementById('distanceInfo').textContent = 
                                `Distance restante: ${distance.toFixed(2)} km`;
                        }

                        // Fit bounds
                        const bounds = new google.maps.LatLngBounds();
                        if (customerLocation && customerLocation.lat) bounds.extend(customerLocation);
                        bounds.extend(deliveryLocation);
                        map.fitBounds(bounds);
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        // Get user location for better tracking
        if (navigator.geolocation) {
            navigator.geolocation.watchPosition(function(position) {
                // Send location to server
                fetch('/api/orders/update_location.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        order_id: <?= $order['id'] ?>,
                        latitude: position.coords.latitude,
                        longitude: position.coords.longitude
                    })
                });
            });
        }
    </script>
</body>
</html>
