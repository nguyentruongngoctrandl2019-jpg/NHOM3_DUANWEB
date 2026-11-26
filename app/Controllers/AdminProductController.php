<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\AdminProduct;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use InvalidArgumentException;
use Throwable;

final class AdminProductController
{
    private AdminProduct $model;

    public function __construct(AdminProduct $model)
    {
        $this->model = $model;
    }

    // ==========================================
    // 1. KIEM TRA QUYEN QUAN TRI
    // ==========================================

    private function authorize(
        ServerRequestInterface $request
    ): ?ResponseInterface {

        $user = $request->getAttribute('user');

        if (
            !is_array($user)
            || empty($user['id'])
        ) {
            return new Response(
                401,
                ['Content-Type' => 'text/plain; charset=utf-8'],
                'Ban chua dang nhap'
            );
        }

        $permissions = $user['permissions'] ?? [];

        if (
            !is_array($permissions)
            || !in_array(
                'products.manage',
                $permissions,
                true
            )
        ) {
            return new Response(
                403,
                ['Content-Type' => 'text/plain; charset=utf-8'],
                'Ban khong co quyen quan ly san pham'
            );
        }

        return null;
    }

    // ==========================================
    // 2. LAY ID NGUOI DUNG
    // ==========================================

    private function userId(
        ServerRequestInterface $request
    ): int {

        $user = $request->getAttribute('user');

        return (int) $user['id'];
    }

    // ==========================================
    // 3. KIEM TRA CSRF
    // ==========================================

    private function verifyCsrf(
        ServerRequestInterface $request
    ): bool {

        $data = $request->getParsedBody();

        $expected = $request->getAttribute(
            'csrf_token'
        );

        $submitted = is_array($data)
            ? ($data['_token'] ?? null)
            : null;

        return is_string($expected)
            && is_string($submitted)
            && $expected !== ''
            && hash_equals($expected, $submitted);
    }

    private function guardPost(
        ServerRequestInterface $request
    ): ?ResponseInterface {

        $denied = $this->authorize($request);

        if ($denied !== null) {
            return $denied;
        }

        if (!$this->verifyCsrf($request)) {
            return new Response(
                403,
                ['Content-Type' => 'text/plain; charset=utf-8'],
                'CSRF token khong hop le'
            );
        }

        return null;
    }

    // ==========================================
    // 4. HIEN THI DANH SACH SAN PHAM
    // ==========================================

    public function index(
        ServerRequestInterface $request,
        array $params = []
    ): ResponseInterface {

        $denied = $this->authorize($request);

        if ($denied !== null) {
            return $denied;
        }

        $query = $request->getQueryParams();

        $keyword = $query['q'] ?? '';

        if (!is_string($keyword)) {
            return new Response(400, [], 'Invalid keyword');
        }

        $keyword = trim($keyword);

        return $this->render('index', [
            'products' => $this->model->all($keyword),
            'keyword' => $keyword,
            'csrfToken' => $request->getAttribute('csrf_token')
        ]);
    }

    // ==========================================
    // 5. HIEN THI FORM THEM SAN PHAM
    // ==========================================

    public function create(
        ServerRequestInterface $request,
        array $params = []
    ): ResponseInterface {

        $denied = $this->authorize($request);

        if ($denied !== null) {
            return $denied;
        }

        return $this->render('create', [
            'categories' => $this->model->categories(),
            'old' => [],
            'error' => '',
            'csrfToken' => $request->getAttribute('csrf_token')
        ]);
    }

    // ==========================================
    // 6. LUU SAN PHAM MOI
    // ==========================================

    public function store(
        ServerRequestInterface $request,
        array $params = []
    ): ResponseInterface {

        $denied = $this->guardPost($request);

        if ($denied !== null) {
            return $denied;
        }

        $data = $request->getParsedBody();

        if (!is_array($data)) {
            return new Response(400, [], 'Invalid data');
        }

        try {

            $id = $this->model->create(
                $data,
                $this->userId($request)
            );

            return $this->redirect(
                '/admin/products/' . $id . '/edit'
            );

        } catch (InvalidArgumentException $e) {

            return $this->render('create', [
                'categories' => $this->model->categories(),
                'old' => $data,
                'error' => $e->getMessage(),
                'csrfToken' => $request->getAttribute('csrf_token')
            ], 422);

        } catch (Throwable $e) {

            return $this->serverError($e);
        }
    }

    // ==========================================
    // 7. HIEN THI FORM CHINH SUA
    // ==========================================

