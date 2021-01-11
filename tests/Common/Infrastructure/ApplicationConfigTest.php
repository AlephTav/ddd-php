<?php

namespace AlephTools\DDD\Tests\Common\Infrastructure;

use AlephTools\DDD\Common\Infrastructure\ApplicationConfig;
use PHPUnit\Framework\TestCase;

class ApplicationConfigTest extends TestCase
{
    public function testResolveDependency(): void
    {
        $config = function(string $key = null, $default = null) {
            return [$key, $default];
        };

        ApplicationConfig::set($config);

        $this->assertSame([null, null], ApplicationConfig::get());
        $this->assertSame(['a', null], ApplicationConfig::get('a'));
        $this->assertSame(['d', 'abc'], ApplicationConfig::get('d', 'abc'));
    }
}