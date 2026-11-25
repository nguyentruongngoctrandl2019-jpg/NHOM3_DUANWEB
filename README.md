## Cài đặt và chạy thử từ bản clone sạch

### 1. Clone nhánh tích hợp

```bash
git clone --branch <nhanh-da-review> <url-repo>
cd <thu-muc-vua-clone>
git rev-parse HEAD
```

### 2. Chuẩn bị database dev

1. Tạo một database dev riêng cho lần chạy thử.
2. Mở phpMyAdmin hoặc công cụ MySQL của nhóm, chọn DB dev và nhập
   các file SQL trong `database/` theo thứ tự: `<danh-sách-file-SQL>`.
3. Cấu hình kết nối DB tại `<file-hoặc-biến-môi-trường-của-dự-án>`.
4. Cấu hình khóa mã hóa liên hệ theo D02. Nếu dùng bản D02 trong hướng dẫn,
   biến cần có là `ORDER_CONTACT_KEY_BASE64`. Lưu khóa ở cấu hình local.

### 3. Cài thư viện và build CSS

Chạy tại thư mục gốc của dự án:

```bash
composer install
npm ci
```

Chạy `npm run` để xem danh sách script. Dùng lệnh build CSS được khai báo
trong `package.json`. Ví dụ, nếu script tên `build` thì chạy:

```bash
npm run build
```

### 4. Chạy website

Lệnh chạy server của nhóm: `<lệnh-server-thực-tế>`.
URL mở trên trình duyệt: `<URL-local-thực-tế>`.

### 5. Demo và ghi kết quả

1. Dùng vai trò khách để xem catalog, đăng ký, thêm sản phẩm vào giỏ,
   đặt COD và xem đơn.
2. Dùng tài khoản được cấp quyền `orders.manage` để xem đơn và cập nhật
   trạng thái theo `docs/order-status-transitions.md`.
3. Chạy lại các ca lỗi D01–D03 và ghi kết quả, minh chứng và commit đã thử
   vào [`tests/manual/results.md`](tests/manual/results.md).