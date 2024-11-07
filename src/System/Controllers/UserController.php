<?php

namespace App\System\Controllers;

use App\System\Facades\Auth\AuthFacade;
use App\System\Facades\Auth\AuthFacadeInterface;
use App\System\Facades\User\UserFacade;
use App\System\Facades\User\UserFacadeInterface;
use App\System\Http\RequestBundle;
use App\System\Http\ResponseBundle;
use App\System\Util\GuidHelper;
use Exception;
use Random\RandomException;

class UserController extends BaseController
{

    private UserFacade $userFacade;
    public function __construct(UserFacadeInterface $userFacade)
    {
        parent::__construct();
        $this->userFacade = $userFacade;
    }

    /**
     * @throws RandomException
     */
    public function getUser(RequestBundle $request): ResponseBundle
    {
        $guid = GuidHelper::createLocalSessionId();

        $this->logInfo($guid, (string)json_encode($request->getBody()), [
            'tags' => ['user', 'request'],
        ]);

        $user = $this->userFacade->getUser($request);

        if ($user instanceof ResponseBundle) {
            return $user;
        }

        $this->logInfo($guid, (string)json_encode($request->getBody()), [
            'tags' => ['user', 'response'],
            'user_id' => $user->getId(),
            'email' => $user->getEmail(),
            'phone' => $user->getPhone(),
        ]);

        return new ResponseBundle(200, $user->jsonSerialize());
    }

    /**
     * @throws RandomException
     * @throws Exception
     */
    public function createUser(RequestBundle $request): ResponseBundle
    {
        $guid = GuidHelper::createLocalSessionId();
        $this->logInfo($guid, (string)json_encode($request->getBody()), [
            'tags' => ['user', 'create', 'response'],  // можна передавати тільки специфічні параметри
        ]);

        $createUserResponse = $this->userFacade->createUser($request);

        if ($createUserResponse instanceof ResponseBundle) {
            return $createUserResponse;
        }

        $this->logInfo($guid, (string)json_encode($createUserResponse), [
            'tags' => ['user', 'create', 'response'],  // можна передавати тільки специфічні параметри
            'user_id' => $createUserResponse['user_id'],
        ]);

        return new ResponseBundle(201, $createUserResponse);
    }

}