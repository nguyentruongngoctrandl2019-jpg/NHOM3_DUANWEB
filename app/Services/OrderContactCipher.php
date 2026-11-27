<?php

declare(strict_types=1);

namespace App\Services;

use RuntimeException;

final class OrderContactCipher
{
    private string $key;

    public function __construct(string $hex)
    {
        if (!preg_match('/\A[0-9a-fA-F]{64}\z/', $hex)) {
            throw new RuntimeException('Configure ORDER_CONTACT_KEY_HEX');
        }

        $this->key = hex2bin($hex);
    }

    public static function fromEnvironment(): self
    {
        return new self(getenv('ORDER_CONTACT_KEY_HEX') ?: '');
    }

    public function encrypt(string $value): string
    {
        $nonce = random_bytes(SODIUM_CRYPTO_AEAD_XCHACHA20POLY1305_IETF_NPUBBYTES);

        return "\x01" . $nonce . sodium_crypto_aead_xchacha20poly1305_ietf_encrypt(
            $value,
            '',
            $nonce,
            $this->key
        );
    }

    public function decrypt(string $value): string
    {
        $length = SODIUM_CRYPTO_AEAD_XCHACHA20POLY1305_IETF_NPUBBYTES;

        if (
            strlen($value) < 1 + $length + SODIUM_CRYPTO_AEAD_XCHACHA20POLY1305_IETF_ABYTES
            || $value[0] !== "\x01"
        ) {
            throw new RuntimeException('Unsupported contact format');
        }

        $plain = sodium_crypto_aead_xchacha20poly1305_ietf_decrypt(
            substr($value, 1 + $length),
            '',
            substr($value, 1, $length),
            $this->key
        );

        if ($plain === false) {
            throw new RuntimeException('Contact authentication failed');
        }

        return $plain;
    }
}