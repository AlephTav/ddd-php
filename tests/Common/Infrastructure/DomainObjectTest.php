<?php

namespace AlephTools\DDD\Tests\Common\Infrastructure;

use AlephTools\DDD\Common\Model\Identity\GlobalId;
use PHPUnit\Framework\TestCase;
use AlephTools\DDD\Common\Infrastructure\DomainObject;
use AlephTools\DDD\Common\Infrastructure\Hash;

/**
 * @property-read mixed $prop1
 * @property-read mixed $prop2
 * @property-read mixed $prop3
 * @property-read mixed $prop4
 */
class DomainTestObject extends DomainObject
{
    private $prop1;
    private $prop2;
    private $prop3;
    private $prop4;
}

class DomainObjectTest extends TestCase
{
    public function testCopy(): void
    {
        $copy = (new DomainTestObject([
            'prop1' => 5,
            'prop2' => 'foo',
            'prop3' => true,
            'prop4' => null
        ]))->copy();

        $this->assertInstanceOf(DomainTestObject::class, $copy);
        $this->assertEquals([
            'prop1' => 5,
            'prop2' => 'foo',
            'prop3' => true,
            'prop4' => null
        ], $copy->toArray());
    }

    public function testCopyWith(): void
    {
        $copy = (new DomainTestObject([
            'prop1' => 5,
            'prop2' => 'foo',
            'prop3' => true,
            'prop4' => null
        ]))->copyWith(['prop3' => false, 'prop2' => 'boo']);

        $this->assertInstanceOf(DomainTestObject::class, $copy);
        $this->assertEquals([
            'prop1' => 5,
            'prop2' => 'boo',
            'prop3' => false,
            'prop4' => null
        ], $copy->toArray());
    }

    public function testHash(): void
    {
        $attributes = [
            'prop1' => 5,
            'prop2' => 'foo',
            'prop3' => true,
            'prop4' => null
        ];
        $obj1 = new DomainTestObject($attributes);
        $obj2 = new DomainTestObject($attributes);
        $attributes['prop3'] = false;
        $obj3 = new DomainTestObject($attributes);

        $this->assertSame($obj1->hash(), $obj2->hash());
        $this->assertNotSame($obj1->hash(), $obj3->hash());
    }

    public function testEquals(): void
    {
        $nestedObj1 = new DomainTestObject(['prop2' => 'boo']);
        $nestedObj2 = new DomainTestObject(['prop2' => 'boo']);

        $id = GlobalId::create();

        $obj1 = new DomainTestObject([
            'prop1' => 5,
            'prop2' => $id,
            'prop3' => true,
            'prop4' => $nestedObj1
        ]);

        $obj2 = new DomainTestObject([
            'prop1' => 5,
            'prop2' => $id,
            'prop3' => true,
            'prop4' => $nestedObj2
        ]);

        $obj3 = new DomainTestObject([
            'prop1' => 5,
            'prop2' => $id,
            'prop3' => false,
            'prop4' => $nestedObj1
        ]);

        $obj4 = new DomainTestObject([
            'prop1' => 5,
            'prop2' => $id,
            'prop3' => true,
            'prop4' => 'foo'
        ]);

        $this->assertFalse($obj1->equals(null));
        $this->assertFalse($obj1->equals('foo'));
        $this->assertFalse($obj1->equals(123));
        $this->assertFalse($obj1->equals(new \stdClass()));
        $this->assertFalse($obj1->equals($obj3));
        $this->assertTrue($obj1->equals($obj2));
        $this->assertFalse($obj1->equals($obj4));
    }

    public function testDomainName(): void
    {
        $obj = new DomainTestObject();

        $this->assertEquals('DomainTestObject', $obj->domainName());
    }
}
