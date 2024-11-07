<?php

use App\System\Container\Container;
use App\System\Services\Auth\AuthService;
use App\System\Repositories\User\UserRepository;
use App\System\Middleware\AuthMiddleware;

$container = new Container();

// Реєстрація UserRepository
$container->bind(UserRepository::class, function ($c) {
    return new UserRepository(); // Додайте конфігурацію або залежності, якщо потрібно
});

// Реєстрація AuthService з залежністю UserRepository
$container->bind(AuthService::class, function ($c) {
    return new AuthService($c->make(UserRepository::class));
});

// Реєстрація AuthMiddleware з залежністю AuthService
$container->bind(AuthMiddleware::class, function ($c) {
    return new AuthMiddleware($c->make(AuthService::class));
});

