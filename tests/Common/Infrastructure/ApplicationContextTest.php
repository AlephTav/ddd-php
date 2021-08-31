<?php

declare(strict_types=1);

namespace AlephTools\DDD\Tests\Common\Infrastructure;

use AlephTools\DDD\Common\Infrastructure\ApplicationContext;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ApplicationContextTest extends TestCase
{
    public function testResolveDependency(): void
    {
        $di = fn (?string $abstract, array $parameters) => [$abstract, $parameters];

        ApplicationContext::set($di);

        self::assertSame([null, []], ApplicationContext::get());
        self::assertSame(['stdClass', []], ApplicationContext::get('stdClass'));
        self::assertSame(['SplFixedArray', ['size' => 10]], ApplicationContext::get('SplFixedArray', ['size' => 10]));
    }
}
