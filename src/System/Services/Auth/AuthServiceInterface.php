<?php

namespace App\System\Services\Auth;

use App\System\Entity\User;
use App\System\Http\ResponseBundle;

interface AuthServiceInterface
{
    public function generateTokens(int $userId): array;
    public function generateAccessToken(int $userId): string;
    public function validateToken(string $token): ?int;
}