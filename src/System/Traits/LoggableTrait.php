<?php

namespace App\System\Traits;

use App\System\Util\Log;

trait LoggableTrait
{
    protected string $globalSessionId;

    protected function logInfo(string $guid, string $message, array $context = []): void
    {
        $this->log('info', $guid, $message, $context);
    }

    protected function logError(string $guid, string $message, array $context = []): void
    {
        $this->log('error', $guid, $message, $context);
    }

    protected function logDebug(string $guid, string $message, array $context = []): void
    {
        $this->log('debug', $guid, $message, $context);
    }

    private function log(string $level, string $guid, string $message, array $context = []): void
    {
        $context = Log::prepareLogContext($this->globalSessionId, $guid, $context);
        $context['method'] = debug_backtrace()[2]['function'] ?? 'unknown';

        Log::log($level, $message, $context);
    }
}
