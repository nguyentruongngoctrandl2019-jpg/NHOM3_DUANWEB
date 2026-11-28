<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Cart;
use App\Services\CartService;

use App\Support\Auth;
use App\Support\Http;
use App\Support\Input;

use DomainException;
use InvalidArgumentException;
use OverflowException;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

final class CartController
{
    // ==========================================
    // 1. KHOI TAO CONTROLLER
    // ==========================================

    public function __construct(
        private Cart $cart,
        private CartService $service
    ) {
    }

    // ==========================================
    // 2. HIEN THI GIO HANG
    // ==========================================

    public function index(
        ServerRequestInterface $request,
        array $params = []
    ): ResponseInterface {

        return $this->page();
    }

    // ==========================================
    // 3. THEM SAN PHAM VAO GIO HANG
    // ==========================================

    public function add(
        ServerRequestInterface $request,
        array $params = []
    ): ResponseInterface {

        return $this->mutate(
            $request,
            'add'
        );
    }

    // ==========================================
    // 4. CAP NHAT SO LUONG SAN PHAM
    // ==========================================

    public function update(
        ServerRequestInterface $request,
        array $params = []
    ): ResponseInterface {

        return $this->mutate(
            $request,
            'update'
        );
    }

    // ==========================================
    // 5. XOA SAN PHAM KHOI GIO HANG
    // ==========================================

    public function remove(
        ServerRequestInterface $request,
        array $params = []
    ): ResponseInterface {

        return $this->mutate(
            $request,
            'remove'
        );
    }

    // ==========================================
    // 6. XU LY THAO TAC GIO HANG
    // ==========================================

    private function mutate(
        ServerRequestInterface $request,
        string $action
    ): ResponseInterface {

        // Kiem tra dang nhap.
        $userId = Auth::userId();

        if ($userId === null) {

            return Http::redirect('/login');
        }

        // Lay du lieu tu form.
        $form = Input::form($request);

        // Lay ID san pham.
        $id = Input::positive(
            $form['product_id'] ?? null
        );

        // Lay so luong san pham.
        $quantity = Input::positive(
            $form['quantity'] ?? null,
            1000000
        );

        // Kiem tra du lieu dau vao.
        if (
            $id === null
            || (
                $action !== 'remove'
                && $quantity === null
            )
        ) {

            return $this->page(
                'Mã sản phẩm hoặc số lượng không hợp lệ.'
            );
        }

        try {

            // Thuc hien thao tac gio hang.
            $this->service->change(
                (int) $userId,
                $id,
                $quantity,
                $action
            );

            // Chuyen huong ve gio hang.
            return Http::redirect('/cart');

        } catch (
            DomainException |
            InvalidArgumentException |
            OverflowException $error
        ) {

            // Hien thi thong bao loi.
            return $this->page(
                $error->getMessage()
            );
        }
    }

    // ==========================================
    // 7. HIEN THI DU LIEU GIO HANG
    // ==========================================

    private function page(
        ?string $error = null
    ): ResponseInterface {

        // Kiem tra dang nhap.
        $userId = Auth::userId();

        if ($userId === null) {

            return Http::redirect('/login');
        }

        // Lay danh sach san pham trong gio.
        $items = $this->cart->activeItems(
            (int) $userId
        );

        // Tinh tong tien va dieu kien thanh toan.
        $summary = CartService::summary(
            $items
        );

        // Truyen du lieu sang View.
        $data = [
            'pageTitle' => 'Giỏ hàng - CT TECH STORE',

            'items' => $items,

            'error' => $error,

            'total' => $summary['total'],

            'canCheckout' => $summary['canCheckout'],
        ];

        // Tra ve giao dien theo chuan PSR-7.
        return Http::view(
            'cart/index',
            $data,
            $error !== null ? 422 : 200
        );
    }
}