    public function edit(
        ServerRequestInterface $request,
        array $params = []
    ): ResponseInterface {

        $denied = $this->authorize($request);

        if ($denied !== null) {
            return $denied;
        }

        $id = $this->getId($params);

        if ($id === null) {
            return $this->notFound();
        }

        $product = $this->model->find($id);

        if ($product === null) {
            return $this->notFound();
        }

        return $this->render('edit', [
            'product' => $product,
            'categories' => $this->model->categories(),
            'old' => [],
            'error' => '',
            'csrfToken' => $request->getAttribute('csrf_token')
        ]);
    }

    // ==========================================
    // 8. CAP NHAT SAN PHAM
    // ==========================================

    public function update(
        ServerRequestInterface $request,
        array $params = []
    ): ResponseInterface {

        $denied = $this->guardPost($request);

        if ($denied !== null) {
            return $denied;
        }

        $id = $this->getId($params);

        if ($id === null) {
            return $this->notFound();
        }

        $product = $this->model->find($id);

        if ($product === null) {
            return $this->notFound();
        }

        $data = $request->getParsedBody();

        if (!is_array($data)) {
            return new Response(400, [], 'Invalid data');
        }

        try {

            // Khong cho form sua ghi de ton kho.
            unset($data['stock']);

            $this->model->update(
                $id,
                $data,
                $this->userId($request)
            );

            return $this->redirect(
                '/admin/products/' . $id . '/edit'
            );

        } catch (InvalidArgumentException $e) {

            return $this->render('edit', [
                'product' => $product,
                'categories' => $this->model->categories(),
                'old' => $data,
                'error' => $e->getMessage(),
                'csrfToken' => $request->getAttribute('csrf_token')
            ], 422);

        } catch (Throwable $e) {

            return $this->serverError($e);
        }
    }

    // ==========================================
    // 9. NGUNG KINH DOANH SAN PHAM
    // ==========================================

    public function disable(
        ServerRequestInterface $request,
        array $params = []
    ): ResponseInterface {

        $denied = $this->guardPost($request);

        if ($denied !== null) {
            return $denied;
        }

        $id = $this->getId($params);

        if (
            $id === null
            || $this->model->find($id) === null
        ) {
            return $this->notFound();
        }

        try {

            $this->model->disable(
                $id,
                $this->userId($request)
            );

            return $this->redirect('/admin/products');

        } catch (Throwable $e) {

            return $this->serverError($e);
        }
    }

    // ==========================================
    // 10. DIEU CHINH TON KHO
    // ==========================================

    public function adjustStock(
        ServerRequestInterface $request,
        array $params = []
    ): ResponseInterface {

        $denied = $this->guardPost($request);

        if ($denied !== null) {
            return $denied;
        }

        $id = $this->getId($params);

        if ($id === null) {
            return $this->notFound();
        }

        $product = $this->model->find($id);

        if ($product === null) {
            return $this->notFound();
        }

        $data = $request->getParsedBody();

        if (!is_array($data)) {
            return new Response(400, [], 'Invalid data');
        }

        $change = filter_var(
            $data['quantity_change'] ?? '',
            FILTER_VALIDATE_INT
        );

        try {

            if ($change === false || $change === 0) {
                throw new InvalidArgumentException(
                    'So luong dieu chinh khong hop le'
                );
            }

            $this->model->adjustStock(
                $id,
                $change,
                $this->userId($request)
            );

            return $this->redirect(
                '/admin/products/' . $id . '/edit'
            );

        } catch (InvalidArgumentException $e) {

            return $this->render('edit', [
                'product' => $this->model->find($id) ?? $product,
                'categories' => $this->model->categories(),
                'old' => [],
                'error' => $e->getMessage(),
                'csrfToken' => $request->getAttribute('csrf_token')
            ], 422);

        } catch (Throwable $e) {

            return $this->serverError($e);
        }
    }

    // ==========================================
    // 11. KIEM TRA ID SAN PHAM
    // ==========================================

