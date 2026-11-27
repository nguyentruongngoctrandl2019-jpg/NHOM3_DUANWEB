-- Import after CT_Tech_Store_21_Bang.sql; DEV ONLY.
USE ct_tech_store_dev;
START TRANSACTION;
INSERT INTO roles(code) VALUES ('admin'),('customer');
INSERT INTO permissions(code) VALUES ('products.manage'),('orders.manage');
INSERT INTO role_has_permissions(role_id,permission_id)
SELECT r.id,p.id FROM roles r CROSS JOIN permissions p WHERE r.code='admin';
INSERT INTO categories(name,slug) VALUES
('Điện thoại','dien-thoai'),('Laptop','laptop'),('Tivi','tivi'),('Tủ lạnh','tu-lanh'),('Máy giặt','may-giat');
INSERT INTO products(category_id,sku,slug,name,description,price,warranty_months,is_active) VALUES
(1,'DT-001','dien-thoai-a','Điện thoại A','Màn hình OLED, 128GB',7990000.00,12,1),
(1,'DT-002','dien-thoai-b','Điện thoại B','Pin dung lượng lớn',4990000.00,12,1),
(2,'LT-001','laptop-a','Laptop A','RAM 16GB, SSD 512GB',15990000.00,24,1),
(2,'LT-002','laptop-b','Laptop B','Phục vụ học tập và văn phòng',11990000.00,24,1),
(3,'TV-001','tivi-a','Smart Tivi A 43 inch','Độ phân giải 4K',8990000.00,24,1),
(3,'TV-002','tivi-b','Smart Tivi B 55 inch','Hỗ trợ kết nối Wi-Fi',12990000.00,24,1),
(4,'TL-001','tu-lanh-a','Tủ lạnh A 250L','Công nghệ tiết kiệm điện',7490000.00,24,1),
(4,'TL-002','tu-lanh-b','Tủ lạnh B 350L','Ngăn đông rộng',10490000.00,24,1),
(5,'MG-001','may-giat-a','Máy giặt A 9kg','Giặt cửa trước',8490000.00,24,1),
(5,'MG-002','may-giat-b','Máy giặt B 10kg','Động cơ inverter',9990000.00,24,0);
INSERT INTO inventory(product_id,quantity) SELECT id, CASE sku WHEN 'DT-002' THEN 0 WHEN 'MG-002' THEN 0 ELSE 10 END FROM products;
COMMIT;
-- Create demo users separately using PHP password_hash(), never plain-text SQL passwords.