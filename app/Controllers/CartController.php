<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Cart;
use App\Services\CartService;
use App\Support\{Auth, Http, Input};
use DomainException;
use Psr\Http\Message\{ServerRequestInterface, ResponseInterface};

final class CartController
{
    public function __construct(private Cart $cart, private CartService $service)
    {
    }

    public function index(ServerRequestInterface $request, array $params): ResponseInterface
    {
        return $this->page();
    }

    public function add(ServerRequestInterface $request, array $params): ResponseInterface
    {
        return $this->mutate($request, 'add');
    }

    public function update(ServerRequestInterface $request, array $params): ResponseInterface
    {
        return $this->mutate($request, 'update');
    }

    public function remove(ServerRequestInterface $request, array $params): ResponseInterface
    {
        return $this->mutate($request, 'remove');
    }

    private function mutate(ServerRequestInterface $request, string $action): ResponseInterface
    {
        $form = Input::form($request);
        $id = Input::positive($form['product_id'] ?? null);
        $quantity = Input::positive($form['quantity'] ?? null, 1000000);

        if ($id === null || ($action !== 'remove' && $quantity === null)) {
            return $this->page('Mã sản phẩm hoặc số lượng không hợp lệ.');
        }

        try {
            $this->service->change(Auth::userId(), $id, $quantity, $action);
            return Http::redirect('/cart');
        } catch (DomainException $error) {
            return $this->page($error->getMessage());
        }
    }

    private function page(?string $error = null): ResponseInterface
    {
        $items = $this->cart->activeItems(Auth::userId());

        return Http::view(
            'cart/index',
            compact('items', 'error') + CartService::summary($items),
            $error ? 422 : 200
        );
    }
}