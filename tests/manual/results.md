# D04 — Kết quả ghép và nghiệm thu website bán hàng điện máy

## 1. Thông tin lần kiểm thử

| Mục                                       | Giá trị thực tế |
| ----------------------------------------- | --------------- |
| Ngày giờ chạy                             | Chưa ghi        |
| Người chạy                                | Chưa ghi        |
| Nhánh đã ghép và review                   | Chưa ghi        |
| Commit đã kiểm tra (`git rev-parse HEAD`) | Chưa ghi        |
| Phiên bản PHP, Composer và Node.js        | Chưa ghi        |
| Tên DB dev và file SQL đã nhập            | Chưa ghi        |
| URL chạy thử                              | Chưa ghi        |

Trạng thái sử dụng: `Chưa chạy`, `Pass`, `Fail`, `Bị chặn`.
Khi chạy xong từng ca, điền kết quả quan sát được và mã đơn hoặc ảnh minh chứng nếu có.

## 2. Kiểm tra bản clone sạch

| Mã     | Thao tác                                                                 | Kết quả mong đợi                                                | Kết quả thực tế | Trạng thái |
| ------ | ------------------------------------------------------------------------ | --------------------------------------------------------------- | --------------- | ---------- |
| SET-01 | Clone mới nhánh tích hợp đã review và chạy `git rev-parse HEAD`.         | Xác định được commit đang nghiệm thu.                           | —               | Chưa chạy  |
| SET-02 | Tạo DB dev riêng, nhập SQL theo thứ tự trong README và cấu hình kết nối. | Import thành công và catalog đọc được dữ liệu mẫu.              | —               | Chưa chạy  |
| SET-03 | Chạy `composer install` tại thư mục gốc dự án.                           | Cài được thư viện PHP từ `composer.lock`.                       | —               | Chưa chạy  |
| SET-04 | Chạy `npm ci` và lệnh build CSS trong README.                            | Cài được thư viện frontend, tạo được CSS và trang tải được CSS. | —               | Chưa chạy  |
| SET-05 | Chạy server bằng lệnh trong README rồi mở URL dự án.                     | Website tải được và các route demo hoạt động.                   | —               | Chưa chạy  |

## 3. Demo chính với hai vai trò

Dùng một tài khoản khách mới và một tài khoản quản trị được cấp quyền
`orders.manage`. Mở hai hồ sơ trình duyệt riêng để giữ hai phiên đăng nhập.
Chọn một sản phẩm điện máy đang còn hàng. Ghi tồn kho trước khi đặt.

| Mã      | Vai trò  | Thao tác                                           | Kết quả mong đợi                                                              | Kết quả thực tế | Trạng thái |
| ------- | -------- | -------------------------------------------------- | ----------------------------------------------------------------------------- | --------------- | ---------- |
| DEMO-01 | Khách    | Mở catalog và chọn một sản phẩm điện máy.          | Xem được tên, giá và chi tiết sản phẩm.                                       | —               | Chưa chạy  |
| DEMO-02 | Khách    | Đăng ký tài khoản mới rồi đăng nhập.               | Đăng ký và đăng nhập thành công bằng tài khoản mới.                           | —               | Chưa chạy  |
| DEMO-03 | Khách    | Thêm sản phẩm vào giỏ và cập nhật số lượng.        | Giỏ hiện đúng sản phẩm, số lượng và tổng tiền.                                | —               | Chưa chạy  |
| DEMO-04 | Khách    | Nhập họ tên, điện thoại, địa chỉ rồi đặt hàng COD. | Tạo đúng một đơn COD và tồn kho giảm đúng số lượng mua.                       | —               | Chưa chạy  |
| DEMO-05 | Khách    | Mở lịch sử đơn, bấm xem đơn vừa đặt.               | Có đúng mã đơn, sản phẩm, số lượng, tổng tiền và trạng thái.                  | —               | Chưa chạy  |
| DEMO-06 | Quản trị | Tìm đơn vừa đặt và chọn một trạng thái hợp lệ.     | Quản trị xem được đơn và cập nhật thành công theo bảng chuyển trạng thái D03. | —               | Chưa chạy  |
| DEMO-07 | Khách    | Tải lại chi tiết đơn.                              | Khách thấy trạng thái mới của đúng đơn vừa cập nhật.                          | —               | Chưa chạy  |

## 4. Chạy lại các ca lỗi D01–D03

