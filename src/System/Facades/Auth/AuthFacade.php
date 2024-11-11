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
use Random\RandomException;

/**
 * Class AuthFacade
 *
 * @package App\System\Facades\Auth
 * @author maslo
 * @since 11.11.2024
 */
class AuthFacade extends BaseFacade implements AuthFacadeInterface
{

    /**
     * @var AuthServiceInterface $authService
     */
    private AuthServiceInterface $authService;
    /**
     * @var UserServiceInterface $userService
     */
    private UserServiceInterface $userService;

    /**
     * @param AuthServiceInterface $authService
     * @param UserServiceInterface $userService
     * @throws RandomException
     */
    public function __construct(AuthServiceInterface $authService, UserServiceInterface $userService)
    {
        parent::__construct();
        $this->authService = $authService;
        $this->userService = $userService;
    }

    /**
     * login
     *
     * @param RequestBundle $request
     * @return array|ResponseBundle
     * @throws RandomException
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
     * refreshToken
     *
     * @param RequestBundle $request
     * @return string|ResponseBundle
     * @throws RandomException
     */
    public function refreshToken(RequestBundle $request): string|ResponseBundle
    {
        $guid = GuidHelper::createLocalSessionId();
        $refreshToken = $request->getBody()['refreshToken'] ?? null;
        $userPhone = $request->getBody()['phone'] ?? null;

        try {
            $requiredFields = ['phone', 'refreshToken'];
            $validationResponse = $this->validateRequiredFields($request, $requiredFields);
            if ($validationResponse instanceof ResponseBundle) {
                return $validationResponse;
            }

            $user = $this->userService->getUserByPhone(DataFormatter::formatPhone($userPhone));

            if (!$user) {
                $this->logWarning($guid, "User with phone {$userPhone} not found.", [
                    'tags' => ['user', 'refreshToken', 'not_found']
                ]);
                return new ResponseBundle(404, ['error' => "User with phone number {$userPhone} not found."], ResultCodes::ERROR_NOT_FOUND);
            }

            $storedToken = $this->authService->validateRefreshToken($user->getId(), $refreshToken);
            if (!$storedToken) {
                return new ResponseBundle(500, ['error' => 'Invalid refresh token'], ResultCodes::ERROR_NOT_FOUND);
            }

            $this->logInfo($guid, (string)json_encode(true), [
                'tags' => ['user', 'getUser', 'response'],
                'user_id' => $user->getId(),
                'phone' => $user->getPhone(),
                'result' => ResultCodes::SUCCESS,
            ]);

            return $this->authService->generateAccessToken($user->getId());

        } catch (Exception $e) {
            $this->logError($guid, (string)json_encode($e->getMessage()), [
                'tags' => ['user', 'getUser', 'response'],
                'result' => ResultCodes::ERROR_INTERNAL_SERVER,
            ]);
            return new ResponseBundle(500, ['error' => $e->getMessage()], ResultCodes::ERROR_INTERNAL_SERVER);
        }
    }
}