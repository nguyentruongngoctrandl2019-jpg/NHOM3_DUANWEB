<?php

declare(strict_types=1);

$pageTitle = htmlspecialchars(
    (string) ($title ?? 'CT Tech Store'),
    ENT_QUOTES | ENT_SUBSTITUTE,
    'UTF-8'
);

$pageContent = $content ?? throw new \LogicException(
    'Layout main.php cần biến $content từ View::render().'
);
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
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
        <?= $pageContent ?>
    </main>
</body>

</html>