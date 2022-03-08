<?php

declare(strict_types=1);

namespace AlephTools\DDD\Tests\Common\Infrastructure\Enums;

use AlephTools\DDD\Common\Infrastructure\Enums\ValuedEnum;
use PHPUnit\Framework\TestCase;

/**
 * @method static ONE(string $method = null)
 * @method static TWO(string $method = null)
 * @method static THREE(string $method = null)
 * @method static HALF(string $method = null)
 */
class ValuedEnumTestObject extends ValuedEnum
{
    private const ONE = 1;
    private const TWO = 2;
    private const THREE = 3;
    private const HALF = 0.5;
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
        self::assertSame(0.5, ValuedEnumTestObject::HALF('value'));
    }
}
