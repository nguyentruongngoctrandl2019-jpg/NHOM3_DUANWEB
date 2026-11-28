<?php

declare(strict_types=1);

$csrfToken = \App\Support\Csrf::token();
?>

<input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">