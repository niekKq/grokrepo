<?php

namespace Framework\Http;

use Psr\Http\Message\ServerRequestInterface;
use GuzzleHttp\Psr7\UploadedFile;
class ServerRequest extends Request implements ServerRequestInterface
{
    private array $serverParams;
    private array $cookieParams;
    private array $queryParams;
    private array $uploadedFiles;
    private $parsedBody;
    private array $attributes;

    public function __construct(
        string $method,
        $uri,
        array $headers = [],
        $body = '',
        string $protocolVersion = '1/1',
        array $serverParams = [],
        array $cookieParams = [],
        array $queryParams = [],
        array $uploadedFiles = [],
        $parsedBody = null,
        array $attributes = []
    ) {
        parent::__construct($method, $uri, $body, $protocolVersion, $headers);
        $this->serverParams = $serverParams;
        $this->cookieParams = $cookieParams;
        $this->queryParams = $queryParams;
        $this->uploadedFiles = $uploadedFiles;
        $this->parsedBody = $parsedBody;
        $this->attributes = $attributes;
    }

    public static function fromSuperglobals(): ServerRequest
    {
        $serverParams = $_SERVER;
        $cookieParams = $_COOKIE;
        $queryParams = $_GET;
        $uploadedFiles = (new self('', ''))->normalizeFiles($_FILES); // Tijdelijke instantie om normalizeFiles te gebruiken
        $parsedBody = $_POST;
        $attributes = [];

        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $port = $_SERVER['SERVER_PORT'] ?? 80;
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        $uri = $scheme . '://' . $host;
        if (($scheme === 'http' && $port != 80) || ($scheme === 'https' && $port != 443)) {
            $uri .= ':' . $port;
        }
        $uri .= $path;

        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (substr($key, 0, 5) === 'HTTP_') {
                $headerName = str_replace('_', '-', substr($key, 5));
                $headerName = strtolower($headerName);
                $headerName = implode('-', array_map('ucfirst', explode('-', $headerName)));
                $headers[$headerName] = $value;
            }
        }

        return new self(
            $method,
            $uri,
            $headers,
            '',
            '1/1',
            $serverParams,
            $cookieParams,
            $queryParams,
            $uploadedFiles,
            $parsedBody,
            $attributes
        );
    }

    private function normalizeFiles(array $files): array
    {
        $normalized = [];
        foreach ($files as $key => $file) {
            if (is_array($file['tmp_name'])) {
                $normalized[$key] = $this->normalizeNestedFiles($file);
            } else {
                $normalized[$key] = new UploadedFile(
                    $file['tmp_name'],
                    $file['size'],
                    $file['error'],
                    $file['name'] ?? null,
                    $file['type'] ?? null
                );
            }
        }
        return $normalized;
    }

    private function normalizeNestedFiles(array $file): array
    {
        $normalized = [];
        foreach ($file['tmp_name'] as $key => $tmpName) {
            $normalized[$key] = new UploadedFile(
                $tmpName,
                $file['size'][$key],
                $file['error'][$key],
                $file['name'][$key] ?? null,
                $file['type'][$key] ?? null
            );
        }
        return $normalized;
    }

    public function getServerParams(): array
    {
        return $this->serverParams;
    }

    public function getCookieParams(): array
    {
        return $this->cookieParams;
    }

    public function withCookieParams(array $cookies): ServerRequestInterface
    {
        $new = clone $this;
        $new->cookieParams = $cookies;
        return $new;
    }

    public function getQueryParams(): array
    {
        return $this->queryParams;
    }

    public function withQueryParams(array $query): ServerRequestInterface
    {
        $new = clone $this;
        $new->queryParams = $query;
        return $new;
    }

    public function getUploadedFiles(): array
    {
        return $this->uploadedFiles;
    }

    public function withUploadedFiles(array $uploadedFiles): ServerRequestInterface
    {
        $new = clone $this;
        $new->uploadedFiles = $this->normalizeFiles($uploadedFiles);
        return $new;
    }

    public function getParsedBody()
    {
        return $this->parsedBody;
    }

    public function withParsedBody($data): ServerRequestInterface
    {
        $new = clone $this;
        $new->parsedBody = $data;
        return $new;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getAttribute(string $name, $default = null)
    {
        return $this->attributes[$name] ?? $default;
    }

    public function withAttribute(string $name, $value): ServerRequestInterface
    {
        $new = clone $this;
        $new->attributes[$name] = $value;
        return $new;
    }

    public function withoutAttribute(string $name): ServerRequestInterface
    {
        $new = clone $this;
        unset($new->attributes[$name]);
        return $new;
    }
}