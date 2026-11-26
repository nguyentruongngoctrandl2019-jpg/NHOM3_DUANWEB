
<?php

declare(strict_types=1);

// Du lieu nhan tu Controller

$product = $product ?? [];
$categories = $categories ?? [];
$old = $old ?? [];
$error = $error ?? '';
$csrfToken = $csrfToken ?? '';

$id = (int) ($product['id'] ?? 0);
$stock = (int) ($product['stock'] ?? 0);

$canSubmit = $id > 0
    && is_string($csrfToken)
    && $csrfToken !== '';

// Bao mat XSS

$e = static function ($value): string {

    return htmlspecialchars(
        is_scalar($value) ? (string) $value : '',
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
};

// Uu tien du lieu vua nhap neu form bi loi

$value = static function (
    string $key,
    string $default = ''
) use ($old, $product): string {

    $data = $old[$key]
        ?? $product[$key]
        ?? $default;

    return is_scalar($data)
        ? (string) $data
        : $default;
};

?>

<!DOCTYPE html>
<html lang="vi">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Chỉnh sửa sản phẩm - CT TECH STORE</title>

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

        .breadcrumb a {
            color: #2563eb;
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
            margin-bottom: 10px;
        }

        .page-heading p {
            color: #64748b;
        }

        /* PANEL */

        .panel {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 25px;
        }

        .panel-header {
            padding: 22px;
            border-bottom: 1px solid #e2e8f0;
        }

        .panel-header h2 {
            font-size: 19px;
            margin-bottom: 5px;
        }

        .panel-header p {
            color: #64748b;
            font-size: 13px;
        }

        .panel-body {
            padding: 25px;
        }

        /* FORM */

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 24px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 9px;
        }

        .full-width {
            grid-column: 1 / -1;
        }

        label {
            font-weight: bold;
            color: #334155;
        }

        .required {
            color: #dc2626;
        }

        .form-control {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            background: white;
            color: #1e293b;
            font-size: 14px;
            font-family: inherit;
            outline: none;
            transition: 0.2s;
        }

        .form-control:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px #dbeafe;
        }

        textarea.form-control {
            min-height: 150px;
            resize: vertical;
        }

        .help-text {
            color: #64748b;
            font-size: 12px;
            line-height: 1.6;
        }

        /* RADIO */

        .radio-group {
            display: flex;
            gap: 25px;
            flex-wrap: wrap;
            padding: 10px 0;
        }

        .radio-item {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            font-weight: normal;
        }

        input[type="radio"] {
            width: 17px;
            height: 17px;
            accent-color: #2563eb;
        }

        /* BUTTON */

        .btn {
            display: inline-block;
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-family: inherit;
            transition: 0.2s;
        }

        .btn-primary {
            background: #2563eb;
            color: white;
        }

        .btn-primary:hover {
            background: #1d4ed8;
        }

        .btn-secondary {
            background: #e2e8f0;
            color: #334155;
        }

        .btn-secondary:hover {
            background: #cbd5e1;
        }

        .btn-danger {
            background: #dc2626;
            color: white;
        }

        .btn-danger:hover {
            background: #b91c1c;
        }

        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .panel-footer {
            padding: 20px 25px;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            flex-wrap: wrap;
        }

        /* TRANG THAI */

        .status {
            display: inline-block;
            padding: 8px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .status-active {
            background: #dcfce7;
            color: #15803d;
        }

        .status-inactive {
            background: #fee2e2;
            color: #dc2626;
        }

        /* ALERT */

        .alert {
            padding: 15px 18px;
            border-radius: 6px;
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #b91c1c;
        }

        .alert-warning {
            background: #fffbeb;
            border: 1px solid #fde68a;
            color: #92400e;
        }

        /* TON KHO */

        .stock-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 22px;
        }

        .stock-box p {
            color: #64748b;
            margin-bottom: 10px;
        }

        .stock-number {
            font-size: 35px;
            font-weight: bold;
            color: #16a34a;
        }

        .stock-empty {
            color: #dc2626;
        }

        .stock-unit {
            color: #64748b;
            margin-left: 5px;
        }

        .stock-preview {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 6px;
            padding: 15px;
            color: #1d4ed8;
            margin-top: 15px;
        }

        .danger-zone {
            border-color: #fecaca;
        }

        .danger-zone .panel-header h2 {
            color: #dc2626;
        }

        .danger-zone p {
            line-height: 1.7;
        }

        .footer {
            padding: 25px;
            text-align: center;
            color: #64748b;
            font-size: 13px;
        }

        /* RESPONSIVE */

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
                flex-direction: column;
                align-items: flex-start;
            }

            .form-grid {
                grid-template-columns: 1fr;
                gap: 18px;
            }

            .panel-body {
                padding: 18px;
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

                Quản trị /

                <a href="/admin/products">
                    Quản lý sản phẩm
                </a>

                / Chỉnh sửa

            </div>

            <!-- TIEU DE -->

            <div class="page-heading">

                <div>

                    <h1>Chỉnh sửa sản phẩm</h1>

                    <p>

                        Sản phẩm #<?= $id ?>

                        -

                        <?= $e($product['sku'] ?? '') ?>

                    </p>

                </div>

                <?php if ((int) ($product['is_active'] ?? 0) === 1): ?>

                    <span class="status status-active">

                        Đang kinh doanh

                    </span>

                <?php else: ?>

                    <span class="status status-inactive">

                        Ngừng kinh doanh

                    </span>

                <?php endif; ?>

            </div>

            <!-- THONG BAO LOI -->

            <?php if ($error !== ''): ?>

                <div class="alert alert-error" role="alert">

                    <strong>Không thể cập nhật sản phẩm!</strong>

                    <p><?= $e($error) ?></p>

                </div>

            <?php endif; ?>

            <?php if (!$canSubmit): ?>

                <div class="alert alert-warning">

                    Phiên bảo mật hoặc ID sản phẩm
                    không hợp lệ.

                </div>

            <?php endif; ?>

            <?php if (empty($categories)): ?>

                <div class="alert alert-warning">

                    Chưa có danh mục sản phẩm hợp lệ.

                </div>

            <?php endif; ?>

            <!-- FORM CAP NHAT SAN PHAM -->

            <form
                method="POST"
                action="/admin/products/<?= $id ?>/edit"
            >

                <input
                    type="hidden"
                    name="_token"
                    value="<?= $e($csrfToken) ?>"
                >

                <div class="panel">

                    <div class="panel-header">

                        <h2>Thông tin sản phẩm</h2>

                        <p>
                            Cập nhật thông tin sản phẩm
                            đang được lưu trong hệ thống.
                        </p>

                    </div>

                    <div class="panel-body">

                        <div class="form-grid">

                            <!-- SKU -->

                            <div class="form-group">

                                <label for="sku">

                                    Mã SKU

                                    <span class="required">*</span>

                                </label>

                                <input
                                    type="text"
                                    id="sku"
                                    name="sku"
                                    class="form-control"
                                    value="<?= $e($value('sku')) ?>"
                                    maxlength="60"
                                    pattern="[A-Za-z0-9_\-]{1,60}"
                                    required
                                >

                                <span class="help-text">

                                    Mã SKU không được trùng
                                    với sản phẩm khác.

                                </span>

                            </div>

                            <!-- TEN SAN PHAM -->

                            <div class="form-group">

                                <label for="name">

                                    Tên sản phẩm

                                    <span class="required">*</span>

                                </label>

                                <input
                                    type="text"
                                    id="name"
                                    name="name"
                                    class="form-control"
                                    value="<?= $e($value('name')) ?>"
                                    maxlength="180"
                                    required
                                >

                            </div>

                            <!-- DANH MUC -->

                            <div class="form-group">

                                <label for="category_id">

                                    Danh mục

                                    <span class="required">*</span>

                                </label>

                                <select
                                    id="category_id"
                                    name="category_id"
                                    class="form-control"
                                    required
                                >

                                    <option value="">
                                        -- Chọn danh mục --
                                    </option>

                                    <?php foreach ($categories as $category): ?>

                                        <option
                                            value="<?= (int) $category['id'] ?>"
                                            <?= $value('category_id') ===
                                                (string) $category['id']
                                                ? 'selected'
                                                : '' ?>
                                        >

                                            <?= $e($category['name']) ?>

                                        </option>

                                    <?php endforeach; ?>

                                </select>

                            </div>

                            <!-- GIA BAN -->

                            <div class="form-group">

                                <label for="price">

                                    Giá bán (VNĐ)

                                    <span class="required">*</span>

                                </label>

                                <input
                                    type="number"
                                    id="price"
                                    name="price"
                                    class="form-control"
                                    value="<?= $e($value('price')) ?>"
                                    min="0"
                                    max="9999999999999.99"
                                    step="0.01"
                                    required
                                >

                            </div>

                            <!-- BAO HANH -->

                            <div class="form-group">

                                <label for="warranty_months">

                                    Bảo hành (tháng)

                                </label>

                                <input
                                    type="number"
                                    id="warranty_months"
                                    name="warranty_months"
                                    class="form-control"
                                    value="<?= $e(
                                        $value('warranty_months', '0')
                                    ) ?>"
                                    min="0"
                                    max="65535"
                                    step="1"
                                    required
                                >

                                <span class="help-text">

                                    Nhập 0 nếu không có bảo hành.

                                </span>

                            </div>

                            <!-- SLUG -->

                            <div class="form-group">

                                <label for="slug">

                                    Slug sản phẩm

                                </label>

                                <input
                                    type="text"
                                    id="slug"
                                    class="form-control"
                                    value="<?= $e($product['slug'] ?? '') ?>"
                                    readonly
                                >

                                <span class="help-text">

                                    Slug được giữ nguyên để
                                    không thay đổi đường dẫn sản phẩm.

                                </span>

                            </div>

                            <!-- MO TA -->

                            <div class="form-group full-width">

                                <label for="description">

                                    Mô tả sản phẩm

                                </label>

                                <textarea
                                    id="description"
                                    name="description"
                                    class="form-control"
                                ><?= $e($value('description')) ?></textarea>

                            </div>

                            <!-- TRANG THAI -->

                            <div class="form-group full-width">

                                <label>

                                    Trạng thái kinh doanh

                                </label>

                                <div class="radio-group">

                                    <label class="radio-item">

                                        <input
                                            type="radio"
                                            name="is_active"
                                            value="1"
                                            <?= $value('is_active', '0') === '1'
                                                ? 'checked'
                                                : '' ?>
                                            required
                                        >

                                        Đang kinh doanh

                                    </label>

                                    <label class="radio-item">

                                        <input
                                            type="radio"
                                            name="is_active"
                                            value="0"
                                            <?= $value('is_active', '0') === '0'
                                                ? 'checked'
                                                : '' ?>
                                        >

                                        Ngừng kinh doanh

                                    </label>

                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="panel-footer">

                        <a
                            href="/admin/products"
                            class="btn btn-secondary"
                        >

                            Quay lại

                        </a>

                        <button
                            type="submit"
                            class="btn btn-primary"
                            <?= !$canSubmit || empty($categories)
                                ? 'disabled'
                                : '' ?>
                        >

                            Lưu thay đổi

                        </button>

                    </div>

                </div>

            </form>

            <!-- FORM DIEU CHINH TON KHO -->

            <form
                method="POST"
                action="/admin/products/<?= $id ?>/stock"
                id="stock-form"
            >

                <input
                    type="hidden"
                    name="_token"
                    value="<?= $e($csrfToken) ?>"
                >

                <div class="panel">

                    <div class="panel-header">

                        <h2>Điều chỉnh tồn kho</h2>

                        <p>
                            Tăng hoặc giảm số lượng sản phẩm
                            trong kho hàng.
                        </p>

                    </div>

                    <div class="panel-body">

                        <div class="stock-box">

                            <p>Số lượng tồn kho hiện tại</p>

                            <span class="stock-number
                                <?= $stock === 0 ? 'stock-empty' : '' ?>">

                                <?= $stock ?>

                            </span>

                            <span class="stock-unit">

                                sản phẩm

                            </span>

                        </div>

                        <div class="form-group">

                            <label for="quantity_change">

                                Số lượng tăng hoặc giảm

                                <span class="required">*</span>

                            </label>

                            <input
                                type="number"
                                id="quantity_change"
                                name="quantity_change"
                                class="form-control"
                                placeholder="VD: 10 hoặc -5"
                                min="-2147483648"
                                max="2147483647"
                                step="1"
                                required
                            >

                            <span class="help-text">

                                Nhập số dương để tăng tồn kho.
                                Nhập số âm để giảm tồn kho.
                                Không được nhập 0.

                            </span>

                        </div>

                        <div
                            class="stock-preview"
                            id="stock-preview"
                            aria-live="polite"
                        >

                            Tồn kho dự kiến:

                            <strong id="new-stock">

                                <?= $stock ?>

                            </strong>

                            sản phẩm

                        </div>

                        <p class="help-text" style="margin-top: 12px;">

                            Số lượng dự kiến có thể thay đổi
                            nếu phát sinh giao dịch đồng thời.
                            Hệ thống sẽ kiểm tra lại tồn kho
                            trước khi cập nhật.

                        </p>

                    </div>

                    <div class="panel-footer">

                        <button
                            type="submit"
                            class="btn btn-primary"
                            <?= !$canSubmit ? 'disabled' : '' ?>
                        >

                            Cập nhật tồn kho

                        </button>

                    </div>

                </div>

            </form>

            <!-- NGUNG KINH DOANH -->

            <?php if ((int) ($product['is_active'] ?? 0) === 1): ?>

                <div class="panel danger-zone">

                    <div class="panel-header">

                        <h2>Ngừng kinh doanh sản phẩm</h2>

                    </div>

                    <div class="panel-body">

                        <p>

                            Sản phẩm sẽ không còn xuất hiện
                            trong danh sách sản phẩm dành cho
                            khách hàng.

                            Dữ liệu sản phẩm, tồn kho và
                            lịch sử đơn hàng vẫn được giữ lại.

                        </p>

                    </div>

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

                        <div class="panel-footer">

                            <button
                                type="submit"
                                class="btn btn-danger"
                                <?= !$canSubmit ? 'disabled' : '' ?>
                            >

                                Ngừng kinh doanh

                            </button>

                        </div>

                    </form>

                </div>

            <?php endif; ?>

        </section>

        <footer class="footer">

            &copy; <?= date('Y') ?>
            CT TECH STORE - Hệ thống quản trị

        </footer>

    </main>

    <!-- JAVASCRIPT TON KHO -->

    <script>

        const currentStock = <?= $stock ?>;

        const quantityInput =
            document.getElementById('quantity_change');

        const stockPreview =
            document.getElementById('new-stock');

        const previewBox =
            document.getElementById('stock-preview');

        const stockForm =
            document.getElementById('stock-form');

        function calculateStock() {

            const raw = quantityInput.value.trim();

            if (raw === '') {

                stockPreview.textContent = currentStock;

                previewBox.style.color = '#1d4ed8';

                return false;
            }

            const change = Number(raw);

            const newStock = currentStock + change;

            if (
                !Number.isInteger(change)
                || change === 0
                || change < -2147483648
                || change > 2147483647
                || newStock < 0
                || newStock > 2147483647
            ) {

                stockPreview.textContent = 'Không hợp lệ';

                previewBox.style.color = '#dc2626';

                return false;
            }

            stockPreview.textContent =
                newStock.toLocaleString('vi-VN');

            previewBox.style.color = '#1d4ed8';

            return true;
        }

        quantityInput.addEventListener(
            'input',
            calculateStock
        );

        stockForm.addEventListener(
            'submit',
            function (event) {

                if (!calculateStock()) {

                    event.preventDefault();

                    alert(
                        'Số lượng điều chỉnh không hợp lệ!'
                    );

                    return;
                }

                const confirmed = confirm(
                    'Bạn có chắc muốn cập nhật tồn kho?'
                );

                if (!confirmed) {
                    event.preventDefault();
                }
            }
        );

    </script>

</body>

</html>
