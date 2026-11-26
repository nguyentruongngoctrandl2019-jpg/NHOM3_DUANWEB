
<?php

declare(strict_types=1);

// Du lieu nhan tu Controller

$categories = $categories ?? [];
$old = $old ?? [];
$error = $error ?? '';
$csrfToken = $csrfToken ?? '';

// Bao mat XSS

$e = static function ($value): string {

    return htmlspecialchars(
        is_scalar($value) ? (string) $value : '',
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
};

// Lay lai du lieu khi form bi loi

$value = static function (
    string $key,
    string $default = ''
) use ($old): string {

    $data = $old[$key] ?? $default;

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

    <title>Thêm sản phẩm - CT TECH STORE</title>

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
            margin-bottom: 25px;
        }

        .page-heading h1 {
            font-size: 27px;
            margin-bottom: 10px;
        }

        .page-heading p {
            color: #64748b;
        }

        /* FORM */

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
        }

        .panel-body {
            padding: 25px;
        }

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

        /* BUTTON */

        .panel-footer {
            padding: 20px 25px;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }

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

        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
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

            <!-- BREADCRUMB -->

            <div class="breadcrumb">

                Quản trị /

                <a href="/admin/products">
                    Quản lý sản phẩm
                </a>

                / Thêm mới

            </div>

            <div class="page-heading">

                <h1>Thêm sản phẩm mới</h1>

                <p>
                    Nhập thông tin sản phẩm
                    cần thêm vào CT TECH STORE.
                </p>

            </div>

            <!-- THONG BAO LOI -->

            <?php if ($error !== ''): ?>

                <div class="alert alert-error" role="alert">

                    <strong>Không thể lưu sản phẩm!</strong>

                    <p><?= $e($error) ?></p>

                </div>

            <?php endif; ?>

            <?php if (empty($categories)): ?>

                <div class="alert alert-warning">

                    Chưa có danh mục sản phẩm.
                    Vui lòng tạo danh mục trước.

                </div>

            <?php endif; ?>

            <?php if (empty($csrfToken)): ?>

                <div class="alert alert-warning">

                    Phiên bảo mật chưa được khởi tạo.
                    Không thể gửi biểu mẫu.

                </div>

            <?php endif; ?>

            <!-- FORM THEM SAN PHAM -->

            <form
                method="POST"
                action="/admin/products/create"
            >

                <input
                    type="hidden"
                    name="_token"
                    value="<?= $e($csrfToken) ?>"
                >

                <div class="panel">

                    <div class="panel-header">

                        <h2>Thông tin sản phẩm</h2>

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
                                    placeholder="VD: TV-SAM-001"
                                    value="<?= $e($value('sku')) ?>"
                                    maxlength="60"
                                    pattern="[A-Za-z0-9_\-]{1,60}"
                                    required
                                >

                                <span class="help-text">

                                    Mã SKU không được trùng lặp.
                                    Chỉ sử dụng chữ, số,
                                    dấu gạch ngang và gạch dưới.

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
                                    placeholder="Nhập tên sản phẩm"
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
                                    placeholder="VD: 12990000"
                                    value="<?= $e($value('price')) ?>"
                                    min="0"
                                    max="9999999999999.99"
                                    step="0.01"
                                    required
                                >

                                <span class="help-text">

                                    Nhập giá không có dấu
                                    phân cách hàng nghìn.

                                </span>

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
                                        $value('warranty_months', '12')
                                    ) ?>"
                                    min="0"
                                    max="65535"
                                    step="1"
                                    required
                                >

                                <span class="help-text">

                                    Nhập 0 nếu sản phẩm
                                    không có bảo hành.

                                </span>

                            </div>

                            <!-- TON KHO -->

                            <div class="form-group">

                                <label for="stock">

                                    Tồn kho ban đầu

                                </label>

                                <input
                                    type="number"
                                    id="stock"
                                    name="stock"
                                    class="form-control"
                                    value="<?= $e(
                                        $value('stock', '0')
                                    ) ?>"
                                    min="0"
                                    max="2147483647"
                                    step="1"
                                    required
                                >

                                <span class="help-text">

                                    Số lượng sản phẩm
                                    được nhập kho ban đầu.

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
                                    placeholder="Nhập thông tin, tính năng và đặc điểm của sản phẩm..."
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
                                            <?= $value('is_active', '1') === '1'
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
                                            <?= $value('is_active', '1') === '0'
                                                ? 'checked'
                                                : '' ?>
                                        >

                                        Ngừng kinh doanh

                                    </label>

                                </div>

                                <span class="help-text">

                                    Sản phẩm đang kinh doanh
                                    có thể xuất hiện trên website.

                                </span>

                            </div>

                        </div>

                    </div>

                    <!-- BUTTON -->

                    <div class="panel-footer">

                        <a
                            href="/admin/products"
                            class="btn btn-secondary"
                        >

                            Hủy bỏ

                        </a>

                        <button
                            type="submit"
                            class="btn btn-primary"
                            <?= empty($categories) || empty($csrfToken)
                                ? 'disabled'
                                : '' ?>
                        >

                            Lưu sản phẩm

                        </button>

                    </div>

                </div>

            </form>

        </section>

        <footer class="footer">

            &copy; <?= date('Y') ?>
            CT TECH STORE - Hệ thống quản trị

        </footer>

    </main>

</body>

</html>