    private function getId(array $params): ?int
    {
        $id = filter_var(
            $params['id'] ?? '',
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1]]
        );

        return $id === false ? null : $id;
    }

    // ==========================================
    // 12. HIEN THI VIEW
    // ==========================================

    private function render(
        string $view,
        array $data = [],
        int $status = 200
    ): ResponseInterface {

        $file = __DIR__
            . '/../Views/admin/products/'
            . $view
            . '.php';

        if (!is_file($file)) {

            return new Response(
                500,
                ['Content-Type' => 'text/plain; charset=utf-8'],
                'View chua duoc tao'
            );
        }

        extract($data, EXTR_SKIP);

        ob_start();

        try {

            require $file;

            $html = ob_get_clean();

            return new Response(
                $status,
                ['Content-Type' => 'text/html; charset=utf-8'],
                $html
            );

        } catch (Throwable $e) {

            ob_end_clean();

            return $this->serverError($e);
        }
    }

    // ==========================================
    // 13. CHUYEN HUONG
    // ==========================================

    private function redirect(
        string $url
    ): ResponseInterface {

        return new Response(
            303,
            ['Location' => $url]
        );
    }

    // ==========================================
    // 14. LOI 404
    // ==========================================

    private function notFound(): ResponseInterface
    {
        return new Response(
            404,
            ['Content-Type' => 'text/plain; charset=utf-8'],
            'San pham khong ton tai'
        );
    }

    // ==========================================
    // 15. LOI HE THONG
    // ==========================================

    private function serverError(
        Throwable $e
    ): ResponseInterface {

        error_log($e->getMessage());

        return new Response(
            500,
            ['Content-Type' => 'text/plain; charset=utf-8'],
            'Da xay ra loi he thong'
        );
    }
}
namespace app\Controllers;

use app\Models\{AdminProduct, Category};
use app\Support\{Http, Input, Money};
use DomainException;
use InvalidArgumentException;
use PDOException;
use Psr\Http\Message\{ServerRequestInterface, ResponseInterface};

final class AdminProductController
{
    public function __construct(private AdminProduct $products, private Category $categories)
    {
    }

    public function index(ServerRequestInterface $request, array $params): ResponseInterface
    {
        return Http::view('admin/products/index', ['products' => $this->products->all()]);
    }

    public function create(ServerRequestInterface $request, array $params): ResponseInterface
    {
        return $this->form($request, null, []);
    }

    public function edit(ServerRequestInterface $request, array $params): ResponseInterface
    {
        $id = Input::positive($params['id'] ?? null);
        $product = $id === null ? null : $this->products->find($id);

        return $product === null
            ? Http::error(404, 'Không tìm thấy sản phẩm.')
            : $this->form($request, $id, $product);
    }

    public function disable(ServerRequestInterface $request, array $params): ResponseInterface
    {
        $id = Input::positive($params['id'] ?? null);

        if ($id === null || $this->products->find($id) === null) {
            return Http::error(404, 'Không tìm thấy sản phẩm.');
        }

        $this->products->disable($id);
        return Http::redirect('/admin/products');
    }

    private function form(ServerRequestInterface $request, ?int $id, array $product): ResponseInterface
    {
        $categories = $this->categories->all();

        if ($request->getMethod() !== 'POST') {
            return Http::view('admin/products/form', compact('id', 'product', 'categories'));
        }

        $form = Input::form($request);
        foreach (['category_id', 'sku', 'name', 'description', 'price', 'stock', 'stock_expected', 'is_active'] as $field) {
            $product[$field] = Input::text($form, $field);
        }

        $product['sku'] = strtoupper($product['sku']);
        $categoryIds = array_map('intval', array_column($categories, 'id'));

        try {
            if (!in_array(Input::positive($product['category_id']), $categoryIds, true)
                || !preg_match('/\A[A-Z0-9][A-Z0-9-]{0,39}\z/', $product['sku'])
                || $product['name'] === ''
                || mb_strlen($product['name']) > 160
                || mb_strlen($product['description']) > 3000
                || ($product['stock'] !== '0' && Input::positive($product['stock'], 1000000) === null)
                || ($id !== null && $product['stock_expected'] !== '0'
                    && Input::positive($product['stock_expected'], 4294967295) === null)
                || !in_array($product['is_active'], ['0', '1'], true)
            ) {
                throw new InvalidArgumentException('Kiểm tra SKU, tên, danh mục, tồn và trạng thái sản phẩm.');
            }

            $product['price'] = Money::toDecimal(Money::fromDecimal($product['price']));
            $this->products->save($id, $product);

            return Http::redirect('/admin/products');
        } catch (DomainException | InvalidArgumentException $exception) {
            $error = $exception->getMessage();
        } catch (PDOException $exception) {
            if (($exception->errorInfo[1] ?? null) !== 1062) {
                throw $exception;
            }

            $error = 'SKU này đã tồn tại.';
        }

        return Http::view('admin/products/form', compact('id', 'product', 'categories', 'error'), 422);
    }
}
