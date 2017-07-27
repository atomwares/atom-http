<?php
/**
 * @link http://www.atomframework.net/
 * @copyright Copyright (c) 2017 Safarov Alisher
 * @license https://github.com/atomwares/atom-http/blob/master/LICENSE (MIT License)
 */

namespace Atom\Http\Exception;

use Atom\Http\HttpException;

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
