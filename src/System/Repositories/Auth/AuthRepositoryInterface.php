<?php

namespace App\System\Repositories\Auth;

use App\System\Entity\User;

interface AuthRepositoryInterface
{
    public function storeRefreshToken(int $userId, $refreshToken, $expiresAt);
    public function getRefreshToken($userId, $refreshToken): ?array;
}