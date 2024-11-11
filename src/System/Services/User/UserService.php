<?php

namespace App\System\Services\User;

use App\System\Entity\User;
use App\System\Exception\InternalServerErrorException;
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
     * @throws Exception
     */
    public function getUserById(int $userId): User
    {
        try {
            $user = $this->userRepository->findById($userId);
            return $user;
        } catch (UserNotFoundException $e) {
            throw $e;
        } catch (Exception $e) {
            throw new InternalServerErrorException("An error occurred while fetching the user.", 500, $e);
        }
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
            return null;
        }

        return $user;
    }
}