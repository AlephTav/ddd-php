<?php

namespace AlephTools\DDD\Tests\Common\Infrastructure;

use DateTime;
use DateTimeInterface;
use AlephTools\DDD\Common\Model\Exceptions\InvalidArgumentException;
use AlephTools\DDD\Common\Infrastructure\Dto;
use PHPUnit\Framework\TestCase;

/**
 * @property string $prop1
 * @property int|null $prop2
 * @property float $prop3
 * @property bool|null $prop4
 * @property array $prop5
 * @property DateTimeInterface|null $prop6
 * @property mixed $prop7
 */
class TypedPropertyTestObject extends Dto
{
    private string $prop1 = '';
    private ?int $prop2 = null;
    private float $prop3 = -1;
    private ?bool $prop4 = null;
    private array $prop5 = [];
    private ?DateTimeInterface $prop6 = null;
    private $prop7;
}

class TypedPropertyDtoTest extends TestCase
{
    public function testDefaultPropertyValues(): void
    {
        $dto = new TypedPropertyTestObject();

        $this->assertSame('', $dto->prop1);
        $this->assertSame(null, $dto->prop2);
        $this->assertSame(-1.0, $dto->prop3);
        $this->assertSame(null, $dto->prop4);
        $this->assertSame([], $dto->prop5);
        $this->assertSame(null, $dto->prop6);
        $this->assertSame(null, $dto->prop7);
    }

    public function testReadWriteValues(): void
    {
        $dto = new TypedPropertyTestObject();

        $dto->prop1 = 'abc';
        $dto->prop2 = 100;
        $dto->prop3 = 0;
        $dto->prop4 = false;
        $dto->prop5 = [1, 2, 3];
        $dto->prop6 = new DateTime();
        $dto->prop7 = new \stdClass();

        $this->assertSame('abc', $dto->prop1);
        $this->assertSame(100, $dto->prop2);
        $this->assertSame(.0, $dto->prop3);
        $this->assertSame(false, $dto->prop4);
        $this->assertSame([1, 2, 3], $dto->prop5);
        $this->assertInstanceOf(DateTime::class, $dto->prop6);
        $this->assertInstanceOf(\stdClass::class, $dto->prop7);
    }

    public function testAssignInvalidPropertyValues(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Property "prop6" must be an instance of DateTimeInterface or null, int used.');

        new TypedPropertyTestObject([
            'prop7' => true,
            'prop6' => 123
        ]);
    }
}