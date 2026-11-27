<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $e($title ?? 'CT Tech Store') ?></title>
    <link rel="stylesheet" href="/assets/app.css">
</head>
<body>
    <header class="wrap">
        <a href="/">CT Tech Store</a>
        <nav aria-label="Điều hướng chính">
            <a href="/products">Sản phẩm</a>
            <a href="/cart">Giỏ hàng</a>
        </nav>
    </header>

    <main id="main" class="wrap grow site-main">
        <?= $content ?>
    </main>
</body>
</html>