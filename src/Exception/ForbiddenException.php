<?php

namespace Atom\Http\Exception;

/**
 * Class ForbiddenException
 *
 * @package Atom\Http\Exception
 */
class ForbiddenException extends HttpException
{
    /**
     * ForbiddenException constructor.
     *
     * @param null $message
     * @param int $code
     * @param \Exception|null $previous
     */
    public function __construct($message = null, $code = 0, \Exception $previous = null)
    {
        parent::__construct(403, $message, $code, $previous);
    }
}
