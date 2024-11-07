<?php

namespace App\System\Providers;

use App\System\Container\Container;
use App\System\Controllers\AuthController;
use App\System\Controllers\UserController;
use App\System\Facades\Auth\AuthFacade;
use App\System\Facades\Auth\AuthFacadeInterface;
use App\System\Facades\User\UserFacade;
use App\System\Facades\User\UserFacadeInterface;
use App\System\Middleware\AuthMiddleware;
use App\System\Middleware\LoggingMiddleware;
use App\System\Middleware\MiddlewareInterface;
use App\System\Repositories\Auth\AuthRepository;
use App\System\Repositories\Auth\AuthRepositoryInterface;
use App\System\Repositories\User\UserRepository;
use App\System\Repositories\User\UserRepositoryInterface;
use App\System\Services\Auth\AuthService;
use App\System\Services\Auth\AuthServiceInterface;
use App\System\Services\User\UserService;
use App\System\Services\User\UserServiceInterface;
use Exception;

class ServiceProvider
{
    /**
     * @throws Exception
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
        $container->bind(UserRepositoryInterface::class, function () {
            return new UserRepository();
        });

        $container->bind(AuthRepositoryInterface::class, function () {
            return new AuthRepository();
        });
    }

    // Регістрація сервісів
    private static function registerServices(Container $container): void
    {
        $container->bind(UserService::class, function () use ($container) {
            return new UserService($container->make(UserRepositoryInterface::class));
        });

        $container->bind(UserServiceInterface::class, function () use ($container) {
            return new UserService($container->make(UserRepositoryInterface::class));
        });

        $container->bind(AuthServiceInterface::class, function () use ($container) {
            return new AuthService(
                $container->make(AuthRepositoryInterface::class),
                $container->make(UserRepositoryInterface::class),
            );
        });
    }

    // Налаштування фасадів

    /**
     * @throws Exception
     */
    private static function configureFacades(Container $container): void
    {
        $container->bind(UserFacadeInterface::class, function ($container) {
            return new UserFacade($container->make(UserServiceInterface::class));
        });

        $container->bind(AuthFacadeInterface::class, function ($container) {
            $authService = $container->make(AuthServiceInterface::class);
            $userService = $container->make(UserServiceInterface::class);
            return new AuthFacade($authService, $userService);
        });
    }

    private static function registerMiddleware(Container $container): void
    {

        $container->bind(AuthMiddleware::class, function () use ($container) {
            return new AuthMiddleware($container->make(AuthServiceInterface::class));
        });

        $container->bind(LoggingMiddleware::class, function () {
            return new LoggingMiddleware();
        });
    }

    private static function registerControllers(Container $container): void
    {
        $container->bind('App\\System\\Controllers\\UserController', function () use ($container) {
            return new UserController(
                $container->make(UserFacadeInterface::class)
            );
        });

        $container->bind('App\\System\\Controllers\\AuthController', function () use ($container) {
            return new AuthController(
                $container->make(AuthFacadeInterface::class)
            );
        });
    }
}
