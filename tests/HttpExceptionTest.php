<?php
/**
 * Created by PhpStorm.
 * User: abdujabbor
 * Date: 7/19/18
 * Time: 11:29 AM
 */

namespace Atom\Tests;


use Atom\Http\HttpException;
use Atom\Http\Message\StatusCode;
use PHPUnit\Framework\TestCase;

class HttpExceptionTest extends TestCase
{
    public function testStatusCodes() {
        $httpException = new HttpException(404, "Not Found");
        $this->assertEquals(404, $httpException->getStatusCode());
        $this->assertEquals($httpException->getReasonPhrase(), StatusCode::getReasonPhrase(404));
    }
}