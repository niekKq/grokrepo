<?php

namespace Framework\Http;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;
use GuzzleHttp\Psr7\Utils;
class Message implements MessageInterface
{
    protected string $protocolVersion;
    protected array $headers;
    protected string $body; 

    public function __construct(string $body = '', string $protocolVersion = '1.1', array $headers = [])
    {
        $this->protocolVersion = $protocolVersion;
        $this->headers = $headers;
        $this->body = $body; // body als string opslaan
    }

    public function getProtocolVersion(): string
    {
        return $this->protocolVersion;
    }

    public function withProtocolVersion(string $version): MessageInterface
    {
        $new = clone $this;
        $new->protocolVersion = $version;
        return $new;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function hasHeader(string $name): bool
    {
        return isset($this->headers[$name]);
    }

    public function getHeader(string $name): array
    {
        return $this->headers[$name] ?? [];
    }

    public function getHeaderLine(string $name): string
    {
        $values = $this->getHeader($name);
        return implode(',', $values);
    }

    public function withHeader(string $name, $value): MessageInterface
    {
        $new = clone $this;
        $new->headers[$name] = is_array($value) ? $value : [$value];
        return $new;
    }

    public function withAddedHeader(string $name, $value): MessageInterface
    {
        $new = clone $this;
        $new->headers[$name] = array_merge($new->headers[$name] ?? [], is_array($value) ? $value : [$value]);
        return $new;
    }

    public function withoutHeader(string $name): MessageInterface
    {
        $new = clone $this;
        unset($new->headers[$name]);
        return $new;
    }


    public function withBody(StreamInterface $body): MessageInterface
    {
  
        $new = clone $this;
        $new->body = (string) $body; // Convert naar string
        return $new;
    }

    public function getBody(): StreamInterface
    {
        // Assuming the body is stored as a string, wrap it in a StreamInterface implementation
        return Utils::streamFor($this->body);
    }
}