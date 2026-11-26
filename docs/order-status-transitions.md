# D03 — Chuyển trạng thái đơn COD

Đối chiếu bảng này với giá trị trong enum thực tế của nhóm trước khi tích hợp.
Nếu enum dùng tên khác, cập nhật TRANSITIONS trong AdminOrderController.

| Trạng thái hiện tại | Trạng thái được chuyển đến |
| ------------------- | -------------------------- |
| pending             | confirmed, cancelled       |
| confirmed           | shipping, cancelled        |
| shipping            | delivered                  |
| delivered           | Kết thúc                   |
| cancelled           | Kết thúc                   |

- GET và POST quản trị đều yêu cầu quyền orders.manage theo B01.
- Trang admin chỉ hiển thị mã đơn, tổng tiền và trạng thái.
- Trang chi tiết của khách chỉ giải mã liên hệ sau khi xác nhận chủ đơn.
- UPDATE chỉ thành công khi mã đơn và trạng thái cũ cùng khớp.
- Trả 422 nếu chuyển trạng thái sai. Trả 409 nếu đơn đã bị cập nhật.
- Việc hoàn tồn khi hủy cần thống nhất với luồng kho của nhóm.
  D03 ở đây chỉ cập nhật trạng thái đơn.
