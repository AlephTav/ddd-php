<?php

namespace AlephTools\DDD\Tests\Common\Model;

use AlephTools\DDD\Common\Model\Gender;
use PHPUnit\Framework\TestCase;

class GenderTest extends TestCase
{
    public function testIdentification(): void
    {
        $female = Gender::FEMALE();
        $this->assertTrue($female->isFemale());
        $this->assertFalse($female->isMale());

        $male = Gender::MALE();
        $this->assertTrue($male->isMale());
        $this->assertFalse($male->isFemale());
    }
}