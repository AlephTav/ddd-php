<?php

namespace AlephTools\DDD\Tests\Common\Infrastructure;

use PHPUnit\Framework\TestCase;
use AlephTools\DDD\Common\Infrastructure\Hash;
use AlephTools\DDD\Common\Infrastructure\ValueObject;

/**
 * @property-read string $prop
 */
class ValueTestObject extends ValueObject
{
    private $prop;

    public function setProp(string $value)
    {
        $this->prop = $value;
    }
}

class ValueObjectTest extends TestCase
{
    public function testComputedHash(): void
    {
        $obj = new ValueTestObject(['prop' => 'foo']);

        $hash = Hash::of(['prop' => 'foo']);
        $this->assertEquals($hash, $obj->hash());

        $obj->setProp('boo');
        $this->assertEquals($hash, $obj->hash());
    }
}