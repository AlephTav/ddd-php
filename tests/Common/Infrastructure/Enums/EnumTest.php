<?php

declare(strict_types=1);

namespace AlephTools\DDD\Tests\Common\Infrastructure\Enums;

use AlephTools\DDD\Common\Infrastructure\Enums\AbstractEnum;
use AlephTools\DDD\Common\Model\Exceptions\InvalidArgumentException;
use AlephTools\DDD\Common\Model\Gender;
use BadMethodCallException;
use PHPUnit\Framework\TestCase;
use stdClass;
use UnexpectedValueException;

/**
 * @method static static C1(string $method = null)
 * @method static static C2(string $method = null)
 * @method static static C3(string $method = null, int $index = 0)
 */
class EnumTestObject extends AbstractEnum
{
    public const C1 = 'foo';
    protected const C2 = null;
    private const C3 = [[1, 2, 3]];

    private $data;

    public function __construct($data)
    {
        parent::__construct();
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

/**
 * @internal
 */
class EnumTest extends TestCase
{
    public function testGetConstants(): void
    {
        $expected = [
            'C1' => 'foo',
            'C2' => null,
            'C3' => [[1, 2, 3]],
        ];

        self::assertSame($expected, EnumTestObject::getConstants());
        self::assertSame($expected, EnumTestObject::getConstants());
    }

    public function testGetConstantNames(): void
    {
        self::assertSame(['C1', 'C2', 'C3'], EnumTestObject::getConstantNames());
    }

    public function testIsValidConstantName(): void
    {
        self::assertTrue(EnumTestObject::isValidConstantName('C1'));
        self::assertTrue(EnumTestObject::isValidConstantName('C2'));
        self::assertTrue(EnumTestObject::isValidConstantName('C3'));
        self::assertFalse(EnumTestObject::isValidConstantName('C4'));
        self::assertFalse(EnumTestObject::isValidConstantName('data'));
    }

    public function testIsValidConstantValue(): void
    {
        self::assertTrue(EnumTestObject::isValidConstantValue('foo'));
        self::assertTrue(EnumTestObject::isValidConstantValue([[1, 2, 3]]));
        self::assertTrue(EnumTestObject::isValidConstantValue(null, true));
        self::assertTrue(EnumTestObject::isValidConstantValue(''));

        self::assertFalse(EnumTestObject::isValidConstantValue('', true));
        self::assertFalse(EnumTestObject::isValidConstantValue('boo'));
        self::assertFalse(EnumTestObject::isValidConstantValue([[1, 3, 2]]));
    }

    public function testValidateSuccess(): void
    {
        EnumTestObject::validate('C1');
        EnumTestObject::validate('C2');
        EnumTestObject::validate('C3');

        self::assertTrue(true);
    }

    public function testValidateFailure(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Constant "EnumTestObject::C4" does not exist. Valid values are C1, C2, C3.');

        EnumTestObject::validate('C4');
    }

    public function testGetConstantValue(): void
    {
        self::assertSame('foo', EnumTestObject::getConstantValue('C1'));
        self::assertNull(EnumTestObject::getConstantValue('C2'));
        self::assertSame([[1, 2, 3]], EnumTestObject::getConstantValue('C3'));
    }

    public function testEnumInstance(): void
    {
        self::assertInstanceOf(EnumTestObject::class, EnumTestObject::C1());
    }

    public function testGetConstantName(): void
    {
        self::assertSame('C1', EnumTestObject::C1('constantName'));
        self::assertSame('C2', EnumTestObject::C2('constantName'));
        self::assertSame('C3', EnumTestObject::C3('constantName'));
    }

    public function testToString(): void
    {
        self::assertSame('C1', (string)EnumTestObject::C1());
        self::assertSame('C2', (string)EnumTestObject::C2());
        self::assertSame('C3', (string)EnumTestObject::C3());
    }

    public function testToJson(): void
    {
        self::assertSame('"C1"', json_encode(EnumTestObject::C1()));
        self::assertSame('"C2"', json_encode(EnumTestObject::C2()));
        self::assertSame('"C3"', json_encode(EnumTestObject::C3()));
    }

    public function testMethodCall(): void
    {
        self::assertSame('"foo"', EnumTestObject::C1('json'));
        self::assertSame('null', EnumTestObject::C2('json'));
        self::assertSame('[1,2,3]', EnumTestObject::C3('json'));

        self::assertSame('foo', EnumTestObject::C1('data'));
        self::assertNull(EnumTestObject::C2('data'));
        self::assertSame([1, 2, 3], EnumTestObject::C3('data'));

        self::assertSame(1, EnumTestObject::C3('data', 0));
        self::assertSame(2, EnumTestObject::C3('data', 1));
        self::assertSame(3, EnumTestObject::C3('data', 2));
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

        self::assertSame(EnumTestObject::C3(), $c3);
        self::assertEquals(EnumTestObject::C3(), $c3);
        self::assertEquals('C3', $c3);
        self::assertTrue(EnumTestObject::C3()->equals($c3));
        self::assertTrue(EnumTestObject::C3()->equals('C3'));

        self::assertNotEquals('C1', $c3);
        self::assertNotEquals(EnumTestObject::C2(), $c3);
        self::assertFalse($c3->equals('C1'));
        self::assertFalse($c3->equals(EnumTestObject::C2()));
    }

    public function testSerializeEnum(): void
    {
        $c1 = EnumTestObject::C1();
        $c1 = unserialize(serialize($c1));

        self::assertNotSame(EnumTestObject::C1(), $c1);
        self::assertEquals(EnumTestObject::C1(), $c1);
        self::assertEquals('C1', $c1);
    }

    public function testClearEnumCache(): void
    {
        $c1 = EnumTestObject::C1();
        AbstractEnum::clearEnumCache();

        self::assertNotSame(EnumTestObject::C1(), $c1);
        self::assertEquals(EnumTestObject::C1(), $c1);
        self::assertEquals('C1', $c1);

        AbstractEnum::clearEnumCache();
        $c1 = unserialize(serialize($c1));

        self::assertSame(EnumTestObject::C1(), $c1);
        self::assertEquals(EnumTestObject::C1(), $c1);
        self::assertEquals('C1', $c1);
    }

    public function testToScalar(): void
    {
        $c1 = EnumTestObject::C1();

        self::assertSame('C1', $c1->toString());
        self::assertSame('C1', $c1->toScalar());
    }

    public function testCastToEnumSuccess(): void
    {
        $female = Gender::from('FEMALE');

        self::assertSame(Gender::FEMALE(), $female);
    }

    public function testCastToEnumFailure(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Constant "Gender::FOO" does not exist. Valid values are FEMALE, MALE.');

        Gender::from('FOO');
    }

    public function testNonScalarEnumConstant(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Constant of ' . Gender::class . ' must be a string, object given.');

        Gender::from(new stdClass());
    }

    public function testNullEnumConstant(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Constant of ' . Gender::class . ' must be a string, NULL given.');

        Gender::from(null);
    }

    public function testEmptyEnumConstant(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Constant of ' . Gender::class . ' must not be empty string.');

        Gender::from('');
    }

    public function testEnumInstanceAsEnumConstant(): void
    {
        $enum = Gender::from(Gender::FEMALE());

        self::assertSame(Gender::FEMALE(), $enum);
    }

    public function testNullableEnumConstantForNull(): void
    {
        $enum = Gender::fromNullable(null);

        self::assertNull($enum);
    }

    public function testNullableEnumForNotNull(): void
    {
        $enum = Gender::fromNullable('MALE');

        self::assertSame(Gender::MALE(), $enum);
    }
}
