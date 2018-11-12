<?php

namespace AlephTools\DDD\Tests\Support\Application;

use PHPUnit\Framework\TestCase;
use AlephTools\DDD\Common\Infrastructure\ApplicationContext;

class ApplicationContextTest extends TestCase
{
    public function testResolveDependency(): void
    {
        $di = function(?string $abstract, array $parameters) {
            return [$abstract, $parameters];
        };

        ApplicationContext::set($di);

        $this->assertSame([null, []], ApplicationContext::get());
        $this->assertSame(['stdClass', []], ApplicationContext::get('stdClass'));
        $this->assertSame(['SplFixedArray', ['size' => 10]], ApplicationContext::get('SplFixedArray', ['size' => 10]));
    }
}