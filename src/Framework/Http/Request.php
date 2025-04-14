<?php

namespace Framework\Http;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;
use GuzzleHttp\Psr7\Uri;

class Request extends Message implements RequestInterface
{
    protected string $method;
    protected UriInterface $uri;

    public function __construct(string $method, $uri, string $body = '', string $protocolVersion = '1/1', array $headers = [])
    {
        parent::__construct($body, $protocolVersion, $headers);
        $this->method = $method;
        $this->uri = $uri instanceof UriInterface ? $uri : new Uri($uri);
    }

    public function getRequestTarget(): string
    {
        $target = $this->uri->getPath();
        $query = $this->uri->getQuery();
        if ($query) {
            $target .= '?' . $query;
        }
        return $target ?: '/';
    }

    public function withRequestTarget(string $requestTarget): RequestInterface
    {
        $new = clone $this;
        $parts = parse_url($requestTarget);
        $path = $parts['path'] ?? '/';
        $query = $parts['query'] ?? '';
        $new->uri = $new->uri->withPath($path)->withQuery($query);
        return $new;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function withMethod(string $method): RequestInterface
    {
        $new = clone $this;
        $new->method = $method;
        return $new;
    }

    public function getUri(): UriInterface
    {
        return $this->uri;
    }

    public function withUri(UriInterface $uri, bool $preserveHost = false): RequestInterface
    {
        $new = clone $this;
        $new->uri = $uri;
        if (!$preserveHost && $uri->getHost() !== '') {
            $new->headers['Host'] = [$uri->getHost()];
        } elseif (!$preserveHost) {
            unset($new->headers['Host']);
        }
        return $new;
    }
}