<?php

declare(strict_types=1);

namespace AlephTools\DDD\Tests\Common\Infrastructure;

use AlephTools\DDD\Common\Infrastructure\DynamicStrictDto;
use AlephTools\DDD\Common\Infrastructure\DynamicWeakDto;
use AlephTools\DDD\Common\Infrastructure\Exceptions\NonExistentPropertyException;
use PHPUnit\Framework\TestCase;

/**
 * @property mixed $prop1
 * @property mixed $prop2
 * @property mixed $prop3
 */
class DynamicDtoTestObject extends DynamicStrictDto
{
    private $prop1;
    private $prop2;
    private $prop3;
}

/**
 * @property mixed $prop1
 * @property mixed $prop2
 * @property mixed $prop3
 */
class DynamicWeakDtoTestObject extends DynamicWeakDto
{
    private $prop1;
    private $prop2;
    private $prop3;
}

/**
 * @internal
 */
class DynamicDtoTest extends TestCase
{
    public function testToArray(): void
    {
        $dto = new DynamicDtoTestObject([
            'prop1' => 1,
            'prop2' => 2,
        ]);

        self::assertSame([
            'prop1' => 1,
            'prop2' => 2,
        ], $dto->toArray());
    }

    public function testToNestedArray(): void
    {
        $dto = new DynamicDtoTestObject([
            'prop1' => new DynamicDtoTestObject([
                'prop2' => 2,
            ]),
            'prop3' => 3,
        ]);

        self::assertSame([
            'prop1' => [
                'prop2' => 2,
            ],
            'prop3' => 3,
        ], $dto->toNestedArray());
    }

    public function testDynamicStrictDto(): void
    {
        $this->expectException(NonExistentPropertyException::class);

        new DynamicDtoTestObject([
            'prop1' => 1,
            'prop4' => 4,
        ]);
    }

    public function testDynamicWeakDto(): void
    {
        $dto = new DynamicWeakDtoTestObject([
            'prop1' => 1,
            'prop4' => 4,
        ]);

        self::assertSame(['prop1' => 1], $dto->toArray());
    }
}
