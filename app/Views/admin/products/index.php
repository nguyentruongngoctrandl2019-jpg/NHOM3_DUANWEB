
<?php

declare(strict_types=1);

// Du lieu nhan tu AdminProductController

$products = $products ?? [];
$keyword = $keyword ?? '';
$csrfToken = $csrfToken ?? '';

// Bao mat XSS

$e = static function ($value): string {
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
};

// Thong ke danh sach san pham

$totalProducts = count($products);

$activeProducts = 0;
$inactiveProducts = 0;
$outOfStock = 0;

foreach ($products as $product) {

    if ((int) $product['is_active'] === 1) {
        $activeProducts++;
    } else {
        $inactiveProducts++;
    }

    if ((int) $product['stock'] === 0) {
        $outOfStock++;
    }
}

?>

<!DOCTYPE html>
<html lang="vi">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Quản lý sản phẩm - CT TECH STORE</title>

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            color: #1e293b;
            font-size: 14px;
        }

        a {
            text-decoration: none;
        }

        /* SIDEBAR */

        .sidebar {
            width: 240px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background: #14243d;
            color: white;
            padding: 25px 16px;
            overflow-y: auto;
        }

        .logo {
            font-size: 21px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 8px;
        }

        .logo span {
            color: #60a5fa;
        }

        .admin-label {
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
            margin-bottom: 35px;
        }

        .sidebar a {
            display: block;
            padding: 15px;
            margin-bottom: 7px;
            color: #cbd5e1;
            border-radius: 6px;
            transition: 0.2s;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: #2563eb;
            color: white;
        }

        /* MAIN */

        .main {
            margin-left: 240px;
            min-height: 100vh;
        }

        .header {
            background: white;
            padding: 20px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e2e8f0;
        }

        .header h3 {
            font-size: 17px;
        }

        .admin-name {
            color: #64748b;
        }

        .content {
            padding: 30px;
        }

        .breadcrumb {
            color: #64748b;
            margin-bottom: 20px;
        }

        .page-heading {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            margin-bottom: 25px;
        }

        .page-heading h1 {
            font-size: 27px;
            margin-bottom: 8px;
        }

        .page-heading p {
            color: #64748b;
        }

        /* BUTTON */

        .btn {
            display: inline-block;
            border: none;
            border-radius: 6px;
            padding: 11px 16px;
            font-size: 14px;
            cursor: pointer;
            font-family: inherit;
            white-space: nowrap;
            transition: 0.2s;
        }

        .btn:hover {
            opacity: 0.85;
        }

        .btn-primary {
            background: #2563eb;
            color: white;
        }

        .btn-secondary {
            background: #e2e8f0;
            color: #334155;
        }

        .btn-edit {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .btn-disable {
            background: #fee2e2;
            color: #dc2626;
        }

        .btn-small {
            padding: 8px 12px;
            font-size: 13px;
        }

        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* THONG KE */

        .stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 25px;
        }

        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 22px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
        }

        .stat-card p {
            color: #64748b;
            margin-bottom: 12px;
        }

        .stat-card h2 {
            font-size: 32px;
        }

        .stat-total {
            color: #2563eb;
        }

        .stat-active {
            color: #16a34a;
        }

        .stat-inactive {
            color: #dc2626;
        }

        .stat-stock {
            color: #d97706;
        }

        /* DANH SACH SAN PHAM */

        .panel {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            overflow: hidden;
        }

        .panel-header {
            padding: 22px;
            border-bottom: 1px solid #e2e8f0;
        }

        .panel-header h2 {
            font-size: 19px;
            margin-bottom: 18px;
        }

        .search-form {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .search-input {
            flex: 1;
            min-width: 180px;
            padding: 12px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 14px;
            outline: none;
        }

        .search-input:focus {
            border-color: #2563eb;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 950px;
        }

        th {
            padding: 16px 14px;
            text-align: left;
            background: #f8fafc;
            color: #475569;
            font-size: 13px;
            white-space: nowrap;
        }

        td {
            padding: 18px 14px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
        }

        tbody tr:hover {
            background: #f8fafc;
        }

        .product-name {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .sku {
            color: #64748b;
            font-size: 12px;
        }

        .price {
            color: #2563eb;
            font-weight: bold;
            white-space: nowrap;
        }

        .stock-normal {
            color: #16a34a;
            font-weight: bold;
        }

        .stock-low {
            color: #d97706;
            font-weight: bold;
        }

        .stock-empty {
            color: #dc2626;
            font-weight: bold;
        }

        /* TRANG THAI */

        .status {
            display: inline-block;
            padding: 7px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            white-space: nowrap;
        }

        .status-active {
            color: #15803d;
            background: #dcfce7;
        }

        .status-inactive {
            color: #dc2626;
            background: #fee2e2;
        }

        .actions {
            display: flex;
            gap: 7px;
            align-items: center;
        }

        .actions form {
            margin: 0;
        }

        .empty {
            padding: 45px;
            text-align: center;
            color: #64748b;
        }

        .panel-footer {
            padding: 18px 22px;
            color: #64748b;
        }

        .footer {
            padding: 25px;
            text-align: center;
            color: #64748b;
            font-size: 13px;
        }

        /* RESPONSIVE */

        @media (max-width: 1100px) {

            .stats {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {

            .sidebar {
                position: relative;
                width: 100%;
                height: auto;
            }

            .main {
                margin-left: 0;
            }

            .content {
                padding: 15px;
            }

            .header {
                padding: 15px;
            }

            .page-heading {
                align-items: flex-start;
                flex-direction: column;
            }

            .stats {
                gap: 10px;
            }

            .stat-card {
                padding: 15px;
            }

            .stat-card h2 {
                font-size: 25px;
            }
        }

    </style>

</head>

<body>

    <!-- SIDEBAR -->

    <aside class="sidebar">

        <div class="logo">
            CT <span>TECH</span> STORE
        </div>

        <div class="admin-label">
            ADMIN PANEL
        </div>

        <nav>

            <a href="/admin">
                Tổng quan
            </a>

            <a href="/admin/products" class="active">
                Quản lý sản phẩm
            </a>

            <a href="/admin/categories">
                Quản lý danh mục
            </a>

            <a href="/admin/orders">
                Quản lý đơn hàng
            </a>

            <a href="/admin/inventory">
                Quản lý kho hàng
            </a>

            <a href="/admin/warranties">
                Quản lý bảo hành
            </a>

            <a href="/admin/users">
                Quản lý người dùng
            </a>

            <a href="/">
                Xem website
            </a>

        </nav>

    </aside>

    <!-- MAIN -->

    <main class="main">

        <header class="header">

            <h3>Hệ thống quản trị</h3>

            <div class="admin-name">
                Quản trị viên
            </div>

        </header>

        <section class="content">

            <div class="breadcrumb">
                Quản trị / Quản lý sản phẩm
            </div>

            <!-- TIEU DE -->

            <div class="page-heading">

                <div>

                    <h1>Quản lý sản phẩm</h1>

                    <p>
                        Quản lý danh sách sản phẩm
                        của CT TECH STORE
                    </p>

                </div>

                <a href="/admin/products/create"
                   class="btn btn-primary">

                    + Thêm sản phẩm

                </a>

            </div>

            <!-- THONG KE -->

            <div class="stats">

                <div class="stat-card">

                    <p>Tổng sản phẩm</p>

                    <h2 class="stat-total">
                        <?= $totalProducts ?>
                    </h2>

                </div>

                <div class="stat-card">

                    <p>Đang kinh doanh</p>

                    <h2 class="stat-active">
                        <?= $activeProducts ?>
                    </h2>

                </div>

                <div class="stat-card">

                    <p>Ngừng kinh doanh</p>

                    <h2 class="stat-inactive">
                        <?= $inactiveProducts ?>
                    </h2>

                </div>

                <div class="stat-card">

                    <p>Hết hàng</p>

                    <h2 class="stat-stock">
                        <?= $outOfStock ?>
                    </h2>

                </div>

            </div>

            <?php if ($keyword !== ''): ?>

                <p style="margin-bottom: 15px; color: #64748b;">
                    Thống kê theo kết quả tìm kiếm.
                </p>

            <?php endif; ?>

            <!-- DANH SACH -->

            <div class="panel">

                <div class="panel-header">

                    <h2>Danh sách sản phẩm</h2>

                    <!-- TIM KIEM -->

                    <form method="GET"
                          action="/admin/products"
                          class="search-form">

                        <input
                            type="text"
                            name="q"
                            class="search-input"
                            placeholder="Nhập tên hoặc mã SKU..."
                            value="<?= $e($keyword) ?>"
                        >

                        <button type="submit"
                                class="btn btn-primary">

                            Tìm kiếm

                        </button>

                        <a href="/admin/products"
                           class="btn btn-secondary">

                            Làm mới

                        </a>

                    </form>

                </div>

                <div class="table-wrapper">

                    <table>

                        <thead>

                            <tr>

                                <th>ID</th>

                                <th>Sản phẩm</th>

                                <th>Danh mục</th>

                                <th>Giá bán</th>

                                <th>Tồn kho</th>

                                <th>Bảo hành</th>

                                <th>Trạng thái</th>

                                <th>Thao tác</th>

                            </tr>

                        </thead>

                        <tbody>

                        <?php if (empty($products)): ?>

                            <tr>

                                <td colspan="8" class="empty">

                                    Không tìm thấy sản phẩm.

                                </td>

                            </tr>

                        <?php else: ?>

                            <?php foreach ($products as $product): ?>

                                <?php

                                $id = (int) $product['id'];

                                $stock = (int) $product['stock'];

                                $isActive =
                                    (int) $product['is_active'] === 1;

                                ?>

                                <tr>

                                    <td>
                                        #<?= $id ?>
                                    </td>

                                    <td>

                                        <div class="product-name">

                                            <?= $e($product['name']) ?>

                                        </div>

                                        <div class="sku">

                                            SKU:
                                            <?= $e($product['sku']) ?>

                                        </div>

                                    </td>

                                    <td>

                                        <?= $e(
                                            $product['category_name']
                                            ?? 'Chưa phân loại'
                                        ) ?>

                                    </td>

                                    <td class="price">

                                        <?= number_format(
                                            (float) $product['price'],
                                            0,
                                            ',',
                                            '.'
                                        ) ?> đ

                                    </td>

                                    <td>

                                        <?php if ($stock === 0): ?>

                                            <span class="stock-empty">
                                                Hết hàng
                                            </span>

                                        <?php elseif ($stock <= 5): ?>

                                            <span class="stock-low">

                                                <?= $stock ?>

                                            </span>

                                        <?php else: ?>

                                            <span class="stock-normal">

                                                <?= $stock ?>

                                            </span>

                                        <?php endif; ?>

                                    </td>

                                    <td>

                                        <?= (int)
                                            $product['warranty_months']
                                        ?> tháng

                                    </td>

                                    <td>

                                        <?php if ($isActive): ?>

                                            <span class="status status-active">

                                                Đang kinh doanh

                                            </span>

                                        <?php else: ?>

                                            <span class="status status-inactive">

                                                Ngừng kinh doanh

                                            </span>

                                        <?php endif; ?>

                                    </td>

                                    <td>

                                        <div class="actions">

                                            <!-- CHINH SUA -->

                                            <a
                                                href="/admin/products/<?= $id ?>/edit"
                                                class="btn btn-edit btn-small"
                                            >

                                                Sửa

                                            </a>

                                            <!-- NGUNG KINH DOANH -->

                                            <?php if ($isActive): ?>

                                                <form
                                                    method="POST"
                                                    action="/admin/products/<?= $id ?>/disable"
                                                    onsubmit="return confirm('Bạn có chắc muốn ngừng kinh doanh sản phẩm này không?');"
                                                >

                                                    <input
                                                        type="hidden"
                                                        name="_token"
                                                        value="<?= $e($csrfToken) ?>"
                                                    >

                                                    <button
                                                        type="submit"
                                                        class="btn btn-disable btn-small"
                                                        <?= $csrfToken === '' ? 'disabled' : '' ?>
                                                    >

                                                        Ngừng bán

                                                    </button>

                                                </form>

                                            <?php else: ?>

                                                <button
                                                    type="button"
                                                    class="btn btn-secondary btn-small"
                                                    disabled
                                                >

                                                    Đã ngừng

                                                </button>

                                            <?php endif; ?>

                                        </div>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        <?php endif; ?>

                        </tbody>

                    </table>

                </div>

                <div class="panel-footer">

                    Hiển thị <?= $totalProducts ?> sản phẩm

                </div>

            </div>

        </section>

        <footer class="footer">

            &copy; <?= date('Y') ?>
            CT TECH STORE - Hệ thống quản trị

        </footer>

    </main>

</body>

</html>
