<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\{AdminProduct, Category};
use App\Support\{Http, Input, Money};
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