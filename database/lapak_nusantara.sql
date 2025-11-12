DROP DATABASE IF EXISTS lapak_nusantara_db;

CREATE DATABASE lapak_nusantara_db
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE lapak_nusantara_db;

CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT uk_categories_name UNIQUE (name),
    CONSTRAINT chk_categories_name CHECK (LENGTH(TRIM(name)) > 0)
) ENGINE=InnoDB;

CREATE INDEX idx_categories_active ON categories(is_active);
CREATE INDEX idx_categories_display_order ON categories(display_order);

CREATE TABLE menu (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama_menu VARCHAR(255) NOT NULL,
    category_id INT NOT NULL,
    harga DECIMAL(10,2) NOT NULL,
    gambar VARCHAR(255) DEFAULT NULL,
    deskripsi TEXT,
    is_available BOOLEAN DEFAULT TRUE,
    is_featured BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_menu_category FOREIGN KEY (category_id) 
        REFERENCES categories(id) 
        ON DELETE RESTRICT 
        ON UPDATE CASCADE,
    CONSTRAINT chk_menu_harga_positive CHECK (harga > 0),
    CONSTRAINT chk_menu_nama_menu CHECK (LENGTH(TRIM(nama_menu)) >= 2),
    CONSTRAINT chk_menu_harga_max CHECK (harga <= 1000000)
) ENGINE=InnoDB;

CREATE INDEX idx_menu_category ON menu(category_id);
CREATE INDEX idx_menu_available ON menu(is_available);
CREATE INDEX idx_menu_featured ON menu(is_featured);
CREATE INDEX idx_menu_price_range ON menu(harga);
CREATE INDEX idx_menu_name_search ON menu(nama_menu);

CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    customer_name VARCHAR(100) NOT NULL,
    whatsapp VARCHAR(20) NOT NULL,
    address TEXT NOT NULL,
    payment_method ENUM('COD', 'Shopeepay', 'Gopay') NOT NULL,
    notes TEXT DEFAULT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('Dipesan', 'Diproses', 'Selesai', 'Dibatalkan') DEFAULT 'Dipesan',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT chk_orders_customer_name CHECK (LENGTH(TRIM(customer_name)) >= 2),
    CONSTRAINT chk_orders_whatsapp_digits CHECK (whatsapp REGEXP '^[0-9]+$'),
    CONSTRAINT chk_orders_whatsapp_length CHECK (CHAR_LENGTH(whatsapp) >= 10 AND CHAR_LENGTH(whatsapp) <= 15),
    CONSTRAINT chk_orders_address_length CHECK (LENGTH(TRIM(address)) >= 2),
    CONSTRAINT chk_orders_total_positive CHECK (total_amount > 0)
) ENGINE=InnoDB;

CREATE INDEX idx_orders_status ON orders(status);
CREATE INDEX idx_orders_payment_method ON orders(payment_method);
CREATE INDEX idx_orders_created_at ON orders(created_at);
CREATE INDEX idx_orders_customer_search ON orders(customer_name);
CREATE INDEX idx_orders_total_amount ON orders(total_amount);

CREATE TABLE order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    menu_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_order_items_order FOREIGN KEY (order_id) 
        REFERENCES orders(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    CONSTRAINT fk_order_items_menu FOREIGN KEY (menu_id) 
        REFERENCES menu(id) 
        ON DELETE RESTRICT 
        ON UPDATE CASCADE,
    CONSTRAINT chk_order_items_quantity CHECK (quantity > 0),
    CONSTRAINT chk_order_items_price CHECK (price > 0)
) ENGINE=InnoDB;

CREATE INDEX idx_order_items_order ON order_items(order_id);
CREATE INDEX idx_order_items_menu ON order_items(menu_id);
CREATE INDEX idx_order_items_price ON order_items(price);

INSERT INTO categories (name, description, display_order, is_active) VALUES
('Makanan Utama', 'Berbagai jenis makanan utama dan lauk pauk tradisional Nusantara', 1, TRUE),
('Minuman', 'Berbagai jenis minuman segar, Jus, dan beverage tradisional', 2, TRUE),
('Dessert', 'Pencuci mulut tradisional dan modern yang lezat', 3, TRUE),
('Snack', 'Cemilan dan makanan ringan untuk sambil menunggu', 4, TRUE),
('Paket Hemat', 'Kombinasi menu dengan harga special', 5, TRUE);

