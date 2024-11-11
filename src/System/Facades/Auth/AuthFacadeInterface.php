<?php

namespace App\System\Facades\Auth;

use App\System\Entity\User;
use App\System\Http\RequestBundle;
use App\System\Http\ResponseBundle;
use App\System\Services\Auth\AuthServiceInterface;
use App\System\Services\User\UserServiceInterface;

interface AuthFacadeInterface
{
    public function __construct(AuthServiceInterface $authService, UserServiceInterface $userService);

    public function login(RequestBundle $request): array|ResponseBundle;

    public function refreshToken(RequestBundle $request): string|ResponseBundle;
}