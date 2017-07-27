<?php
/**
 * @link http://www.atomframework.net/
 * @copyright Copyright (c) 2017 Safarov Alisher
 * @license https://github.com/atomwares/atom-http/blob/master/LICENSE (MIT License)
 */

namespace Atom\Http;

use Atom\Http\Message\HeaderCollection;
use InvalidArgumentException;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Class Message
 *
 * @package Atom\Http\Message
 */
abstract class Message implements MessageInterface
{
    /**
     * @var string
     */
    protected $protocolVersion = '1.1';
    /**
     * @var StreamInterface|null
     */
    protected $body;
    /**
     * @var HeaderCollection
     */
    protected $headers;

    /**
     * @var array
     */
    protected static $protocolVersions = ['1.0', '1.1', '2.0'];

    /**
     * Message constructor.
     *
     * @param StreamInterface|null $body
     * @param array $headers
     */
    public function __construct(StreamInterface $body = null, array $headers = [])
    {
        $this->body = $body;
        $this->headers = new HeaderCollection($headers);
    }

    /**
     * Clone Stream and HeaderCollection object
     */
    public function __clone()
    {
        if ($this->body) {
            $this->body = clone $this->body;
        }

        $this->headers = clone $this->headers;
    }

    /**
     * @inheritdoc
     */
    public function getProtocolVersion()
    {
        return $this->protocolVersion;
    }

    /**
     * @inheritdoc
     */
    public function withProtocolVersion($version)
    {
        if (! isset(static::$protocolVersions[$version])) {
            throw new InvalidArgumentException(
                'Invalid HTTP version. Must be one of: '
                . implode(', ', array_keys(static::$protocolVersions))
            );
        }

        $clone = clone $this;
        $clone->protocolVersion = $version;

        return $clone;
    }

    /**
     * @inheritdoc
     */
    public function getHeaders()
    {
        return $this->headers->all();
    }

    /**
     * @inheritdoc
     */
    public function hasHeader($name)
    {
        return $this->headers->has($name);
    }

    /**
     * @inheritdoc
     */
    public function getHeader($name)
    {
        return $this->headers->get($name);
    }

    /**
     * @inheritdoc
     */
    public function getHeaderLine($name)
    {
        return implode(',', $this->headers->get($name));
    }

    /**
     * @inheritdoc
     */
    public function withHeader($name, $value)
    {
        $clone = clone $this;
        $clone->headers->set($name, $value);

        return $clone;
    }

    /**
     * @inheritdoc
     */
    public function withAddedHeader($name, $value)
    {
        $clone = clone $this;
        $clone->headers->add($name, $value);

        return $clone;
    }

    /**
     * @inheritdoc
     */
    public function withoutHeader($name)
    {
        $clone = clone $this;
        $clone->headers->remove($name);

        return $clone;
    }

    /**
     * @inheritdoc
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @inheritdoc
     */
    public function withBody(StreamInterface $body)
    {
        $clone = clone $this;
        $clone->body = $body;

        return $clone;
    }
}
