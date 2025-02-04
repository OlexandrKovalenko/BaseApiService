<?php

namespace App\System\Services\User;

use App\System\Entity\User;
use App\System\Exception\InternalServerErrorException;
use App\System\Exception\UserException;
use App\System\Exception\UserNotFoundException;
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
     * getUserById
     *
     * @param int $userId
     * @return User
     * @throws RandomException
     * @throws UserException
     */
    public function getUserById(int $userId): User
    {
        return $this->userRepository->findById($userId);
    }

    /**
     * @throws Exception
     */
    public function createUser(User $user): int
    {
        try {
            $user->beforeSave();
            return $this->userRepository->store($user);
        } catch (Exception $e) {
            throw new Exception("Failed to create user: " . $e->getMessage());
        }
    }

    /**
     * getUserByPhone
     *
     * @param string $userPhone
     * @return User
     * @throws RandomException
     * @throws UserException
     */
    public function getUserByPhone(string $userPhone): User
    {
        return $this->userRepository->findByPhone($userPhone);
    }
}