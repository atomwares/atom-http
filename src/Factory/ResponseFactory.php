<?php

namespace Atom\Http\Factory;

use Atom\Http\Message\Response;
use Fig\Http\Message\StatusCodeInterface;
use Interop\Http\Factory\ResponseFactoryInterface;

/**
 * Class ResponseFactory
 *
 * @package Atom\Http\Factory
 */
class ResponseFactory implements ResponseFactoryInterface, StatusCodeInterface
{
    /**
     * @inheritdoc
     */
    public function createResponse($code = self::STATUS_OK)
    {
        return new Response($code);
    }
}
