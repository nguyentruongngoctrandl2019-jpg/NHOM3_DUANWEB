<section class="panel">
    <h1>Đăng nhập</h1>

    <?php if (!empty($success)): ?>
        <p role="status">Tạo tài khoản thành công. Bạn có thể đăng nhập.</p>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <p role="alert"><?= $e($error) ?></p>
    <?php endif; ?>

    <form class="stack" action="/login" method="post">
        <?php require $csrf; ?>

        <label>
            Email
            <input type="email" name="email" maxlength="254" autocomplete="email" required
                value="<?= $e($email ?? '') ?>">
        </label>

        <label>
            Mật khẩu
            <input type="password" name="password" autocomplete="current-password" required>
        </label>

        <button type="submit" class="button">Đăng nhập</button>
    </form>

    <p>Chưa có tài khoản? <a href="/register">Đăng ký</a></p>
</section>