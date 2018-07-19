<?php
/**
 * Created by PhpStorm.
 * User: abdujabbor
 * Date: 7/19/18
 * Time: 11:11 AM
 */

namespace Atom\Tests;

use Atom\Http\Factory\UriFactory;
use Atom\Http\Message\Uri;
use PHPUnit\Framework\TestCase;

class UriFactoryTest extends TestCase
{
    public function testCreateUri()
    {
        $uriFactory = new UriFactory();

        $uri =$uriFactory->createUri("http://localhost:8000/home?account=somename&status=ok");


        $this->assertEquals(true, $uri instanceof Uri);

        $this->assertEquals(8000, $uri->getPort());

        $this->assertEquals('/home', $uri->getPath());

        $this->assertEquals("localhost", $uri->getHost());

        $this->assertEquals("account=somename&status=ok", $uri->getQuery());
    }
}
