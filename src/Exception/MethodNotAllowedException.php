<?php
/**
 * @link http://www.atomframework.net/
 * @copyright Copyright (c) 2017 Safarov Alisher
 * @license https://github.com/atomwares/atom-http/blob/master/LICENSE (MIT License)
 */

namespace Atom\Http\Exception;

use Atom\Http\HttpException;

/**
 * Class MethodNotAllowedException
 *
 * @package Atom\Http\Exception
 */
class MethodNotAllowedException extends HttpException
{
    /**
     * @var array
     */
    protected $allowed;

    /**
     * MethodNotAllowedException constructor.
     *
     * @param array $allowed
     * @param null $message
     * @param int $code
     * @param \Exception|null $previous
     */
    public function __construct(array $allowed, $message = null, $code = 0, \Exception $previous = null)
    {
        $this->allowed = $allowed;

        parent::__construct(405, $message, $code, $previous);
    }

    /**
     * @return array
     */
    public function getAllowed()
    {
        return $this->allowed;
    }
}
