<?php

/** @var \Closure(mixed): string $e */
/** @var string $csrf */

$title = 'Đăng nhập';
?>

<section class="panel narrow">
    <h1>Đăng nhập</h1>

    <?php if (!empty($success)): ?>
        <p class="notice" role="status">
            Tạo tài khoản thành công. Bạn có thể đăng nhập.
        </p>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <p class="alert" role="alert"><?= $e($error) ?></p>
    <?php endif; ?>

    <form class="stack" action="/login" method="post">
        <?php require $csrf; ?>

        <label for="login-email">Email</label>
        <input id="login-email" type="email" name="email" maxlength="254" autocomplete="email"
            value="<?= $e($email ?? '') ?>" required>

        <label for="login-password">Mật khẩu</label>
        <input id="login-password" type="password" name="password" autocomplete="current-password" required>

        <button class="button" type="submit">Đăng nhập</button>
    </form>

    <p>Chưa có tài khoản? <a href="/register">Đăng ký</a></p>
</section>