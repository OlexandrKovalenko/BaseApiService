<?php
namespace App\System\Facades;

use App\System\Core\ResultCodes;
use App\System\Entity\User;
use App\System\Exception\UserNotFoundException;
use App\System\Http\RequestBundle;
use App\System\Http\ResponseBundle;
use App\System\Services\User\UserService;
use App\System\Util\DataFormatter;
use Exception;

class UserFacade extends BaseFacade
{
    private UserService $userService;

    // Конструктор для інжекції залежності через контейнер
    public function __construct(UserService $userService)
    {
        parent::__construct();
        $this->userService = $userService;
    }

    public function createUser(RequestBundle $request): array|ResponseBundle
    {
        try {
            $requiredFields = ['first_name', 'last_name', 'phone', 'email', 'password'];
            $validationResponse = $this->validateRequiredFields($request, $requiredFields);
            if ($validationResponse instanceof ResponseBundle) {
                return $validationResponse;
            }

            $data = [
                'first_name' => $request->getBody()['first_name'],
                'last_name' => $request->getBody()['last_name'],
                'phone' => DataFormatter::formatPhone($request->getBody()['phone']),
                'email' => $request->getBody()['email'],
                'password' => $request->getBody()['password'],
                'note' => $request->getBody()['note'] ?? null
            ];

            $rules = [
                'email' => 'notEmpty|email|min:4|max:100',
                'phone' => 'notEmpty|phone|string|min:12|max:12',
                'password' => 'notEmpty|string|min:8|max:255',
                'first_name' => 'notEmpty|string|min:8|max:255',
                'last_name' => 'notEmpty|string|min:8|max:255',
            ];


            if (!$this->validator->validate($data, $rules)) {
                return new ResponseBundle(400, [
                    'errors' => $this->validator->getErrors(),
                ]);
            }
            $user = new User($data);
            $user = $this->userService->createUser($user);

            return ['status' => 'success', 'user_id' => $user];
        } catch (Exception $e) {
            return new ResponseBundle(500, ['error' => $e->getMessage()], ResultCodes::ERROR_INTERNAL_SERVER);
        }
    }

    public function getUser(RequestBundle $request): User|ResponseBundle
    {
        try {
            $requiredFields = ['id'];
            $validationResponse = $this->validateRequiredFields($request, $requiredFields);
            if ($validationResponse instanceof ResponseBundle) {
                return $validationResponse;
            }

            $userId = (int) $request->getBody()['id'];
            return $this->userService->getUserById($userId);
        } catch (UserNotFoundException $e) {
            // Перехоплюємо помилку і повертаємо відповідь з помилкою
            return new ResponseBundle(404, ['error' => $e->getMessage()], ResultCodes::ERROR_NOT_FOUND);
        } catch (Exception $e) {
            // Інші непередбачувані помилки
            return new ResponseBundle(500, ['error' => $e->getMessage()], ResultCodes::ERROR_INTERNAL_SERVER);
        }
    }
}