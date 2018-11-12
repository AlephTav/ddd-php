<?php

namespace AlephTools\DDD\Tests\Common\Infrastructure\Enums;

use PHPUnit\Framework\TestCase;
use AlephTools\DDD\Common\Model\Gender;

class NamedEnumTest extends TestCase
{
    public function testGetName(): void
    {
        $this->assertSame('Female', Gender::FEMALE('name'));
        $this->assertSame('Male', Gender::MALE('name'));
    }
}