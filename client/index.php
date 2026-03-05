<?php
require_once '../config/db.php';
require_once '../config/functions.php';

$pageTitle = 'Mon Compte - Client';

// Require client role
$user = requireRole(['client']);

$orders = getOrdersByUser($user['id']);

// Calculate stats
$totalOrders = count($orders);
$deliveredOrders = count(array_filter($orders, function($o) { return $o['status'] === 'livre'; }));
$totalSpent = array_sum(array_map(function($o) { 
    return in_array($o['status'], ['livre', 'en_livraison', 'en_preparation', 'paye']) ? $o['total_amount'] : 0; 
}, $orders));

$statusColors = [
    'en_attente' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
    'paye' => 'bg-blue-100 text-blue-800 border-blue-200',
    'en_preparation' => 'bg-purple-100 text-purple-800 border-purple-200',
    'en_livraison' => 'bg-orange-100 text-orange-800 border-orange-200',
    'livre' => 'bg-green-100 text-green-800 border-green-200',
    'annule' => 'bg-red-100 text-red-800 border-red-200',
];

$statusLabels = [
    'en_attente' => 'En attente de paiement',
    'paye' => 'Payé - En préparation',
    'en_preparation' => 'En préparation',
    'en_livraison' => 'En livraison',
    'livre' => 'Livré',
    'annule' => 'Annulé',
];

$statusIcons = [
    'en_attente' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
    'paye' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
    'en_preparation' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>',
    'en_livraison' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path></svg>',
    'livre' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>',
    'annule' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>',
];
?>

<!DOCTYPE html>
<html lang>
    <meta charset="UTF-8">
    ="fr">
<head<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="flex items-center justify-between" style="height: 4rem;">
                <div class="flex items-center gap-4">
                    <a href="/" class="flex items-center gap-2">
                        <div class="w-10 h-10 bg-gradient-to-r from-blue-600 to-blue-800 rounded-lg flex items-center justify-center text-white font-bold text-xl">H</div>
                        <span class="text-xl font-bold text-gray-900">HOROZON</span>
                    </a>
                </div>
                
                <nav class="hidden md:flex items-center gap-6">
                    <a href="/" class="header-nav-link">Accueil</a>
                    <a href="/produits.php" class="header-nav-link">Produits</a>
                    <a href="/client/index.php" class="header-nav-link active">Mon compte</a>
                </nav>
                
                <div class="flex items-center gap-3">
                    <a href="/produits.php" class="btn btn-primary btn-sm">Continuer mes achats</a>
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
                            <a href="/client/index.php" class="dropdown-item">Mon compte</a>
                            <a href="/logout.php" class="dropdown-item text-red-600">Déconnexion</a>
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
                        <h1 class="text-3xl font-bold text-gradient">Mon Compte</h1>
                        <p class="text-gray-600 mt-1">Bienvenue, <span class="font-semibold text-blue-600"><?= htmlspecialchars($user['name']) ?></span></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="container py-8">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="stat-card">
                    <div class="flex items-center justify-between mb-4">
                        <div class="stat-icon" style="background: linear-gradient(to bottom right, #3b82f6, #1d4ed8);">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="stat-label">Total Commandes</p>
                    <p class="stat-value"><?= $totalOrders ?></p>
                </div>

                <div class="stat-card">
                    <div class="flex items-center justify-between mb-4">
                        <div class="stat-icon" style="background: linear-gradient(to bottom right, #22c55e, #16a34a);">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="stat-label">Commandes livrées</p>
                    <p class="stat-value"><?= $deliveredOrders ?></p>
                </div>

                <div class="stat-card">
                    <div class="flex items-center justify-between mb-4">
                        <div class="stat-icon" style="background: linear-gradient(to bottom right, #f59e0b, #d97706);">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="stat-label">Total dépensé</p>
                    <p class="stat-value">$<?= number_format($totalSpent, 2) ?></p>
                </div>
            </div>

            <!-- Orders List -->
            <div class="card">
                <div class="card-header">
                    <h2 class="text-lg font-semibold">Mes commandes</h2>
                </div>
                <div class="divide-y divide-gray-200">
                    <?php foreach ($orders as $order): ?>
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h3 class="text-lg font-semibold">Commande #<?= $order['id'] ?></h3>
                                    <p class="text-sm text-gray-500"><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xl font-bold">$<?= number_format($order['total_amount'], 2) ?></p>
                                    <span class="status-badge <?= $statusColors[$order['status']] ?>">
                                        <?= $statusIcons[$order['status']] ?>
                                        <?= $statusLabels[$order['status']] ?>
                                    </span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="text-sm text-gray-600">
                                    <p>Livraison à: <?= htmlspecialchars($order['customer_address']) ?>, <?= htmlspecialchars($order['customer_city']) ?></p>
                                </div>
                                <a href="/client/commande.php?id=<?= $order['id'] ?>" class="btn btn-secondary btn-sm">Voir les détails</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <?php if (count($orders) === 0): ?>
                        <div class="p-8 text-center">
                            <p class="text-gray-500 mb-4">Vous n'avez pas encore passé de commande</p>
                            <a href="/produits.php" class="btn btn-primary">Découvrir nos produits</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <script src="/js/main.js"></script>
</body>
</html>
