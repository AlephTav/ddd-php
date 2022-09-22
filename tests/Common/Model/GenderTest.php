<?php

declare(strict_types=1);

namespace Tests\AlephTools\DDD\Common\Model;

use AlephTools\DDD\Common\Model\Gender;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class GenderTest extends TestCase
{
    public function testIdentification(): void
    {
        $female = Gender::FEMALE();
        self::assertTrue($female->isFemale());
        self::assertFalse($female->isMale());

        $male = Gender::MALE();
        self::assertTrue($male->isMale());
        self::assertFalse($male->isFemale());
    }
}
