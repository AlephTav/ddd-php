<?php

declare(strict_types=1);

namespace AlephTools\DDD\Tests\Common\Infrastructure\Enums;

use AlephTools\DDD\Common\Infrastructure\Enums\ValuedEnum;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @method static ONE(string $method = null)
 * @method static TWO(string $method = null)
 * @method static THREE(string $method = null)
 * @method static A(string $method = null)
 */
class ValuedEnumTestObject extends ValuedEnum
{
    private const ONE = 1;
    private const TWO = 2;
    private const THREE = 3;

    private const A = 'a';
}

/**
 * @internal
 */
class ValuedEnumTest extends TestCase
{
    public function testGetValue(): void
    {
        self::assertSame(1, ValuedEnumTestObject::ONE('value'));
        self::assertSame(2, ValuedEnumTestObject::TWO('value'));
        self::assertSame(3, ValuedEnumTestObject::THREE('value'));
    }

    public function testInvalidConstantValue(): void
    {
        $this->expectException(InvalidArgumentException::class);

        ValuedEnumTestObject::A('value');
    }
}
