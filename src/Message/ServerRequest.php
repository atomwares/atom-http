<?php
/**
 * @link http://www.atomframework.net/
 * @copyright Copyright (c) 2017 Safarov Alisher
 * @license https://github.com/atomwares/atom-http/blob/master/LICENSE (MIT License)
 */

namespace Atom\Http\Message;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class ServerRequest
 *
 * @package Atom\Http\Message
 */
class ServerRequest extends Request implements ServerRequestInterface
{
    /**
     * @var string[]
     */
    protected $serverParams = [];
    /**
     * @var string[]
     */
    protected $cookieParams = [];
    /**
     * @var UploadedFile[]
     */
    protected $uploadedFiles = [];
    /**
     * @var array
     */
    protected $queryParams = [];
    /**
     * @var array
     */
    protected $attributes = [];
    /**
     * @var null|array|object
     */
    protected $parsedBody;

    /**
     * ServerRequest constructor.
     *
     * @param string $method
     * @param UriInterface $uri
     * @param array $serverParams
     * @param StreamInterface|null $body
     * @param array $headers
     * @param array $cookieParams
     * @param UploadedFile[] $uploadedFiles
     */
    public function __construct(
        $method,
        UriInterface $uri,
        array $serverParams,
        StreamInterface $body = null,
        array $headers = [],
        array $cookieParams = [],
        array $uploadedFiles = []
    ) {
        parent::__construct($method, $uri, $body, $headers);
        $this->serverParams = $serverParams;
        $this->cookieParams = $cookieParams;
        $this->uploadedFiles = $uploadedFiles;
    }

    /**
     * @inheritdoc
     */
    public function getServerParams()
    {
        return $this->serverParams;
    }

    /**
     * @inheritdoc
     */
    public function getCookieParams()
    {
        return $this->cookieParams;
    }

    /**
     * @inheritdoc
     */
    public function withCookieParams(array $cookies)
    {
        $clone = clone $this;
        $clone->cookieParams = $cookies;

        return $clone;
    }

    /**
     * @inheritdoc
     */
    public function getQueryParams()
    {
        return $this->queryParams;
    }

    /**
     * @inheritdoc
     */
    public function withQueryParams(array $query)
    {
        $clone = clone $this;
        $clone->queryParams = $query;

        return $clone;
    }

    /**
     * @inheritdoc
     */
    public function getUploadedFiles()
    {
        return $this->uploadedFiles;
    }

    /**
     * @inheritdoc
     */
    public function withUploadedFiles(array $uploadedFiles)
    {
        $clone = clone $this;
        $clone->uploadedFiles = $uploadedFiles;

        return $clone;
    }

    /**
     * @inheritdoc
     */
    public function getParsedBody()
    {
        return $this->parsedBody;
    }

    /**
     * @inheritdoc
     */
    public function withParsedBody($data)
    {
        $clone = clone $this;
        $clone->parsedBody = $data;

        return $clone;
    }

    /**
     * @inheritdoc
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @inheritdoc
     */
    public function getAttribute($name, $default = null)
    {
        if (! array_key_exists($name, $this->attributes)) {
            return $default;
        }

        return $this->attributes[$name];
    }

    /**
     * @inheritdoc
     */
    public function withAttribute($name, $value)
    {
        $clone = clone $this;
        $clone->attributes[$name] = $value;

        return $clone;
    }

    /**
     * @inheritdoc
     */
    public function withoutAttribute($name)
    {
        $clone = clone $this;
        unset($clone->attributes[$name]);

        return $clone;
    }
}
