<?php

namespace AlephTools\DDD\Tests\Common\Infrastructure;

use RuntimeException;
use PHPUnit\Framework\TestCase;
use AlephTools\DDD\Common\Model\Exceptions\InvalidArgumentException;
use AlephTools\DDD\Common\Infrastructure\Dto;

/**
 * @property mixed $prop1
 * @property int $prop2
 * @property-read bool $prop3
 * @property-write string $prop4
 */
class DtoTestObject extends Dto
{
    private $prop1;
    protected $prop2;
    private $prop3;
    protected $prop4;
    private $prop5 = 'private';

    protected function getProp1()
    {
        return $this->prop1;
    }

    protected function setProp2(int $value): void
    {
        $this->prop2 = $value;
    }

    public function getProp3(): ?bool
    {
        return $this->prop3;
    }

    public function setProp4(string $value): void
    {
        $this->prop4 = $value;
    }

    protected function validateProp2(): void
    {
        $this->assertArgumentMax($this->prop2, 10, 'error message 1');
    }

    private function validateProp4(): void
    {
        $this->assertArgumentNotEmpty($this->prop4, 'error message 2');
    }
}

/**
 * @property mixed $notExistingProp
 */
class DtoTestObjectWithNonExistentProperty extends Dto {}

class DtoTest extends TestCase
{
    public function testCreationSuccess(): void
    {
        new DtoTestObject([
            'prop1' => null,
            'prop2' => 5,
            'prop3' => true,
            'prop4' => 'boo'
        ]);

        $this->assertTrue(true);
    }

    public function testCreationFailureNotExistingProperty(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Property "prop5" does not exist.');

        new DtoTestObject([
            'prop1' => null,
            'prop2' => 5,
            'prop3' => true,
            'prop4' => 'boo',
            'prop5' => 123
        ]);
    }

    public function testToArray(): void
    {
        $properties = [
            'prop1' => null,
            'prop2' => 5,
            'prop3' => true,
            'prop4' => 'boo'
        ];
        $obj = new DtoTestObject($properties);

        $this->assertEquals($properties, $obj->toArray());
    }

    public function testToNestedArray(): void
    {
        $nestedProperties = [
            'prop1' => null,
            'prop2' => 5,
            'prop3' => true,
            'prop4' => 'boo'
        ];
        $nestedObj = new DtoTestObject($nestedProperties);

        $properties = [
            'prop1' => $nestedObj,
            'prop2' => 7,
            'prop3' => false,
            'prop4' => 'foo'
        ];
        $obj = new DtoTestObject($properties);

        $properties['prop1'] = $nestedProperties;
        $this->assertEquals($properties, $obj->toNestedArray());
    }

    public function testToJson(): void
    {
        $attributes = [
            'prop1' => null,
            'prop2' => 5,
            'prop3' => true,
            'prop4' => 'boo'
        ];
        $obj = new DtoTestObject($attributes);

        $expected = json_encode($attributes);
        $this->assertEquals($expected, json_encode($obj));
        $this->assertEquals($expected, $obj->toJson());
    }

    public function testToString(): void
    {
        $attributes = [
            'prop1' => null,
            'prop2' => 5,
            'prop3' => true,
            'prop4' => 'boo'
        ];
        $obj = new DtoTestObject($attributes);

        $expected = print_r($obj, true);
        $this->assertEquals($expected, $obj->toString());
        $this->assertEquals($expected, (string)$obj);
    }

    public function testValidate(): void
    {
        $n = 1;
        foreach (['prop2' => 15, 'prop4' => ''] as $property => $value) {
            try {
                new DtoTestObject([$property => $value]);
            } catch (InvalidArgumentException $e) {
                $this->assertEquals('error message ' . $n, $e->getMessage());
            }
            ++$n;
        }
    }

    public function testGetSuccessDirectPropertyRead(): void
    {
        $obj = new DtoTestObject([
            'prop2' => 5,
            'prop4' => 'foo'
        ]);

        $this->assertEquals(5, $obj->prop2);
    }

