<?php
/**
 * @link http://www.atomframework.net/
 * @copyright Copyright (c) 2017 Safarov Alisher
 * @license https://github.com/atomwares/atom-http/blob/master/LICENSE (MIT License)
 */

namespace Atom\Http\Factory;

use Atom\Http\Message\ServerRequest;
use Atom\Http\Message\Uri;
use Interop\Http\Factory\ServerRequestFactoryInterface;
use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class ServerRequestFactory
 *
 * @package Atom\Http\Factory
 */
class ServerRequestFactory implements ServerRequestFactoryInterface
{
    /**
     * @var UriFactory
     */
    protected $uriFactory;
    /**
     * @var StreamFactory
     */
    protected $streamFactory;

    /**
     * @var UploadedFileFactory
     */
    protected $uploadedFileFactory;

    /**
     * @inheritdoc
     */
    public function createServerRequest($method, $uri)
    {
        if (! $uri instanceof UriInterface) {
            if (! $this->uriFactory) {
                $this->uriFactory = new UriFactory();
            }

            $uri = $this->uriFactory->createUri($uri);
        }

        return new ServerRequest($method, $uri, $_SERVER);
    }

    /**
     * @inheritdoc
     */
    public function createServerRequestFromArray(array $server)
    {
        if (! $this->uriFactory) {
            $this->uriFactory = new UriFactory();
        }

        return new ServerRequest(
            static::resolveMethod($server),
            $this->uriFactory->createUri(static::composeUri($server)),
            $server
        );
    }

    /**
     * @return ServerRequestInterface
     */
    public function createServerRequestFromGlobal()
    {
        if (! $this->streamFactory) {
            $this->streamFactory = new StreamFactory();
        }

        if (! $this->uploadedFileFactory) {
            $this->uploadedFileFactory = new UploadedFileFactory();
        }

        $request = $this->createServerRequestFromArray($_SERVER)
            ->withBody(
                $this->streamFactory->createStreamFromResource(fopen('php://input', 'r'))
            )
            ->withQueryParams($_GET)
            ->withParsedBody($_POST)
            ->withCookieParams($_COOKIE)
            ->withUploadedFiles(
                $this->uploadedFileFactory->createUploadedFilesFromArray($_FILES)
            );

        foreach (getallheaders() as $name => $value) {
            $request = $request->withHeader($name, $value);
        }

        return $request;
    }

    /**
     * @param array $server
     *
     * @return string
     */
    protected static function composeUri($server)
    {
        return Uri::composeComponents(
            static::resolveScheme($server),
            static::resolveAuthority($server),
            static::resolvePath($server),
            static::resolveQuery($server)
        );
    }

    /**
     * @param array $server
     *
     * @throws InvalidArgumentException
     * @return string
     */
    protected static function resolveMethod($server)
    {
        if (isset($server['REQUEST_METHOD'])) {
            return $server['REQUEST_METHOD'];
        }

        throw new InvalidArgumentException(
            'Undefined HTTP method in server params'
        );
    }

    /**
     * @param array $server
     *
     * @return string
     */
    protected static function resolveScheme($server)
    {
        return ! isset($server['HTTPS']) || $server['HTTPS'] === 'off' ? 'http' : 'https';
    }

    /**
     * @param array $server
     *
     * @return string
     */
    protected static function resolveAuthority($server)
    {
        $authority = static::resolveHostPort($server);

        if (($userInfo = static::resolveUserInfo($server)) !== '') {
            $authority = $userInfo . '@' . $authority;
        }

        return $authority;
    }

    /**
     * @param array $server
     *
     * @throws InvalidArgumentException
     * @return string
     */
    protected static function resolveHostPort($server)
    {
        if (isset($server['HTTP_HOST'])) {
            $host = $server['HTTP_HOST'];
        } elseif (isset($server['SERVER_NAME'])) {
            $host = $server['SERVER_NAME'];
        } else {
            throw new InvalidArgumentException(
                'Undefined HTTP host in server params'
            );
        }

        $port = null;

        if (isset($server['SERVER_PORT'])) {
            $port = $server['SERVER_PORT'];
        }

        if (preg_match('/:(\d+)$/', $host, $matches)) {
            $host = rtrim($host, ":{$matches[1]}");
            $port = (int)$matches[1];
        }

        return $host . ($port !== null ? ":$port" : '');
    }

    /**
     * @param array $server
     *
     * @return string
     */
    protected static function resolveUserInfo($server)
    {
        $userInfo = isset($server['PHP_AUTH_USER']) ? $server['PHP_AUTH_USER'] : '';
        $userInfo .= (isset($server['PHP_AUTH_PW']) ? ':' . $server['PHP_AUTH_PW'] : '');

        return $userInfo;
    }

    /**
     * @param array $server
     *
     * @return string
     */
    protected static function resolvePath($server)
    {
        return isset($server['REQUEST_URI']) ? parse_url($server['REQUEST_URI'], PHP_URL_PATH) : '';
    }

    /**
     * @param array $server
     *
     * @return string
     */
    protected static function resolveQuery($server)
    {
        return isset($server['QUERY_STRING']) ? $server['QUERY_STRING'] : '';
    }
}
