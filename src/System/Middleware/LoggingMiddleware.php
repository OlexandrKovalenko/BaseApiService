<?php

namespace App\System\Middleware;

use App\System\Http\ResponseBundle;
use App\System\Traits\LoggableTrait;
use App\System\Util\GuidHelper;
use App\System\Util\Log;
use Random\RandomException;

class LoggingMiddleware
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
    public function handle($request, $next)
    {
        Log::init('Middleware');
        $guid = GuidHelper::createLocalSessionId();
        $message = [
            'method' => $request->getMethod(),
            'uri' => $request->getUri(),
            'params' => $request->getParams(),
            'headers' => $request->getHeaders()];

        $this->logInfo($guid, json_encode($message), [
            'class' => __CLASS__,
            'method' => __METHOD__,
            'tags' => ['request', 'fetch']
        ]);
        // Пропускаємо запит до наступного обробника
        return $next($request);
    }

    /**
     * @throws RandomException
     */
    public function after(ResponseBundle $response): ResponseBundle
    {
        Log::init('Middleware');
        // Додаємо логування відповіді
        $guid = GuidHelper::createLocalSessionId();
        $this->logInfo($guid, 'Response: ' . json_encode($response->getData()), [
            'class' => __CLASS__,
            'method' => __METHOD__,
            'tags' => ['response', 'after']
        ]);

        return $response;
    }
}