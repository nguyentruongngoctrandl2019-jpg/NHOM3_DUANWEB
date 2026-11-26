<?php

$total = $total ?? null;
$error = $error ?? null;

if (
    !isset($items, $contact, $token, $canCheckout, $e, $money, $csrf)
    || !is_array($items)
    || !is_array($contact)
    || !is_string($token)
    || !is_bool($canCheckout)
    || !is_callable($e)
    || !is_callable($money)
    || !is_string($csrf)
    || !is_file($csrf)
) {
    http_response_code(500);
    echo '<p>Chưa tải được thông tin thanh toán. Vui lòng mở lại từ giỏ hàng.</p>';
    return;
}
?>