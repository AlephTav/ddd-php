<?php

namespace AlephTools\DDD\Tests\Common\Infrastructure;

use AlephTools\DDD\Common\Model\Exceptions\InvalidArgumentException;
use RuntimeException;
use AlephTools\DDD\Common\Infrastructure\Dto;
use PHPUnit\Framework\TestCase;

/**
 * @property-read int|null $prop1
 * @property-read mixed $prop2
 * @property mixed $prop3
 */
class FastDtoParentTestObject extends Dto
{
    protected $prop1;
    protected $prop2;
    protected $prop3;

    protected static function getPropertyDefinitions(): ?array
    {
        return [
            'prop1' => self::PROP_READ_SETTER_VALIDATOR,
            'prop2' => self::PROP_READ_GETTER,
            'prop3' => self::PROP_READ_WRITE | self::PROP_VALIDATOR
        ];
    }

    protected function setProp1(?int $value): void
    {
        $this->prop1 = $value;
    }

    protected function getProp2()
    {
        return $this->prop2;
    }

    protected function validateProp1(): void
    {
        $this->assertArgumentMax($this->prop1, 10, 'validate error 1');
    }

    protected function validateProp3(): void
    {
        $this->assertArgumentNotNull($this->prop3, 'validate error 2');
    }
}

/**
 * @property-write string $prop4
 * @property string $prop5;
 */
class FastDtoTestObject extends FastDtoParentTestObject
{
    protected $prop4 = 'default';
    protected $prop5;

    protected static function getPropertyDefinitions(): ?array
    {
        return array_merge(parent::getPropertyDefinitions(), [
            'prop4' => self::PROP_WRITE | self::PROP_SETTER,
            'prop5' => self::PROP_READ_WRITE_GETTER_VALIDATOR
        ]);
    }

    protected function setProp4(string $prop4): void
    {
        $this->prop4 = $prop4;
    }

    protected function getProp5(): string
    {
        return $this->prop5;
    }

    protected function validateProp5(): void
    {
        $this->assertArgumentNotEmpty($this->prop5, 'validate error 3');
    }
}

class FastDtoTest extends TestCase
{
    public function testCreationSuccess(): void
    {
        new FastDtoTestObject([
            'prop1' => 5,
            'prop2' => null,
            'prop3' => true,
            'prop4' => 'foo',
            'prop5' => 'test'
        ]);

        $this->assertTrue(true);
    }

    public function testCreationFailureNotExistingProperty(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Property "prop6" does not exist.');

        new FastDtoTestObject([
            'prop1' => 5,
            'prop2' => null,
            'prop3' => true,
            'prop4' => 'foo',
            'prop5' => 'test',
            'prop6' => 123
        ]);
    }

    public function testToArray(): void
    {
        $properties = [
            'prop1' => 5,
            'prop2' => null,
            'prop3' => true,
            'prop4' => 'foo',
            'prop5' => 'test'
        ];
        $obj = new FastDtoTestObject($properties);

        $this->assertEquals($properties, $obj->toArray());
    }

    public function testToNestedArray(): void
    {
        $nestedProperties = [
            'prop1' => 5,
            'prop2' => null,
            'prop3' => true,
            'prop4' => 'foo',
            'prop5' => 'test'
        ];
        $nestedObj = new FastDtoTestObject($nestedProperties);

        $properties = [
            'prop1' => 3,
            'prop2' => $nestedObj,
            'prop3' => false,
            'prop4' => 'test',
            'prop5' => 'foo'
        ];
        $obj = new FastDtoTestObject($properties);

        $properties['prop2'] = $nestedProperties;
        $this->assertEquals($properties, $obj->toNestedArray());
    }

    public function testToJson(): void
    {
        $properties = [
            'prop1' => 5,
            'prop2' => null,
            'prop3' => true,
            'prop4' => 'foo',
            'prop5' => 'test'
        ];
        $obj = new FastDtoTestObject($properties);

        $expected = json_encode($properties);
        $this->assertEquals($expected, json_encode($obj));
        $this->assertEquals($expected, $obj->toJson());
    }

    public function testSerialize(): void
    {
        $obj = new FastDtoTestObject([
            'prop1' => 5,
            'prop2' => null,
            'prop3' => true,
            'prop4' => 'foo',
            'prop5' => 'test'
        ]);

        $new = unserialize(serialize($obj));

        $this->assertSame($obj->toArray(), $new->toArray());
    }

