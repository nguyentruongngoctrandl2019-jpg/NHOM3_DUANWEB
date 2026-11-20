<?php
declare(strict_types=1);

require_once __DIR__ . '/../Support/View.php';

use App\Support\View;

View::render('layouts/preview', [
    'pageTitle' => 'CT TECH STORE - Trang chủ'
]);

