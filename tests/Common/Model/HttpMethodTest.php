<?php

namespace AlephTools\DDD\Tests\Common\Model;

use AlephTools\DDD\Common\Model\HttpMethod;
use PHPUnit\Framework\TestCase;

class HttpMethodTest extends TestCase
{
    public function testIdentification(): void
    {
        $this->assertTrue(HttpMethod::GET()->isGet());
        $this->assertTrue(HttpMethod::POST()->isPost());
        $this->assertTrue(HttpMethod::PUT()->isPut());
        $this->assertTrue(HttpMethod::PATCH()->isPatch());
        $this->assertTrue(HttpMethod::DELETE()->isDelete());
        $this->assertTrue(HttpMethod::HEAD()->isHead());
        $this->assertTrue(HttpMethod::OPTIONS()->isOptions());
        $this->assertTrue(HttpMethod::CONNECT()->isConnect());
        $this->assertTrue(HttpMethod::TRACE()->isTrace());
    }

    public function testHasBody(): void
    {
        $this->assertFalse(HttpMethod::GET()->hasBody());
        $this->assertTrue(HttpMethod::POST()->hasBody());
        $this->assertTrue(HttpMethod::PUT()->hasBody());
        $this->assertTrue(HttpMethod::PATCH()->hasBody());
        $this->assertFalse(HttpMethod::DELETE()->hasBody());
        $this->assertFalse(HttpMethod::HEAD()->hasBody());
        $this->assertFalse(HttpMethod::OPTIONS()->hasBody());
        $this->assertFalse(HttpMethod::CONNECT()->hasBody());
        $this->assertFalse(HttpMethod::TRACE()->hasBody());
    }
}