<?php

namespace App\System\Services\User;

use App\System\Entity\User;
use App\System\Repositories\User\UserRepository;
use App\System\Repositories\User\UserRepositoryInterface;
use App\System\Services\BaseService;
use Exception;
use Random\RandomException;

class UserService extends BaseService implements UserServiceInterface
{

    private UserRepository $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @throws Exception
     */
    public function getUserById(int $userId): ?User
    {
        $user = $this->userRepository->findById($userId);

        if (!$user) {
            throw new Exception("User with ID {$userId} not found.");
        }
        return $user;
    }

    /**
     * @throws Exception
     */
    public function createUser(User $user): int
    {
        try {
            $user->beforeSave();
            $userId = $this->userRepository->store($user);
            return $userId;
        } catch (Exception $e) {
            throw new Exception("Failed to create user: " . $e->getMessage());
        }
    }

    /**
     * @throws RandomException
     * @throws Exception
     */
    public function getUserByPhone(int $userPhone): ?User
    {
        $user = $this->userRepository->findByPhone($userPhone);

        if (!$user) {
            throw new Exception("User with phone number {$userPhone} not found.");
        }
        return $user;
    }
}