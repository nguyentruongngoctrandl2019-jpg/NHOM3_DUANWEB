<h1>Lỗi <?= (int) ($status ?? 500) ?></h1>

<p class="panel" role="alert">
    <?= htmlspecialchars(
        (string) ($message ?? 'Hệ thống đang gặp lỗi. Vui lòng thử lại sau.'),
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    ) ?>
</p>