<?php

namespace App\System\Providers;

use App\System\Facades\UserFacade;
use App\System\Middleware\AuthMiddleware;
use App\System\Middleware\LoggingMiddleware;
use App\System\Repositories\User\UserRepository;
use App\System\Services\Auth\AuthService;
use App\System\Services\User\UserService;
use App\System\Container\Container;

class ServiceProvider
{
    /**
     * @throws \Exception
     */
    public static function initialize(Container $container): void
    {
        // Регістрація репозиторіїв
        self::registerRepositories($container);

        // Регістрація сервісів
        self::registerServices($container);

        // Налаштування фасаду для UserService
        self::configureFacades($container);

        // Регістрація інших компонентів (наприклад, Middleware, Controllers)
        self::registerMiddleware($container);
        self::registerControllers($container);
    }

    // Регістрація репозиторіїв
    private static function registerRepositories(Container $container): void
    {
        $container->bind(UserRepository::class, function () {
            return new UserRepository();
        });
    }

    // Регістрація сервісів
    private static function registerServices(Container $container): void
    {
        $container->bind(UserService::class, function () use ($container) {
            return new UserService($container->make(UserRepository::class));
        });

        $container->bind(AuthService::class, function () use ($container) {
            return new AuthService($container->make(UserRepository::class));
        });
    }

    // Налаштування фасадів

    /**
     * @throws \Exception
     */
    private static function configureFacades(Container $container): void
    {
        $container->bind(UserFacade::class, function () use ($container) {
            $userService = $container->make(UserService::class);
            return new UserFacade($userService);
        });
    }

    // Регістрація Middleware
    private static function registerMiddleware(Container $container): void
    {
        $container->bind(AuthMiddleware::class, function () use ($container) {
            return new AuthMiddleware($container->make(AuthService::class));
        });
        $container->bind(LoggingMiddleware::class, function () {
            return new LoggingMiddleware();
        });
    }

    // Регістрація контролерів
    private static function registerControllers(Container $container): void
    {
        $container->bind('App\\System\\Controllers\\UserController', function () use ($container) {
            return new \App\System\Controllers\UserController($container->make(UserService::class));
        });
    }
}
