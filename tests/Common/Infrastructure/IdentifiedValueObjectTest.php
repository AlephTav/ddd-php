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
        $obj1 = new IdentifiedValueObjectTestObject(['prop' => 'foo']);
        $obj2 = new IdentifiedValueObjectTestObject(['prop' => 'foo']);
        $obj3 = new IdentifiedValueObjectTestObject(['prop' => 'poo']);

        $this->assertEquals($obj1->hash(), $obj2->hash());
        $this->assertNotEquals($obj1->hash(), $obj3->hash());
    }

    public function testComputedHashWithId(): void
    {
        $id1 = new LocalId(1);
        $id2 = new LocalId(2);

        $obj1 = new IdentifiedValueObjectTestObject(['id' => $id1, 'prop' => 'foo']);
        $obj2 = new IdentifiedValueObjectTestObject(['id' => $id1, 'prop' => 'poo']);
        $obj3 = new IdentifiedValueObjectTestObject(['id' => $id2, 'prop' => 'foo']);

        $this->assertSame($obj1->hash(), $obj2->hash());
        $this->assertNotSame($obj1->hash(), $obj3->hash());
    }
}