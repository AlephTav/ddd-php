<?php

declare(strict_types=1);

namespace AlephTools\DDD\Tests\Common\Infrastructure;

use AlephTools\DDD\Common\Infrastructure\IdentifiedDomainObject;
use AlephTools\DDD\Common\Model\Identity\LocalId;
use PHPUnit\Framework\TestCase;
use stdClass;

class IdentifiedDomainObjectTestId extends LocalId
{
}

/**
 * @property-read mixed $prop
 */
class IdentifiedDomainObjectTestObject extends IdentifiedDomainObject
{
    private $prop;
}

/**
 * @internal
 */
class IdentifiedDomainObjectTest extends TestCase
{
    public function testCreation(): void
    {
        $id = new IdentifiedDomainObjectTestId(1);

        $obj = new IdentifiedDomainObjectTestObject(['id' => $id]);

        self::assertSame($id, $obj->id);
        self::assertSame(1, $obj->toIdentity());
        self::assertSame('1', $obj->toIdentityString());

        $obj = new IdentifiedDomainObjectTestObject();
        self::assertNull($obj->id);
        self::assertNull($obj->toIdentity());
        $this->assertNUll($obj->toIdentityString());
    }

    public function testComparison(): void
    {
        $id1 = new IdentifiedDomainObjectTestId(1);
        $id2 = new IdentifiedDomainObjectTestId(1);
        $id3 = new IdentifiedDomainObjectTestId(2);
        $obj1 = new IdentifiedDomainObjectTestObject(['id' => $id1, 'prop' => 'a']);
        $obj2 = new IdentifiedDomainObjectTestObject(['id' => $id2, 'prop' => 'b']);
        $obj3 = new IdentifiedDomainObjectTestObject(['id' => $id3, 'prop' => 'a']);

        self::assertFalse($obj1->equals(null));
        self::assertFalse($obj1->equals('foo'));
        self::assertFalse($obj1->equals(123));
        self::assertFalse($obj1->equals(new stdClass()));
        self::assertTrue($obj1->equals($obj2));
        self::assertTrue($obj2->equals($obj1));
        self::assertFalse($obj1->equals($obj3));
        self::assertFalse($obj3->equals($obj2));

        $obj4 = new IdentifiedDomainObjectTestObject(['prop' => 'a']);
        $obj5 = new IdentifiedDomainObjectTestObject(['prop' => 'a']);
        $obj6 = new IdentifiedDomainObjectTestObject(['prop' => 'b']);

        self::assertTrue($obj4->equals($obj5));
        self::assertTrue($obj5->equals($obj4));
        self::assertFalse($obj5->equals($obj6));
        self::assertFalse($obj6->equals($obj4));
    }

    public function testHash(): void
    {
        $id1 = new IdentifiedDomainObjectTestId(1);
        $id2 = new IdentifiedDomainObjectTestId(2);
        $obj1 = new IdentifiedDomainObjectTestObject(['id' => $id1, 'prop' => 'a']);
        $obj2 = new IdentifiedDomainObjectTestObject(['id' => $id1, 'prop' => 'b']);
        $obj3 = new IdentifiedDomainObjectTestObject(['id' => $id2, 'prop' => 'b']);

        self::assertSame($obj1->hash(), $obj2->hash());
        self::assertNotSame($obj1->hash(), $obj3->hash());

        $obj1 = new IdentifiedDomainObjectTestObject(['prop' => 'a']);
        $obj2 = new IdentifiedDomainObjectTestObject(['prop' => 'a']);
        $obj3 = new IdentifiedDomainObjectTestObject(['prop' => 'b']);

        self::assertSame($obj1->hash(), $obj2->hash());
        self::assertNotSame($obj1->hash(), $obj3->hash());
    }
}
