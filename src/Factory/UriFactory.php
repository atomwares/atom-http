<?php
/**
 * @link http://www.atomframework.net/
 * @copyright Copyright (c) 2017 Safarov Alisher
 * @license https://github.com/atomwares/atom-http/blob/master/LICENSE (MIT License)
 */

namespace Atom\Http\Factory;

use Atom\Http\Message\Uri;
use Interop\Http\Factory\UriFactoryInterface;
use InvalidArgumentException;

/**
 * Class UriFactory
 *
 * @package Atom\Http\Factory
 */
class UriFactory implements UriFactoryInterface
{
    /**
     * @inheritdoc
     */
    public function createUri($uri = '')
    {
        $parts = parse_url($uri);

        if ($parts === false) {
            throw new InvalidArgumentException(
                'The source URI string appears to be malformed'
            );
        }

        $scheme = isset($parts['scheme']) ? $parts['scheme'] : '';
        $host = isset($parts['host']) ? $parts['host'] : '';
        $port = isset($parts['port']) ? $parts['port'] : null;
        $path = isset($parts['path']) ? $parts['path'] : '';
        $query = isset($parts['query']) ? $parts['query'] : '';
        $fragment = isset($parts['fragment']) ? $parts['fragment'] : '';
        $user = isset($parts['user']) ? $parts['user'] : null;
        $password = isset($parts['pass']) ? $parts['pass'] : null;

        return new Uri($scheme, $host, $port, $path, $query, $fragment, $user, $password);
    }
}
