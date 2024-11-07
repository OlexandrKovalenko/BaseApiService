<?php

namespace App\System\Http;

class RequestBundle
{
    private string $method;
    private string $uri;
    private array $params = [];
    private array $headers = [];
    private array $attributes = []; // для збереження додаткових даних, як-от user_id

    public function __construct(string $method, string $uri, array $params = [], array $headers = [])
    {
        $this->method = $method;
        $this->uri = $uri;
        $this->params = $params;
        $this->headers = $headers;
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

    public function getBody(): array
    {
        return json_decode(file_get_contents('php://input'), true) ?? [];
    }

    public function setAttribute(string $key, $value): void
    {
        $this->attributes[$key] = $value;
    }

    public function getAttribute(string $key)
    {
        return $this->attributes[$key] ?? null;
    }
}
