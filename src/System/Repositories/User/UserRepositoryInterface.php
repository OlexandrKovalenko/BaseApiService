<?php

namespace App\System\Repositories\User;

use App\System\Entity\User;

interface UserRepositoryInterface
{
    public function store(User $user): int;

    public function update(User $user): User;

    public function findById(int $id): ?User;
}