<?php

namespace App\System\Services\User;

use App\System\Entity\User;

interface UserServiceInterface
{
    public function getUserById(int $userId): ?User;

    public function getUserByPhone(string $userPhone): User;

    public function createUser(User $user): int;
}