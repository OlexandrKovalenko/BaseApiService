<?php

namespace App\System\Middleware;

use App\System\Http\RequestBundle;
use App\System\Http\ResponseBundle;
use App\System\Traits\LoggableTrait;
use App\System\Util\GuidHelper;
use App\System\Util\Log;
use Random\RandomException;

class LoggingMiddleware  implements MiddlewareInterface
{
    protected string $globalSessionId;
    use LoggableTrait;

    /**
     * @throws RandomException
     */
    public function __construct()
    {
        $this->globalSessionId = GuidHelper::getOrCreateGlobalSessionId();
    }

    public function __destruct()
    {
        GuidHelper::resetGlobalSessionId();
    }

    /**
     * @throws RandomException
     */
    public function handle(RequestBundle $request, ResponseBundle $response, callable $next)
    {
        Log::init('Middleware');
        $guid = GuidHelper::createLocalSessionId();
        $message = [
            'method' => $request->getMethod(),
            'uri' => $request->getUri(),
            'params' => $request->getParams(),
            'headers' => $request->getHeaders()];

        $this->logInfo($guid, 'Request: ' . json_encode($message), [
            'tags' => ['middleware', 'request'],
            'data' => $request->getBody()
        ]);
        return $next($request, $response);
    }

    /**
     * @throws RandomException
     */
    public function after(ResponseBundle $response): ResponseBundle
    {
        $guid = GuidHelper::createLocalSessionId();

        $this->logInfo($guid, 'Response: ' . json_encode($response->getBody()), [
            'tags' => ['middleware', 'response'],
            'data' => $response->getBody()
        ]);

        GuidHelper::resetGlobalSessionId();
        return $response;
    }
}