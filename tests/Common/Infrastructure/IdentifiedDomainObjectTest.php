<?php

namespace AlephTools\DDD\Tests\Common\Infrastructure;

use PHPUnit\Framework\TestCase;
use AlephTools\DDD\Common\Infrastructure\Hash;
use AlephTools\DDD\Common\Infrastructure\IdentifiedDomainObject;
use AlephTools\DDD\Common\Model\Identity\LocalId;

class IdentifiedDomainObjectTestId extends LocalId {}

/**
 * @property-read mixed $prop
 */
class IdentifiedDomainObjectTestObject extends IdentifiedDomainObject
{
    private $prop;
}

class IdentifiedDomainObjectTest extends TestCase
{
    public function testCreation(): void
    {
        $id = new IdentifiedDomainObjectTestId(1);

        $obj = new IdentifiedDomainObjectTestObject(['id' => $id]);

        $this->assertSame($id, $obj->id);
        $this->assertSame(1, $obj->toIdentity());

        $obj = new IdentifiedDomainObjectTestObject();
        $this->assertNull($obj->id);
        $this->assertNull($obj->toIdentity());
    }

    public function testComparison(): void
    {
        $id1 = new IdentifiedDomainObjectTestId(1);
        $id2 = new IdentifiedDomainObjectTestId(1);
        $id3 = new IdentifiedDomainObjectTestId(2);
        $obj1 = new IdentifiedDomainObjectTestObject(['id' => $id1, 'prop' => 'a']);
        $obj2 = new IdentifiedDomainObjectTestObject(['id' => $id2, 'prop' => 'b']);
        $obj3 = new IdentifiedDomainObjectTestObject(['id' => $id3, 'prop' => 'a']);

        $this->assertFalse($obj1->equals(null));
        $this->assertFalse($obj1->equals('foo'));
        $this->assertFalse($obj1->equals(123));
        $this->assertFalse($obj1->equals(new \stdClass()));
        $this->assertTrue($obj1->equals($obj2));
        $this->assertTrue($obj2->equals($obj1));
        $this->assertFalse($obj1->equals($obj3));
        $this->assertFalse($obj3->equals($obj2));

        $obj4 = new IdentifiedDomainObjectTestObject(['prop' => 'a']);
        $obj5 = new IdentifiedDomainObjectTestObject(['prop' => 'a']);
        $obj6 = new IdentifiedDomainObjectTestObject(['prop' => 'b']);

        $this->assertTrue($obj4->equals($obj5));
        $this->assertTrue($obj5->equals($obj4));
        $this->assertFalse($obj5->equals($obj6));
        $this->assertFalse($obj6->equals($obj4));
    }

    public function testHash(): void
    {
        $id = new IdentifiedDomainObjectTestId(1);
        $obj = new IdentifiedDomainObjectTestObject(['id' => $id, 'prop' => 'a']);

        $this->assertSame($id->hash(), $obj->hash());

        $obj = new IdentifiedDomainObjectTestObject(['prop' => 'a']);

        $this->assertSame(Hash::of(['id' => null, 'prop' => 'a']), $obj->hash());
    }
}