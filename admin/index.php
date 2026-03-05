<?php
require_once '../config/db.php';
require_once '../config/functions.php';

$pageTitle = 'Tableau de bord Admin - HOROZON ALBASERVICE';

// Require admin role
$user = requireRole(['admin']);

$stats = getOrderStats();
$usersStats = getUsersCount();
$productsCount = getProductsCount();
$recentOrders = getAllOrders();

// Calculate revenue
$currentMonthRevenue = $stats['totalSales'];
$previousMonthRevenue = $stats['totalSales'] * 0.75;
$revenueChange = $previousMonthRevenue > 0 ? (($currentMonthRevenue - $previousMonthRevenue) / $previousMonthRevenue) * 100 : 0;

// Calculate average order value
$avgOrderValue = $stats['total'] > 0 ? $stats['totalSales'] / $stats['total'] : 0;

// Calculate order change
$orderChange = 12; // Simulated change

// Low stock products
$lowStockProducts = getLowStockProducts();

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
    'paye' => 'Payé',
    'en_preparation' => 'En préparation',
    'en_livraison' => 'En livraison',
    'livre' => 'Livré',
    'annule' => 'Annulé',
];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
                    <a href="/admin/index.php" class="header-nav-link active">Dashboard</a>
                    <a href="/admin/produits.php" class="header-nav-link">Produits</a>
                    <a href="/admin/utilisateurs.php" class="header-nav-link">Utilisateurs</a>
                </nav>
                
                <div class="flex items-center gap-3">
                    <span class="text-sm text-gray-600"><?= htmlspecialchars($user['name']) ?></span>
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
                            <a href="/admin/index.php" class="dropdown-item">Dashboard</a>
                            <a href="/admin/produits.php" class="dropdown-item">Produits</a>
                            <a href="/admin/utilisateurs.php" class="dropdown-item">Utilisateurs</a>
                            <div class="dropdown-divider"></div>
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
                        <h1 class="text-3xl font-bold text-gradient">Tableau de bord Admin</h1>
                        <p class="text-gray-600 mt-1">Bienvenue, <span class="font-semibold text-blue-600"><?= htmlspecialchars($user['name']) ?></span></p>
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="/admin/produits.php?action=add" class="btn btn-primary">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Nouveau produit
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="container py-8">
            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Orders -->
                <div class="stat-card">
                    <div class="flex items-center justify-between mb-4">
                        <div class="stat-icon" style="background: linear-gradient(to bottom right, #3b82f6, #1d4ed8);">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                        <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-1 rounded-full">+12% ce mois</span>
                    </div>
                    <p class="stat-label">Total Commandes</p>
                    <p class="stat-value"><?= $stats['total'] ?></p>
                </div>

                <!-- Total Sales -->
                <div class="stat-card">
                    <div class="flex items-center justify-between mb-4">
                        <div class="stat-icon" style="background: linear-gradient(to bottom right, #22c55e, #16a34a);">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <span class="text-xs font-medium <?= $revenueChange >= 0 ? 'text-green-600 bg-green-50' : 'text-red-600 bg-red-50' ?> px-2 py-1 rounded-full">
                            <?= $revenueChange >= 0 ? '+' : '' ?><?= number_format($revenueChange, 1) ?>%
                        </span>
                    </div>
                    <p class="stat-label">Ventes Totales</p>
                    <p class="stat-value">$<?= number_format($stats['totalSales'], 2) ?></p>
                </div>

                <!-- Total Users -->
                <div class="stat-card">
                    <div class="flex items-center justify-between mb-4">
                        <div class="stat-icon" style="background: linear-gradient(to bottom right, #8b5cf6, #6d28d9);">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="stat-label">Utilisateurs</p>
                    <p class="stat-value"><?= $usersStats['total'] ?></p>
                    <p class="text-sm text-gray-500"><?= $usersStats['clients'] ?> clients, <?= $usersStats['livreurs'] ?> livreurs</p>
                </div>

                <!-- Total Products -->
                <div class="stat-card">
                    <div class="flex items-center justify-between mb-4">
                        <div class="stat-icon" style="background: linear-gradient(to bottom right, #f59e0b, #d97706);">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="stat-label">Produits</p>
                    <p class="stat-value"><?= $productsCount ?></p>
                </div>
            </div>

            <!-- Orders by Status -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
                <div class="bg-white rounded-xl p-4 border text-center">
                    <p class="text-2xl font-bold text-yellow-600"><?= $stats['enAttente'] ?></p>
                    <p class="text-sm text-gray-500">En attente</p>
                </div>
                <div class="bg-white rounded-xl p-4 border text-center">
                    <p class="text-2xl font-bold text-blue-600"><?= $stats['paye'] ?></p>
                    <p class="text-sm text-gray-500">Payés</p>
                </div>
                <div class="bg-white rounded-xl p-4 border text-center">
                    <p class="text-2xl font-bold text-purple-600"><?= $stats['enPreparation'] ?></p>
                    <p class="text-sm text-gray-500">En préparation</p>
                </div>
                <div class="bg-white rounded-xl p-4 border text-center">
                    <p class="text-2xl font-bold text-orange-600"><?= $stats['enLivraison'] ?></p>
                    <p class="text-sm text-gray-500">En livraison</p>
                </div>
                <div class="bg-white rounded-xl p-4 border text-center">
                    <p class="text-2xl font-bold text-green-600"><?= $stats['livre'] ?></p>
                    <p class="text-sm text-gray-500">Livrés</p>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="card">
                <div class="card-header flex items-center justify-between">
                    <h2 class="text-lg font-semibold">Commandes récentes</h2>
                    <a href="/admin/commandes.php" class="text-blue-600 hover:underline text-sm">Voir tout</a>
                </div>
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Client</th>
                                <th>Total</th>
                                <th>Statut</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($recentOrders, 0, 8) as $order): ?>
                                <tr>
                                    <td>#<?= $order['id'] ?></td>
                                    <td><?= htmlspecialchars($order['customer_name']) ?></td>
                                    <td>$<?= number_format($order['total_amount'], 2) ?></td>
                                    <td>
                                        <span class="status-badge status-<?= $order['status'] ?>">
                                            <?= $statusLabels[$order['status']] ?? $order['status'] ?>
                                        </span>
                                    </td>
                                    <td><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>
                                    <td>
                                        <a href="/admin/commande.php?id=<?= $order['id'] ?>" class="text-blue-600 hover:underline">Voir</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (count($recentOrders) === 0): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-8 text-gray-500">Aucune commande</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <script src="/js/main.js"></script>
</body>
</html>
