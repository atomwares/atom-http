<?php
/**
 * @link http://www.atomframework.net/
 * @copyright Copyright (c) 2017 Safarov Alisher
 * @license https://github.com/atomwares/atom-http/blob/master/LICENSE (MIT License)
 */

namespace Atom\Http\Factory;

use Atom\Http\Message\Request;
use Interop\Http\Factory\RequestFactoryInterface;

/**
 * Class RequestFactory
 *
 * @package Atom\Http\Factory
 */
class RequestFactory implements RequestFactoryInterface
{
    /**
     * @var UriFactory
     */
    protected $uriFactory;

    /**
     * @inheritdoc
     */
    public function createRequest($method, $uri)
    {
        if (is_string($uri)) {
            if (! $this->uriFactory) {
                $this->uriFactory = new UriFactory();
            }

            $uri = $this->uriFactory->createUri($uri);
        }

        return new Request($method, $uri);
    }
}
