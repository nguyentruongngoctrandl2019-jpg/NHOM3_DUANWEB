<?php

declare(strict_types=1);

namespace App\Support;

use GuzzleHttp\Psr7\Response;
use PhpDevCommunity\Router;
use PhpDevCommunity\Exception\RouteNotFound;
use PhpDevCommunity\Exception\MethodNotAllowed;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use ReflectionClass;
use RuntimeException;
use Throwable;

class Kernel
{
    private Router $router;
    private array $metadata;
    private string $baseUrl;
    private array $factories;

    public function __construct(
        Router $router,
        array $metadata,
        string $baseUrl = '',
        array $factories = []
    ) {
        $this->router = $router;
        $this->metadata = $metadata;
        $this->baseUrl = $baseUrl;
        $this->factories = $factories;
    }

    public function handle(
        ServerRequestInterface $request
    ): ResponseInterface {

        try {

            // Tim route phu hop voi request.
            $matched = $this->router->match($request);

            $handler = $matched->getHandler();
            $params = $matched->getAttributes();

            $metadata = $this->findMetadata(
                $request,
                $handler
            );

            // Kiem tra quyen truy cap.
            $guard = $this->checkAccess(
                $metadata['access']
            );

            if ($guard !== null) {
                return $guard;
            }

            // Bao ve cac request thay doi du lieu.
            if (
                !in_array(
                    strtoupper($request->getMethod()),
                    ['GET', 'HEAD', 'OPTIONS'],
                    true
                )
            ) {

                // Ho tro tich hop CSRF tu task A03.
                $csrfClass = __NAMESPACE__ . '\\Csrf';

                if (!class_exists($csrfClass)) {
                    return $this->json(
                        503,
                        'CSRF module is not ready'
                    );
                }

                $body = $request->getParsedBody();

                $token = is_array($body)
                    ? ($body['_csrf'] ?? null)
                    : null;

                if (
                    !is_string($token) ||
                    !$csrfClass::verify($token)
                ) {
                    return $this->json(
                        403,
                        'Invalid CSRF token'
                    );
                }
            }

            // Lay Controller va Action.
            [$controllerClass, $action] = $handler;

            $controller = $this->makeController(
                $controllerClass
            );

            // Kiem tra Action co ton tai hay khong.
            if (!is_callable([$controller, $action])) {
                throw new RuntimeException(
                    'Controller action is not callable'
                );
            }

            // Goi Action cua Controller.
            $response = $controller->$action(
                $request,
                $params
            );

            // Kiem tra Response theo chuan PSR-7.
            if (!$response instanceof ResponseInterface) {
                throw new RuntimeException(
                    'Controller must return ResponseInterface'
                );
            }

            return $response;

        } catch (RouteNotFound $e) {

            return $this->json(
                404,
                'Route not found'
            );

        } catch (MethodNotAllowed $e) {

            $allowed = $this->allowedMethods($request);

            return $this->json(
                405,
                'Method not allowed'
            )->withHeader(
                'Allow',
                implode(', ', $allowed)
            );

        } catch (Throwable $e) {

            error_log(
                get_class($e) . ': ' . $e->getMessage()
            );

            return $this->json(
                500,
                'Internal server error'
            );
        }
    }

    // Tim thong tin metadata cua route.
    private function findMetadata(
        ServerRequestInterface $request,
        mixed $handler
    ): array {

        foreach ($this->metadata as $item) {

            if (
                $item['handler'] !== $handler ||
                $item['method'] !== strtoupper(
                    $request->getMethod()
                )
            ) {
                continue;
            }

            $matcher = new Router(
                [$item['route']],
                $this->baseUrl
            );

            try {

                $matcher->match($request);

                return $item;

            } catch (RouteNotFound | MethodNotAllowed $e) {

                continue;
            }
        }

        throw new RuntimeException(
            'Route metadata not found'
        );
    }

    // Lay danh sach HTTP Method duoc phep.
    private function allowedMethods(
        ServerRequestInterface $request
    ): array {

        $allowed = [];

        foreach ($this->metadata as $item) {

            $matcher = new Router(
                [$item['route']],
                $this->baseUrl
            );

            try {

                $matcher->match(
                    $request->withMethod($item['method'])
                );

                $allowed[] = $item['method'];

            } catch (RouteNotFound | MethodNotAllowed $e) {

                continue;
            }
        }

        return array_values(
            array_unique($allowed)
        );
    }

    // Kiem tra dang nhap va phan quyen.
    private function checkAccess(
        string $access
    ): ?ResponseInterface {

        // Route public khong yeu cau dang nhap.
        if ($access === 'public') {
            return null;
        }

        // Ho tro tich hop Auth tu task A03.
        $authClass = __NAMESPACE__ . '\\Auth';

        if (!class_exists($authClass)) {

            return $this->json(
                503,
                'Authentication module is not ready'
            );
        }

        // Chua dang nhap thi chuyen ve trang login.
        if ($authClass::userId() === null) {

            return new Response(
                303,
                ['Location' => '/login']
            );
        }

        // Kiem tra quyen cua nguoi dung.
        if (
            $access !== 'login' &&
            !$authClass::can($access)
        ) {

            return $this->json(
                403,
                'Access denied'
            );
        }

        return null;
    }

    // Khoi tao Controller.
    private function makeController(
        string $class
    ): object {

        // Su dung Factory neu da duoc khai bao.
        if (isset($this->factories[$class])) {

            return ($this->factories[$class])();
        }

        // Kiem tra Controller co ton tai.
        if (!class_exists($class)) {

            throw new RuntimeException(
                'Controller not found: ' . $class
            );
        }

        $reflection = new ReflectionClass($class);

        $constructor = $reflection->getConstructor();

        // Controller co dependency phai su dung Factory.
        if (
            $constructor !== null &&
            $constructor->getNumberOfRequiredParameters() > 0
        ) {

            throw new RuntimeException(
                'Controller factory is required: ' . $class
            );
        }

        return $reflection->newInstance();
    }

    // Tao JSON Response.
    private function json(
        int $status,
        string $message
    ): ResponseInterface {

        return new Response(
            $status,
            [
                'Content-Type' =>
                    'application/json; charset=utf-8'
            ],
            json_encode(
                [
                    'status' => $status,
                    'message' => $message
                ],
                JSON_UNESCAPED_UNICODE
            )
        );
    }

    // Gui HTTP Response ve trinh duyet.
    public function send(
        ResponseInterface $response
    ): void {

        http_response_code(
            $response->getStatusCode()
        );

        foreach (
            $response->getHeaders() as $name => $values
        ) {

            foreach ($values as $value) {

                header(
                    $name . ': ' . $value,
                    false
                );
            }
        }

        // Request HEAD khong tra ve noi dung body.
        if (
            ($_SERVER['REQUEST_METHOD'] ?? '') !== 'HEAD'
        ) {

            echo $response->getBody();
        }
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