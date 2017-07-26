<?php

namespace Atom\Http\Exception;

/**
 * Class NotFoundException
 *
 * @package Atom\Http\Exception
 */
class NotFoundException extends HttpException
{
    /**
     * NotFoundException constructor.
     *
     * @param null $message
     * @param int $code
     * @param \Exception|null $previous
     */
    public function __construct($message = null, $code = 0, \Exception $previous = null)
    {
        parent::__construct(404, $message, $code, $previous);
    }
}
