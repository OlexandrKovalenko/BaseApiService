<?php

namespace App\System\Controllers;

use App\System\Controllers\BaseController;
use App\System\Core\ResultCodes;
use App\System\Facades\Auth\AuthFacadeInterface;
use App\System\Http\RequestBundle;
use App\System\Http\ResponseBundle;
use App\System\Util\GuidHelper;
use Exception;
use Random\RandomException;

class AuthController extends BaseController
{

    private AuthFacadeInterface $authFacade;
    public function __construct(AuthFacadeInterface $authFacade)
    {
        parent::__construct();
        $this->authFacade = $authFacade;
    }

    /**
     * @throws RandomException
     */
    public function login (RequestBundle $request): ResponseBundle
    {
        $guid = GuidHelper::createLocalSessionId();
        $this->logInfo($guid, (string)json_encode($request->getBody()), [
            'tags' => ['auth', 'login','request'],
        ]);
        try {
            $process = $this->authFacade->login($request);
            if ($process instanceof ResponseBundle) {
                return $process;
            }

            return new ResponseBundle(500, ['tokens' => $process], ResultCodes::SUCCESS);
        } catch (Exception $e) {
            return new ResponseBundle(400, ['error' => $e->getMessage()], ResultCodes::ERROR_BAD_REQUEST);
        }
    }
}