Dùng dữ liệu thử nghiệm riêng cho từng ca. Với ca ghi đơn hoặc tồn kho,
ghi mã đơn, số đơn và tồn kho trước/sau để đối chiếu.

| Mã     | Thao tác                                                                                | Kết quả mong đợi                                                        | Kết quả thực tế | Trạng thái |
| ------ | --------------------------------------------------------------------------------------- | ----------------------------------------------------------------------- | --------------- | ---------- |
| ERR-01 | Mở checkout khi giỏ rỗng.                                                               | Báo giỏ trống và không tạo đơn.                                         | —               | Chưa chạy  |
| ERR-02 | Nhập số điện thoại sai rồi gửi form.                                                    | Báo lỗi ở trường điện thoại, giữ thông tin đã nhập và không tạo đơn.    | —               | Chưa chạy  |
| ERR-03 | Mở form khi đủ hàng, sau đó giảm tồn xuống dưới số lượng trong giỏ trước khi đặt.       | Báo thiếu tồn, giữ giỏ, không tạo đơn và không trừ thêm tồn.            | —               | Chưa chạy  |
| ERR-04 | Gửi lại cùng form với cùng `checkout_token` trong cùng phiên.                           | Trả về cùng mã đơn, chỉ có một đơn mới và tồn chỉ giảm một lần.         | —               | Chưa chạy  |
| ERR-05 | Hai tài khoản khách cùng đặt sản phẩm còn một chiếc.                                    | Chỉ một đơn thành công. Tồn về 0. Khách còn lại nhận lỗi thiếu tồn.     | —               | Chưa chạy  |
| ERR-06 | Trên DB dev riêng, phối hợp người phụ trách DB mô phỏng lỗi khi ghi dòng `order_items`. | Đơn và các dòng đã ghi trong lần thử được rollback. Tồn kho giữ nguyên. | —               | Chưa chạy  |
| ERR-07 | Khách B mở URL chi tiết đơn của khách A.                                                | HTTP 404 và không hiển thị dữ liệu đơn của A.                           | —               | Chưa chạy  |
| ERR-08 | Dùng tài khoản khách truy cập trang quản trị và kiểm tra yêu cầu cập nhật trạng thái.   | Cả hai thao tác trả HTTP 403. Trạng thái không đổi.                     | —               | Chưa chạy  |
| ERR-09 | Với tài khoản quản trị, gửi một chuyển trạng thái trái với bảng D03.                    | HTTP 422, có thông báo rõ và trạng thái không đổi.                      | —               | Chưa chạy  |
| ERR-10 | Mở cùng đơn ở hai tab quản trị. Cập nhật ở tab đầu rồi gửi form cũ từ tab còn lại.      | Lần gửi từ tab cũ trả HTTP 409. Trạng thái mới được giữ nguyên.         | —               | Chưa chạy  |

## 5. Lỗi cần giao lại cho chủ module

| Mã ca | Module | Mô tả lỗi thực tế          | Người phụ trách | PR sửa riêng | Kết quả chạy lại |
| ----- | ------ | -------------------------- | --------------- | ------------ | ---------------- |
| —     | —      | Chưa ghi nhận vì chưa chạy | —               | —            | —                |

Mỗi lỗi được giao cho người phụ trách module và sửa bằng PR riêng.
Sau khi PR được ghép, ghi commit mới và kết quả chạy lại của ca bị ảnh hưởng.

## Nghiệm thu trên nhánh tích hợp

- Nhánh:
- Commit đã kiểm tra:
- File SQL đã nhập:
- Người kiểm tra:

| Ca kiểm thử | Kết quả thực tế | Trạng thái |
| --- | --- | --- |
| Giỏ → COD → Xem đơn | Mã đơn: …, tồn trước: …, tồn sau: … | Chưa chạy |
| Admin cập nhật trạng thái | … | Chưa chạy |
| Gửi lại cùng token | … | Chưa chạy |
| Thiếu tồn hoặc lỗi ghi đơn | … | Chưa chạy |
| Khách mở đơn người khác | … | Chưa chạy |

## 6. Kết luận nghiệm thu

- Demo catalog → đăng ký → giỏ → COD → xem đơn → admin: Chưa chạy.
- Ca lỗi đã chạy lại: Chưa ghi.
- Commit được nghiệm thu: Chưa ghi.
- Kết luận: Chưa nghiệm thu.
