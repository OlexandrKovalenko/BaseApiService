<?php

namespace App\System\Controllers;

use App\System\Facades\User\UserFacade;
use App\System\Http\RequestBundle;
use App\System\Http\ResponseBundle;
use App\System\Util\GuidHelper;
use Exception;
use Random\RandomException;

class UserController extends BaseController
{

    private UserFacade $userFacade;
    public function __construct()
    {
        parent::__construct();
        $this->userFacade = $this->get(UserFacade::class);
    }

    /**
     * @throws RandomException
     */
    public function getUser(RequestBundle $request): ResponseBundle
    {
        $guid = GuidHelper::createLocalSessionId();
        $this->logInfo($guid, (string)json_encode($request), [
            'class' => __CLASS__,
            'method' => __METHOD__,
            'tags' => ['user', 'request'],
        ]);

        $user = $this->userFacade->getUser($request);

        if ($user instanceof ResponseBundle) {
            return $user;
        }

        $this->logInfo($guid, (string)json_encode($user->jsonSerialize()), [
            'class' => __CLASS__,
            'method' => __METHOD__,
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

        $createUserResponse = $this->userFacade->createUser($request);
        if ($createUserResponse instanceof ResponseBundle) {
            return $createUserResponse;
        }

        $this->logInfo($guid, (string)json_encode($createUserResponse), [
            'class' => __CLASS__,
            'method' => __METHOD__,
            'tags' => ['user', 'create', 'response'],
            'user_id' => $createUserResponse['user_id'],
        ]);

        return new ResponseBundle(201, $createUserResponse);
    }
}