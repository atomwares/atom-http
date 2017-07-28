<?php
/**
 * @link http://www.atomframework.net/
 * @copyright Copyright (c) 2017 Safarov Alisher
 * @license https://github.com/atomwares/atom-http/blob/master/LICENSE (MIT License)
 */

namespace Atom\Http\Middleware;

use Atom\Http\Factory;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use UnexpectedValueException;

/**
 * Class CallableMiddleware
 *
 * @package Atom\Middleware
 */
class CallableMiddleware implements MiddlewareInterface
{
    /**
     * @var callable
     */
    private $handler;

    /**
     * CallableMiddleware constructor.
     *
     * @param callable $handler
     */
    public function __construct(callable $handler)
    {
        $this->handler = $handler;
    }

    /**
     * @param ServerRequestInterface $request
     * @param DelegateInterface $delegate
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $response = ($this->handler)($request, $delegate);

        if (is_scalar($response) || (is_object($response) && method_exists($response, '__toString'))) {
            $response = Factory::createResponse()
                ->withBody(Factory::createStream($response));
        }

        if (! $response instanceof ResponseInterface) {
            throw new UnexpectedValueException(sprintf(
                'Invalid response value; must be a scalar or instance of %s, received %s',
                ResponseInterface::class,
                (is_object($response) ? get_class($response) : gettype($response))
            ));
        }

        return $response;
    }
}
