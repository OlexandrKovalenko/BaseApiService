<?php

namespace App\System\Controllers;

use App\System\Core\ResultCodes;
use App\System\Facades\Auth\AuthFacadeInterface;
use App\System\Http\RequestBundle;
use App\System\Http\ResponseBundle;
use App\System\Util\GuidHelper;
use Exception;
use Random\RandomException;

/**
 * Class AuthController
 *
 * @package App\System\Controllers
 * @author maslo
 * @since 08.11.2024
 */
class AuthController extends BaseController
{

    /**
     * @var AuthFacadeInterface $authFacade
     */
    private AuthFacadeInterface $authFacade;

    /**
     * @param AuthFacadeInterface $authFacade
     * @throws RandomException
     */
    public function __construct(AuthFacadeInterface $authFacade)
    {
        parent::__construct();
        $this->authFacade = $authFacade;
    }

    /**
     * login user and return tokens if successful.
     *
     * @param RequestBundle $request
     * @return ResponseBundle
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

    /**
     * refresh
     *
     * @param RequestBundle $request
     * @return ResponseBundle
     * @throws RandomException
     */
    public function refresh(RequestBundle $request): ResponseBundle
    {
        $guid = GuidHelper::createLocalSessionId();
        $this->logInfo($guid, (string)json_encode($request->getBody()), [
            'tags' => ['auth', 'login', 'refresh', 'request'],
        ]);

        try {
            $refreshToken = $request->getBody()['refreshToken'] ?? null;
            if (!$refreshToken) {
                return new ResponseBundle(400, ['error' => 'Refresh token is missing'], ResultCodes::ERROR_BAD_REQUEST);
            }

            $response = $this->authFacade->refreshToken($request);

            if ($response instanceof ResponseBundle) {
                return $response;
            }

            $this->logInfo($guid, (string)json_encode($response), [
                'tags' => ['auth', 'login', 'refresh', 'response'],
            ]);
            return new ResponseBundle(200, ['AccessToken' => $response], ResultCodes::SUCCESS);

        } catch (Exception $e) {
            return new ResponseBundle(500, ['error' => $e->getMessage()], ResultCodes::ERROR_INTERNAL_SERVER);
        }
    }
}