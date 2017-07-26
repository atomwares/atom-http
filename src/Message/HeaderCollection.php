<?php

namespace Atom\Http\Message;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;

/**
 * Class HeaderCollection
 *
 * @package Atom\Http\Message
 */
class HeaderCollection implements IteratorAggregate, ArrayAccess, Countable
{
    /**
     * @var array
     */
    protected $headers = [];

    /**
     * HeaderCollection constructor.
     *
     * @param $headers
     */
    public function __construct($headers)
    {
        $this->headers = $headers;
    }

    /**
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->headers);
    }

    /**
     * @param string $offset Case-insensitive header field name.
     * @param string|string[] $value Header value(s).
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * @param string $offset
     *
     * @return string|string[]
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * @param string $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * @param string $offset
     */
    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->headers);
    }


    /**
     * @param string $name Case-insensitive header field name.
     * @param string|string[] $value Header value(s).
     */
    public function set($name, $value)
    {
        $originalName = $this->getOriginalName($name);

        $name = $originalName !== false ? $originalName : $name;

        $this->push($name, $value);
    }

    /**
     * @param string $name
     * @param string|string[] $value
     */
    public function add($name, $value)
    {
        if ($originalName = $this->getOriginalName($name) !== false) {
            $this->headers[$originalName][] = $value;
        } else {
            $this->push($name, $value);
        }
    }

    /**
     * @param string $name
     *
     * @return string|string[]
     */
    public function get($name)
    {
        if ($this->has($name)) {
            return $this->headers[$name];
        }

        return [];
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->headers;
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function has($name)
    {
        return $this->getOriginalName($name) !== false;
    }

    /**
     * @param string $name
     */
    public function remove($name)
    {
        if ($this->has($name)) {
            unset($this->headers[$name]);
        }
    }

    /**
     * @param $name
     *
     * @return bool|string
     */
    protected function getOriginalName($name)
    {
        foreach (array_keys($this->headers) as $key) {
            if (strtolower($key) === strtolower($name)) {
                return $key;
            }
        }

        return false;
    }

    /**
     * @param string $name
     * @param string|string[] $value
     */
    protected function push($name, $value)
    {
        if (strtolower($name) === 'host') {
            $this->headers = [$name => (array)$value] + $this->headers;
        } else {
            $this->headers[$name] = (array)$value;
        }
    }
}
