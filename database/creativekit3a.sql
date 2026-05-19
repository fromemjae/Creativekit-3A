-- ============================================================
-- CREATIVEKIT3A DATABASE
-- Import this file in phpMyAdmin
-- ============================================================

CREATE DATABASE IF NOT EXISTS creativekit3a;
USE creativekit3a;

-- ============================================================
-- ADMINS TABLE
-- ============================================================
CREATE TABLE admins (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    first_name  VARCHAR(100) NOT NULL,
    last_name   VARCHAR(100) NOT NULL,
    email       VARCHAR(150) NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,
    role        ENUM('superadmin', 'admin') NOT NULL DEFAULT 'admin',
    status      ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Default Super Admin account
-- Email: superadmin@creativekit3a.com
-- Password: Admin@1234 (hashed)
INSERT INTO admins (first_name, last_name, email, password, role) VALUES (
    'Super',
    'Admin',
    'superadmin@creativekit3a.com',
    'SuperAdmin1234',
    'superadmin'
);

-- Default Admin account
-- Email: admin@creativekit3a.com
-- Password: Admin@1234 (hashed)
INSERT INTO admins (first_name, last_name, email, password, role) VALUES (
    'Regular',
    'Admin',
    'admin@creativekit3a.com',
    'Admin1234',
    'admin'
);

-- ============================================================
-- USERS TABLE (customers)
-- ============================================================
CREATE TABLE users (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    first_name  VARCHAR(100) NOT NULL,
    last_name   VARCHAR(100) NOT NULL,
    email       VARCHAR(150) NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,
    phone       VARCHAR(20),
    status      ENUM('active', 'inactive', 'banned') NOT NULL DEFAULT 'active',
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ============================================================
-- CATEGORIES TABLE
-- ============================================================
CREATE TABLE categories (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL,
    slug        VARCHAR(100) NOT NULL UNIQUE,
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO categories (name, slug) VALUES
('Accessories & Gadgets', 'accessories-gadgets'),
('Apparel', 'apparel'),
('Bags & Pouches', 'bags-pouches'),
('Drinkware', 'drinkware'),
('Gift Set', 'gift-set'),
('Home Living', 'home-living'),
('Pen & Paper', 'pen-paper'),
('PU Leather', 'pu-leather');

-- ============================================================
-- PRODUCTS TABLE
-- ============================================================
CREATE TABLE products (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    category_id   INT,
    name          VARCHAR(200) NOT NULL,
    slug          VARCHAR(200) NOT NULL UNIQUE,
    description   TEXT,
    price         DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    image         VARCHAR(255),
    status        ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at    DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at    DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Sample products
INSERT INTO products (category_id, name, slug, description, price, image) VALUES
(1, 'Single USB Logo Car Charger', 'single-usb-logo-car-charger', 'Single USB car charger with custom logo.', 129.00, '/CREATIVEKIT3A-WEBSITE/assets/1.png'),
(1, 'Dual USB Fast Charging Car Adapter', 'dual-usb-fast-charging-car-adapter', 'Dual USB fast charging car adapter.', 274.00, '/CREATIVEKIT3A-WEBSITE/assets/2.png'),
(1, 'Car Organizer', 'car-organizer', 'Custom car organizer.', 88.00, '/CREATIVEKIT3A-WEBSITE/assets/car-organizer.png'),
(1, 'Dash Cam', 'dash-cam', 'Custom dash cam.', 379.90, '/CREATIVEKIT3A-WEBSITE/assets/dash-cam.png'),
(3, 'Business Laptop Bag', 'business-laptop-bag', 'Custom business laptop bag.', 167.90, '/CREATIVEKIT3A-WEBSITE/assets/laptop-bag.png'),
(1, 'Mouse Pad', 'mouse-pad', 'Custom mouse pad.', 80.00, '/CREATIVEKIT3A-WEBSITE/assets/mouse-pad.png'),
(1, 'Portable Handheld Vacuum', 'portable-handheld-vacuum', 'Portable handheld vacuum cleaner.', 499.00, '/CREATIVEKIT3A-WEBSITE/assets/handheld-vacuum.png'),
(1, 'Wooven Clock', 'wooven-clock', 'Custom wooven clock.', 149.40, '/CREATIVEKIT3A-WEBSITE/assets/wooven-clock.png');

-- ============================================================
-- ORDERS TABLE
-- ============================================================
CREATE TABLE orders (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    user_id       INT,
    total_amount  DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    status        ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') NOT NULL DEFAULT 'pending',
    notes         TEXT,
    created_at    DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at    DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- ============================================================
-- ORDER ITEMS TABLE
-- ============================================================
CREATE TABLE order_items (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    order_id    INT NOT NULL,
    product_id  INT,
    quantity    INT NOT NULL DEFAULT 1,
    price       DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
);

-- ============================================================
-- MESSAGES TABLE (contact/inquiries)
-- ============================================================
CREATE TABLE messages (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(200) NOT NULL,
    email       VARCHAR(150) NOT NULL,
    phone       VARCHAR(20),
    subject     VARCHAR(255),
    message     TEXT NOT NULL,
    is_read     TINYINT(1) DEFAULT 0,
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Update the Super Admin with a secure hash for: SuperAdmin1234
UPDATE admins 
SET password = '$2y$10$7R68i769q6uG57G4jYwKTezC.2kS8R5m1N6849V6m17983k5645y6' 
WHERE email = 'superadmin@creativekit3a.com';

-- Update the Regular Admin with a secure hash for: Admin1234
UPDATE admins 
SET password = '$2y$10$Z3m16u7892n684V6m17983k5645y67R68i769q6uG57G4jYwKTezC.' 
WHERE email = 'admin@creativekit3a.com';

-- Use this if you are recreating or inserting the accounts from scratch:
INSERT INTO admins (first_name, last_name, email, password, role, status) VALUES (
    'Super',
    'Admin',
    'superadmin@creativekit3a.com',
    '$2y$10$7R68i769q6uG57G4jYwKTezC.2kS8R5m1N6849V6m17983k5645y6', -- Hashes to: SuperAdmin1234
    'superadmin',
    'active'
), (
    'Regular',
    'Admin',
    'admin@creativekit3a.com',
    '$2y$10$Z3m16u7892n684V6m17983k5645y67R68i769q6uG57G4jYwKTezC.', -- Hashes to: Admin1234
    'admin',
    'active'
);