<?php
/**
 * Configuration de la base de données MySQL
 */

// Paramètres de connexion MySQL
define('DB_HOST', 'localhost');
define('DB_NAME', 'horozon_albaservice');
define('DB_USER', 'root');
define('DB_PASS', '');

// Connexion PDO
function getDB() {
    static $db = null;
    
    if ($db === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            $db = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die("Erreur de connexion à la base de données: " . $e->getMessage());
        }
    }
    
    return $db;
}

// Initialiser la base de données
function initDatabase() {
    $db = getDB();
    
    // Créer les tables si elles n'existent pas
    $queries = [
        // Table users
        "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL UNIQUE,
            phone VARCHAR(50),
            password VARCHAR(255) NOT NULL,
            profile_image TEXT,
            role ENUM('client', 'admin', 'livreur') NOT NULL DEFAULT 'client',
            address TEXT,
            city VARCHAR(100),
            latitude DECIMAL(10, 8),
            longitude DECIMAL(11, 8),
            is_active TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        
        // Table categories
        "CREATE TABLE IF NOT EXISTS categories (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            description TEXT,
            image VARCHAR(500),
            is_active TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        
        // Table products
        "CREATE TABLE IF NOT EXISTS products (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            description TEXT,
            price DECIMAL(10, 2) NOT NULL,
            original_price DECIMAL(10, 2),
            image VARCHAR(500),
            category_id INT,
            stock INT NOT NULL DEFAULT 0,
            is_popular TINYINT(1) DEFAULT 0,
            is_promotion TINYINT(1) DEFAULT 0,
            is_active TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        
        // Table orders
        "CREATE TABLE IF NOT EXISTS orders (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            customer_name VARCHAR(255) NOT NULL,
            customer_phone VARCHAR(50) NOT NULL,
            customer_address TEXT NOT NULL,
            customer_city VARCHAR(100) NOT NULL,
            customer_latitude DECIMAL(10, 8),
            customer_longitude DECIMAL(11, 8),
            total_amount DECIMAL(10, 2) NOT NULL,
            status ENUM('en_attente', 'paye', 'en_preparation', 'en_livraison', 'livre', 'annule') NOT NULL DEFAULT 'en_attente',
            delivery_person_id INT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (delivery_person_id) REFERENCES users(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        
        // Table order_details
        "CREATE TABLE IF NOT EXISTS order_details (
            id INT AUTO_INCREMENT PRIMARY KEY,
            order_id INT NOT NULL,
            product_id INT NOT NULL,
            product_name VARCHAR(255) NOT NULL,
            product_price DECIMAL(10, 2) NOT NULL,
            quantity INT NOT NULL,
            subtotal DECIMAL(10, 2) NOT NULL,
            FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
            FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        
        // Table payments
        "CREATE TABLE IF NOT EXISTS payments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            order_id INT NOT NULL,
            amount DECIMAL(10, 2) NOT NULL,
            payment_method VARCHAR(50) NOT NULL DEFAULT 'mobile_money',
            transaction_number VARCHAR(100),
            status ENUM('en_attente', 'valide', 'rejete') NOT NULL DEFAULT 'en_attente',
            payment_phone VARCHAR(50),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            validated_at TIMESTAMP NULL,
            validated_by INT,
            FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
            FOREIGN KEY (validated_by) REFERENCES users(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        
        // Table delivery_locations
        "CREATE TABLE IF NOT EXISTS delivery_locations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            order_id INT NOT NULL,
            delivery_person_id INT NOT NULL,
            latitude DECIMAL(10, 8) NOT NULL,
            longitude DECIMAL(11, 8) NOT NULL,
            accuracy DECIMAL(6, 2),
            speed DECIMAL(6, 2),
            heading DECIMAL(6, 2),
            timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
            FOREIGN KEY (delivery_person_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        
        // Table settings
        "CREATE TABLE IF NOT EXISTS settings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            setting_key VARCHAR(255) NOT NULL UNIQUE,
            setting_value TEXT NOT NULL,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    ];
    
    foreach ($queries as $query) {
        $db->exec($query);
    }
    
    // Insérer les données par défaut
    insertDefaultData($db);
}

// Insérer les données par défaut
function insertDefaultData($db) {
    // Vérifier si l'admin existe déjà
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute(['vente@gmail.com']);
    
    if ($stmt->rowCount() == 0) {
        // Créer l'admin par défaut
        $password = hashPassword('admin.com');
        $stmt = $db->prepare("INSERT INTO users (name, email, phone, password, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute(['Administrateur', 'vente@gmail.com', '+243 000 000 001', $password, 'admin']);
    }
    
    // Vérifier si les catégories existent
    $stmt = $db->query("SELECT id FROM categories LIMIT 1");
    if ($stmt->rowCount() == 0) {
        // Créer les catégories par défaut
        $categories = [
            ['Vêtements', 'Vêtements pour hommes, femmes et enfants'],
            ['Sacs', 'Sacs à main, sacs à dos, sacs de voyage'],
            ['Chaussures', 'Chaussures pour toutes les occasions'],
            ['Accessoires', 'Montres, bijoux, ceintures et plus']
        ];
        
        $stmt = $db->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
        foreach ($categories as $cat) {
            $stmt->execute($cat);
        }
        
        // Créer des produits exemples
        $products = [
            ['Chemise Elegante', 'Chemise de qualité supérieure', 25.00, 1, 50, 1, 0],
            ['Pantalon Classique', 'Pantalon confortable et élégant', 35.00, 1, 30, 1, 0],
            ['Robe Moderna', 'Robe élégante pour toutes occasions', 45.00, 1, 25, 1, 1, 55.00],
            ['Sac à Main Luxe', 'Sac à main de marque', 60.00, 2, 20, 1, 0],
            ['Sac à Dos', 'Sac à dos résistant et pratique', 30.00, 2, 40, 0, 0],
            ['Chaussures Sport', 'Chaussures de sport de qualité', 55.00, 3, 35, 1, 0],
            ['Sandales', 'Sandales élégantes et confortables', 20.00, 3, 45, 0, 1, 28.00],
            ['Montre Connectée', 'Montre intelligente avec toutes les fonctionnalités', 80.00, 4, 15, 1, 0]
        ];
        
        $stmt = $db->prepare("INSERT INTO products (name, description, price, category_id, stock, is_popular, is_promotion, original_price) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        foreach ($products as $prod) {
            $stmt->execute($prod);
        }
    }
    
    // Vérifier si les paramètres existent
    $stmt = $db->query("SELECT id FROM settings WHERE setting_key = 'payment_phone'");
    if ($stmt->rowCount() == 0) {
        $stmt = $db->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)");
        $stmt->execute(['payment_phone', '+243 000 000 000']);
        $stmt->execute(['shop_name', 'HORIZON ALBASERVICE']);
        $stmt->execute(['shop_address', 'Kindu, Congo']);
    }
}
