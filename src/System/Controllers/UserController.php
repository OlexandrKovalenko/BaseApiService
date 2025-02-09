<?php
declare(strict_types=1);

namespace App\System\Controllers;

use App\System\Entity\User;
use App\System\Facades\User\UserFacade;
use App\System\Facades\User\UserFacadeInterface;
use App\System\Http\RequestBundle;
use App\System\Http\ResponseBundle;
use App\System\Util\GuidHelper;
use Exception;
use Random\RandomException;


/**
 * Class UserController
 *
 * @package App\System\Controllers
 * @author maslo
 * @since 08.11.2024
 */
class UserController extends BaseController
{

    /**
     * @var UserFacade $userFacade
     */
    private UserFacade $userFacade;

    /**
     * @param UserFacadeInterface $userFacade
     * @throws RandomException
     */
    public function __construct(UserFacadeInterface $userFacade)
    {
        parent::__construct();
        $this->userFacade = $userFacade;
    }

    /**
     * getUser
     *
     * @param RequestBundle $request
     * @return ResponseBundle
     * @throws RandomException
     */
    public function getUser(RequestBundle $request): ResponseBundle
    {
        $guid = GuidHelper::createLocalSessionId();

        $this->logInfo($guid, (string)json_encode($request->getBody()), [
            'tags' => ['user', 'request'],
        ]);

            $result = $this->userFacade->getUser($request);

            if ($result instanceof ResponseBundle)
            {
                $this->logError($guid, (string)json_encode($result), [
                    'tags' => ['user', 'response'],
                ]);
                return $result;
            } else {
                $this->logInfo($guid, (string)json_encode($result->jsonSerialize()), [
                    'tags' => ['user', 'response'],
                    'user_id' => $result->getId(),
                    'email' => $result->getEmail(),
                    'phone' => $result->getPhone(),
                ]);

                return new ResponseBundle(200, $result->jsonSerialize());
            }
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