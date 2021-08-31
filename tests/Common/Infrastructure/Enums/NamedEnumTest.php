<?php

declare(strict_types=1);

namespace AlephTools\DDD\Tests\Common\Infrastructure\Enums;

use AlephTools\DDD\Common\Model\Gender;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class NamedEnumTest extends TestCase
{
    public function testGetName(): void
    {
        self::assertSame('Female', Gender::FEMALE('name'));
        self::assertSame('Male', Gender::MALE('name'));
    }
}
