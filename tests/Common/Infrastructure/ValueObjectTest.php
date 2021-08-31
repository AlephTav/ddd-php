<?php

declare(strict_types=1);

namespace AlephTools\DDD\Tests\Common\Infrastructure;

use AlephTools\DDD\Common\Infrastructure\ValueObject;
use PHPUnit\Framework\TestCase;

/**
 * @property-read string $prop
 */
class ValueTestObject extends ValueObject
{
    private $prop;

    public function setProp(string $value): void
    {
        $this->prop = $value;
    }
}

/**
 * @internal
 */
class ValueObjectTest extends TestCase
{
    public function testComputedHash(): void
    {
        $obj = new ValueTestObject(['prop' => 'foo']);
        $hash = $obj->hash();
        $obj->setProp('boo');

        self::assertEquals($hash, $obj->hash());
    }
}
