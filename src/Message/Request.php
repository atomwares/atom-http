<?php
/**
 * @link http://www.atomframework.net/
 * @copyright Copyright (c) 2017 Safarov Alisher
 * @license https://github.com/atomwares/atom-http/blob/master/LICENSE (MIT License)
 */

namespace Atom\Http\Message;

use Atom\Http\Message;
use InvalidArgumentException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class Request
 *
 * @package Atom\Http\Message
 */
class Request extends Message implements RequestInterface
{
    /**
     * @var string
     */
    protected $requestTarget = '/';
    /**
     * @var string
     */
    protected $method;
    /**
     * @var UriInterface
     */
    protected $uri;

    /**
     * Request constructor.
     *
     * @param string|null $method
     * @param UriInterface $uri
     * @param StreamInterface|null $body
     * @param array $headers
     */
    public function __construct(
        $method,
        UriInterface $uri,
        StreamInterface $body = null,
        array $headers = []
    ) {
        parent::__construct($body, $headers);
        $this->method = static::filterMethod($method);
        $this->uri = $uri;

        if ($this->uri) {
            $this->requestTarget = $this->uri->getPath();
            if ($query = $this->uri->getQuery()) {
                $this->requestTarget .= '?' . $query;
            }
        }

        if (! $this->hasHeader('Host')) {
            $this->updateHostHeaderFromUri();
        }
    }

    /**
     * Clone Uri and parents object
     */
    public function __clone()
    {
        parent::__clone();
        $this->uri = clone $this->uri;
    }

    /**
     * @inheritdoc
     */
    public function getRequestTarget()
    {
        return $this->requestTarget;
    }

    /**
     * @inheritdoc
     */
    public function withRequestTarget($requestTarget)
    {
        if (preg_match('#\s#', $requestTarget)) {
            throw new InvalidArgumentException(
                'Invalid request target provided; must be a string and cannot contain whitespace'
            );
        }

        $clone = clone $this;
        $clone->requestTarget = $requestTarget;

        return $clone;
    }

    /**
     * @inheritdoc
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @inheritdoc
     */
    public function withMethod($method)
    {
        $method = static::filterMethod($method);
        $clone = clone $this;
        $clone->method = $method;

        return $clone;
    }

    /**
     * @inheritdoc
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @inheritdoc
     */
    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        $clone = clone $this;
        $clone->uri = $uri;

        if (! $preserveHost) {
            $clone->updateHostHeaderFromUri();
        }

        return $clone;
    }

    /**
     * @inheritdoc
     */
    public function isMethod($method)
    {
        return $this->getMethod() === $method;
    }

    /**
     * @return bool
     */
    public function isGet()
    {
        return $this->isMethod('GET');
    }

    /**
     * @return bool
     */
    public function isPost()
    {
        return $this->isMethod('POST');
    }

    /**
     * @return bool
     */
    public function isPut()
    {
        return $this->isMethod('PUT');
    }

    /**
     * @return bool
     */
    public function isPatch()
    {
        return $this->isMethod('PATCH');
    }

    /**
     * @return bool
     */
    public function isDelete()
    {
        return $this->isMethod('DELETE');
    }

    /**
     * @return bool
     */
    public function isHead()
    {
        return $this->isMethod('HEAD');
    }

    /**
     * @return bool
     */
    public function isOptions()
    {
        return $this->isMethod('OPTIONS');
    }

    /**
     * @return bool
     */
    public function isXhr()
    {
        return $this->getHeaderLine('X-Requested-With') === 'XMLHttpRequest';
    }

    /**
     * @param string $method
     *
     * @return string
     */
    protected static function filterMethod($method)
    {
        if (! is_string($method)) {
            throw new InvalidArgumentException(sprintf(
                'Invalid HTTP method; must be a string, received %s',
                (is_object($method) ? get_class($method) : gettype($method))
            ));
        }

        if (! preg_match("/^[!#$%&'*+.^_`|~0-9a-z-]+$/i", $method)) {
            throw new InvalidArgumentException(sprintf(
                'Unsupported HTTP method "%s" provided',
                $method
            ));
        }

        return strtoupper($method);
    }

    /**
     * Update Host header from Uri
     */
    protected function updateHostHeaderFromUri()
    {
        if ($host = $this->uri->getHost()) {
            if (($port = $this->uri->getPort()) !== null) {
                $host .= ':' . $port;
            }

            $this->headers->set('Host', $host);
        }
    }
}
