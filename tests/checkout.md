# D01 — Kịch bản checkout thủ công

Chuẩn bị DB thử nghiệm, hai khách A/B đăng nhập ở hai hồ sơ trình duyệt riêng, một sản phẩm tồn 2 và một sản phẩm tồn 1. Dùng dữ liệu giao hàng giả lập. Ghi tồn và số đơn trước mỗi ca, khôi phục fixture giữa các ca.

| Ca | Thao tác | Kết quả mong đợi |
| --- | --- | --- |
| D01-01 Giỏ rỗng | A mở checkout với giỏ trống. Kiểm tra thêm trường hợp mở form khi có hàng, sau đó xóa giỏ ở tab khác trước khi gửi. | Giỏ trống hiển thị tổng 0 ₫ và link về giỏ. Yêu cầu từ form cũ bị từ chối, số đơn giữ nguyên. |
| D01-02 Thiếu tồn | A thêm 2 sản phẩm tồn 2 rồi mở form. Giảm tồn thử nghiệm xuống 1 trước khi đặt. | Báo thiếu tồn, giữ giỏ, không tạo đơn. Tồn vẫn bằng 1. |
| D01-03 Gửi lại form | A đặt 1 sản phẩm tồn 2, sau đó gửi lại cùng nội dung và cùng checkout_token trong cùng phiên. | Cùng mã đơn. DB có đúng một đơn mới và tồn còn 1. |
| D01-04 Tranh sản phẩm cuối | A và B cùng mở form cho sản phẩm còn 1, bấm Đặt hàng gần như đồng thời. Lặp với fixture mới. | Đúng một đơn thành công, tồn bằng 0. Khách còn lại nhận lỗi thiếu tồn và giữ giỏ. |
| D01-05 Đơn của người khác | A đặt đơn rồi sao chép URL chi tiết. B mở URL trong phiên B. | Trả 404 theo hợp đồng đề xuất. Thông tin đơn của A không xuất hiện. |
| D01-06 Lỗi trường | Nhập số điện thoại sai với tên và địa chỉ hợp lệ, gửi rồi sửa lại số. | Hiện lỗi dưới trường điện thoại, giữ dữ liệu đã nhập, số đơn giữ nguyên đến khi gửi dữ liệu hợp lệ. |

Ghi ngày chạy, người chạy, Pass/Fail, mã đơn và tồn thực tế khi có. Chạy sau khi C01 đã nối với view và dữ liệu thử nghiệm phù hợp B01.
