<?php

declare(strict_types=1);

namespace AlephTools\DDD\Tests\Common\Infrastructure;

use AlephTools\DDD\Common\Infrastructure\IdentifiedValueObject;
use AlephTools\DDD\Common\Model\Identity\AbstractId;
use AlephTools\DDD\Common\Model\Identity\LocalId;
use PHPUnit\Framework\TestCase;

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

/**
 * @internal
 */
class IdentifiedValueObjectTest extends TestCase
{
    public function testComputedHashWithoutId(): void
    {
        $obj1 = new IdentifiedValueObjectTestObject(['prop' => 'foo']);
        $obj2 = new IdentifiedValueObjectTestObject(['prop' => 'foo']);
        $obj3 = new IdentifiedValueObjectTestObject(['prop' => 'poo']);

        self::assertEquals($obj1->hash(), $obj2->hash());
        self::assertNotEquals($obj1->hash(), $obj3->hash());
    }

    public function testComputedHashWithId(): void
    {
        $id1 = new LocalId(1);
        $id2 = new LocalId(2);

        $obj1 = new IdentifiedValueObjectTestObject(['id' => $id1, 'prop' => 'foo']);
        $obj2 = new IdentifiedValueObjectTestObject(['id' => $id1, 'prop' => 'poo']);
        $obj3 = new IdentifiedValueObjectTestObject(['id' => $id2, 'prop' => 'foo']);

        self::assertSame($obj1->hash(), $obj2->hash());
        self::assertNotSame($obj1->hash(), $obj3->hash());
    }
}
