<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Services\CartService;
use App\Support\View;

class CartController
{
    private CartService $cart;

    public function __construct()
    {
        $this->cart = new CartService();
    }

    // Hiển thị giỏ hàng
    public function index(): void
    {
        View::render('cart/index', [
            'pageTitle' => 'Giỏ hàng - CT TECH STORE',
            'items' => $this->cart->getItems(),
            'total' => $this->cart->getTotal()
        ]);
    }

    // Thêm sản phẩm vào giỏ hàng
    public function add(): void
    {
        $productId = filter_input(
            INPUT_POST,
            'product_id',
            FILTER_VALIDATE_INT
        );

        $quantity = filter_input(
            INPUT_POST,
            'quantity',
            FILTER_VALIDATE_INT
        );

        if (!$productId || !$quantity || $quantity < 1) {
            http_response_code(400);
            echo 'Dữ liệu sản phẩm không hợp lệ.';
            return;
        }

        // TODO: Lấy sản phẩm thật từ Product Model
        // do thành viên phụ trách Database cung cấp.
        // Không lấy giá sản phẩm từ form HTML.

        http_response_code(501);
        echo 'Chức năng thêm vào giỏ đang chờ kết nối Product Model.';
    }

    // Cập nhật số lượng
    public function update(): void
    {
        $productId = filter_input(
            INPUT_POST,
            'product_id',
            FILTER_VALIDATE_INT
        );

        $quantity = filter_input(
            INPUT_POST,
            'quantity',
            FILTER_VALIDATE_INT
        );

        if (!$productId || !$quantity || $quantity < 1) {
            http_response_code(400);
            echo 'Số lượng không hợp lệ.';
            return;
        }

        $this->cart->update($productId, $quantity);

        header('Location: /cart');
        exit;
    }

    // Xóa sản phẩm khỏi giỏ hàng
    public function remove(): void
    {
        $productId = filter_input(
            INPUT_POST,
            'product_id',
            FILTER_VALIDATE_INT
        );

        if (!$productId || $productId < 1) {
            http_response_code(400);
            echo 'Sản phẩm không hợp lệ.';
            return;
        }

        $this->cart->remove($productId);

        header('Location: /cart');
        exit;
    }
}

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
