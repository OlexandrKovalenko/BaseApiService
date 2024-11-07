<?php

namespace App\System\Facades\Auth;

use App\System\Core\ResultCodes;
use App\System\Entity\User;
use App\System\Facades\Auth\AuthFacadeInterface;
use App\System\Facades\BaseFacade;
use App\System\Http\RequestBundle;
use App\System\Http\ResponseBundle;
use App\System\Repositories\User\UserRepositoryInterface;
use App\System\Services\Auth\AuthServiceInterface;
use App\System\Services\User\UserServiceInterface;
use App\System\Util\DataFormatter;
use App\System\Util\GuidHelper;
use Exception;

class AuthFacade extends BaseFacade implements AuthFacadeInterface
{

    private AuthServiceInterface $authService;
    private UserServiceInterface $userService;

    public function __construct(AuthServiceInterface $authService, UserServiceInterface $userService)
    {
        parent::__construct();
        $this->authService = $authService;
        $this->userService = $userService;
    }

    /**
     * @throws Exception
     */
    public function login(RequestBundle $request): array|ResponseBundle
    {
        $guid = GuidHelper::createLocalSessionId();

        try {
            $requiredFields = ['phone', 'password'];
            $validationResponse = $this->validateRequiredFields($request, $requiredFields);
            if ($validationResponse instanceof ResponseBundle) {
                return $validationResponse;
            }

            $userPhone = DataFormatter::formatPhone($request->getBody()['phone']);
            $password = $request->getBody()['password'];

            $rules = [
                'phone' => 'notEmpty|phone|string|min:12|max:12',
                'password' => 'notEmpty|string|min:8|max:255',
            ];
            if (!$this->validator->validate(['phone' => $userPhone, 'password' => $password], $rules)) {
                var_dump($this->validator->getErrors());
                return new ResponseBundle(400, ['error' => $this->validator->getErrors()], ResultCodes::ERROR_INTERNAL_SERVER);
            }

            $user = $this->userService->getUserByPhone($userPhone);

            //todo if (!$user || !password_verify($password, $user->getPassword()))
            if (!$user || $password != $user->getPassword()) {
                throw new Exception('Invalid phone or password.');
            }

            return $this->authService->generateTokens($user->getId());
        } catch (Exception $e) {

            $this->logError($guid, (string)json_encode($e->getMessage()), [
                'tags' => ['user', 'getUser', 'response'],  // можна передавати тільки специфічні параметри
                'result' => ResultCodes::ERROR_INTERNAL_SERVER,
            ]);
            return new ResponseBundle(500, ['error' => $e->getMessage()], ResultCodes::ERROR_INTERNAL_SERVER);
        }
    }

    /**
     * @throws Exception
     */
    public function refreshToken(int $userId, string $refreshToken): array
    {
        $storedToken = $this->authService->validateRefreshToken($userId, $refreshToken);

        if (!$storedToken) {
            throw new Exception("Invalid refresh token");
        }

        return $this->authService->generateTokens($userId);
    }
}