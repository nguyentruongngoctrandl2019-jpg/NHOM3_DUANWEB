<?php

declare(strict_types=1);

namespace app\Support;

use app\Controllers\{
    AdminOrderController,
    AdminProductController,
    AuthController,
    CartController,
    CheckoutController,
    HealthController,
    OrderController,
    ProductController
};
use app\Models\{AdminProduct, Cart, Category, CheckoutInventory, Order, Permission, Product, User};
use app\Services\{CartService, CheckoutService};
use PDO;
use PhpDevCommunity\Exception\{MethodNotAllowed, RouteNotFound};
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};

final class Kernel
{
    private ?PDO $db = null;

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        [$router, $routes, $accessMap] = require __DIR__ . '/../../routes/web.php';

        try {
            $matched = $router->match($request);
        } catch (RouteNotFound) {
            return Http::error(404, 'Không tìm thấy trang.');
        } catch (MethodNotAllowed) {
            $allowed = [];

            foreach ($routes as $route) {
                if ($route->match($request->getUri()->getPath())) {
                    $allowed = array_merge($allowed, $route->getMethods());
                }
            }

            return Http::error(
                405,
                'Phương thức không được hỗ trợ.',
                ['Allow' => implode(', ', array_unique($allowed))]
            );
        }

        [$class, $action] = $matched->getHandler();

        if ($class !== HealthController::class && Auth::userId() !== null) {
            Auth::refresh(new Permission($this->db()));
        }

        $access = $accessMap[$matched->getName()];

        if ($access !== 'public' && Auth::userId() === null) {
            return Http::redirect('/login');
        }

        if ($access !== 'public' && $access !== 'login' && !Auth::can($access)) {
            return Http::error(403, 'Bạn không có quyền truy cập.');
        }

        if ($request->getMethod() === 'POST'
            && !Csrf::verify(Input::text(Input::form($request), '_csrf'))
        ) {
            return Http::error(403, 'Phiên biểu mẫu không hợp lệ. Hãy tải lại trang.');
        }

        return $this->controller($class)->$action($request, $matched->getAttributes());
    }

    private function db(): PDO
    {
        return $this->db ??= Database::connect(
            require __DIR__ . '/../../config/database.php'
        );
    }

    private function controller(string $class): object
    {
        if ($class === HealthController::class) {
            return new HealthController();
        }

        $db = $this->db();

        return match ($class) {
            AuthController::class => new AuthController(new User($db)),
            ProductController::class => new ProductController(new Product($db), new Category($db)),
            CartController::class => new CartController(
                new Cart($db),
                new CartService($db, new Cart($db), new CheckoutInventory($db))
            ),
            CheckoutController::class => new CheckoutController(
                new Cart($db),
                new CheckoutService($db, new Cart($db), new Order($db), new CheckoutInventory($db))
            ),
            OrderController::class => new OrderController(new Order($db)),
            AdminOrderController::class => new AdminOrderController(new Order($db), $db),
            AdminProductController::class => new AdminProductController(
                new AdminProduct($db),
                new Category($db)
            ),
        };
    }
}