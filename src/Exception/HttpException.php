<?php

namespace Atom\Http\Exception;

use Atom\Http\Message\StatusCode;
use Exception;

/**
 * Class HttpException
 *
 * @package Atom\Http\Exception
 */
class HttpException extends Exception
{
    /**
     * @var int
     */
    protected $statusCode;

    /**
     * HttpException constructor.
     *
     * @param int $statusCode
     * @param null $message
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct($statusCode, $message = null, $code = 0, Exception $previous = null)
    {
        $this->statusCode = $statusCode;

        parent::__construct($message, $code, $previous);
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @return string
     */
    public function getReasonPhrase()
    {
        return StatusCode::getReasonPhrase($this->statusCode);
    }
}
