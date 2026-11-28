<?php

declare(strict_types=1);

namespace App\Support;

use App\Controllers\AdminOrderController;
use App\Controllers\AdminProductController;
use App\Controllers\AuthController;
use App\Controllers\CartController;
use App\Controllers\CheckoutController;
use App\Controllers\HealthController;
use App\Controllers\OrderController;
use App\Controllers\ProductController;

use App\Models\AdminProduct;
use App\Models\Cart;
use App\Models\Category;
use App\Models\CheckoutInventory;
use App\Models\Order;
use App\Models\Permission;
use App\Models\Product;
use App\Models\User;

use App\Services\CartService;
use App\Services\CheckoutService;

use GuzzleHttp\Psr7\Response;

use PhpDevCommunity\Router;
use PhpDevCommunity\Exception\RouteNotFound;
use PhpDevCommunity\Exception\MethodNotAllowed;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use PDO;
use ReflectionClass;
use RuntimeException;
use Throwable;

final class Kernel
{
    private ?PDO $db = null;

    private Router $router;

    private array $metadata;

    private string $baseUrl;

    private array $factories;

    // Khoi tao Kernel.
    public function __construct(
        ?Router $router = null,
        array $metadata = [],
        string $baseUrl = '',
        array $factories = []
    ) {
        if ($router === null) {
            $config = require __DIR__
                . '/../../routes/web.php';

            if (
                !is_array($config)
                || !isset($config['router'])
                || !$config['router'] instanceof Router
            ) {
                throw new RuntimeException(
                    'Cấu hình Router không hợp lệ.'
                );
            }

            $router = $config['router'];
            $metadata = $config['metadata'] ?? [];
            $baseUrl = $config['base_url'] ?? '';
        }

        $this->router = $router;
        $this->metadata = $metadata;
        $this->baseUrl = $baseUrl;
        $this->factories = $factories;
    }

    // ==========================================
    // 1. XU LY REQUEST
    // ==========================================

    public function handle(
        ServerRequestInterface $request
    ): ResponseInterface {
        try {
            $matched = $this->router->match($request);

        } catch (RouteNotFound $error) {
            return Http::error(
                404,
                'Không tìm thấy trang.'
            );

        } catch (MethodNotAllowed $error) {
            return Http::error(
                405,
                'Phương thức không được hỗ trợ.'
            )->withHeader(
                'Allow',
                implode(
                    ', ',
                    $this->allowedMethods($request)
                )
            );
        }

        try {
            // Lay thong tin Route.
            $handler = $matched->getHandler();
            $params = $matched->getAttributes();
            $routeName = $matched->getName();

            // Kiem tra Controller va Action.
            if (
                !is_array($handler)
                || count($handler) !== 2
                || !is_string($handler[0] ?? null)
                || !is_string($handler[1] ?? null)
            ) {
                throw new RuntimeException(
                    'Route handler không hợp lệ.'
                );
            }

            [$class, $action] = $handler;

            // Lay quyen truy cap theo ten Route.
            $metadata = $this->metadata[$routeName]
                ?? null;

            if (is_array($metadata)) {
                $access = $metadata['access'] ?? null;
            } else {
                $access = $metadata;
            }

            if (!is_string($access) || $access === '') {
                throw new RuntimeException(
                    'Không tìm thấy quyền của Route: '
                    . $routeName
                );
            }

            // Cap nhat quyen nguoi dung.
            if (
                $class !== HealthController::class
                && Auth::userId() !== null
            ) {
                Auth::refresh(
                    new Permission($this->db())
                );
            }

            // Kiem tra dang nhap va phan quyen.
            $guard = $this->checkAccess($access);

            if ($guard !== null) {
                return $guard;
            }

            // Kiem tra CSRF.
            $method = strtoupper(
                $request->getMethod()
            );

            if (!in_array(
                $method,
                ['GET', 'HEAD', 'OPTIONS'],
                true
            )) {
                $body = $request->getParsedBody();

                $token = is_array($body)
                    ? ($body['_csrf'] ?? null)
                    : null;

                if (
                    !is_string($token)
                    || !Csrf::verify($token)
                ) {
                    return Http::error(
                        403,
                        'Phiên biểu mẫu không hợp lệ.'
                    );
                }
            }

            // Khoi tao Controller.
            $controller = $this->makeController($class);

            if (!is_callable([$controller, $action])) {
                throw new RuntimeException(
                    'Không tìm thấy phương thức: '
                    . $class . '::' . $action
                );
            }

            // Goi Action.
            $response = $controller->$action(
                $request,
                $params
            );

            // Kiem tra Response PSR-7.
            if (!$response instanceof ResponseInterface) {
                throw new RuntimeException(
                    'Controller phải trả về ResponseInterface.'
                );
            }

            return $response;

        } catch (Throwable $error) {
            error_log(
                'Kernel: ' . (string) $error
            );

            throw $error;
        }
    }

