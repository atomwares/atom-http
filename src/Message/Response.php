<?php
/**
 * @link http://www.atomframework.net/
 * @copyright Copyright (c) 2017 Safarov Alisher
 * @license https://github.com/atomwares/atom-http/blob/master/LICENSE (MIT License)
 */

namespace Atom\Http\Message;

use Atom\Http\Message;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Class Response
 *
 * @package Atom\Http\Message
 */
class Response extends Message implements ResponseInterface
{
    /**
     * @var int
     */
    protected $statusCode = 200;
    /**
     * @var string
     */
    protected $reasonPhrase = '';

    /**
     * Response constructor.
     *
     * @param int $statusCode
     * @param StreamInterface|null $body
     * @param array $headers
     */
    public function __construct(
        $statusCode = 200,
        StreamInterface $body = null,
        array $headers = []
    ) {
        parent::__construct($body, $headers);
        $this->statusCode = self::filterStatus($statusCode);
    }

    /**
     * @inheritdoc
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @inheritdoc
     */
    public function withStatus($code, $reasonPhrase = '')
    {
        $statusCode = self::filterStatus($code);
        $reasonPhrase = self::filterReason($reasonPhrase);
        $clone = clone $this;
        $clone->statusCode = $statusCode;
        $clone->reasonPhrase = $reasonPhrase;

        return $clone;
    }

    /**
     * @inheritdoc
     */
    public function getReasonPhrase()
    {
        if ($this->reasonPhrase === '') {
            $this->reasonPhrase = StatusCode::getReasonPhrase($this->statusCode);
        }

        return $this->reasonPhrase;
    }

    /**
     * @param $code
     *
     * @return int
     */
    protected static function filterStatus($code)
    {
        if (! is_integer($code) || $code < StatusCode::STATUS_CODE_MIN || $code > StatusCode::STATUS_CODE_MAX) {
            throw new InvalidArgumentException(sprintf(
                'Invalid status code provided; must be an integer between %d and %d, inclusive; received %s',
                StatusCode::STATUS_CODE_MIN,
                StatusCode::STATUS_CODE_MAX,
                (is_object($code) ? get_class($code) : gettype($code))
            ));
        }

        return $code;
    }

    /**
     * @param string $phrase
     */
    protected static function filterReason($phrase)
    {
        if (! is_string($phrase)) {
            throw new InvalidArgumentException(sprintf(
                'Invalid reason phrase provided; must be a string; received %s',
                (is_object($phrase) ? get_class($phrase) : gettype($phrase))
            ));
        }
    }
}
