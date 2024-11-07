<?php

namespace App\System\Services\Auth;

interface AuthServiceInterface
{
    public function generateTokens(int $userId): array;
    public function validateToken(string $token): ?int;
}