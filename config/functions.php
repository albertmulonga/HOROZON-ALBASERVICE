<?php
/**
 * Fonctions utilitaires pour l'application
 */

require_once 'db.php';

// Démarrer la session
session_start();

// Hasher un mot de passe
function hashPassword($password) {
    return hash('sha256', $password);
}

// Vérifier un mot de passe
function verifyPassword($password, $hash) {
    return hash('sha256', $password) === $hash;
}

// Créer une session utilisateur
function createSession($userId) {
    $_SESSION['user_id'] = $userId;
    $_SESSION['session_token'] = bin2hex(random_bytes(32));
}

// Détruire la session
function destroySession() {
    session_destroy();
}

// Obtenir l'URL de base du site
function getBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $path = dirname($_SERVER['SCRIPT_NAME']);
    if ($path === '/' || $path === '\\') {
        $path = '';
    }
    return $protocol . '://' . $host . $path;
}

// Rediriger vers le tableau de bord approprié
function redirectToDashboard($role) {
    switch ($role) {
        case 'admin':
            header('Location: admin/index.php');
            break;
        case 'livreur':
            header('Location: livreur/index.php');
            break;
        default:
            header('Location: client/index.php');
    }
    exit;
}

// Obtenir l'utilisateur connecté
function getCurrentUser() {
    if (!isset($_SESSION['user_id'])) {
        return null;
    }
    
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ? AND is_active = 1");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    
    return $user ?: null;
}

// Vérifier si l'utilisateur est connecté
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Vérifier le rôle de l'utilisateur
function requireRole($roles) {
    $user = getCurrentUser();
    
    if (!$user) {
        header('Location: login.php');
        exit;
    }
    
    if (!in_array($user['role'], $roles)) {
        header('Location: login.php');
        exit;
    }
    
    return $user;
}

// Obtenir tous les produits
function getProducts($categoryId = null, $search = null) {
    $db = getDB();
    
    $query = "SELECT p.*, c.name as category_name FROM products p 
              LEFT JOIN categories c ON p.category_id = c.id 
              WHERE p.is_active = 1";
    $params = [];
    
    if ($categoryId) {
        $query .= " AND p.category_id = ?";
        $params[] = $categoryId;
    }
    
    if ($search) {
        $query .= " AND p.name LIKE ?";
        $params[] = "%$search%";
    }
    
    $query .= " ORDER BY p.created_at DESC";
    
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    
    return $stmt->fetchAll();
}

// Obtenir les produits populaires
function getPopularProducts() {
    $db = getDB();
    $stmt = $db->prepare("SELECT p.*, c.name as category_name FROM products p 
                          LEFT JOIN categories c ON p.category_id = c.id 
                          WHERE p.is_active = 1 AND p.is_popular = 1 
                          ORDER BY p.created_at DESC LIMIT 8");
    $stmt->execute();
    
    return $stmt->fetchAll();
}

// Obtenir un produit par ID
function getProductById($id) {
    $db = getDB();
    $stmt = $db->prepare("SELECT p.*, c.name as category_name FROM products p 
                          LEFT JOIN categories c ON p.category_id = c.id 
                          WHERE p.id = ?");
    $stmt->execute([$id]);
    
    return $stmt->fetch();
}

// Obtenir toutes les catégories
function getCategories() {
    $db = getDB();
    $stmt = $db->query("SELECT * FROM categories WHERE is_active = 1 ORDER BY name");
    
    return $stmt->fetchAll();
}

// Obtenir une catégorie par ID
function getCategoryById($id) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$id]);
    
    return $stmt->fetch();
}

// Créer un utilisateur
function createUser($name, $email, $phone, $password, $role = 'client', $address = null, $city = null, $profileImage = null, $latitude = null, $longitude = null) {
    $db = getDB();
    
    // Vérifier si l'email existe déjà
    $stmt = $db->prepare("SELECT id FROM users WHERE LOWER(email) = LOWER(?)");
    $stmt->execute([$email]);
    
    if ($stmt->rowCount() > 0) {
        return ['error' => 'Cet email est déjà utilisé'];
    }
    
    $passwordHash = hashPassword($password);
    
    $stmt = $db->prepare("INSERT INTO users (name, email, phone, password, role, address, city, profile_image, latitude, longitude) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    try {
        $stmt->execute([$name, strtolower($email), $phone, $passwordHash, $role, $address, $city, $profileImage, $latitude, $longitude]);
        
        $userId = $db->lastInsertId();
        createSession($userId);
        
        return ['success' => true, 'user_id' => $userId];
    } catch (Exception $e) {
        return ['error' => 'Erreur lors de la création du compte'];
    }
}

