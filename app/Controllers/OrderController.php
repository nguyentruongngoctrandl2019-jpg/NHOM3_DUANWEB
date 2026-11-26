<?php

declare(strict_types=1);

namespace app\Controllers;

use app\Models\Order;
use app\Services\OrderContactCipher;
use app\Support\{Auth, Http};
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};

final class OrderController
{
    public function __construct(private Order $orders)
    {
    }

    public function index(ServerRequestInterface $request, array $params): ResponseInterface
    {
        return Http::view('orders/index', [
            'orders' => $this->orders->list(Auth::userId()),
        ]);
    }

    public function show(ServerRequestInterface $request, array $params): ResponseInterface
    {
        $publicId = $params['publicId'] ?? '';

        if (!preg_match('/\A[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}\z/', $publicId)) {
            return Http::error(404, 'Không tìm thấy đơn hàng.');
        }

        $order = $this->orders->findOwned($publicId, Auth::userId());

        if ($order === null) {
            return Http::error(404, 'Không tìm thấy đơn hàng.');
        }

        $cipher = OrderContactCipher::fromEnvironment();
        $phone = $cipher->decrypt($order['phone_encrypted']);
        $address = $cipher->decrypt($order['address_encrypted']);
        $items = $this->orders->items((int) $order['id']);

        return Http::view('orders/show', compact('order', 'phone', 'address', 'items'));
    }
}