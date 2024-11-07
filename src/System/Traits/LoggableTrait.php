<?php

namespace App\System\Traits;

use App\System\Util\Log;

trait LoggableTrait
{
    protected string $globalSessionId;

    protected function logInfo(string $guid, string $message, array $context = []): void
    {
        $fullContext = $this->getStandardContext($context);
        $this->log('info', $guid, $message, $fullContext);
    }

    protected function logError(string $guid, string $message, array $context = []): void
    {
        $fullContext = $this->getStandardContext($context);
        $this->log('error', $guid, $message, $fullContext);
    }

    protected function logDebug(string $guid, string $message, array $context = []): void
    {
        $fullContext = $this->getStandardContext($context);
        $this->log('debug', $guid, $message, $fullContext);
    }

    private function log(string $level, string $guid, string $message, array $context = []): void
    {
        $context = Log::prepareLogContext($this->globalSessionId, $guid, $context);
        $context['method'] = debug_backtrace()[2]['function'] ?? 'unknown';

        Log::log($level, $message, $context);
    }

    protected function getStandardContext(array $extraContext = []): array
    {
        $backtrace = debug_backtrace();
        $callingMethod = $backtrace[2]['function'] ?? 'unknown function';

        return array_merge([
            'class' => get_class($this),
            'method' => $callingMethod,
            'tags' => ['user', 'request'],
        ], $extraContext);
    }
}
