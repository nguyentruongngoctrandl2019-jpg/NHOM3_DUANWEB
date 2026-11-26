<?php

declare(strict_types=1);

namespace app\Controllers;

use app\Models\{Category, Product};
use app\Support\{Http, Input};
use Psr\Http\Message\{ServerRequestInterface, ResponseInterface};

final class ProductController
{
    public function __construct(private Product $products, private Category $categories)
    {
    }

    public function index(ServerRequestInterface $request, array $params): ResponseInterface
    {
        $query = $request->getQueryParams();
        $filters = [
            'q' => mb_substr(Input::text($query, 'q'), 0, 100),
            'category' => Input::positive($query['category'] ?? null),
            'page' => Input::positive($query['page'] ?? null) ?? 1,
        ];

        $data = $this->products->search($filters);

        return Http::view('catalog/index', $data + [
            'categories' => $this->categories->all(),
            'filters' => $filters,
        ]);
    }

    public function show(ServerRequestInterface $request, array $params): ResponseInterface
    {
        $id = Input::positive($params['id'] ?? null);
        $product = $id === null ? null : $this->products->findActive($id);

        return $product === null
            ? Http::error(404, 'Không tìm thấy sản phẩm.')
            : Http::view('catalog/show', ['product' => $product, 'title' => $product['name']]);
    }
}