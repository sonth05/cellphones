-- MySQL schema for CellphoneS e-commerce system
CREATE DATABASE IF NOT EXISTS cellphones CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE cellphones;

-- Users table (customers, staff, managers)
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('manager','staff','customer') NOT NULL DEFAULT 'customer',
  phone VARCHAR(30) NULL,
  address TEXT NULL,
  customer_group ENUM('member','vip','dealer') NOT NULL DEFAULT 'member',
  is_active BOOLEAN DEFAULT TRUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Categories table (hierarchical structure)
CREATE TABLE IF NOT EXISTS categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  parent_id INT NULL,
  name VARCHAR(120) NOT NULL,
  slug VARCHAR(160) NOT NULL UNIQUE,
  description TEXT NULL,
  image VARCHAR(255) NULL,
  sort_order INT DEFAULT 0,
  is_active BOOLEAN DEFAULT TRUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Products table
CREATE TABLE IF NOT EXISTS products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  category_id INT NULL,
  name VARCHAR(200) NOT NULL,
  slug VARCHAR(220) NOT NULL UNIQUE,
  sku VARCHAR(80) NOT NULL UNIQUE,
  price DECIMAL(12,2) NOT NULL,
  original_price DECIMAL(12,2) NULL,
  stock INT NOT NULL DEFAULT 0,
  min_stock_level INT DEFAULT 5,
  brand VARCHAR(80) NULL,
  image VARCHAR(255) NULL,
  gallery TEXT NULL, -- JSON array of image URLs
  description TEXT NULL,
  specifications TEXT NULL, -- JSON object for product specs
  weight DECIMAL(8,2) NULL,
  dimensions VARCHAR(100) NULL,
  warranty_period INT NULL, -- months
  is_featured BOOLEAN DEFAULT FALSE,
  is_active BOOLEAN DEFAULT TRUE,
  view_count INT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Cart table (for guest users)
