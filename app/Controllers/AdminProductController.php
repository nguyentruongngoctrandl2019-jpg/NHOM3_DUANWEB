<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\AdminProduct;
use App\Models\Category;

use App\Support\Auth;
use App\Support\Http;
use App\Support\Input;
use App\Support\Money;

use DomainException;
use InvalidArgumentException;
use OverflowException;
use PDOException;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

final class AdminProductController
{
    // ==========================================
    // 1. KHOI TAO CONTROLLER
    // ==========================================

    public function __construct(
        private AdminProduct $products,
        private Category $categories
    ) {
    }

    // ==========================================
    // 2. KIEM TRA QUYEN QUAN TRI
    // ==========================================

    private function authorize(): ?ResponseInterface
    {
        if (Auth::userId() === null) {

            return Http::redirect('/login');
        }

        if (!Auth::can('products.manage')) {

            return Http::error(
                403,
                'Bạn không có quyền quản lý sản phẩm.'
            );
        }

        return null;
    }

    // ==========================================
    // 3. DANH SACH SAN PHAM
    // ==========================================

    public function index(
        ServerRequestInterface $request,
        array $params = []
    ): ResponseInterface {

        $denied = $this->authorize();

        if ($denied !== null) {
            return $denied;
        }

        $query = $request->getQueryParams();

        $keyword = $query['q'] ?? '';

        if (!is_string($keyword)) {

            return Http::error(
                400,
                'Từ khóa không hợp lệ.'
            );
        }

        $keyword = trim($keyword);

        return Http::view(
            'admin/products/index',
            [
                'pageTitle' => 'Quản lý sản phẩm',

                'products' =>
                    $this->products->all($keyword),

                'keyword' => $keyword,
            ]
        );
    }

    // ==========================================
    // 4. HIEN THI FORM THEM SAN PHAM
    // ==========================================

    public function create(
        ServerRequestInterface $request,
        array $params = []
    ): ResponseInterface {

        $denied = $this->authorize();

        if ($denied !== null) {
            return $denied;
        }

        // Ho tro route POST cu cua nhom.
        if ($request->getMethod() === 'POST') {

            return $this->store(
                $request,
                $params
            );
        }

        return $this->renderForm(
            null,
            []
        );
    }

    // ==========================================
    // 5. LUU SAN PHAM MOI
    // ==========================================

    public function store(
        ServerRequestInterface $request,
        array $params = []
    ): ResponseInterface {

        $denied = $this->authorize();

        if ($denied !== null) {
            return $denied;
        }

        return $this->saveProduct(
            $request,
            null,
            []
        );
    }

    // ==========================================
    // 6. HIEN THI FORM CHINH SUA
    // ==========================================

    public function edit(
        ServerRequestInterface $request,
        array $params = []
    ): ResponseInterface {

        $denied = $this->authorize();

        if ($denied !== null) {
            return $denied;
        }

        // Ho tro route POST cu cua nhom.
        if ($request->getMethod() === 'POST') {

            return $this->update(
                $request,
                $params
            );
        }

        $id = $this->getId($params);

        if ($id === null) {
            return $this->notFound();
        }

        $product = $this->products->find($id);

        if ($product === null) {
            return $this->notFound();
        }

        return $this->renderForm(
            $id,
            $product
        );
    }

    // ==========================================
    // 7. CAP NHAT SAN PHAM
    // ==========================================

    public function update(
        ServerRequestInterface $request,
        array $params = []
    ): ResponseInterface {

        $denied = $this->authorize();

        if ($denied !== null) {
            return $denied;
        }

        $id = $this->getId($params);

        if ($id === null) {
            return $this->notFound();
        }

        $product = $this->products->find($id);

        if ($product === null) {
            return $this->notFound();
        }

        return $this->saveProduct(
            $request,
            $id,
            $product
        );
    }

    // ==========================================
    // 8. NGUNG KINH DOANH SAN PHAM
    // ==========================================

    public function disable(
        ServerRequestInterface $request,
        array $params = []
    ): ResponseInterface {

        $denied = $this->authorize();

        if ($denied !== null) {
            return $denied;
        }

        $id = $this->getId($params);

        if (
            $id === null
            || $this->products->find($id) === null
        ) {
            return $this->notFound();
        }

        $this->products->disable(
            $id,
            (int) Auth::userId()
        );

        return Http::redirect(
            '/admin/products'
        );
    }

    // ==========================================
    // 9. DIEU CHINH TON KHO
    // ==========================================

    public function adjustStock(
        ServerRequestInterface $request,
        array $params = []
    ): ResponseInterface {

        $denied = $this->authorize();

        if ($denied !== null) {
            return $denied;
        }

        $id = $this->getId($params);

        if ($id === null) {
            return $this->notFound();
        }

        $product = $this->products->find($id);

        if ($product === null) {
            return $this->notFound();
        }

        $form = Input::form($request);

        $change = filter_var(
            $form['quantity_change'] ?? '',
            FILTER_VALIDATE_INT
        );

        try {

            if (
                $change === false
                || $change === 0
            ) {

                throw new InvalidArgumentException(
                    'Số lượng điều chỉnh không hợp lệ.'
                );
            }

            $this->products->adjustStock(
                $id,
                $change,
                (int) Auth::userId()
            );

            return Http::redirect(
                '/admin/products/' . $id . '/edit'
            );

        } catch (
            DomainException |
            InvalidArgumentException |
            OverflowException $error
        ) {

            return $this->renderForm(
                $id,
                $this->products->find($id) ?? $product,
                $error->getMessage(),
                422
            );
        }
    }

    // ==========================================
    // 10. XU LY LUU SAN PHAM
    // ==========================================

