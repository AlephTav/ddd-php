<?php

namespace AlephTools\DDD\Tests\Common\Infrastructure;

use PHPUnit\Framework\TestCase;
use AlephTools\DDD\Common\Infrastructure\Hash;
use AlephTools\DDD\Common\Infrastructure\IdentifiedValueObject;
use AlephTools\DDD\Common\Model\Identity\AbstractId;
use AlephTools\DDD\Common\Model\Identity\LocalId;

/**
 * @property-read string $prop
 */
class IdentifiedValueObjectTestObject extends IdentifiedValueObject
{
    private $prop;

    public function setProp(string $value): void
    {
        $this->prop = $value;
    }

    public function assignId(AbstractId $id): void
    {
        $this->id = $id;
    }
}

class IdentifiedValueObjectTest extends TestCase
{
    public function testComputedHashWithoutId(): void
    {
        $obj = new IdentifiedValueObjectTestObject(['prop' => 'foo']);

        $hash = Hash::of(['id' => null, 'prop' => 'foo']);
        $this->assertEquals($hash, $obj->hash());

        $obj->setProp('boo');
        $this->assertEquals($hash, $obj->hash());
    }

    public function testComputedHashWithId(): void
    {
        $id = new class(1) extends LocalId {};

        $obj = new IdentifiedValueObjectTestObject(['id' => $id, 'prop' => 'foo']);

        $hash = $id->hash();
        $this->assertEquals($hash, $obj->hash());

        $obj->setProp('boo');
        $this->assertEquals($hash, $obj->hash());

        $id = new class(2) extends LocalId {};
        $obj->assignId($id);
        $this->assertEquals($hash, $obj->hash());
    }
}