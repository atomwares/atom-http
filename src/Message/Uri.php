<?php
/**
 * @link http://www.atomframework.net/
 * @copyright Copyright (c) 2017 Safarov Alisher
 * @license https://github.com/atomwares/atom-http/blob/master/LICENSE (MIT License)
 */

namespace Atom\Http\Message;

use InvalidArgumentException;
use Psr\Http\Message\UriInterface;

/**
 * Class Uri
 *
 * @package Atom\Http\Message
 */
class Uri implements UriInterface
{
    /**
     *
     */
    const CHAR_UNRESERVED = '\w\-\.~\pL';
    /**
     *
     */
    const CHAR_SUB_DELIMS = '!\$&\'\(\)\*\+,;=';

    /**
     * @var string
     */
    protected $scheme = '';
    /**
     * @var string
     */
    protected $host = '';
    /**
     * @var int|null
     */
    protected $port;
    /**
     * @var string
     */
    protected $path = '';
    /**
     * @var string
     */
    protected $query = '';
    /**
     * @var string
     */
    protected $fragment = '';
    /**
     * @var string|null
     */
    protected $user;
    /**
     * @var string|null
     */
    protected $password;

    /**
     * @var array
     */
    protected static $standardSchemes = [
        'http'  => 80,
        'https' => 443,
    ];

    /**
     * Uri constructor.
     *
     * @param string $scheme
     * @param string $host
     * @param int|null $port
     * @param string $path
     * @param string $query
     * @param string $fragment
     * @param string|null $user
     * @param string|null $password
     */
    public function __construct(
        $scheme = '',
        $host = '',
        $port = null,
        $path = '/',
        $query = '',
        $fragment = '',
        $user = null,
        $password = null
    ) {
        $this->scheme = $this->filterScheme($scheme);
        $this->host = $this->filterHost($host);
        $this->port = $this->filterPort($port);
        $this->path = $this->filterPath($path);
        $this->query = $this->filterQuery($query);
        $this->fragment = $this->filterQuery($fragment);
        $this->user = $this->filterUser($user);
        $this->password = $this->filterPassword($password);
    }

    /**
     * @inheritdoc
     */
    public function __toString()
    {
        return static::composeComponents(
            $this->scheme,
            $this->getAuthority(),
            $this->path,
            $this->query,
            $this->fragment
        );
    }

    /**
     * @inheritdoc
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * @inheritdoc
     */
    public function getAuthority()
    {
        $authority = $this->host;

        if (($userInfo = $this->getUserInfo()) !== '') {
            $authority = $userInfo . '@' . $authority;
        }

        if ($this->port !== null) {
            $authority .= ':' . $this->port;
        }

        return $authority;
    }

    /**
     * @inheritdoc
     */
    public function getUserInfo()
    {
        $userInfo = '';

        if ($this->user !== null) {
            $userInfo = $this->user;
        }

        if ($this->password !== null) {
            $userInfo .= ':' . $this->password;
        }

        return $userInfo;
    }

    /**
     * @inheritdoc
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @inheritdoc
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @inheritdoc
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @inheritdoc
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @inheritdoc
     */
    public function getFragment()
    {
        return $this->fragment;
    }

    /**
     * @inheritdoc
     */
    public function withScheme($scheme)
    {
        $scheme = static::filterScheme($scheme);
        $clone = clone $this;
        $clone->scheme = $scheme;

        return $clone;
    }

    /**
     * @inheritdoc
     */
    public function withUserInfo($user, $password = null)
    {
        $user = static::filterUser($user);
        $password = static::filterPassword($password);
        $clone = clone $this;
        $clone->user = $user;
        $clone->password = $password;

        return $clone;
    }

    /**
     * @inheritdoc
     */
    public function withHost($host)
    {
        $host = static::filterHost($host);
        $clone = clone $this;
        $clone->host = $host;

        return $clone;
    }

    /**
     * @inheritdoc
     */
    public function withPort($port)
    {
        $port = static::filterPort($port);
        $clone = clone $this;
        $clone->port = $port;

        return $clone;
    }

    /**
     * @inheritdoc
     */
    public function withPath($path)
    {
        $path = static::filterPath($path);
        $clone = clone $this;
        $clone->path = $path;

        return $clone;
    }

    /**
     * @inheritdoc
     */
    public function withQuery($query)
    {
        $query = static::filterQuery($query);
        $clone = clone $this;
        $clone->query = $query;

        return $clone;
    }

    /**
     * @inheritdoc
     */
    public function withFragment($fragment)
    {
        $fragment = static::filterFragment($fragment);
        $clone = clone $this;
        $clone->fragment = $fragment;

        return $clone;
    }

