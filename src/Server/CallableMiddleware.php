<?php
/**
 * @link http://www.atomframework.net/
 * @copyright Copyright (c) 2017 Safarov Alisher
 * @license https://github.com/atomwares/atom-http/blob/master/LICENSE (MIT License)
 */

namespace Atom\Http\Server;

use Atom\Http\Factory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
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
    private $middleware;

    /**
     * CallableMiddleware constructor.
     * @param callable $middleware
     */
    public function __construct(callable $middleware)
    {
        $this->middleware = $middleware;
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = ($this->middleware)($request, $handler);

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
