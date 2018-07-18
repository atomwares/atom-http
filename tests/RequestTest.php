<?php
/**
 * Created by PhpStorm.
 * User: abdujabbor
 * Date: 7/18/18
 * Time: 3:20 PM
 */

namespace Atom\Tests;




use Atom\Http\Message\Request;
use Atom\Http\Message\Uri;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    public function testMethods() {
        $uri = new Uri("https", "localhost", null, "/");
        $request = new Request("get", $uri);
        $this->assertEquals(true, $request->isGet());


        $request = new Request("post", $uri);
        $this->assertEquals(false, $request->isGet());

        $request = new Request("options", $uri);
        $this->assertEquals(true, $request->isOptions());
    }
}