<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Order;
use App\Support\{Database, Http, Input};
use PDO;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};

final class AdminOrderController
{
    public function __construct(private Order $orders, private PDO $db)
    {
    }

    public function index(ServerRequestInterface $request, array $params): ResponseInterface
    {
        return Http::view('admin/orders/index', ['orders' => $this->orders->list()]);
    }

    public function status(ServerRequestInterface $request, array $params): ResponseInterface
    {
        $publicId = $params['publicId'] ?? '';

        if (!preg_match('/\A[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}\z/', $publicId)) {
            return Http::error(404, 'Không tìm thấy đơn hàng.');
        }

        $data = Input::form($request);
        $old = Input::text($data, 'old_status');
        $new = Input::text($data, 'status');

        if (!in_array($new, Order::TRANSITIONS[$old] ?? [], true)) {
            return Http::error(422, 'Chuyển trạng thái không hợp lệ.');
        }

        $changed = Database::transaction($this->db, function () use ($publicId, $old, $new): bool {
            if (!$this->orders->transition($publicId, $old, $new)) {
                return false;
            }

            if ($new === 'cancelled') {
                $this->orders->restoreStock($publicId);
            }

            return true;
        });

        return $changed
            ? Http::redirect('/admin/orders')
            : Http::error(409, 'Đơn đã đổi trạng thái hoặc không còn tồn tại.');
    }
}