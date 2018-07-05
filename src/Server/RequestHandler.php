<?php
/**
 * @link http://www.atomframework.net/
 * @copyright Copyright (c) 2017 Safarov Alisher
 * @license https://github.com/atomwares/atom-http/blob/master/LICENSE (MIT License)
 */

namespace Atom\Http\Server;

use Atom\Http\Factory\ResponseFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use InvalidArgumentException;

/**
 * Class RequestHandler
 *
 * @package Atom\Middleware
 */
class RequestHandler implements RequestHandlerInterface
{
    /**
     * @var CallableMiddleware|MiddlewareInterface
     */
    protected $middleware;
    /**
     * @var RequestHandlerInterface
     */
    protected $nextHandler;
    /**
     * @var ResponseFactory
     */
    protected $responseFactory;

    /**
     * RequestHandler constructor.
     *
     * @param MiddlewareInterface|callable $middleware
     * @param RequestHandlerInterface $nextHandler
     */
    public function __construct(
        $middleware,
        RequestHandlerInterface $nextHandler
    ) {
        if (is_callable($middleware)) {
            $middleware = new CallableMiddleware($middleware);
        }

        if ($middleware !== null && ! $middleware instanceof MiddlewareInterface) {
            throw new InvalidArgumentException(sprintf(
                'Invalid middleware provided; must be an instance of %s, received %s',
                MiddlewareInterface::class,
                (is_object($middleware) ? get_class($middleware) : gettype($middleware))
            ));
        }

        $this->middleware = $middleware;
        $this->nextHandler = $nextHandler;
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->middleware->process($request, $this->nextHandler);
    }
}
