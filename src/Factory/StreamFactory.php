<?php

namespace Atom\Http\Factory;

use Atom\Http\Message\Stream;
use Interop\Http\Factory\StreamFactoryInterface;

/**
 * Class StreamFactory
 *
 * @package Atom\Http\Factory
 */
class StreamFactory implements StreamFactoryInterface
{
    /**
     * @inheritdoc
     */
    public function createStream($content = '')
    {
        $resource = fopen('php://temp', 'r+');
        fwrite($resource, $content);
        rewind($resource);

        return new Stream($resource);
    }

    /**
     * @inheritdoc
     */
    public function createStreamFromFile($filename, $mode = 'r')
    {
        return new Stream(fopen($filename, $mode));
    }

    /**
     * @inheritdoc
     */
    public function createStreamFromResource($resource)
    {
        return new Stream($resource);
    }
}
