
<?php
use App\Support\View;

$pageTitle = $pageTitle ?? 'CT TECH STORE';
$content = $content ?? '';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title><?= View::e($pageTitle) ?></title>

<link rel="stylesheet" href="/assets/app.css?v=3">
</head>

<body class="bg-[#F9FCE7] text-gray-900">

    <?php require __DIR__ . '/header.php'; ?>

    <main class="min-h-screen">
        <?= $content ?>
    </main>

    <?php require __DIR__ . '/footer.php'; ?>

</body>
</html>
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