    // ==========================================
    // 2. KET NOI DATABASE
    // ==========================================

    private function db(): PDO
    {
        if ($this->db === null) {
            $config = require __DIR__
                . '/../../config/database.php';

            $this->db = Database::connect($config);
        }

        return $this->db;
    }

    // ==========================================
    // 3. KIEM TRA QUYEN TRUY CAP
    // ==========================================

    private function checkAccess(
        string $access
    ): ?ResponseInterface {
        if ($access === 'public') {
            return null;
        }

        if (Auth::userId() === null) {
            return new Response(
                303,
                ['Location' => '/login']
            );
        }

        if (
            $access !== 'login'
            && !Auth::can($access)
        ) {
            return Http::error(
                403,
                'Bạn không có quyền truy cập.'
            );
        }

        return null;
    }

    // ==========================================
    // 4. KHOI TAO CONTROLLER
    // ==========================================

    private function makeController(
        string $class
    ): object {
        // Su dung Factory tuy chinh.
        if (isset($this->factories[$class])) {
            $factory = $this->factories[$class];

            if (!is_callable($factory)) {
                throw new RuntimeException(
                    'Controller Factory không hợp lệ.'
                );
            }

            return $factory();
        }

        if (!class_exists($class)) {
            throw new RuntimeException(
                'Không tìm thấy Controller: ' . $class
            );
        }

        // Health khong can Database.
        if ($class === HealthController::class) {
            return new HealthController();
        }

        return match ($class) {

            // DANG NHAP VA DANG KY.
            AuthController::class =>
                new AuthController(
                    new User($this->db())
                ),

            // SAN PHAM.
            ProductController::class =>
                new ProductController(
                    new Product($this->db()),
                    new Category($this->db())
                ),

            // GIO HANG.
            CartController::class =>
                new CartController(
                    new Cart($this->db()),
                    new CartService(
                        $this->db(),
                        new Cart($this->db()),
                        new CheckoutInventory(
                            $this->db()
                        )
                    )
                ),

            // THANH TOAN.
            CheckoutController::class =>
                new CheckoutController(
                    new Cart($this->db()),
                    new CheckoutService(
                        $this->db(),
                        new Cart($this->db()),
                        new Order($this->db()),
                        new CheckoutInventory(
                            $this->db()
                        )
                    )
                ),

            // DON HANG KHACH HANG.
            OrderController::class =>
                new OrderController(
                    new Order($this->db())
                ),

            // QUAN LY DON HANG.
            AdminOrderController::class =>
                new AdminOrderController(
                    new Order($this->db()),
                    $this->db()
                ),

            // QUAN LY SAN PHAM.
            AdminProductController::class =>
                new AdminProductController(
                    new AdminProduct($this->db()),
                    new Category($this->db())
                ),

            default =>
                $this->createDefaultController($class),
        };
    }

    // ==========================================
    // 5. CONTROLLER MAC DINH
    // ==========================================

    private function createDefaultController(
        string $class
    ): object {
        $reflection = new ReflectionClass($class);

        $constructor = $reflection->getConstructor();

        if (
            $constructor !== null
            && $constructor
                ->getNumberOfRequiredParameters() > 0
        ) {
            throw new RuntimeException(
                'Controller cần Factory: ' . $class
            );
        }

        return $reflection->newInstance();
    }

    // ==========================================
    // 6. HTTP METHOD DUOC PHEP
    // ==========================================

    private function allowedMethods(
        ServerRequestInterface $request
    ): array {
        $allowed = [];

        foreach ($this->metadata as $item) {
            if (
                !is_array($item)
                || !isset($item['route'], $item['method'])
            ) {
                continue;
            }

            $matcher = new Router(
                [$item['route']],
                $this->baseUrl
            );

            try {
                $matcher->match(
                    $request->withMethod(
                        $item['method']
                    )
                );

                $allowed[] = $item['method'];

            } catch (
                RouteNotFound |
                MethodNotAllowed $error
            ) {
                continue;
            }
        }

        return array_values(
            array_unique($allowed)
        );
    }

    // ==========================================
    // 7. GUI RESPONSE
    // ==========================================

    public function send(
        ResponseInterface $response
    ): void {
        $status = $response->getStatusCode();

        if (!headers_sent()) {
            http_response_code($status);

            foreach (
                $response->getHeaders()
                as $name => $values
            ) {
                foreach ($values as $value) {
                    header(
                        $name . ': ' . $value,
                        false
                    );
                }
            }
        }

        $method = strtoupper(
            $_SERVER['REQUEST_METHOD'] ?? 'GET'
        );

        if (
            $method !== 'HEAD'
            && $status >= 200
            && !in_array(
                $status,
                [204, 205, 304],
                true
            )
        ) {
            echo (string) $response->getBody();
        }
    }
}