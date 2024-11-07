<?php

namespace App\System\Services\Auth;

use App\System\Entity\User;

interface AuthServiceInterface
{
    public function generateTokens(int $userId): array;
    public function validateToken(string $token): ?int;
}