<?php
require_once '../config/db.php';
require_once '../config/functions.php';

$pageTitle = 'Tableau de bord Livreur - HORIZON ALBASERVICE';

// Require livreur role
$user = requireRole(['livreur']);

$orders = getDeliveryPersonOrders($user['id']);

$statusColors = [
    'en_attente' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
    'paye' => 'bg-blue-100 text-blue-800 border-blue-200',
    'en_preparation' => 'bg-purple-100 text-purple-800 border-purple-200',
    'en_livraison' => 'bg-orange-100 text-orange-800 border-orange-200',
    'livre' => 'bg-green-100 text-green-800 border-green-200',
    'annule' => 'bg-red-100 text-red-800 border-red-200',
];

$statusLabels = [
    'en_attente' => 'En attente',
    'paye' => 'Payé - À récupérer',
    'en_preparation' => 'En préparation',
    'en_livraison' => 'En livraison',
    'livre' => 'Livré',
    'annule' => 'Annulé',
];

$activeOrders = array_filter($orders, function($o) { 
    return in_array($o['status'], ['paye', 'en_preparation', 'en_livraison']); 
});
$completedOrders = array_filter($orders, function($o) { 
    return $o['status'] === 'livre'; 
});
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
                        <span class="text-xl font-bold text-gray-900">HORIZON ALBASERVICE</span>
                    </a>
                </div>
                
                <nav class="hidden md:flex items-center gap-6">
                    <a href="../index.php" class="header-nav-link">Accueil</a>
                    <a href="../produits.php" class="header-nav-link">Produits</a>
                    <a href="../index.php" class="header-nav-link active">Mes livraisons</a>
                </nav>
                
                <div class="flex items-center gap-3">
                    <div id="gpsStatus" class="flex items-center gap-2 text-sm text-gray-600">
                        <span id="gpsDot" class="w-2 h-2 rounded-full bg-gray-400"></span>
                        <span id="gpsText">GPS inactif</span>
                    </div>
                    <div class="dropdown">
                        <button onclick="toggleDropdown()" class="flex items-center gap-2 cursor-pointer">
                            <div class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center text-white font-semibold">
                                <?= strtoupper(substr($user['name'], 0, 1)) ?>
                            </div>
                        </button>
                        <div id="userDropdown" class="dropdown-menu">
                            <div class="p-3 border-b">
                                <p class="font-semibold"><?= htmlspecialchars($user['name']) ?></p>
                                <p class="text-sm text-gray-500">Livreur</p>
                            </div>
                            <a href="../index.php" class="dropdown-item">Mes livraisons</a>
                            <div class="dropdown-divider"></div>
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
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h1 class="text-3xl font-bold" style="background: linear-gradient(to right, #ea580c, #c2410c); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                            Tableau de bord Livreur
                        </h1>
                        <p class="text-gray-600 mt-1">Bienvenue, <span class="font-semibold text-orange-600"><?= htmlspecialchars($user['name']) ?></span></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="container py-8">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="stat-card">
                    <div class="flex items-center justify-between mb-4">
                        <div class="stat-icon" style="background: linear-gradient(to bottom right, #f97316, #ea580c);">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path>
                            </svg>
                        </div>
                        <span class="text-xs font-medium text-orange-600 bg-orange-50 px-2 py-1 rounded-full">À livrer</span>
                    </div>
                    <p class="stat-label">Commandes à livrer</p>
                    <p class="stat-value"><?= count($activeOrders) ?></p>
                </div>

                <div class="stat-card">
                    <div class="flex items-center justify-between mb-4">
                        <div class="stat-icon" style="background: linear-gradient(to bottom right, #22c55e, #16a34a);">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="stat-label">Livraisons terminées</p>
                    <p class="stat-value"><?= count($completedOrders) ?></p>
                </div>

                <div class="stat-card">
                    <div class="flex items-center justify-between mb-4">
                        <div class="stat-icon" style="background: linear-gradient(to bottom right, #3b82f6, #1d4ed8);">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="stat-label">Total</p>
                    <p class="stat-value"><?= count($orders) ?></p>
                </div>
            </div>

            <!-- Orders to Deliver -->
            <div class="card">
                <div class="card-header">
                    <h2 class="text-lg font-semibold flex items-center gap-2">
                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path>
                        </svg>
                        Commandes à livrer
                    </h2>
                </div>
                <div class="divide-y divide-gray-200">
                    <?php foreach ($activeOrders as $order): ?>
                        <?php 
                        // Get order details with customer location
                        $orderDetails = getOrderById($order['id']);
                        ?>
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h3 class="text-lg font-semibold">Commande #<?= $order['id'] ?></h3>
                                    <p class="text-sm text-gray-500"><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xl font-bold">$<?= number_format($order['total_amount'], 2) ?></p>
                                    <span class="status-badge <?= $statusColors[$order['status']] ?>">
                                        <?= $statusLabels[$order['status']] ?>
                                    </span>
                                </div>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4 mb-4">
                                <p class="font-medium"><?= htmlspecialchars($order['customer_name']) ?></p>
                                <p class="text-sm text-gray-600"><?= htmlspecialchars($order['customer_phone']) ?></p>
                                <p class="text-sm text-gray-600"><?= htmlspecialchars($order['customer_address']) ?>, <?= htmlspecialchars($order['customer_city']) ?></p>
                                <?php if ($order['customer_latitude'] && $order['customer_longitude']): ?>
                                    <p class="text-sm text-green-600 mt-2">📍 Localisation disponible</p>
                                <?php endif; ?>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <?php if ($order['status'] === 'paye' || $order['status'] === 'en_preparation'): ?>
                                    <form method="POST" action="/api/orders/update_status/index.php">
                                        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                        <input type="hidden" name="status" value="en_livraison">
                                        <button type="submit" class="btn btn-primary btn-sm">Commencer la livraison</button>
                                    </form>
                                <?php elseif ($order['status'] === 'en_livraison'): ?>
                                    <?php if ($order['customer_latitude'] && $order['customer_longitude']): ?>
                                        <button onclick="showRoute(<?= $order['customer_latitude'] ?>, <?= $order['customer_longitude'] ?>, '<?= addslashes($order['customer_address']) ?>')" class="btn btn-secondary btn-sm">
                                            📍 Voir l'itinéraire
                                        </button>
                                    <?php endif; ?>
                                    <form method="POST" action="/api/orders/update_status/index.php">
                                        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                        <input type="hidden" name="status" value="livre">
                                        <button type="submit" class="btn btn-success btn-sm">Marquer comme livré</button>
                                    </form>
                                <?php endif; ?>
                                <a href="tel:<?= htmlspecialchars($order['customer_phone']) ?>" class="btn btn-secondary btn-sm">📞 Appeler</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <?php if (count($activeOrders) === 0): ?>
                        <div class="p-8 text-center">
                            <p class="text-gray-500">Aucune commande à livrer</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Completed Orders -->
            <div class="card mt-8">
                <div class="card-header">
                    <h2 class="text-lg font-semibold flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Livraisons terminées
                    </h2>
                </div>
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Client</th>
                                <th>Total</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($completedOrders as $order): ?>
                                <tr>
                                    <td>#<?= $order['id'] ?></td>
                                    <td><?= htmlspecialchars($order['customer_name']) ?></td>
                                    <td>$<?= number_format($order['total_amount'], 2) ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (count($completedOrders) === 0): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-8 text-gray-500">Aucune livraison terminée</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- Map Modal -->
    <div id="mapModal" class="modal" style="display: none;">
        <div class="modal-content" style="max-width: 900px; width: 95%;">
            <div class="modal-header">
                <h3>Itinéraire vers le client</h3>
                <button onclick="closeMapModal()" class="text-gray-500 hover:text-gray-700">&times;</button>
            </div>
            <div class="modal-body">
                <div id="routeMap" style="height: 500px; width: 100%; border-radius: 8px;"></div>
                <div id="routeInfo" class="mt-4 text-center">
                    <p class="text-lg font-semibold">Calcul de l'itinéraire...</p>
                </div>
            </div>
        </div>
    </div>

    <script src="../js/main.js"></script>
    <script>
        let map;
        let directionsRenderer;
        let directionsService;
        let watchId;
        let currentOrderId = null;

        // Start GPS tracking when page loads
        document.addEventListener('DOMContentLoaded', function() {
            startGPSTracking();
        });

        function startGPSTracking() {
            if (!navigator.geolocation) {
                updateGPSStatus(false, 'GPS non supporté');
                return;
            }

            updateGPSStatus(true, 'Initialisation...');

            // Get initial position
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    updateGPSStatus(true, 'Actif');
                    sendLocation(position.coords);
                },
                function(error) {
                    updateGPSStatus(false, 'Permission refusée');
                },
                { enableHighAccuracy: true }
            );

            // Watch position continuously
            watchId = navigator.geolocation.watchPosition(
                function(position) {
                    updateGPSStatus(true, 'Actif');
                    sendLocation(position.coords);
                },
                function(error) {
                    console.error('GPS Error:', error);
                },
                { 
                    enableHighAccuracy: true,
                    maximumAge: 10000,
                    timeout: 5000
                }
            );
        }

        function updateGPSStatus(active, text) {
            const dot = document.getElementById('gpsDot');
            const statusText = document.getElementById('gpsText');
            
            if (active) {
                dot.className = 'w-2 h-2 rounded-full bg-green-500 animate-pulse';
            } else {
                dot.className = 'w-2 h-2 rounded-full bg-red-500';
            }
            statusText.textContent = text;
        }

        function sendLocation(coords) {
            // Find the active order to send location
            const activeOrders = document.querySelectorAll('[data-order-id]');
            if (activeOrders.length === 0) return;

            // Get the first active order ID
            fetch('/api/orders/update_location/index.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    order_id: currentOrderId || 1, // Default to first order if no specific order
                    latitude: coords.latitude,
                    longitude: coords.longitude,
                    accuracy: coords.accuracy,
                    speed: coords.speed,
                    heading: coords.heading
                })
            }).catch(error => console.error('Error sending location:', error));
        }

        function showRoute(destLat, destLng, destAddress) {
            document.getElementById('mapModal').style.display = 'flex';
            
            if (!navigator.geolocation) {
                alert('La géolocalisation n\'est pas supportée par votre navigateur');
                return;
            }

            navigator.geolocation.getCurrentPosition(
                function(position) {
                    initRouteMap(position.coords.latitude, position.coords.longitude, destLat, destLng, destAddress);
                },
                function(error) {
                    // Use default location if GPS not available
                    initRouteMap(-3.4667, 25.8667, destLat, destLng, destAddress);
                }
            );
        }

        function initRouteMap(startLat, startLng, destLat, destLng, destAddress) {
            const mapDiv = document.getElementById('routeMap');
            
            if (!map) {
                map = new google.maps.Map(mapDiv, {
                    center: { lat: startLat, lng: startLng },
                    zoom: 14
                });

                directionsService = new google.maps.DirectionsService();
                directionsRenderer = new google.maps.DirectionsRenderer();
                directionsRenderer.setMap(map);
            }

            const request = {
                origin: { lat: startLat, lng: startLng },
                destination: { lat: destLat, lng: destLng },
                travelMode: google.maps.TravelMode.DRIVING
            };

            directionsService.route(request, function(result, status) {
                if (status === 'OK') {
                    directionsRenderer.setDirections(result);
                    
                    // Show distance and duration
                    const leg = result.routes[0].legs[0];
                    document.getElementById('routeInfo').innerHTML = `
                        <p class="text-lg">
                            <span class="font-semibold">Distance:</span> ${leg.distance.text} | 
                            <span class="font-semibold">Temps estimé:</span> ${leg.duration.text}
                        </p>
                        <p class="text-sm text-gray-600">Adresse: ${destAddress}</p>
                    `;
                } else {
                    // If no route found, just show markers
                    map.setCenter({ lat: destLat, lng: destLng });
                    
                    new google.maps.Marker({
                        position: { lat: startLat, lng: startLng },
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

                    new google.maps.Marker({
                        position: { lat: destLat, lng: destLng },
                        map: map,
                        title: 'Client: ' + destAddress,
                        icon: {
                            path: google.maps.SymbolPath.CIRCLE,
                            scale: 12,
                            fillColor: '#f97316',
                            fillOpacity: 1,
                            strokeColor: '#ffffff',
                            strokeWeight: 3
                        }
                    });

                    // Calculate straight line distance
                    const distance = google.maps.geometry.spherical.computeDistanceBetween(
                        new google.maps.LatLng(startLat, startLng),
                        new google.maps.LatLng(destLat, destLng)
                    ) / 1000;

                    document.getElementById('routeInfo').innerHTML = `
                        <p class="text-lg">
                            <span class="font-semibold">Distance directe:</span> ${distance.toFixed(2)} km
                        </p>
                        <p class="text-sm text-gray-600">Adresse: ${destAddress}</p>
                    `;
                }
            });
        }

        function closeMapModal() {
            document.getElementById('mapModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('mapModal');
            if (event.target === modal) {
                closeMapModal();
            }
        }
    </script>
</body>
</html>