    private function saveProduct(
        ServerRequestInterface $request,
        ?int $id,
        array $product
    ): ResponseInterface {

        $form = Input::form($request);

        $fields = [
            'category_id',
            'sku',
            'name',
            'description',
            'price',
            'stock',
            'stock_expected',
            'warranty_months',
            'is_active',
        ];

        $data = [];

        foreach ($fields as $field) {

            if (array_key_exists($field, $form)) {

                $data[$field] = Input::text(
                    $form,
                    $field
                );
            }
        }

        $data['sku'] = strtoupper(
            trim($data['sku'] ?? '')
        );

        $data['name'] = trim(
            $data['name'] ?? ''
        );

        $data['description'] =
            $data['description'] ?? '';

        $data['price'] =
            $data['price'] ?? '';

        $data['warranty_months'] =
            $data['warranty_months']
            ?? $product['warranty_months']
            ?? 0;

        $data['is_active'] =
            $data['is_active'] ?? '0';

        if ($id === null) {

            $data['stock'] =
                $data['stock'] ?? '0';
        }

        try {

            // Kiem tra danh muc.
            $categories = $this->categories->all();

            $categoryIds = array_map(
                'intval',
                array_column(
                    $categories,
                    'id'
                )
            );

            $categoryId = Input::positive(
                $data['category_id'] ?? null
            );

            if (
                $categoryId === null
                || !in_array(
                    $categoryId,
                    $categoryIds,
                    true
                )
            ) {

                throw new InvalidArgumentException(
                    'Danh mục sản phẩm không hợp lệ.'
                );
            }

            // Kiem tra SKU.
            if (!preg_match(
                '/\A[A-Z0-9][A-Z0-9_-]{0,39}\z/',
                $data['sku']
            )) {

                throw new InvalidArgumentException(
                    'Mã SKU không hợp lệ.'
                );
            }

            // Kiem tra ten san pham.
            if (
                $data['name'] === ''
                || preg_match(
                    '/\A.{1,160}\z/us',
                    $data['name']
                ) !== 1
            ) {

                throw new InvalidArgumentException(
                    'Tên sản phẩm phải từ 1 đến 160 ký tự.'
                );
            }

            // Kiem tra mo ta.
            if (preg_match(
                '/\A.{0,3000}\z/us',
                $data['description']
            ) !== 1) {

                throw new InvalidArgumentException(
                    'Mô tả không được vượt quá 3000 ký tự.'
                );
            }

            // Kiem tra va chuan hoa gia tien.
            $data['price'] = Money::toDecimal(
                Money::fromDecimal(
                    $data['price']
                )
            );

            // Kiem tra trang thai.
            if (!in_array(
                $data['is_active'],
                ['0', '1'],
                true
            )) {

                throw new InvalidArgumentException(
                    'Trạng thái sản phẩm không hợp lệ.'
                );
            }

            // Kiem tra ton kho.
            if (isset($data['stock'])) {

                $stock = filter_var(
                    $data['stock'],
                    FILTER_VALIDATE_INT,
                    [
                        'options' => [
                            'min_range' => 0,
                            'max_range' => 1000000,
                        ]
                    ]
                );

                if ($stock === false) {

                    throw new InvalidArgumentException(
                        'Số lượng tồn kho không hợp lệ.'
                    );
                }

                $data['stock'] = $stock;
            }

            // Khong ghi de ton kho neu form
            // khong gui stock_expected.
            if (
                $id !== null
                && !isset($data['stock_expected'])
            ) {

                unset($data['stock']);
            }

            // Luu du lieu.
            $this->products->save(
                $id,
                $data,
                (int) Auth::userId()
            );

            return Http::redirect(
                '/admin/products'
            );

        } catch (
            DomainException |
            InvalidArgumentException |
            OverflowException $error
        ) {

            return $this->renderForm(
                $id,
                array_replace($product, $data),
                $error->getMessage(),
                422,
                $data
            );

        } catch (PDOException $error) {

            if (
                (int) ($error->errorInfo[1] ?? 0)
                !== 1062
            ) {
                throw $error;
            }

            return $this->renderForm(
                $id,
                array_replace($product, $data),
                'Mã SKU đã tồn tại.',
                422,
                $data
            );
        }
    }

    // ==========================================
    // 11. HIEN THI FORM SAN PHAM
    // ==========================================

    private function renderForm(
        ?int $id,
        array $product,
        ?string $error = null,
        int $status = 200,
        array $old = []
    ): ResponseInterface {

        $categories = $this->categories->all();

        $base = __DIR__
            . '/../Views/admin/products/';

        // Uu tien form dung chung.
        if (is_file($base . 'form.php')) {

            $view = 'admin/products/form';

        } else {

            // Ho tro form rieng cua nhom.
            $view = $id === null
                ? 'admin/products/create'
                : 'admin/products/edit';
        }

        return Http::view(
            $view,
            [
                'pageTitle' => $id === null
                    ? 'Thêm sản phẩm'
                    : 'Chỉnh sửa sản phẩm',

                'id' => $id,

                'product' => $product,

                'categories' => $categories,

                'old' => $old,

                'error' => $error,
            ],
            $status
        );
    }

    // ==========================================
    // 12. KIEM TRA ID SAN PHAM
    // ==========================================

    private function getId(array $params): ?int
    {
        $id = filter_var(
            $params['id'] ?? '',
            FILTER_VALIDATE_INT,
            [
                'options' => [
                    'min_range' => 1,
                ]
            ]
        );

        return $id === false
            ? null
            : $id;
    }

    // ==========================================
    // 13. XU LY LOI 404
    // ==========================================

    private function notFound(): ResponseInterface
    {
        return Http::error(
            404,
            'Không tìm thấy sản phẩm.'
        );
    }
}