INSERT INTO menu (nama_menu, category_id, harga, gambar, deskripsi, is_available, is_featured) VALUES
('Nasi Ayam Bakar', 1, 25000, 'image/nasi_ayam.png', 'Nasi putih dengan ayam bakar bumbu rica-rica, lengkap dengan sambal dan lalapan', TRUE, TRUE),
('Sate Ayam', 1, 30000, 'image/sate_ayam.png', '10 tusuk sate ayam muda dengan bumbu kacang spesial dan lontong', TRUE, TRUE),
('Gudeg Nangka', 1, 22000, 'image/gudeg.jpg', 'Gudeg tradisional Yogyakarta dengan nangka muda, lengkap dengan nasi dan lauk', TRUE, FALSE),
('Rendang Sapi', 1, 35000, 'image/rendang.jpg', 'Rendang daging sapi khas Padang yang empuk dan kaya rempah', TRUE, FALSE),
('Ayam Pop', 1, 28000, 'image/ayam_pop.png', 'Ayam kampung rebus dengan bumbu kuning khas Sumatra Barat', TRUE, FALSE),
('Es Teh Manis', 2, 5000, 'image/es_teh.png', 'Es teh manis segar dengan gula aren pilihan', TRUE, FALSE),
('Jus Alpukat', 2, 12000, 'image/jus_alpukat.png', 'Jus alpukat segar dengan susu dan gula', TRUE, TRUE),
('Es Cendol', 2, 8000, 'image/cendol.png', 'Es cendol dengan santan, gula aren, dan es serut', TRUE, FALSE),
('Kopi Hitam', 2, 6000, 'image/kopi_hitam.png', 'Kopi hitam robusta lokal yang harum', TRUE, FALSE),
('Klepon Jus', 2, 10000, 'image/klepon_jus.png', 'Minuman unik kombinasi klepon dan jus buah', TRUE, FALSE),
('Puding Coklat', 3, 8000, 'image/puding_coklat.png', 'Puding coklat lembut dengan Vla vanila', TRUE, FALSE),
('Es Krim Spesial', 3, 15000, 'image/es_krim.png', '3 scoops es krim dengan topping dan wafer', TRUE, TRUE),
('Pancake Nusantara', 3, 18000, 'image/pancake.png', 'Pancake tradisional dengan Kinca dan keju parut', TRUE, FALSE),
('Klepon', 4, 5000, 'image/klepon.jpg', '25 butir klepon segar dengan kelapa parut', TRUE, FALSE),
('Risol Mayo', 4, 4000, 'image/risol.jpg', 'Risol dengan isian mayo dan sayuran segar', TRUE, FALSE),
('Tempe Mendoan', 4, 3000, 'image/tempe_mendoan.png', 'Tempe mendoan khas Banyumas yang renyah', TRUE, FALSE),
('Paket Hemat Sari', 5, 35000, 'image/paket_hemat.png', 'Nasi + Ayam Bakar + Es Teh + Klepon', TRUE, TRUE),
('Paket Lengkap Nusantara', 5, 50000, 'image/paket_lengkap.png', 'Nasi + Rendang + Es Cendol + Pancake', TRUE, FALSE);

INSERT INTO orders (customer_name, whatsapp, address, payment_method, notes, total_amount, status) VALUES
('Ahmad Fadli', '081234567890', 'Jl. Raya No. 123, Bandung', 'COD', 'Tolong pedasnya sedang ya, makasih', 37000, 'Dipesan'),
('Siti Nurhaliza', '081345678901', 'Komplek Permata Hijau, Jakarta Selatan', 'Shopeepay', 'Bisa cepet? lagi bergerak nih', 35000, 'Diproses'),
('Budi Santoso', '082345678902', 'Perum Griya Asri, Surabaya', 'Gopay', 'Datang jam 2 siang ya', 28000, 'Selesai'),
('Yuli Astuti', '082334157792', 'UM', 'COD', 'Enak', 9000, 'Dipesan'),
('Rahman Hakim', '083456789012', 'Kampus UNPAD, Jatinangor', 'COD', 'Mahasiswa nih, budget minim hehe', 42000, 'Dipesan');

INSERT INTO order_items (order_id, menu_id, quantity, price) VALUES
(1, 1, 1, 25000),
(1, 6, 1, 5000),
(1, 13, 1, 7000),
(2, 15, 1, 35000),
(3, 3, 1, 22000),
(3, 6, 1, 5000),
(4, 6, 1, 5000),
(4, 14, 1, 4000),
(5, 2, 1, 30000),
(5, 7, 1, 12000);

DELIMITER //
CREATE TRIGGER prevent_menu_deletion
BEFORE DELETE ON menu
FOR EACH ROW
BEGIN
    DECLARE order_count INT DEFAULT 0;
    
    SELECT COUNT(*) INTO order_count
    FROM order_items oi
    INNER JOIN orders o ON oi.order_id = o.id
    WHERE oi.menu_id = OLD.id 
        AND o.status IN ('Dipesan', 'Diproses');
    
    IF order_count > 0 THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'Cannot delete menu item that is referenced in active orders';
    END IF;
END//
DELIMITER ;

DELIMITER //
CREATE TRIGGER update_order_total_insert
AFTER INSERT ON order_items
FOR EACH ROW
BEGIN
    UPDATE orders 
    SET total_amount = (
        SELECT COALESCE(SUM(quantity * price), 0)
        FROM order_items 
        WHERE order_id = NEW.order_id
    )
    WHERE id = NEW.order_id;
END//
DELIMITER ;

DELIMITER //
CREATE TRIGGER update_order_total_update
AFTER UPDATE ON order_items
FOR EACH ROW
BEGIN
    UPDATE orders 
    SET total_amount = (
        SELECT COALESCE(SUM(quantity * price), 0)
        FROM order_items 
        WHERE order_id = NEW.order_id
    )
    WHERE id = NEW.order_id;
END//
DELIMITER ;

DELIMITER //
CREATE TRIGGER update_order_total_delete
AFTER DELETE ON order_items
FOR EACH ROW
BEGIN
    UPDATE orders 
    SET total_amount = (
        SELECT COALESCE(SUM(quantity * price), 0)
        FROM order_items 
        WHERE order_id = OLD.order_id
    )
    WHERE id = OLD.order_id;
END//
DELIMITER ;