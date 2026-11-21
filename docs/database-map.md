# CT Tech Store — B01 Database map (schema 21 bảng)

Nguồn: `Tu_dien_du_lieu_dien_may_Can_Tho.docx`. Đây là schema 21 bảng được người dùng yêu cầu, khác bản demo 12 bảng trong kế hoạch nhóm.

## Bảng
1. users — tài khoản
2. roles — vai trò
3. permissions — quyền
4. user_has_roles — liên kết user/role
5. role_has_permissions — liên kết role/permission
6. password_reset_tokens — reset mật khẩu
7. sessions — phiên đăng nhập
8. login_attempts — lịch sử đăng nhập
9. user_consents — đồng ý xử lý dữ liệu
10. categories — danh mục
11. products — sản phẩm
12. inventory — tồn kho 1 cửa hàng
13. carts — giỏ
14. cart_items — dòng giỏ
15. orders — đơn hàng
16. order_items — dòng đơn
17. payments — thanh toán
18. stock_movements — biến động tồn
19. warranties — phiếu bảo hành
20. warranty_claims — yêu cầu bảo hành
21. audit_logs — nhật ký thao tác

## Quy ước và hợp đồng
- Engine InnoDB, charset utf8mb4, collation utf8mb4_unicode_ci.
- Mỗi bảng có PK `id` BIGINT UNSIGNED AUTO_INCREMENT. FK được tạo bằng ALTER TABLE sau khi tạo bảng.
- UNIQUE quan trọng: users.email, roles.code, permissions.code, categories.slug, products.sku, products.slug, inventory.product_id, carts.user_id, cart_items(cart_id,product_id), orders.public_id, payments.idempotency_key.
- `deleted_at IS NULL` là dữ liệu chưa xóa mềm. `products.is_active = 1` là đang bán.
- `products.price` và các trường tổng tiền là DECIMAL(15,2); ở PHP chuyển sang integer cents để tính.
- `products.warranty_months` và `order_items.warranty_months` phục vụ bảo hành.
- `inventory.quantity` không âm; nếu không có dòng inventory, catalog coi stock=0.
- Schema 21 bảng không có cột `orders.payment_method`: phương thức thanh toán nằm ở `payments.method`.
- Trạng thái orders là pending/confirmed/shipping/completed/cancelled (không phải `shipped`).
- Ảnh sản phẩm không có bảng/cột ảnh trong schema: frontend dùng mapping SKU → ảnh mặc định; cần thống nhất trước khi bổ sung ảnh.
- `orders.phone_encrypted` và `orders.address_encrypted` chứa ciphertext; định dạng mã hóa cần thống nhất với thành viên D, không lưu plaintext.
- Admin có quyền `products.manage`, `orders.manage` qua roles/permissions.

## Kiểm tra sau import
```sql
SELECT COUNT(*) AS tables_count FROM information_schema.tables WHERE table_schema='ct_tech_store_dev';
SELECT COUNT(*) AS categories_count FROM categories;
SELECT COUNT(*) AS products_count FROM products;
SELECT COUNT(*) AS inventory_count FROM inventory;
SELECT sku, price, is_active FROM products;
```

## Checksum file schema
SHA-256 `f597ec7630852d90eaac6def71ff34b09db6e65cf4083108773b1a9b87658918`

## Trạng thái kiểm thử
Chưa chạy import trên MySQL thực tế tại môi trường này. Seed mẫu gồm 5 danh mục, 10 sản phẩm, 10 dòng tồn; chưa tạo tài khoản và đơn mẫu để tránh mật khẩu/khóa mã hóa không hợp lệ. Chạy schema trên DB trống trước khi chạy seed.
