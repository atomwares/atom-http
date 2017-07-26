<?php

namespace Atom\Http\Factory;

use Atom\Http\Message\Uri;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;

/**
 * Class Factory
 *
 * @package Atom\Http\Factory
 */
abstract class Factory
{
    /**
     * @var UriFactory
     */
    protected static $uriFactory;
    /**
     * @var RequestFactory
     */
    protected static $requestFactory;
    /**
     * @var ServerRequestFactory
     */
    protected static $serverRequestFactory;
    /**
     * @var ResponseFactory
     */
    protected static $responseFactory;
    /**
     * @var StreamFactory
     */
    protected static $streamFactory;
    /**
     * @var UploadedFileFactory
     */
    protected static $uploadedFileFactory;

    /**
     * @param string $uri
     *
     * @return Uri
     */
    public static function createUri($uri = '')
    {
        if (! static::$uriFactory) {
            static::$uriFactory = new UriFactory();
        }

        return static::$uriFactory->createUri($uri);
    }

    /**
     * @param $method
     * @param $uri
     *
     * @return RequestInterface
     */
    public static function createRequest($method, $uri)
    {
        if (! static::$requestFactory) {
            static::$requestFactory = new RequestFactory();
        }

        return static::$requestFactory->createRequest($method, $uri);
    }

    /**
     * @param $method
     * @param $uri
     *
     * @return ServerRequestInterface
     */
    public static function createServerRequest($method, $uri)
    {
        if (! static::$serverRequestFactory) {
            static::$serverRequestFactory = new ServerRequestFactory();
        }

        return static::$serverRequestFactory->createServerRequest($method, $uri);
    }

    /**
     * @param array $server
     *
     * @return ServerRequestInterface
     */
    public static function createServerRequestFromArray(array $server)
    {
        if (! static::$serverRequestFactory) {
            static::$serverRequestFactory = new ServerRequestFactory();
        }

        return static::$serverRequestFactory->createServerRequestFromArray($server);
    }

    /**
     * @return ServerRequestInterface
     */
    public static function createServerRequestFromGlobal()
    {
        if (! static::$serverRequestFactory) {
            static::$serverRequestFactory = new ServerRequestFactory();
        }

        return static::$serverRequestFactory->createServerRequestFromGlobal();
    }

    /**
     * @param int $code
     *
     * @return ResponseInterface
     */
    public static function createResponse($code = 200)
    {
        if (! static::$responseFactory) {
            static::$responseFactory = new ResponseFactory();
        }

        return static::$responseFactory->createResponse($code);
    }

    /**
     * @param string $content
     *
     * @return StreamInterface
     */
    public static function createStream($content = '')
    {
        if (! static::$streamFactory) {
            static::$streamFactory = new StreamFactory();
        }

        return static::$streamFactory->createStream($content);
    }

    /**
     * @param string $filename
     * @param string $mode
     *
     * @return StreamInterface
     */
    public static function createStreamFromFile($filename, $mode = 'r')
    {
        if (! static::$streamFactory) {
            static::$streamFactory = new StreamFactory();
        }

        return static::$streamFactory->createStreamFromFile($filename, $mode);
    }

    /**
     * @param resource $resource
     *
     * @return StreamInterface
     */
    public static function createStreamFromResource($resource)
    {
        if (! static::$streamFactory) {
            static::$streamFactory = new StreamFactory();
        }

        return static::$streamFactory->createStreamFromResource($resource);
    }

    /**
     * @param string|resource $file
     * @param integer $size
     * @param integer $error
     * @param string $clientFilename
     * @param string $clientMediaType
     *
     * @return UploadedFileInterface
     */
    public static function createUploadedFile(
        $file,
        $size = null,
        $error = UPLOAD_ERR_OK,
        $clientFilename = null,
        $clientMediaType = null
    ) {
        if (! static::$uploadedFileFactory) {
            static::$uploadedFileFactory = new UploadedFileFactory();
        }

        return static::$uploadedFileFactory->createUploadedFile(
            $file,
            $size,
            $error,
            $clientFilename,
            $clientMediaType
        );
    }

    /**
     * @param array $files
     *
     * @return UploadedFileInterface[]
     */
    public static function createUploadedFilesFromArray(array $files)
    {
        if (! static::$uploadedFileFactory) {
            static::$uploadedFileFactory = new UploadedFileFactory();
        }

        return static::$uploadedFileFactory->createUploadedFilesFromArray($files);
    }
}
