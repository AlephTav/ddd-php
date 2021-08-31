<?php

declare(strict_types=1);

namespace AlephTools\DDD\Tests\Common\Infrastructure;

use AlephTools\DDD\Common\Infrastructure\Dto;
use AlephTools\DDD\Common\Model\Exceptions\InvalidArgumentException;
use DateTime;
use DateTimeInterface;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * @property string $prop1
 * @property int|null $prop2
 * @property float $prop3
 * @property bool|null $prop4
 * @property array $prop5
 * @property DateTimeInterface|null $prop6
 * @property mixed $prop7
 */
class TypedPropertyFastTestObject extends Dto
{
    private string $prop1 = '';
    private ?int $prop2 = null;
    private float $prop3 = -1;
    private ?bool $prop4 = null;
    private array $prop5 = [];
    private ?DateTimeInterface $prop6 = null;
    private $prop7;

    protected static function getPropertyDefinitions(): ?array
    {
        return [
            'prop1' => self::PROP_READ_WRITE,
            'prop2' => self::PROP_READ_WRITE,
            'prop3' => self::PROP_READ_WRITE,
            'prop4' => self::PROP_READ_WRITE,
            'prop5' => self::PROP_READ_WRITE,
            'prop6' => self::PROP_READ_WRITE,
            'prop7' => self::PROP_READ_WRITE,
        ];
    }
}

/**
 * @internal
 */
class TypedPropertyFastDtoTest extends TestCase
{
    public function testDefaultPropertyValues(): void
    {
        $dto = new TypedPropertyTestObject();

        self::assertSame('', $dto->prop1);
        self::assertNull($dto->prop2);
        self::assertSame(-1.0, $dto->prop3);
        self::assertNull($dto->prop4);
        self::assertSame([], $dto->prop5);
        self::assertNull($dto->prop6);
        self::assertNull($dto->prop7);
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
        $dto->prop7 = new stdClass();

        self::assertSame('abc', $dto->prop1);
        self::assertSame(100, $dto->prop2);
        self::assertSame(.0, $dto->prop3);
        self::assertFalse($dto->prop4);
        self::assertSame([1, 2, 3], $dto->prop5);
        self::assertInstanceOf(DateTime::class, $dto->prop6);
        self::assertInstanceOf(stdClass::class, $dto->prop7);
    }

    public function testAssignInvalidPropertyValues(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Property "prop6" must be an instance of DateTimeInterface or null, int used.');

        new TypedPropertyTestObject([
            'prop7' => true,
            'prop6' => 123,
        ]);
    }
}
