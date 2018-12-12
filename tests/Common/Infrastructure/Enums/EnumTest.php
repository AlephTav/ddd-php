<?php

namespace AlephTools\DDD\Tests\Common\Infrastructure\Enums;

use BadMethodCallException;
use UnexpectedValueException;
use PHPUnit\Framework\TestCase;
use AlephTools\DDD\Common\Infrastructure\Enums\AbstractEnum;

/**
 * @method static C1(string $method = null)
 * @method static C2(string $method = null)
 * @method static C3(string $method = null, int $index = 0)
 */
class EnumTestObject extends AbstractEnum
{
    public const C1 = 'foo';
    protected const C2 = null;
    private const C3 = [[1, 2, 3]];

    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function getJson(): string
    {
        return json_encode($this->data);
    }

    public function getData(int $index = null)
    {
        if (is_array($this->data) && $index !== null) {
            return $this->data[$index];
        }
        return $this->data;
    }
}

class EnumTest extends TestCase
{
    public function testGetConstants(): void
    {
        $expected = [
            'C1' => 'foo',
            'C2' => null,
            'C3' => [[1, 2, 3]]
        ];

        $this->assertSame($expected, EnumTestObject::getConstants());
        $this->assertSame($expected, EnumTestObject::getConstants());
    }

    public function testGetConstantNames(): void
    {
        $this->assertSame(['C1', 'C2', 'C3'], EnumTestObject::getConstantNames());
    }

    public function testIsValidConstantName(): void
    {
        $this->assertTrue(EnumTestObject::isValidConstantName('C1'));
        $this->assertTrue(EnumTestObject::isValidConstantName('C2'));
        $this->assertTrue(EnumTestObject::isValidConstantName('C3'));
        $this->assertFalse(EnumTestObject::isValidConstantName('C4'));
        $this->assertFalse(EnumTestObject::isValidConstantName('data'));
    }

    public function testIsValidConstantValue(): void
    {
        $this->assertTrue(EnumTestObject::isValidConstantValue('foo'));
        $this->assertTrue(EnumTestObject::isValidConstantValue([[1, 2, 3]]));
        $this->assertTrue(EnumTestObject::isValidConstantValue(null, true));
        $this->assertTrue(EnumTestObject::isValidConstantValue(''));

        $this->assertFalse(EnumTestObject::isValidConstantValue('', true));
        $this->assertFalse(EnumTestObject::isValidConstantValue('boo'));
        $this->assertFalse(EnumTestObject::isValidConstantValue([[1, 3, 2]]));
    }

    public function testValidateSuccess(): void
    {
        EnumTestObject::validate('C1');
        EnumTestObject::validate('C2');
        EnumTestObject::validate('C3');

        $this->assertTrue(true);
    }

    public function testValidateFailure(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Constant "EnumTestObject::C4" does not exist. Valid values are C1, C2, C3.');

        EnumTestObject::validate('C4');
    }

    public function testGetConstantValue(): void
    {
        $this->assertSame('foo', EnumTestObject::getConstantValue('C1'));
        $this->assertSame(null, EnumTestObject::getConstantValue('C2'));
        $this->assertSame([[1, 2, 3]], EnumTestObject::getConstantValue('C3'));
    }

    public function testEnumInstance(): void
    {
        $this->assertInstanceOf(EnumTestObject::class, EnumTestObject::C1());
    }

    public function testGetConstantName(): void
    {
        $this->assertSame('C1', EnumTestObject::C1('constantName'));
        $this->assertSame('C2', EnumTestObject::C2('constantName'));
        $this->assertSame('C3', EnumTestObject::C3('constantName'));
    }

    public function testToString(): void
    {
        $this->assertSame('C1', (string)EnumTestObject::C1());
        $this->assertSame('C2', (string)EnumTestObject::C2());
        $this->assertSame('C3', (string)EnumTestObject::C3());
    }

    public function testToJson(): void
    {
        $this->assertSame('"C1"', json_encode(EnumTestObject::C1()));
        $this->assertSame('"C2"', json_encode(EnumTestObject::C2()));
        $this->assertSame('"C3"', json_encode(EnumTestObject::C3()));
    }

    public function testMethodCall(): void
    {
        $this->assertSame('"foo"', EnumTestObject::C1('json'));
        $this->assertSame('null', EnumTestObject::C2('json'));
        $this->assertSame('[1,2,3]', EnumTestObject::C3('json'));

        $this->assertSame('foo', EnumTestObject::C1('data'));
        $this->assertSame(null, EnumTestObject::C2('data'));
        $this->assertSame([1, 2, 3], EnumTestObject::C3('data'));

        $this->assertSame(1, EnumTestObject::C3('data', 0));
        $this->assertSame(2, EnumTestObject::C3('data', 1));
        $this->assertSame(3, EnumTestObject::C3('data', 2));
    }

    public function testBadMethodCall(): void
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('Method getFoo does not exist.');

        EnumTestObject::C1('foo');
    }

    public function testCompare(): void
    {
        $c3 = EnumTestObject::C3();

        $this->assertSame(EnumTestObject::C3(), $c3);
        $this->assertEquals(EnumTestObject::C3(), $c3);
        $this->assertEquals('C3', $c3);

        $this->assertNotEquals('C1', $c3);
        $this->assertNotEquals(EnumTestObject::C2(), $c3);
    }

    public function testSerializeEnum(): void
    {
        $c1 = EnumTestObject::C1();
        $c1 = unserialize(serialize($c1));

        $this->assertNotSame(EnumTestObject::C1(), $c1);
        $this->assertEquals(EnumTestObject::C1(), $c1);
        $this->assertEquals('C1', $c1);
    }

    public function testClearEnumCache(): void
    {
        $c1 = EnumTestObject::C1();
        AbstractEnum::clear();

        $this->assertNotSame(EnumTestObject::C1(), $c1);
        $this->assertEquals(EnumTestObject::C1(), $c1);
        $this->assertEquals('C1', $c1);

        AbstractEnum::clear();
        $c1 = unserialize(serialize($c1));

        $this->assertSame(EnumTestObject::C1(), $c1);
        $this->assertEquals(EnumTestObject::C1(), $c1);
        $this->assertEquals('C1', $c1);
    }
}