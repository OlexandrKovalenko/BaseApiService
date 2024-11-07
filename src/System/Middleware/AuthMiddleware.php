<?php

namespace App\System\Middleware;

use App\System\Services\Auth\AuthService;
use App\System\Http\RequestBundle;
use App\System\Http\ResponseBundle;

class AuthMiddleware implements MiddlewareInterface
{
    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function handle(RequestBundle $request, ResponseBundle $response, callable $next)
    {
        $token = $request->getHeader('Authorization');

        if (!$token || !$userId = $this->authService->validateToken($token)) {
            // Повертаємо відповідь про помилку доступу з кодом 401
            return new ResponseBundle(401, ['error' => 'Unauthorized']);
        }

        // Додаємо ідентифікатор користувача в атрибути запиту для подальшої обробки
        $request->setAttribute('user_id', $userId);

        $request->setAttribute('user_id', 1);

        // Передаємо запит наступному елементу в ланцюзі обробки
        return $next($request, $response);
    }
}
