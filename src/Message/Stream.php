<?php
/**
 * @link http://www.atomframework.net/
 * @copyright Copyright (c) 2017 Safarov Alisher
 * @license https://github.com/atomwares/atom-http/blob/master/LICENSE (MIT License)
 */

namespace Atom\Http\Message;

use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;
use RuntimeException;

/**
 * Class Stream
 *
 * @package Atom\Http\Message
 */
class Stream implements StreamInterface
{
    /**
     * @var resource
     */
    protected $resource;
    /**
     * @var int
     */
    protected $size;
    /**
     * @var bool
     */
    protected $readable;
    /**
     * @var bool
     */
    protected $writable;
    /**
     * @var bool
     */
    protected $seekable;

    /**
     * @var array
     */
    protected static $modes = [
        'readable' => [
            'r', 'rb', 'rt', 'r+', 'r+b', 'r+t',
            'w+', 'w+b', 'w+t',
            'a+', 'a+b', 'a+t',
            'x+', 'x+b', 'x+t',
            'c+', 'c+b', 'c+t',
        ],
        'writable' => [
            'r+', 'r+b', 'r+t',
            'w', 'wb', 'wt', 'w+', 'w+b', 'w+t',
            'a', 'ab', 'at', 'a+t', 'a+b', 'a+t',
            'x', 'xb', 'xt', 'x+', 'x+b', 'x+t',
            'c', 'cb', 'ct', 'c+', 'c+b', 'c+t',
        ],
    ];

    /**
     * Stream constructor.
     *
     * @param $resource
     */
    public function __construct($resource)
    {
        if (! is_resource($resource)) {
            throw new InvalidArgumentException(sprintf(
                'Invalid stream provided; must be a resource argument; received %s',
                (is_object($resource) ? get_class($resource) : gettype($resource))
            ));
        }

        $this->resource = $resource;

        if ($meta = $this->getMetadata()) {
            $this->seekable = $meta['seekable'];
            $this->readable = in_array($meta['mode'], static::$modes['readable']);
            $this->writable = in_array($meta['mode'], static::$modes['writable']);
        }
    }


    /**
     * @inheritdoc
     */
    public function __toString()
    {
        try {
            $this->rewind();

            return $this->getContents();
        } catch (RuntimeException $e) {
            return '';
        }
    }

    /**
     * @inheritdoc
     */
    public function close()
    {
        fclose($this->detach());
    }

    /**
     * @inheritdoc
     */
    public function detach()
    {
        $resource = $this->resource;
        unset($this->resource);

        $this->size = null;
        $this->readable = $this->writable = $this->seekable = false;

        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function getSize()
    {
        if ($this->size === null) {
            $this->size = fstat($this->resource)['size'];
        }

        return $this->size;
    }

    /**
     * @inheritdoc
     */
    public function tell()
    {
        $position = ftell($this->resource);

        if ($position === false) {
            throw new RuntimeException('Error occurred during tell operation');
        }

        return $position;
    }

    /**
     * @inheritdoc
     */
    public function eof()
    {
        return feof($this->resource);
    }

    /**
     * @inheritdoc
     */
    public function isSeekable()
    {
        return $this->seekable;
    }

    /**
     * @inheritdoc
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        if (! $this->isSeekable()) {
            throw new RuntimeException('Stream is not seekable');
        }

        if (fseek($this->resource, $offset, $whence) === false) {
            throw new RuntimeException('Error seeking within stream');
        }
    }

    /**
     * @inheritdoc
     */
    public function rewind()
    {
        if (! $this->isSeekable() || rewind($this->resource) === false) {
            throw new RuntimeException('Could not rewind stream');
        }
    }

    /**
     * @inheritdoc
     */
    public function isWritable()
    {
        return $this->writable;
    }

    /**
     * @inheritdoc
     */
    public function write($string)
    {
        if (! $this->isWritable()) {
            throw new RuntimeException('Stream is not writable');
        }

        $written = fwrite($this->resource, $string);

        if ($written === false) {
            throw new RuntimeException('Error writing to stream');
        }

        $this->size = null;

        return $written;
    }

    /**
     * @inheritdoc
     */
    public function isReadable()
    {
        return $this->readable;
    }

    /**
     * @inheritdoc
     */
    public function read($length)
    {
        if (! $this->isReadable()) {
            throw new RuntimeException('Stream is not readable');
        }

        $contents = fread($this->resource, $length);

        if ($contents === false) {
            throw new RuntimeException('Error reading from stream');
        }

        return $contents;
    }

    /**
     * @inheritdoc
     */
    public function getContents()
    {
        if (! $this->isReadable()) {
            throw new RuntimeException('Stream is not readable');
        }

        $contents = stream_get_contents($this->resource);

        if ($contents === false) {
            throw new RuntimeException('Error reading from stream');
        }

        return $contents;
    }

    /**
     * @inheritdoc
     */
    public function getMetadata($key = null)
    {
        $meta = stream_get_meta_data($this->resource);

        if ($key === null) {
            return $meta;
        }

        return isset($meta[$key]) ? $meta[$key] : null;
    }
}
