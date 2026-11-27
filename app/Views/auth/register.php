<?php $title = 'Đăng ký tài khoản'; ?>

<section class="panel narrow">
    <h1>Đăng ký tài khoản</h1>

    <?php if (!empty($error)): ?>
        <p role="alert"><?= $e($error) ?></p>
    <?php endif; ?>

    <form class="stack" method="post" action="/register">
        <?php require $csrf; ?>

        <label>
            Họ và tên
            <input type="text" name="name" autocomplete="name" maxlength="100" required value="<?= $e($name ?? '') ?>">
        </label>

        <label>
            Email
            <input type="email" name="email" autocomplete="email" maxlength="254" required
                value="<?= $e($email ?? '') ?>">
        </label>

        <label>
            Mật khẩu
            <input type="password" name="password" minlength="8" autocomplete="new-password" required>
        </label>

        <label>
            Nhập lại mật khẩu
            <input type="password" name="password_confirmation" minlength="8" autocomplete="new-password" required>
        </label>

        <button class="button" type="submit">Đăng ký</button>
    </form>

    <p>Đã có tài khoản? <a href="/login">Đăng nhập</a></p>
</section>