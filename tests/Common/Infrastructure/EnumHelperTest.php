<?php

namespace AlephTools\DDD\Tests\Common\Infrastructure;

use AlephTools\DDD\Common\Infrastructure\EnumHelper;
use AlephTools\DDD\Common\Model\Exceptions\InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use AlephTools\DDD\Common\Model\Gender;

class EnumHelperTest extends TestCase
{
    public function testCastToEnumSuccess(): void
    {
        $female = EnumHelper::toEnum(Gender::class, 'FEMALE');

        $this->assertSame(Gender::FEMALE(), $female);
    }

    public function testCastToEnumFailure(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Constant "Gender::FOO" does not exist. Valid values are FEMALE, MALE.');

        EnumHelper::toEnum(Gender::class, 'FOO');
    }
}