// Connecter un utilisateur
function loginUser($email, $password) {
    $db = getDB();
    
    $stmt = $db->prepare("SELECT * FROM users WHERE LOWER(email) = LOWER(?) AND is_active = 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if (!$user) {
        return ['error' => 'Email ou mot de passe incorrect'];
    }
    
    if (!verifyPassword($password, $user['password'])) {
        return ['error' => 'Email ou mot de passe incorrect'];
    }
    
    createSession($user['id']);
    
    return ['success' => true, 'user' => $user];
}

// Obtenir toutes les commandes
function getAllOrders() {
    $db = getDB();
    $stmt = $db->query("SELECT o.*, u.name as user_name, u.phone as user_phone 
                        FROM orders o 
                        LEFT JOIN users u ON o.user_id = u.id 
                        ORDER BY o.created_at DESC");
    
    return $stmt->fetchAll();
}

// Obtenir les commandes d'un utilisateur
function getOrdersByUser($userId) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$userId]);
    
    return $stmt->fetchAll();
}

// Obtenir une commande par ID
function getOrderById($orderId) {
    $db = getDB();
    $stmt = $db->prepare("SELECT o.*, u.name as user_name, u.phone as user_phone, u.address as user_address, u.city as user_city
                        FROM orders o 
                        LEFT JOIN users u ON o.user_id = u.id 
                        WHERE o.id = ?");
    $stmt->execute([$orderId]);
    $order = $stmt->fetch();
    
    if ($order) {
        // Obtenir les détails de la commande
        $stmt = $db->prepare("SELECT * FROM order_details WHERE order_id = ?");
        $stmt->execute([$orderId]);
        $order['details'] = $stmt->fetchAll();
    }
    
    return $order;
}

// Créer une commande
function createOrder($userId, $customerName, $customerPhone, $customerAddress, $customerCity, $items, $latitude = null, $longitude = null) {
    $db = getDB();
    
    $totalAmount = 0;
    foreach ($items as $item) {
        $totalAmount += $item['price'] * $item['quantity'];
    }
    
    try {
        $db->beginTransaction();
        
        // Créer la commande
        $stmt = $db->prepare("INSERT INTO orders (user_id, customer_name, customer_phone, customer_address, customer_city, customer_latitude, customer_longitude, total_amount, status) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'en_attente')");
        $stmt->execute([$userId, $customerName, $customerPhone, $customerAddress, $customerCity, $latitude, $longitude, $totalAmount]);
        
        $orderId = $db->lastInsertId();
        
        // Créer les détails de la commande
        $stmt = $db->prepare("INSERT INTO order_details (order_id, product_id, product_name, product_price, quantity, subtotal) 
                              VALUES (?, ?, ?, ?, ?, ?)");
        
        foreach ($items as $item) {
            $subtotal = $item['price'] * $item['quantity'];
            $stmt->execute([$orderId, $item['id'], $item['name'], $item['price'], $item['quantity'], $subtotal]);
        }
        
        $db->commit();
        
        return ['success' => true, 'order_id' => $orderId];
    } catch (Exception $e) {
        $db->rollBack();
        return ['error' => 'Erreur lors de la création de la commande'];
    }
}

// Mettre à jour le statut d'une commande
function updateOrderStatus($orderId, $status) {
    $db = getDB();
    $stmt = $db->prepare("UPDATE orders SET status = ? WHERE id = ?");
    
    try {
        $stmt->execute([$status, $orderId]);
        return ['success' => true];
    } catch (Exception $e) {
        return ['error' => 'Erreur lors de la mise à jour'];
    }
}

// Assigner un livreur à une commande
function assignDeliveryPerson($orderId, $deliveryPersonId) {
    $db = getDB();
    $stmt = $db->prepare("UPDATE orders SET delivery_person_id = ?, status = 'en_preparation' WHERE id = ?");
    
    try {
        $stmt->execute([$deliveryPersonId, $orderId]);
        return ['success' => true];
    } catch (Exception $e) {
        return ['error' => 'Erreur lors de l\'assignation'];
    }
}

// Obtenir tous les utilisateurs
function getAllUsers() {
    $db = getDB();
    $stmt = $db->query("SELECT * FROM users ORDER BY created_at DESC");
    
    return $stmt->fetchAll();
}

// Obtenir les utilisateurs par rôle
function getUsersByRole($role) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM users WHERE role = ? ORDER BY created_at DESC");
    $stmt->execute([$role]);
    
    return $stmt->fetchAll();
}

// Créer un utilisateur par l'admin
function createUserByAdmin($name, $email, $phone, $password, $role, $address = null, $city = null, $profileImage = null) {
    $db = getDB();
    
    // Vérifier si l'email existe déjà
    $stmt = $db->prepare("SELECT id FROM users WHERE LOWER(email) = LOWER(?)");
    $stmt->execute([$email]);
    
    if ($stmt->rowCount() > 0) {
        return ['error' => 'Cet email est déjà utilisé'];
    }
    
    $passwordHash = hashPassword($password);
    
    // Gérer la photo de profil si elle est fournie
    $profileImagePath = null;
    if ($profileImage && isset($profileImage['tmp_name']) && $profileImage['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 2 * 1024 * 1024; // 2MB
        
        if (in_array($profileImage['type'], $allowedTypes) && $profileImage['size'] <= $maxSize) {
            $uploadDir = 'uploads/profile/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $extension = pathinfo($profileImage['name'], PATHINFO_EXTENSION);
            $filename = 'profile_' . time() . '_' . uniqid() . '.' . $extension;
            $destination = $uploadDir . $filename;
            
            if (move_uploaded_file($profileImage['tmp_name'], $destination)) {
                $profileImagePath = $destination;
            }
        }
    }
    
    $stmt = $db->prepare("INSERT INTO users (name, email, phone, password, role, address, city, profile_image) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    
    try {
        $stmt->execute([$name, strtolower($email), $phone, $passwordHash, $role, $address, $city, $profileImagePath]);
        
        return ['success' => true];
    } catch (Exception $e) {
        return ['error' => 'Erreur lors de la création de l\'utilisateur'];
    }
}

// Inscription d'un client (self-registration)
function registerUser($name, $email, $phone = null, $password, $profileImage = null) {
    $db = getDB();
    
    // Vérifier si l'email existe déjà
    $stmt = $db->prepare("SELECT id FROM users WHERE LOWER(email) = LOWER(?)");
    $stmt->execute([$email]);
    
    if ($stmt->rowCount() > 0) {
        return ['error' => 'Cet email est déjà utilisé. Veuillez vous connecter ou contacter l\'administrateur.'];
    }
    
    $passwordHash = hashPassword($password);
    
    // Par défaut, les nouveaux inscrits sont des clients
    $stmt = $db->prepare("INSERT INTO users (name, email, phone, password, profile_image, role, is_active) 
                          VALUES (?, ?, ?, ?, ?, 'client', 1)");
    
    try {
        $stmt->execute([$name, strtolower($email), $phone, $passwordHash, $profileImage]);
        
        return ['success' => true, 'message' => 'Compte créé avec succès! Vous pouvez maintenant passer vos commandes.'];
    } catch (Exception $e) {
        return ['error' => 'Erreur lors de la création du compte. Veuillez réessayer.'];
    }
}

// Basculer le statut d'un utilisateur
function toggleUserStatus($userId) {
    $db = getDB();
    $stmt = $db->query("SELECT is_active FROM users WHERE id = $userId");
    $user = $stmt->fetch();
    
    if ($user) {
        $newStatus = $user['is_active'] ? 0 : 1;
        $stmt = $db->prepare("UPDATE users SET is_active = ? WHERE id = ?");
        $stmt->execute([$newStatus, $userId]);
        
        return ['success' => true];
    }
    
    return ['error' => 'Utilisateur non trouvé'];
}

// Supprimer un utilisateur
function deleteUser($userId) {
    $db = getDB();
    $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
    
    try {
        $stmt->execute([$userId]);
        return ['success' => true];
    } catch (Exception $e) {
        return ['error' => 'Erreur lors de la suppression'];
    }
}

// Mettre à jour le profil utilisateur
function updateUserProfile($userId, $name, $email, $phone = null, $address = null, $city = null) {
    $db = getDB();
    
    // Vérifier si l'email est déjà utilisé par un autre utilisateur
    $stmt = $db->prepare("SELECT id FROM users WHERE LOWER(email) = LOWER(?) AND id != ?");
    $stmt->execute([$email, $userId]);
    
    if ($stmt->rowCount() > 0) {
        return ['error' => 'Cet email est déjà utilisé par un autre utilisateur'];
    }
    
    $stmt = $db->prepare("UPDATE users SET name = ?, email = ?, phone = ?, address = ?, city = ? WHERE id = ?");
    
    try {
        $stmt->execute([$name, strtolower($email), $phone, $address, $city, $userId]);
        return ['success' => true];
    } catch (Exception $e) {
        return ['error' => 'Erreur lors de la mise à jour du profil'];
    }
}

// Mettre à jour le mot de passe utilisateur
function updateUserPassword($userId, $currentPassword, $newPassword) {
    $db = getDB();
    
    // Vérifier le mot de passe actuel
    $stmt = $db->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    
    if (!verifyPassword($currentPassword, $user['password'])) {
        return ['error' => 'Le mot de passe actuel est incorrect'];
    }
    
    $newHash = hashPassword($newPassword);
    $stmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
    
    try {
        $stmt->execute([$newHash, $userId]);
        return ['success' => true];
    } catch (Exception $e) {
        return ['error' => 'Erreur lors de la mise à jour du mot de passe'];
    }
}

// Mettre à jour la photo de profil
function updateUserPhoto($userId, $file) {
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $maxSize = 2 * 1024 * 1024; // 2MB
    
    if (!in_array($file['type'], $allowedTypes)) {
        return ['error' => 'Type de fichier non autorisé. Utilisez JPG, PNG ou GIF'];
    }
    
    if ($file['size'] > $maxSize) {
        return ['error' => 'La taille du fichier ne doit pas dépasser 2MB'];
    }
    
    $uploadDir = 'uploads/profile/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'profile_' . $userId . '_' . time() . '.' . $extension;
    $destination = $uploadDir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        $db = getDB();
        $stmt = $db->prepare("UPDATE users SET profile_image = ? WHERE id = ?");
        
        try {
            $stmt->execute([$destination, $userId]);
            return ['success' => true];
        } catch (Exception $e) {
            return ['error' => 'Erreur lors de la mise à jour de la photo'];
        }
    }
    
    return ['error' => 'Erreur lors du téléchargement de l\'image'];
}

// Créer un produit
function createProduct($name, $description, $price, $categoryId, $stock, $isPopular, $isPromotion, $originalPrice = null) {
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO products (name, description, price, category_id, stock, is_popular, is_promotion, original_price) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    
    try {
        $stmt->execute([$name, $description, $price, $categoryId, $stock, $isPopular ? 1 : 0, $isPromotion ? 1 : 0, $originalPrice]);
        
        return ['success' => true];
    } catch (Exception $e) {
        return ['error' => 'Erreur lors de la création du produit'];
    }
}

// Mettre à jour un produit
function updateProduct($id, $name, $description, $price, $categoryId, $stock, $isPopular, $isPromotion, $originalPrice = null) {
    $db = getDB();
    $stmt = $db->prepare("UPDATE products SET name = ?, description = ?, price = ?, category_id = ?, stock = ?, is_popular = ?, is_promotion = ?, original_price = ? 
                          WHERE id = ?");
    
    try {
        $stmt->execute([$name, $description, $price, $categoryId, $stock, $isPopular ? 1 : 0, $isPromotion ? 1 : 0, $originalPrice, $id]);
        
        return ['success' => true];
    } catch (Exception $e) {
        return ['error' => 'Erreur lors de la mise à jour'];
    }
}

// Supprimer un produit
function deleteProduct($id) {
    $db = getDB();
    $stmt = $db->prepare("UPDATE products SET is_active = 0 WHERE id = ?");
    
    try {
        $stmt->execute([$id]);
        return ['success' => true];
    } catch (Exception $e) {
        return ['error' => 'Erreur lors de la suppression'];
    }
}

// Obtenir les statistiques des commandes
function getOrderStats() {
    $db = getDB();
    
    $orders = getAllOrders();
    
    $stats = [
        'total' => count($orders),
        'enAttente' => 0,
        'paye' => 0,
        'enPreparation' => 0,
        'enLivraison' => 0,
        'livre' => 0,
        'totalSales' => 0
    ];
    
    foreach ($orders as $order) {
        switch ($order['status']) {
            case 'en_attente': $stats['enAttente']++; break;
            case 'paye': $stats['paye']++; break;
            case 'en_preparation': $stats['enPreparation']++; break;
            case 'en_livraison': $stats['enLivraison']++; break;
            case 'livre': 
                $stats['livre']++; 
                $stats['totalSales'] += $order['total_amount'];
                break;
        }
    }
    
    return $stats;
}

// Obtenir le nombre d'utilisateurs
function getUsersCount() {
    $db = getDB();
    $stmt = $db->query("SELECT role, COUNT(*) as count FROM users GROUP BY role");
    $results = $stmt->fetchAll();
    
    $counts = ['total' => 0, 'clients' => 0, 'livreurs' => 0];
    
    foreach ($results as $row) {
        $counts['total'] += $row['count'];
        if ($row['role'] == 'client') $counts['clients'] = $row['count'];
        if ($row['role'] == 'livreur') $counts['livreurs'] = $row['count'];
    }
    
    return $counts;
}

// Obtenir le nombre de produits
function getProductsCount() {
    $db = getDB();
    $stmt = $db->query("SELECT COUNT(*) as count FROM products WHERE is_active = 1");
    $result = $stmt->fetch();
    
    return $result['count'];
}

// Obtenir les produits en faible stock
function getLowStockProducts($limit = 5) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM products WHERE is_active = 1 AND stock <= 10 ORDER BY stock ASC LIMIT ?");
    $stmt->execute([$limit]);
    
    return $stmt->fetchAll();
}

// Obtenir les paramètres du site
function getSetting($key) {
    $db = getDB();
    $stmt = $db->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
    $stmt->execute([$key]);
    $result = $stmt->fetch();
    
    return $result ? $result['setting_value'] : null;
}

// Obtenir les commandes du livreur
function getDeliveryPersonOrders($deliveryPersonId) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM orders WHERE delivery_person_id = ? ORDER BY created_at DESC");
    $stmt->execute([$deliveryPersonId]);
    
    return $stmt->fetchAll();
}

// Créer un paiement
function createPayment($orderId, $amount, $transactionNumber, $paymentPhone) {
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO payments (order_id, amount, transaction_number, payment_phone, status) 
                          VALUES (?, ?, ?, ?, 'en_attente')");
    
    try {
        $stmt->execute([$orderId, $amount, $transactionNumber, $paymentPhone]);
        
        // Mettre à jour le statut de la commande
        updateOrderStatus($orderId, 'paye');
        
        return ['success' => true];
    } catch (Exception $e) {
        return ['error' => 'Erreur lors du paiement'];
    }
}

// Valider un paiement
function validatePayment($paymentId, $adminId) {
    $db = getDB();
    
    $stmt = $db->prepare("UPDATE payments SET status = 'valide', validated_at = NOW(), validated_by = ? WHERE id = ?");
    $stmt->execute([$adminId, $paymentId]);
    
    // Obtenir la commande associée
    $stmt = $db->prepare("SELECT order_id FROM payments WHERE id = ?");
    $stmt->execute([$paymentId]);
    $payment = $stmt->fetch();
    
    if ($payment) {
        updateOrderStatus($payment['order_id'], 'paye');
    }
    
    return ['success' => true];
}

// Rediriger vers le tableau de bord approprié
