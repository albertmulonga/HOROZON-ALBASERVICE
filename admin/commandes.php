<?php
require_once '../config/db.php';
require_once '../config/functions.php';

$pageTitle = 'Gestion des Commandes - HOROZON ALBASERVICE';

// Require admin role
$user = requireRole(['admin']);

// Get all orders
$orders = getAllOrders();

// Get all livreurs
$livreurs = getUsersByRole('livreur');

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $orderId = (int)$_POST['order_id'];
    
    if ($_POST['action'] === 'update_status') {
        $status = $_POST['status'];
        updateOrderStatus($orderId, $status);
    } elseif ($_POST['action'] === 'assign_livreur') {
        $livreurId = (int)$_POST['livreur_id'];
        assignDeliveryPerson($orderId, $livreurId);
    } elseif ($_POST['action'] === 'validate_payment') {
        // Validate payment
        $db = getDB();
        $stmt = $db->prepare("UPDATE payments SET status = 'valide', validated_at = NOW(), validated_by = ? WHERE order_id = ?");
        $stmt->execute([$user['id'], $orderId]);
        
        // Update order status
        updateOrderStatus($orderId, 'en_preparation');
    }
    
    // Refresh orders
    $orders = getAllOrders();
    $livreurs = getUsersByRole('livreur');
}

$statusLabels = [
    'en_attente' => 'En attente',
    'paye' => 'Payé',
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
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="flex items-center justify-between" style="height: 4rem;">
                <div class="flex items-center gap-4">
                    <a href="/" class="flex items-center gap-2">
                        <div class="w-10 h-10 bg-gradient-to-r from-blue-600 to-blue-800 rounded-lg flex items-center justify-center text-white font-bold text-xl">HK</div>
                        <span class="text-xl font-bold text-gray-900">HIRIZON</span>
                    </a>
                </div>
                
                <nav class="hidden md:flex items-center gap-6">
                    <a href="/admin/index.php" class="header-nav-link">Dashboard</a>
                    <a href="/admin/produits.php" class="header-nav-link">Produits</a>
                    <a href="/admin/utilisateurs.php" class="header-nav-link">Utilisateurs</a>
                    <a href="/admin/commandes.php" class="header-nav-link active">Commandes</a>
                    <a href="/admin/livreurs.php" class="header-nav-link">Livreurs</a>
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
                            <a href="/admin/commandes.php" class="dropdown-item">Commandes</a>
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
                        <h1 class="text-3xl font-bold text-gradient">Gestion des Commandes</h1>
                        <p class="text-gray-600 mt-1">Gérez toutes les commandes et paiements</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="container py-8">
            <!-- Orders Table -->
            <div class="card">
                <div class="card-header flex items-center justify-between">
                    <h2 class="text-lg font-semibold">Toutes les commandes</h2>
                    <div class="flex gap-2">
                        <select id="statusFilter" class="input" onchange="filterOrders()">
                            <option value="">Tous les statuts</option>
                            <option value="en_attente">En attente</option>
                            <option value="paye">Payé</option>
                            <option value="en_preparation">En préparation</option>
                            <option value="en_livraison">En livraison</option>
                            <option value="livre">Livré</option>
                            <option value="annule">Annulé</option>
                        </select>
                    </div>
                </div>
                <div class="table-container">
                    <table class="table" id="ordersTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Client</th>
                                <th>Téléphone</th>
                                <th>Adresse</th>
                                <th>Total</th>
                                <th>Statut</th>
                                <th>Livreur</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <tr data-status="<?= $order['status'] ?>">
                                    <td>#<?= $order['id'] ?></td>
                                    <td><?= htmlspecialchars($order['customer_name']) ?></td>
                                    <td><?= htmlspecialchars($order['customer_phone']) ?></td>
                                    <td><?= htmlspecialchars($order['customer_address']) ?>, <?= htmlspecialchars($order['customer_city']) ?></td>
                                    <td>$<?= number_format($order['total_amount'], 2) ?></td>
                                    <td>
                                        <span class="status-badge <?= $statusColors[$order['status']] ?>">
                                            <?= $statusLabels[$order['status']] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($order['delivery_person_id']): ?>
                                            <?php 
                                                $db = getDB();
                                                $stmt = $db->prepare("SELECT name FROM users WHERE id = ?");
                                                $stmt->execute([$order['delivery_person_id']]);
                                                $livreur = $stmt->fetch();
                                                echo htmlspecialchars($livreur['name'] ?? 'N/A');
                                            ?>
                                        <?php else: ?>
                                            <span class="text-gray-400">Non assigné</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>
                                    <td>
                                        <div class="flex gap-2">
                                            <button onclick="viewOrder(<?= $order['id'] ?>)" class="btn btn-secondary btn-sm">Voir</button>
                                            
                                            <?php if ($order['status'] === 'en_attente'): ?>
                                                <!-- Validate Payment -->
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="action" value="validate_payment">
                                                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                                    <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Valider ce paiement?')">Valider paiement</button>
                                                </form>
                                            <?php endif; ?>
                                            
                                            <?php if (in_array($order['status'], ['paye', 'en_preparation'])): ?>
                                                <!-- Assign Livreur -->
                                                <button onclick="assignLivreur(<?= $order['id'] ?>)" class="btn btn-primary btn-sm">Assigner livreur</button>
                                            <?php endif; ?>
                                            
                                            <?php if (in_array($order['status'], ['en_preparation'])): ?>
                                                <!-- Start Delivery -->
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="action" value="update_status">
                                                    <input type="hidden" name="status" value="en_livraison">
                                                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                                    <button type="submit" class="btn btn-warning btn-sm">En livraison</button>
                                                </form>
                                            <?php endif; ?>
                                            
                                            <?php if (in_array($order['status'], ['en_livraison'])): ?>
                                                <!-- Mark as Delivered -->
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="action" value="update_status">
                                                    <input type="hidden" name="status" value="livre">
                                                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                                    <button type="submit" class="btn btn-success btn-sm">Livré</button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (count($orders) === 0): ?>
                                <tr>
                                    <td colspan="9" class="text-center py-8 text-gray-500">Aucune commande</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- Assign Livreur Modal -->
    <div id="assignModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Assigner un livreur</h3>
                <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">&times;</button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="assign_livreur">
                <input type="hidden" name="order_id" id="modalOrderId">
                <div class="modal-body">
                    <label class="form-label">Sélectionner un livreur</label>
                    <select name="livreur_id" class="input" required>
                        <option value="">-- Sélectionner --</option>
                        <?php foreach ($livreurs as $livreur): ?>
                            <option value="<?= $livreur['id'] ?>"><?= htmlspecialchars($livreur['name']) ?> (<?= htmlspecialchars($livreur['phone'] ?? 'Sans téléphone') ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="closeModal()" class="btn btn-secondary">Annuler</button>
                    <button type="submit" class="btn btn-primary">Assigner</button>
                </div>
            </form>
        </div>
    </div>

    <script src="/js/main.js"></script>
    <script>
        function filterOrders() {
            const status = document.getElementById('statusFilter').value;
            const rows = document.querySelectorAll('#ordersTable tbody tr');
            
            rows.forEach(row => {
                if (!status || row.dataset.status === status) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        function viewOrder(orderId) {
            window.location.href = '/admin/commande.php?id=' + orderId;
        }

        function assignLivreur(orderId) {
            document.getElementById('modalOrderId').value = orderId;
            document.getElementById('assignModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('assignModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('assignModal');
            if (event.target === modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>
