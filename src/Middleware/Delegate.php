<?php
/**
 * @link http://www.atomframework.net/
 * @copyright Copyright (c) 2017 Safarov Alisher
 * @license https://github.com/atomwares/atom-http/blob/master/LICENSE (MIT License)
 */

namespace Atom\Http\Middleware;

use Atom\Http\Factory\ResponseFactory;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class Delegate
 *
 * @package Atom\Middleware
 */
class Delegate implements DelegateInterface
{
    /**
     * @var CallableMiddleware|MiddlewareInterface|null
     */
    protected $middleware;
    /**
     * @var DelegateInterface
     */
    protected $next;
    /**
     * @var ResponseFactory
     */
    protected $responseFactory;

    /**
     * Delegate constructor.
     *
     * @param MiddlewareInterface|callable|null $middleware
     * @param DelegateInterface|null $next
     */
    public function __construct(
        $middleware = null,
        DelegateInterface $next = null
    ) {
        if (is_callable($middleware)) {
            $middleware = new CallableMiddleware($middleware);
        }

        if ($middleware !== null &&
            ! $middleware instanceof MiddlewareInterface
        ) {
            throw new InvalidArgumentException(sprintf(
                'Invalid middleware provided; must be an instance of %s, received %s',
                MiddlewareInterface::class,
                (is_object($middleware) ? get_class($middleware) : gettype($middleware))
            ));
        }

        $this->middleware = $middleware;
        $this->next = $next;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request)
    {
        if (! $this->responseFactory) {
            $this->responseFactory = new ResponseFactory();
        }

        $response = $this->responseFactory->createResponse();

        if ($this->middleware && $this->next) {
            $response = $this->middleware->process(
                $request,
                $this->next
            );
        }

        return $response;
    }
}
