<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Cart;
use App\Services\{CartService, CheckoutService};
use App\Support\{Auth, Http, Input};
use DomainException;
use InvalidArgumentException;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};

final class CheckoutController
{
    public function __construct(private Cart $cart, private CheckoutService $service)
    {
    }

    public function index(ServerRequestInterface $request, array $params): ResponseInterface
    {
        $bytes = random_bytes(16);
        $bytes[6] = chr((ord($bytes[6]) & 0x0f) | 0x40);
        $bytes[8] = chr((ord($bytes[8]) & 0x3f) | 0x80);
        $token = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($bytes), 4));

        $_SESSION['checkout_tokens'][$token] = true;
        $_SESSION['checkout_tokens'] = array_slice($_SESSION['checkout_tokens'], -32, 32, true);

        return $this->page($token);
    }

    public function place(ServerRequestInterface $request, array $params): ResponseInterface
    {
        $form = Input::form($request);
        $token = Input::text($form, 'checkout_token');

        if (!isset($_SESSION['checkout_tokens'][$token])) {
            return Http::error(403, 'Mã xác nhận đặt hàng hết hạn. Hãy mở lại trang thanh toán.');
        }

        $contact = [];

        foreach (['recipient_name', 'phone', 'address'] as $field) {
            $contact[$field] = Input::text($form, $field);
        }

        $invalid = $contact['recipient_name'] === ''
            || mb_strlen($contact['recipient_name']) > 100
            || !preg_match('/\A(?:0[0-9]{9}|\+84[0-9]{9})\z/', $contact['phone'])
            || mb_strlen($contact['address']) < 8
            || mb_strlen($contact['address']) > 255;

        if ($invalid) {
            return $this->page(
                $token,
                $contact,
                'Kiểm tra họ tên, số điện thoại Việt Nam và địa chỉ từ 8 đến 255 ký tự.'
            );
        }

        try {
            $publicId = $this->service->place(Auth::userId(), $token, $contact);
            return Http::redirect('/orders/' . $publicId);
        } catch (DomainException | InvalidArgumentException $error) {
            return $this->page($token, $contact, $error->getMessage(), 409);
        }
    }

    private function page(
        string $token,
        array $contact = [],
        ?string $error = null,
        int $status = 422
    ): ResponseInterface {
        $items = $this->cart->activeItems(Auth::userId());

        return Http::view(
            'checkout/index',
            compact('token', 'contact', 'items', 'error') + CartService::summary($items),
            $error ? $status : 200
        );
    }
}