    public function testGetSuccessGetter(): void
    {
        $obj = new DtoTestObject([
            'prop2' => 5,
            'prop4' => 'foo',
            'prop3' => true
        ]);

        $this->assertEquals(5, $obj->prop3);
    }

    public function testGetFailureWriteOnly(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Property "prop4" is write only.');

        $obj = new DtoTestObject([
            'prop2' => 5,
            'prop4' => 'foo'
        ]);
        /** @var mixed $obj */
        $obj->prop4;
    }

    public function testGetFailureNoAccessibleGetter(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Property "prop1" does not have accessible getter.');

        (new DtoTestObject([
            'prop1' => true,
            'prop2' => 5,
            'prop4' => 'foo'
        ]))->prop1;
    }

    public function testSetSuccessDirectPropertyWrite(): void
    {
        $obj = new DtoTestObject([
            'prop2' => 5,
            'prop3' => false,
            'prop4' => 'foo'
        ]);
        $obj->prop1 = 'test';

        $this->assertEquals('test', $obj->toArray()['prop1']);
    }

    public function testSetSuccessSetter(): void
    {
        $obj = new DtoTestObject([
            'prop2' => 5,
            'prop3' => false,
            'prop4' => 'foo'
        ]);
        $obj->prop4 = 'test';

        $this->assertEquals('test', $obj->toArray()['prop4']);
    }

    public function testSetFailureReadOnly(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Property "prop3" is read only.');

        $obj = new DtoTestObject([
            'prop2' => 5,
            'prop3' => false,
            'prop4' => 'foo'
        ]);
        /** @var mixed $obj */
        $obj->prop3 = true;
    }

    public function testSetFailureNoAccessibleSetter(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Property "prop2" does not have accessible setter.');

        $obj = new DtoTestObject([
            'prop2' => 5,
            'prop3' => false,
            'prop4' => 'foo'
        ]);
        $obj->prop2 = 3;
    }

    public function testIssetSuccess(): void
    {
        $obj = new DtoTestObject([
            'prop2' => 5,
            'prop4' => 'foo'
        ]);

        $this->assertTrue(isset($obj->prop2));
        $this->assertFalse(isset($obj->prop3));
    }

    public function testIssetFailureWriteOnly(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Property "prop4" is write only.');

        $obj = new DtoTestObject([
            'prop2' => 5,
            'prop4' => 'foo'
        ]);
        /** @var mixed $obj */
        $foo = isset($obj->prop4);
    }

    public function testIssetFailureNoAccessibleGetter(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Property "prop1" does not have accessible getter.');

        $obj = new DtoTestObject([
            'prop2' => 5,
            'prop4' => 'foo'
        ]);
        $foo = isset($obj->prop1);
    }

    public function testUnsetSuccess(): void
    {
        $obj = new DtoTestObject([
            'prop1' => 'test',
            'prop2' => 5,
            'prop4' => 'foo'
        ]);
        unset($obj->prop1);

        $this->assertNull($obj->toArray()['prop1']);
    }

    public function testUnsetFailureReadOnly(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Property "prop3" is read only.');


        $obj = new DtoTestObject([
            'prop3' => true,
            'prop2' => 5,
            'prop4' => 'foo'
        ]);
        unset($obj->prop3);
    }

    public function testUnsetFailureNoAccessibleSetter(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Property "prop2" does not have accessible setter.');


        $obj = new DtoTestObject([
            'prop3' => true,
            'prop2' => 5,
            'prop4' => 'foo'
        ]);
        unset($obj->prop2);
    }

    public function testSerialize(): void
    {
        $obj = new DtoTestObject([
            'prop1' => null,
            'prop2' => 5,
            'prop3' => true,
            'prop4' => 'boo'
        ]);

        $new = unserialize(serialize($obj));

        $this->assertSame($obj->toArray(), $new->toArray());
    }

    public function testDtoWithNotExistingProperty(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Property "notExistingProp" is not connected with the appropriate class field.');

        new DtoTestObjectWithNonExistentProperty(['notExistingProp' => 'test']);
    }

    public function testProcessingTypeError(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Property "prop2" must be of the type integer, string given.');

        new DtoTestObject(['prop2' => 'test']);
    }
}
