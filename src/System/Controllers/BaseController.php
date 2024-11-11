<?php

namespace App\System\Controllers;

use App\System\Container\Container;
use App\System\Core\ResultCodes;
use App\System\Exception\InternalServerErrorException;
use App\System\Exception\UserNotFoundException;
use App\System\Exception\ValidationException;
use App\System\Http\ResponseBundle;
use App\System\Providers\ServiceProvider;
use App\System\Traits\LoggableTrait;
use App\System\Util\GuidHelper;
use App\System\Util\Log;
use Exception;
use Random\RandomException;

/**
 * Class BaseController
 *
 * @package App\System\Controllers
 * @author maslo
 * @since 08.11.2024
 */
abstract class BaseController
{
    use LoggableTrait;

    /**
     * @var string $globalSessionId
     */
    protected string $globalSessionId;

    /**
     * @var Container $container
     */
    protected Container $container;

    /**
     * @throws RandomException
     * @throws Exception
     */
    public function __construct()
    {
        $this->container = $this->initializeContainer();
        $this->globalSessionId = GuidHelper::getOrCreateGlobalSessionId();
        Log::init('Controller');
    }

    /**
     *
     */
    public function __destruct()
    {
        //GuidHelper::resetGlobalSessionId();
    }

    /**
     * @throws Exception
     */
    private function initializeContainer(): Container
    {
        $container = new Container();
        ServiceProvider::initialize($container);  // Ініціалізація контейнера через ServiceProvider
        return $container;
    }

    /**
     * @throws Exception
     */
    protected function get($class)
    {
        return $this->container->make($class);
    }

    /**
     * jsonResponse
     *
     * @param $data
     * @param int $status
     * @return void
     */
    protected function jsonResponse($data, int $status = 200): void
    {

        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    /**
     * getInputData
     *
     * @return mixed
     */
    protected function getInputData(): mixed
    {
        return json_decode(file_get_contents('php://input'), true);
    }

    /**
     * handleException
     *
     * @param Exception $e
     * @return ResponseBundle
     */
    protected function handleException(Exception $e): ResponseBundle
    {
        if ($e instanceof UserNotFoundException) {
            return new ResponseBundle($e->getCode(), [
                'error' => $e->getMessage(),
            ], ResultCodes::ERROR_NOT_FOUND);
        } elseif ($e instanceof ValidationException) {
            return new ResponseBundle($e->getCode(), [
                'error' => $e->getMessage(),
            ], ResultCodes::ERROR_BAD_REQUEST);
        } elseif ($e instanceof InternalServerErrorException) {
            return new ResponseBundle($e->getCode(), [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ], ResultCodes::ERROR_INTERNAL_SERVER);
        } else {
            // Для всіх інших невизначених помилок
            return new ResponseBundle(500, [
                'error' => 'Unknown error occurred',
                'trace' => $e->getTraceAsString(),
            ], ResultCodes::ERROR_INTERNAL_SERVER);
        }
    }
}
