-- =====================================================
-- BASE DE DONNÉES HOROZON ALBASERVICE
-- Pour XAMPP - Importer ce fichier dans phpMyAdmin
-- =====================================================

-- Créer la base de données
CREATE DATABASE IF NOT EXISTS horozon_albaservice CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE horozon_albaservice;

-- =====================================================
-- TABLE DES UTILISATEURS
-- =====================================================
CREATE TABLE IF NOT EXISTS users (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE DES CATÉGORIES
-- =====================================================
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    image VARCHAR(500),
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE DES PRODUITS
-- =====================================================
CREATE TABLE IF NOT EXISTS products (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE DES COMMANDES
-- =====================================================
CREATE TABLE IF NOT EXISTS orders (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE DES DÉTAILS DE COMMANDE
-- =====================================================
CREATE TABLE IF NOT EXISTS order_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT,
    product_name VARCHAR(255) NOT NULL,
    product_price DECIMAL(10, 2) NOT NULL,
    quantity INT NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE DES PAIEMENTS
-- =====================================================
CREATE TABLE IF NOT EXISTS payments (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE DES LOCALISATIONS DE LIVRAISON
-- =====================================================
CREATE TABLE IF NOT EXISTS delivery_locations (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE DES PARAMÈTRES
-- =====================================================
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(255) NOT NULL UNIQUE,
    setting_value TEXT NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- DONNÉES PAR DÉFAUT - ADMIN
-- =====================================================
-- Mot de passe: admin.com (hashé en bcrypt)
INSERT INTO users (name, email, phone, password, role) VALUES 
('Administrateur', 'vente@gmail.com', '+243 000 000 001', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- =====================================================
-- DONNÉES PAR DÉFAUT - CATÉGORIES
-- =====================================================
INSERT INTO categories (name, description, image) VALUES 
('Vêtements', 'Vêtements pour hommes, femmes et enfants', 'https://images.unsplash.com/photo-1489987707025-afc232f7ea0f?w=400'),
('Sacs', 'Sacs à main, sacs à dos, sacs de voyage', 'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=400'),
('Chaussures', 'Chaussures pour toutes les occasions', 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400'),
('Accessoires', 'Montres, bijoux, ceintures et plus', 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=400');

-- =====================================================
-- DONNÉES PAR DÉFAUT - PRODUITS
-- =====================================================
INSERT INTO products (name, description, price, original_price, image, category_id, stock, is_popular, is_promotion) VALUES
('Chemise Élégante', 'Chemise de qualité supérieure pour homme', 25.00, NULL, 'https://images.unsplash.com/photo-1602810318383-e386cc2a3ccf?w=400', 1, 50, 1, 0),
('Pantalon Classique', 'Pantalon confortable et élégant', 35.00, NULL, 'https://images.unsplash.com/photo-1624378439575-d8705ad7ae80?w=400', 1, 30, 1, 0),
('Robe Moderne', 'Robe élégante pour toutes occasions', 45.00, 55.00, 'https://images.unsplash.com/photo-1595777457583-95e059d581b8?w=400', 1, 25, 1, 1),
('Sac à Main Luxe', 'Sac à main de marque élégant', 60.00, NULL, 'https://images.unsplash.com/photo-1584917865442-de89df76afd3?w=400', 2, 20, 1, 0),
('Sac à Dos', 'Sac à dos résistant et pratique', 30.00, NULL, 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=400', 2, 40, 0, 0),
('Chaussures Sport', 'Chaussures de sport de qualité', 55.00, NULL, 'https://images.unsplash.com/photo-1460353581641-37baddab0fa2?w=400', 3, 35, 1, 0),
('Sandales', 'Sandales élégantes et confortables', 20.00, 28.00, 'https://images.unsplash.com/photo-1543163521-1bf539c55dd2?w=400', 3, 45, 0, 1),
('Montre Connectée', 'Montre intelligente avec toutes les fonctionnalités', 80.00, NULL, 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=400', 4, 15, 1, 0),
('Ceinture en Cuir', 'Ceinture en cuir véritable', 25.00, NULL, 'https://images.unsplash.com/photo-1624222247344-550fb60583dc?w=400', 4, 60, 0, 0),
('Lunettes de Soleil', 'Lunettes de soleil tendance', 35.00, NULL, 'https://images.unsplash.com/photo-1572635196237-14b3f281503f?w=400', 4, 40, 1, 0);

-- =====================================================
-- DONNÉES PAR DÉFAUT - PARAMÈTRES
-- =====================================================
INSERT INTO settings (setting_key, setting_value) VALUES 
('payment_phone', '+243 000 000 000'),
('shop_name', 'HOROZON ALBASERVICE'),
('shop_address', 'Kindu, Congo');

-- =====================================================
-- CRÉER UN UTILISATEUR TEST (CLIENT)
-- =====================================================
-- Mot de passe: test123
INSERT INTO users (name, email, phone, password, role, address, city, latitude, longitude) VALUES 
('Client Test', 'test@gmail.com', '+243 000 000 002', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'client', 'Avenue du Commerce', 'Kindu', -2.9437, 25.9225);

-- =====================================================
-- CRÉER UN LIVREUR TEST
-- =====================================================
-- Mot de passe: livreur123
INSERT INTO users (name, email, phone, password, role, address, city, latitude, longitude) VALUES 
('Livreur Test', 'livreur@gmail.com', '+243 000 000 003', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'livreur', 'Avenue Principale', 'Kindu', -2.9437, 25.9225);

-- =====================================================
-- CRÉER UNE COMMANDE TEST
-- =====================================================
INSERT INTO orders (user_id, customer_name, customer_phone, customer_address, customer_city, customer_latitude, customer_longitude, total_amount, status, delivery_person_id) VALUES
(2, 'Client Test', '+243 000 000 002', 'Avenue du Commerce', 'Kindu', -2.9437, 25.9225, 85.00, 'en_livraison', 3);

-- =====================================================
-- DÉTAILS DE LA COMMANDE TEST
-- =====================================================
INSERT INTO order_details (order_id, product_id, product_name, product_price, quantity, subtotal) VALUES
(1, 1, 'Chemise Élégante', 25.00, 1, 25.00),
(1, 4, 'Sac à Main Luxe', 60.00, 1, 60.00);

-- =====================================================
-- PAIEMENT POUR COMMANDE TEST
-- =====================================================
INSERT INTO payments (order_id, amount, payment_method, transaction_number, status, payment_phone) VALUES
(1, 85.00, 'mobile_money', 'MM123456789', 'valide', '+243 000 000 002');

-- =====================================================
-- LOCALISATION DU LIVREUR POUR COMMANDE TEST
-- =====================================================
INSERT INTO delivery_locations (order_id, delivery_person_id, latitude, longitude, accuracy) VALUES
(1, 3, -2.9437, 25.9225, 10.0);

-- =====================================================
-- FIN DU FICHIER SQL
-- =====================================================
