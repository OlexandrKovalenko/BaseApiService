<?php
namespace App\System\Facades\User;

use App\System\Core\ResultCodes;
use App\System\Entity\User;
use App\System\Exception\InternalServerErrorException;
use App\System\Exception\UserException;
use App\System\Exception\UserNotFoundException;
use App\System\Facades\BaseFacade;
use App\System\Http\RequestBundle;
use App\System\Http\ResponseBundle;
use App\System\Services\User\UserService;
use App\System\Services\User\UserServiceInterface;
use App\System\Util\DataFormatter;
use App\System\Util\GuidHelper;
use Exception;
use Random\RandomException;

class UserFacade extends BaseFacade implements UserFacadeInterface
{
    private UserService $userService;

    // Конструктор для інжекції залежності через контейнер
    public function __construct(UserServiceInterface  $userService)
    {
        parent::__construct();
        $this->userService = $userService;
    }

    /**
     * @throws RandomException
     */
    public function createUser(RequestBundle $request): array|ResponseBundle
    {
        $guid = GuidHelper::createLocalSessionId();
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
                'first_name' => 'notEmpty|string|min:2|max:255',
                'last_name' => 'notEmpty|string|min:2|max:255',
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
            $this->logError($guid, (string)json_encode($e->getMessage()), [
                'tags' => ['user', 'createUser', 'response'],
                'result' => ResultCodes::ERROR_INTERNAL_SERVER,
            ]);
            return new ResponseBundle(500, ['error' => $e->getMessage()], ResultCodes::ERROR_INTERNAL_SERVER);
        }
    }

    /**
     * getUser
     *
     * @param RequestBundle $request
     * @return User|ResponseBundle
     * @throws RandomException
     */
    public function getUser(RequestBundle $request): User|ResponseBundle
    {
        $guid = GuidHelper::createLocalSessionId();

        $requiredFields = ['id'];
        $validationResponse = $this->validateRequiredFields($request, $requiredFields);

        if ($validationResponse instanceof ResponseBundle) {
            $this->logError($guid, (string)json_encode($validationResponse->getBody()), [
                'tags' => ['user', 'validation'],
                'result' => ResultCodes::ERROR_BAD_REQUEST,
            ]);
            return $validationResponse;
        }

        try {
            $userId = (int) $request->getBody()['id'];
            return $this->userService->getUserById($userId);
        } catch (UserException $e) {
            $this->logError($guid, json_encode($e->getMessage()), [
                'tags' => ['user', 'getUser', 'facade'],
                'result' => $e->getCode(),
            ]);
            return new ResponseBundle($e->getCode(), ['error' => $e->getMessage()], $e->getCode());
        }
    }

    /**
     * @throws RandomException
     */
    public function getUserByPhone(RequestBundle $request): User|ResponseBundle
    {
        $guid = GuidHelper::createLocalSessionId();

        try {
            $requiredFields = ['phone'];
            $validationResponse = $this->validateRequiredFields($request, $requiredFields);

            if ($validationResponse instanceof ResponseBundle) {
                $this->logError($guid, (string)json_encode($validationResponse->getBody()), [
                    'tags' => ['user', 'validation'],
                    'result' => ResultCodes::ERROR_BAD_REQUEST,
                ]);
                return $validationResponse;
            }

            $userPhone = (string) $request->getBody()['phone'];
            return $this->userService->getUserByPhone($userPhone);
        } catch (UserException $e) {
            $this->logError($guid, json_encode($e->getMessage()), [
                'tags' => ['user', 'getUser', 'facade'],
                'result' => $e->getCode(),
            ]);
            return new ResponseBundle($e->getCode(), ['error' => $e->getMessage()], $e->getCode());
        }
    }
}