    public function testToString(): void
    {
        $properties = [
            'prop1' => 5,
            'prop2' => null,
            'prop3' => true,
            'prop4' => 'foo',
            'prop5' => 'test'
        ];
        $obj = new FastDtoTestObject($properties);

        $expected = print_r($obj, true);
        $this->assertEquals($expected, $obj->toString());
        $this->assertEquals($expected, (string)$obj);
    }

    public function testValidate(): void
    {
        $n = 1;
        foreach (['prop1' => 15, 'prop3' => null, 'prop5' => ''] as $property => $value) {
            try {
                $properties = [
                    'prop1' => 5,
                    'prop2' => null,
                    'prop3' => true,
                    'prop4' => 'foo',
                    'prop5' => 'test'
                ];
                $properties[$property] = $value;
                new FastDtoTestObject($properties);
            } catch (InvalidArgumentException $e) {
                $this->assertEquals('validate error ' . $n, $e->getMessage());
            }
            ++$n;
        }
    }

    public function testSuccessPropertyRead(): void
    {
        $obj = new FastDtoTestObject([
            'prop1' => 5,
            'prop2' => [1, 2, 3],
            'prop3' => true,
            'prop4' => 'foo',
            'prop5' => 'test'
        ]);

        $this->assertSame(5, $obj->prop1);
        $this->assertSame([1, 2, 3], $obj->prop2);
        $this->assertSame(true, $obj->prop3);
        $this->assertSame('test', $obj->prop5);
        $this->assertSame('foo', $obj->toArray()['prop4']);
    }

    public function testFailedPropertyRead(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Property "prop4" is not readable.');

        $obj = new FastDtoTestObject([
            'prop1' => 5,
            'prop2' => null,
            'prop3' => true,
            'prop5' => 'test'
        ]);
        /** @var mixed $obj */
        $obj->prop4;
    }

    public function testSuccessPropertyWrite(): void
    {
        $obj = new FastDtoTestObject([
            'prop1' => 5,
            'prop2' => null,
            'prop3' => true,
            'prop5' => 'test'
        ]);
        $obj->prop3 = 123;
        $obj->prop4 = 'test';
        $obj->prop5 = 'foo';

        $this->assertSame(123, $obj->prop3);
        $this->assertSame('foo', $obj->prop5);
        $this->assertEquals('test', $obj->toArray()['prop4']);
    }

    public function testFailedPropertyWrite(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Property "prop1" is not writable.');

        $obj = new FastDtoTestObject([
            'prop1' => 5,
            'prop2' => null,
            'prop3' => true,
            'prop5' => 'test'
        ]);
        /** @var mixed $obj */
        $obj->prop1 = 3;
    }

    public function testSuccessIsset(): void
    {
        $obj = new FastDtoTestObject([
            'prop1' => 5,
            'prop2' => null,
            'prop3' => true,
            'prop5' => 'test'
        ]);

        $this->assertFalse(isset($obj->prop2));
        $this->assertTrue(isset($obj->prop1));
        $this->assertTrue(isset($obj->prop3));
        $this->assertTrue(isset($obj->prop5));
    }

    public function testFailedIsset(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Property "prop4" is not readable.');

        $obj = new FastDtoTestObject([
            'prop1' => 5,
            'prop2' => null,
            'prop3' => true,
            'prop5' => 'test'
        ]);
        /** @var mixed $obj */
        $foo = isset($obj->prop4);
    }

    public function testSuccessUnset(): void
    {
        $obj = new FastDtoTestObject([
            'prop1' => 5,
            'prop2' => null,
            'prop3' => true,
            'prop5' => 'test'
        ]);
        unset($obj->prop3);

        $this->assertNull($obj->prop3);
    }

    public function testFailedUnset(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Property "prop2" is not writable.');

        $obj = new FastDtoTestObject([
            'prop1' => 5,
            'prop2' => [123],
            'prop3' => true,
            'prop5' => 'test'
        ]);
        unset($obj->prop2);
    }

    public function testProcessingTypeError(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Property "prop1" must be of the type int or null, string given.');

        new FastDtoTestObject(['prop1' => 'test']);
    }
}