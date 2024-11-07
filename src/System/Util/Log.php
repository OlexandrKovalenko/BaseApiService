<?php

namespace App\System\Util;

use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class Log
{
    private static $logger;

    public static function init($chanel = null): void
    {
        self::$logger = new Logger($chanel);
        $handler = new StreamHandler(__DIR__ . '/../../../logs/app.log', Logger::INFO);
        $handler->setFormatter(new JsonFormatter());
        self::$logger->pushHandler($handler);
    }

    public static function getLogger()
    {
        return self::$logger;
    }

    public static function log($level, $message, array $context = []): void
    {
        // Додаємо метадані до контексту
        self::$logger->log($level, $message, $context);
    }

    public static function prepareLogContext(string $guid, string $executionGuid, array $context = []): array
    {
        $context['timestamp'] = date('Y-m-d H:i:s');
        $context['session'] = $guid;
        $context['executionSession'] = $executionGuid;
        $context['class'] = basename(str_replace('\\', '/', $context['class'])) ?? 'unknown';
        $context['method'] = $context['method'] ?? 'unknown';
        $context['tags'] = $context['tags'] ?? [];

        return $context;
    }
}