<?php

namespace Framework\Http;

use Psr\Http\Message\ResponseInterface;

class Response extends Message implements ResponseInterface
{
    private int $statusCode;
    private string $reasonPhrase;

    public function __construct(string $body, int $statusCode = 200, string $protocolVersion = '1.1', array $headers = [])
    {
        parent::__construct($body, $protocolVersion, $headers);
        $this->statusCode = $statusCode;
        $this->reasonPhrase = $this->getDefaultReasonPhrase($statusCode);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getReasonPhrase(): string
    {
        return $this->reasonPhrase;
    }

    public function withStatus(int $code, string $reasonPhrase = ''): ResponseInterface
    {
        $new = clone $this;
        $new->statusCode = $code;
        $new->reasonPhrase = $reasonPhrase ?: $this->getDefaultReasonPhrase($code);
        return $new;
    }

    private function getDefaultReasonPhrase(int $code): string
    {
        $phrases = [
            200 => 'OK',
            404 => 'Not Found',
            500 => 'Internal Server Error',
        ];
        return $phrases[$code] ?? '';
    }
}