CREATE TABLE IF NOT EXISTS cart (
  id INT AUTO_INCREMENT PRIMARY KEY,
  session_id VARCHAR(255) NOT NULL,
  product_id INT NOT NULL,
  quantity INT NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Orders table
CREATE TABLE IF NOT EXISTS orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_number VARCHAR(50) NOT NULL UNIQUE,
  user_id INT NOT NULL,
  total DECIMAL(12,2) NOT NULL,
  discount_amount DECIMAL(12,2) DEFAULT 0,
  shipping_fee DECIMAL(12,2) DEFAULT 0,
  status ENUM('pending','confirmed','processing','shipping','delivered','cancelled','returned') NOT NULL DEFAULT 'pending',
  payment_method ENUM('cod','bank_transfer','credit_card','ewallet') NOT NULL,
  payment_status ENUM('pending','paid','failed','refunded') DEFAULT 'pending',
  recipient_name VARCHAR(120) NOT NULL,
  recipient_phone VARCHAR(30) NOT NULL,
  recipient_address TEXT NOT NULL,
  recipient_city VARCHAR(100) NULL,
  recipient_district VARCHAR(100) NULL,
  recipient_ward VARCHAR(100) NULL,
  shipping_code VARCHAR(100) NULL,
  shipping_company VARCHAR(100) NULL,
  notes TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Order items table
CREATE TABLE IF NOT EXISTS order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  product_id INT NOT NULL,
  product_name VARCHAR(200) NOT NULL, -- Store product name at time of order
  product_sku VARCHAR(80) NOT NULL,
  quantity INT NOT NULL,
  unit_price DECIMAL(12,2) NOT NULL,
  total_price DECIMAL(12,2) NOT NULL,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Order status history
CREATE TABLE IF NOT EXISTS order_status_history (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  status VARCHAR(50) NOT NULL,
  notes TEXT NULL,
  updated_by INT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  FOREIGN KEY (updated_by) REFERENCES users(id)
);

-- Customer reviews
CREATE TABLE IF NOT EXISTS reviews (
  id INT AUTO_INCREMENT PRIMARY KEY,
  product_id INT NOT NULL,
  user_id INT NOT NULL,
  order_id INT NULL,
  rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
  title VARCHAR(200) NULL,
  content TEXT NULL,
  images TEXT NULL, -- JSON array of image URLs
  is_verified BOOLEAN DEFAULT FALSE,
  is_approved BOOLEAN DEFAULT FALSE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (order_id) REFERENCES orders(id)
);

-- Wishlist
CREATE TABLE IF NOT EXISTS wishlist (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  product_id INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY unique_user_product (user_id, product_id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Promotions/Coupons
CREATE TABLE IF NOT EXISTS promotions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(50) NOT NULL UNIQUE,
  name VARCHAR(200) NOT NULL,
  description TEXT NULL,
  type ENUM('percentage','fixed_amount','free_shipping') NOT NULL,
  value DECIMAL(10,2) NOT NULL,
  min_order_amount DECIMAL(12,2) NULL,
  max_discount_amount DECIMAL(12,2) NULL,
  usage_limit INT NULL,
  used_count INT DEFAULT 0,
  start_date DATETIME NOT NULL,
  end_date DATETIME NOT NULL,
  is_active BOOLEAN DEFAULT TRUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Promotion usage tracking
CREATE TABLE IF NOT EXISTS promotion_usage (
  id INT AUTO_INCREMENT PRIMARY KEY,
  promotion_id INT NOT NULL,
  user_id INT NOT NULL,
  order_id INT NOT NULL,
  discount_amount DECIMAL(12,2) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (promotion_id) REFERENCES promotions(id),
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (order_id) REFERENCES orders(id)
);

-- System settings
CREATE TABLE IF NOT EXISTS settings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  key_name VARCHAR(100) NOT NULL UNIQUE,
  value TEXT NULL,
  description VARCHAR(255) NULL,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Indexes for performance
CREATE INDEX idx_products_name ON products(name);
CREATE INDEX idx_products_brand ON products(brand);
CREATE INDEX idx_products_category ON products(category_id);
CREATE INDEX idx_products_price ON products(price);
CREATE INDEX idx_products_stock ON products(stock);
CREATE INDEX idx_products_featured ON products(is_featured);
CREATE INDEX idx_products_active ON products(is_active);
CREATE INDEX idx_categories_slug ON categories(slug);
CREATE INDEX idx_categories_parent ON categories(parent_id);
CREATE INDEX idx_orders_user ON orders(user_id);
CREATE INDEX idx_orders_status ON orders(status);
CREATE INDEX idx_orders_date ON orders(created_at);
CREATE INDEX idx_order_items_order ON order_items(order_id);
CREATE INDEX idx_order_items_product ON order_items(product_id);
CREATE INDEX idx_reviews_product ON reviews(product_id);
CREATE INDEX idx_cart_session ON cart(session_id);

-- Insert default admin user (password: admin123)
INSERT INTO users(name,email,password_hash,role,phone) VALUES 
('Quản lý hệ thống', 'admin@cellphones.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'manager', '0900000000'),
('Nhân viên bán hàng', 'staff@cellphones.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'staff', '0900000001')
ON DUPLICATE KEY UPDATE email=email;

-- Insert categories
INSERT IGNORE INTO categories(name,slug,parent_id,description) VALUES
('Điện thoại','dien-thoai',NULL,'Điện thoại di động chính hãng'),
('Laptop','laptop',NULL,'Laptop và máy tính xách tay'),
('Phụ kiện','phu-kien',NULL,'Phụ kiện điện thoại và laptop'),
('Smarthome','smarthome',NULL,'Thiết bị nhà thông minh'),
('iPhone','iphone',(SELECT id FROM categories WHERE slug='dien-thoai'),'iPhone chính hãng Apple'),
('Samsung','samsung',(SELECT id FROM categories WHERE slug='dien-thoai'),'Điện thoại Samsung Galaxy'),
('Xiaomi','xiaomi',(SELECT id FROM categories WHERE slug='dien-thoai'),'Điện thoại Xiaomi'),
('OPPO','oppo',(SELECT id FROM categories WHERE slug='dien-thoai'),'Điện thoại OPPO'),
('Vivo','vivo',(SELECT id FROM categories WHERE slug='dien-thoai'),'Điện thoại Vivo'),
('MacBook','macbook',(SELECT id FROM categories WHERE slug='laptop'),'MacBook Apple'),
('Laptop Gaming','laptop-gaming',(SELECT id FROM categories WHERE slug='laptop'),'Laptop chơi game'),
('Ốp lưng','op-lung',(SELECT id FROM categories WHERE slug='phu-kien'),'Ốp lưng điện thoại'),
('Tai nghe','tai-nghe',(SELECT id FROM categories WHERE slug='phu-kien'),'Tai nghe và loa'),
('Sạc dự phòng','sac-du-phong',(SELECT id FROM categories WHERE slug='phu-kien'),'Pin sạc dự phòng'),
('Camera an ninh','camera-an-ninh',(SELECT id FROM categories WHERE slug='smarthome'),'Camera giám sát'),
('Bóng đèn thông minh','bong-den-thong-minh',(SELECT id FROM categories WHERE slug='smarthome'),'Bóng đèn điều khiển từ xa');

-- Insert sample products
INSERT IGNORE INTO products(category_id,name,slug,sku,price,original_price,stock,brand,image,description,is_featured) VALUES
((SELECT id FROM categories WHERE slug='iphone'),'iPhone 15 Pro Max 256GB','iphone-15-pro-max-256gb','IP15PM256','28990000','29990000',50,'Apple','images/products/iphone-15-pro-max.jpg','iPhone 15 Pro Max với chip A17 Pro mạnh mẽ, camera 48MP và màn hình 6.7 inch',TRUE),
((SELECT id FROM categories WHERE slug='samsung'),'Samsung Galaxy S24 Ultra 512GB','samsung-galaxy-s24-ultra-512gb','SGS24U512','26990000','27990000',30,'Samsung','images/products/galaxy-s24-ultra.jpg','Galaxy S24 Ultra với S Pen, camera 200MP và hiệu năng AI vượt trội',TRUE),
((SELECT id FROM categories WHERE slug='xiaomi'),'Xiaomi 14 Ultra 512GB','xiaomi-14-ultra-512gb','XM14U512','19990000','20990000',25,'Xiaomi','images/products/xiaomi-14-ultra.jpg','Xiaomi 14 Ultra với camera Leica và hiệu năng flagship',FALSE),
((SELECT id FROM categories WHERE slug='macbook'),'MacBook Air M3 256GB','macbook-air-m3-256gb','MBA-M3-256','24990000','25990000',20,'Apple','images/products/macbook-air-m3.jpg','MacBook Air với chip M3, màn hình 13.6 inch Liquid Retina',TRUE),
((SELECT id FROM categories WHERE slug='tai-nghe'),'AirPods Pro 2 USB-C','airpods-pro-2-usb-c','APP2-USB-C','6490000','6990000',100,'Apple','images/products/airpods-pro-2.jpg','AirPods Pro thế hệ 2 với cổng sạc USB-C và chống ồn chủ động',TRUE),
((SELECT id FROM categories WHERE slug='op-lung'),'Ốp lưng iPhone 15 Pro Max','op-lung-iphone-15-pro-max','OL-IP15PM','299000','399000',200,'CellphoneS','images/products/op-lung-iphone-15.jpg','Ốp lưng trong suốt bảo vệ iPhone 15 Pro Max',FALSE);

-- Insert system settings
INSERT IGNORE INTO settings(key_name,value,description) VALUES
('site_name','CellphoneS','Tên website'),
('site_description','Hệ thống quản lý bán hàng CellphoneS','Mô tả website'),
('currency','VND','Đơn vị tiền tệ'),
('shipping_fee','30000','Phí vận chuyển mặc định'),
('free_shipping_threshold','500000','Ngưỡng miễn phí vận chuyển'),
('low_stock_threshold','10','Ngưỡng cảnh báo hết hàng'),
('order_auto_confirm_hours','24','Tự động xác nhận đơn hàng sau (giờ)');


