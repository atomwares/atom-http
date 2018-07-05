<?php
/**
 * @link http://www.atomframework.net/
 * @copyright Copyright (c) 2017 Safarov Alisher
 * @license https://github.com/atomwares/atom-http/blob/master/LICENSE (MIT License)
 */

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

        return $this->createStreamFromResource($resource);
    }

    /**
     * @inheritdoc
     */
    public function createStreamFromFile($filename, $mode = 'r')
    {
        return $this->createStreamFromResource(fopen($filename, $mode));
    }

    /**
     * @inheritdoc
     */
    public function createStreamFromResource($resource)
    {
        return new Stream($resource);
    }
}
