<?php

namespace App\System\Http;

use DateTimeImmutable;
use Exception;

class RequestBundle
{
    private string $method;
    private string $uri;
    private array $params = [];
    private array $headers = [];
    private array $attributes = [];

    private array $body;
    private DateTimeImmutable $requestTime;
    private string $clientIp;

    /**
     * @throws Exception
     */
    public function __construct(string $uri)
    {
        $this->body = $this->getJsonBody();
        $this->requestTime = new DateTimeImmutable('@' . $_SERVER['REQUEST_TIME']);
        $this->method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $this->params = $this->getParameters();
        $this->headers = $this->getAllHeaders();
        $this->clientIp = $this->determineClientIp();
        $this->uri = $uri;

        //$this->params = $params;
        //$this->headers = $headers;
    }

    private function getJsonBody(): array
    {
        $rawInput = file_get_contents('php://input');
        return json_decode($rawInput, true) ?? [];
    }


    private function getParameters(): array
    {
        return $this->method === 'GET' ? $_GET : ($_POST ?? []);
    }

    private function getAllHeaders(): array
    {
        if (function_exists('getallheaders')) {
            return getallheaders();
        }

        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (str_starts_with($name, 'HTTP_')) {
                $headerName = str_replace('_', '-', substr($name, 5));
                $headers[$headerName] = $value;
            }
        }
        return $headers;
    }

    private function determineClientIp(): string
    {
        if (!empty($_SERVER['REMOTE_ADDR'])) {
            return $_SERVER['REMOTE_ADDR'];
        }

        $possibleHeaders = ['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP'];
        foreach ($possibleHeaders as $header) {
            if (!empty($_SERVER[$header])) {
                return explode(',', $_SERVER[$header])[0];
            }
        }
        return '0.0.0.0';
    }

    public function getBody(): array
    {
        return $this->body;
    }

    public function getRequestTime(): DateTimeImmutable
    {
        return $this->requestTime;
    }

    public function getMethod(): string
    {
        return $this->method;
    }


    public function getUri(): string
    {
        return $this->uri;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getHeader(string $name): ?string
    {
        return $this->headers[$name] ?? null;
    }


    public function setAttribute(string $key, $value): void
    {
        $this->attributes[$key] = $value;
    }

    public function getAttributes(): ?array
    {
        return $this->attributes ?? null;
    }

    public function getAttribute(string $key)
    {
        return $this->attributes[$key] ?? null;
    }

    public function getClientIp(): string
    {
        return $this->clientIp;
    }
}
