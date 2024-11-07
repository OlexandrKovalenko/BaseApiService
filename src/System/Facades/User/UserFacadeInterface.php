<?php

namespace App\System\Facades\User;

use App\System\Entity\User;
use App\System\Http\RequestBundle;
use App\System\Http\ResponseBundle;

interface UserFacadeInterface
{
    public function createUser(RequestBundle $request): array|ResponseBundle;

    public function getUser(RequestBundle $request): User|ResponseBundle;

    public function getUserByPhone(RequestBundle $request): User|ResponseBundle;
}