    /**
     * @param string $scheme
     * @param string $authority
     * @param string $path
     * @param string $query
     * @param string $fragment
     *
     * @return string
     */
    public static function composeComponents($scheme, $authority, $path, $query, $fragment = '')
    {
        $uri = '';

        if ($scheme !== '') {
            $uri .= $scheme . ':';
        }

        if ($authority !== '') {
            $uri .= '//' . $authority;
        }

        $uri .= $path;

        if ($query !== '') {
            $uri .= '?' . $query;
        }

        if ($fragment !== '') {
            $uri .= '#' . $fragment;
        }

        return $uri;
    }

    /**
     * @param $scheme
     *
     * @return string
     */
    protected static function filterScheme($scheme)
    {
        if (! is_string($scheme)) {
            throw new InvalidArgumentException(sprintf(
                'Invalid scheme provided; must be a string argument; ; received %s',
                (is_object($scheme) ? get_class($scheme) : gettype($scheme))
            ));
        }

        $scheme = str_replace('://', '', strtolower($scheme));

        if (! isset(static::$standardSchemes[$scheme])) {
            throw new InvalidArgumentException(sprintf(
                'Unsupported scheme "%s"; must be any empty string or in the set (%s)',
                $scheme,
                implode(', ', array_keys(static::$standardSchemes))
            ));
        }

        return $scheme;
    }

    /**
     * @param string|null $user
     *
     * @return string|null
     */
    protected static function filterUser($user)
    {
        if ($user !== null && ! is_string($user)) {
            throw new InvalidArgumentException(sprintf(
                'Invalid user-info; user must be a string argument; received %s',
                (is_object($user) ? get_class($user) : gettype($user))
            ));
        }

        return $user;
    }

    /**
     * @param string|null $password
     *
     * @return string|null
     */
    protected static function filterPassword($password)
    {
        if ($password !== null && ! is_string($password)) {
            throw new InvalidArgumentException(sprintf(
                'Invalid user-info; password must be a string argument; received %s',
                (is_object($password) ? get_class($password) : gettype($password))
            ));
        }

        return $password;
    }

    /**
     * @param string $host
     *
     * @return string
     */
    protected static function filterHost($host)
    {
        if (! is_string($host)) {
            throw new InvalidArgumentException(sprintf(
                'Invalid host; must be a string argument; received %s',
                (is_object($host) ? get_class($host) : gettype($host))
            ));
        }

        return $host;
    }

    /**
     * @param int|null $port
     *
     * @return int|null
     */
    protected static function filterPort($port)
    {
        if ($port !== null) {
            if (! is_integer($port)) {
                throw new InvalidArgumentException(sprintf(
                    'Invalid port provided; must be an integer or null argument; received %s',
                    (is_object($port) ? get_class($port) : gettype($port))
                ));
            }

            if ($port < 1 || $port > 65535) {
                throw new InvalidArgumentException(sprintf(
                    'Invalid port "%d" specified; must be a valid TCP/UDP port',
                    $port
                ));
            }

            if (in_array($port, static::$standardSchemes)) {
                $port = null;
            }
        }

        return $port;
    }

    /**
     * @param string $path
     *
     * @return string
     */
    protected static function filterPath($path)
    {
        if (! is_string($path)) {
            throw new InvalidArgumentException(sprintf(
                'Invalid path provided; must be a string argument; received %s',
                (is_object($path) ? get_class($path) : gettype($path))
            ));
        }

        if ($path && $path[0] === '/') {
            $path = '/' . ltrim($path, '/');
        }

        return preg_replace_callback(
            '/(?:[^' . self::CHAR_UNRESERVED . ':@&=\+\$,\/;%]+|%(?![A-Fa-f0-9]{2}))/u',
            function (array $matches) {
                rawurlencode($matches[0]);
            },
            $path
        );
    }

    /**
     * @param string $query
     *
     * @return string
     */
    protected static function filterQuery($query)
    {
        if (! is_string($query)) {
            throw new InvalidArgumentException(sprintf(
                'Invalid query provided; must be a string argument',
                (is_object($query) ? get_class($query) : gettype($query))
            ));
        }

        return static::filterQueryOrFragment(ltrim($query, '?'));
    }

    /**
     * @param string $fragment
     *
     * @return string
     */
    protected static function filterFragment($fragment)
    {
        if (! is_string($fragment)) {
            throw new InvalidArgumentException(sprintf(
                'Invalid fragment provided; must be a string argument; received %s',
                (is_object($fragment) ? get_class($fragment) : gettype($fragment))
            ));
        }

        return static::filterQueryOrFragment(ltrim($fragment, '#'));
    }

    /**
     * @param string $value
     *
     * @return string
     */
    protected static function filterQueryOrFragment($value)
    {
        return preg_replace_callback(
            '/(?:[^' . self::CHAR_UNRESERVED . self::CHAR_SUB_DELIMS . '%:@\/\?]+|%(?![A-Fa-f0-9]{2}))/u',
            function (array $matches) {
                rawurlencode($matches[0]);
            },
            $value
        );
    }
}
