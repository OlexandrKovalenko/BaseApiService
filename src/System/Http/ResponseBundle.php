<?php

namespace App\System\Http;

use App\System\Core\ResultCodes;

class ResponseBundle
{
    private int $statusCode;
    private array $data;
    private int $resultCode;

    public function __construct(int $statusCode = 200, array $data = [], int $resultCode = ResultCodes::SUCCESS)
    {
        $this->statusCode = $statusCode;
        $this->data = $data;
        $this->resultCode = $resultCode;
    }

    public function setStatusCode(int $statusCode): self
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function setData(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function getData(): array
    {
        return [
            'Result' => $this->resultCode,
            'Data' => $this->data,
        ];
    }

    public function setResultCode(int $resultCode): self
    {
        $this->resultCode = $resultCode;
        return $this;
    }

    public function send(): void
    {
        http_response_code($this->statusCode);
        header('Content-Type: application/json');
        $response = [
            'Result' => $this->resultCode,
            'response' => $this->data,
        ];
        echo json_encode($response);
    }
}
