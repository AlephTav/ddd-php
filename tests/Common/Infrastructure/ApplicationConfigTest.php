<?php

declare(strict_types=1);

namespace AlephTools\DDD\Tests\Common\Infrastructure;

use AlephTools\DDD\Common\Infrastructure\ApplicationConfig;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ApplicationConfigTest extends TestCase
{
    public function testResolveDependency(): void
    {
        $config = fn (string $key = null, $default = null) => [$key, $default];

        ApplicationConfig::set($config);

        self::assertSame([null, null], ApplicationConfig::get());
        self::assertSame(['a', null], ApplicationConfig::get('a'));
        self::assertSame(['d', 'abc'], ApplicationConfig::get('d', 'abc'));
    }
}
