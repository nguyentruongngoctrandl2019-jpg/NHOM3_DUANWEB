# D01 — Hợp đồng tích hợp checkout

Đề xuất để D và C đối chiếu A03/C01 và mapping B01. Các tên khóa dưới đây là đầu vào của view D01. Controller của dự án cần chuyển đổi dữ liệu hiện có sang mảng này khi render.

| Khóa trong $checkout | Kiểu và nguồn |
| --- | --- |
| action_url, cart_url | URL nội bộ do router tạo cho POST checkout và trang giỏ |
| csrf_token, checkout_token | Chuỗi mã CSRF theo phiên và mã chống tạo trùng do server cấp |
| items | Danh sách phần tử có name (chuỗi), quantity (số nguyên) |
| total_vnd | Số nguyên VND do server tính, bằng 0 khi giỏ rỗng |
| old | Mảng giá trị chuỗi đã nhập của customer_name, phone, address. Ban đầu dùng [] |
| errors | Mảng thông báo tiếng Việt theo tên trường hoặc khóa checkout. Ban đầu dùng [] |
| can_submit | Boolean cho biết khách được gửi form |

Form POST gửi customer_name, phone, address và hai token. Giữ số điện thoại kiểu chuỗi. Server xác thực phiên, CSRF, token đặt hàng và thông tin người nhận. Tổng tiền được tính lại từ dữ liệu hiện hành theo C01. Nếu tổng thay đổi so với lúc mở checkout, hiển thị tổng mới và yêu cầu xác nhận lại.

| Fixture | errors | can_submit |
| --- | --- | --- |
| Giỏ rỗng | [] | false |
| Thiếu tồn | checkout → Sản phẩm không còn đủ số lượng. Vui lòng cập nhật giỏ hàng. | false |
| Điện thoại sai | phone → Vui lòng nhập số điện thoại hợp lệ. | true |
| Token hết hạn | checkout → Phiên đặt hàng đã hết hạn. Vui lòng thử lại. | true nếu có token mới và giỏ hợp lệ |

## Hành vi cần chốt với C

1. Gắn checkout_token với khách và phiên bản giỏ. Đặt ràng buộc duy nhất trên cặp chủ đơn và token. Sau khi xác thực phiên và CSRF, nếu token đã thành công thì trả về cùng đơn của chủ token, kể cả khi giỏ đã rỗng.
2. Giỏ trong DB dùng khóa bản ghi giỏ trong transaction, với cùng quy tắc khóa ở luồng cập nhật giỏ. Giỏ trong session cần đồng bộ checkout và sửa giỏ trên cùng session, kiểm tra phiên bản trước khi đặt và chỉ xóa giỏ đã mua sau commit. Khi thử lại sau commit, đối chiếu phiên bản giỏ trước khi dọn phần đã mua.
3. Khóa dòng tồn kho theo thứ tự ID nhất quán hoặc dùng cập nhật có điều kiện đủ tồn. Ghi đơn, chi tiết và trừ tồn trong cùng transaction. Rollback toàn bộ nếu thiếu tồn hoặc có lỗi. Nếu hai request cùng token đụng ràng buộc duy nhất, rollback rồi đọc lại đơn đã commit.
4. Thành công chuyển hướng 303 đến chi tiết đơn. Truy vấn chi tiết theo ID đơn và chủ đơn lấy từ phiên. Đề xuất trả 404 khi khách mở đơn của người khác.

Khi có A03, C01 và B01, xác nhận route thực tế, khóa phiên, cách lưu giỏ, bảng/cột tồn kho và vị trí lưu checkout_token trước khi chạy test tích hợp.
