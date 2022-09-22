<?php

declare(strict_types=1);

namespace Tests\AlephTools\DDD\Common\Model;

use AlephTools\DDD\Common\Model\HttpMethod;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class HttpMethodTest extends TestCase
{
    public function testIdentification(): void
    {
        self::assertTrue(HttpMethod::GET()->isGet());
        self::assertTrue(HttpMethod::POST()->isPost());
        self::assertTrue(HttpMethod::PUT()->isPut());
        self::assertTrue(HttpMethod::PATCH()->isPatch());
        self::assertTrue(HttpMethod::DELETE()->isDelete());
        self::assertTrue(HttpMethod::HEAD()->isHead());
        self::assertTrue(HttpMethod::OPTIONS()->isOptions());
        self::assertTrue(HttpMethod::CONNECT()->isConnect());
        self::assertTrue(HttpMethod::TRACE()->isTrace());
    }

    public function testHasBody(): void
    {
        self::assertFalse(HttpMethod::GET()->hasBody());
        self::assertTrue(HttpMethod::POST()->hasBody());
        self::assertTrue(HttpMethod::PUT()->hasBody());
        self::assertTrue(HttpMethod::PATCH()->hasBody());
        self::assertFalse(HttpMethod::DELETE()->hasBody());
        self::assertFalse(HttpMethod::HEAD()->hasBody());
        self::assertFalse(HttpMethod::OPTIONS()->hasBody());
        self::assertFalse(HttpMethod::CONNECT()->hasBody());
        self::assertFalse(HttpMethod::TRACE()->hasBody());
    }
}
