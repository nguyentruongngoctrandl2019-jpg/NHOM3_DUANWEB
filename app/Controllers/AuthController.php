<?php

declare(strict_types=1);

namespace app\Controllers;

use app\Models\User;
use app\Support\{Auth, Http, Input, Session};
use PDOException;
use Psr\Http\Message\{ServerRequestInterface, ResponseInterface};

final class AuthController
{
    public function __construct(private User $users)
    {
    }

    public function login(ServerRequestInterface $request, array $params): ResponseInterface
    {
        if (Auth::userId() !== null) {
            return Http::redirect('/products');
        }

        if ($request->getMethod() !== 'POST') {
            $success = $_SESSION['registered'] ?? false;
            unset($_SESSION['registered']);
            return Http::view('auth/login', compact('success'));
        }

        $form = Input::form($request);
        $email = strtolower(Input::text($form, 'email'));
        $password = is_string($form['password'] ?? null) ? $form['password'] : '';
        $user = strlen($email) <= 254 && filter_var($email, FILTER_VALIDATE_EMAIL)
            ? $this->users->findByEmail($email)
            : null;

        if (!$user || strlen($password) > 72 || !password_verify($password, $user['password_hash'])) {
            return Http::view('auth/login', [
                'email' => $email,
                'error' => 'Email hoặc mật khẩu chưa đúng.',
            ], 422);
        }

        Session::signIn((int) $user['id']);
        return Http::redirect('/products');
    }

    public function register(ServerRequestInterface $request, array $params): ResponseInterface
    {
        if (Auth::userId() !== null) {
            return Http::redirect('/products');
        }

        if ($request->getMethod() !== 'POST') {
            return Http::view('auth/register');
        }

        $form = Input::form($request);
        $name = Input::text($form, 'name');
        $email = strtolower(Input::text($form, 'email'));
        $password = is_string($form['password'] ?? null) ? $form['password'] : '';

        $error = match (true) {
            $name === '' || mb_strlen($name) > 100 => 'Họ tên phải có từ 1 đến 100 ký tự.',
            strlen($email) > 254 || !filter_var($email, FILTER_VALIDATE_EMAIL) => 'Email không hợp lệ.',
            strlen($password) < 8 || strlen($password) > 72 || str_contains($password, "\0")
            => 'Mật khẩu cần từ 8 đến 72 byte.',
            $password !== ($form['password_confirmation'] ?? null) => 'Mật khẩu xác nhận chưa khớp.',
            default => null,
        };

        if ($error === null) {
            try {
                $this->users->register($name, $email, $password);
                $_SESSION['registered'] = true;
                return Http::redirect('/login');
            } catch (PDOException $exception) {
                if (($exception->errorInfo[1] ?? null) !== 1062) {
                    throw $exception;
                }

                $error = 'Email này đã được đăng ký.';
            }
        }

        return Http::view('auth/register', compact('name', 'email', 'error'), 422);
    }

    public function logout(ServerRequestInterface $request, array $params): ResponseInterface
    {
        Session::signOut();
        return Http::redirect('/login');
    }
}