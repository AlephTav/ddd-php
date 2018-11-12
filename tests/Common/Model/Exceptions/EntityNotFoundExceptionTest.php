<?php

namespace AlephTools\DDD\Tests\Common\Model\Exceptions;

use PHPUnit\Framework\TestCase;
use AlephTools\DDD\Common\Model\Exceptions\EntityNotFoundException;
use AlephTools\DDD\Common\Model\Identity\GlobalId;

class SomeEntityId extends GlobalId {}

class EntityNotFoundExceptionTest extends TestCase
{
    public function testGenerateMessageFromId(): void
    {
        $id = SomeEntityId::create();
        $e = new EntityNotFoundException($id);

        $this->assertSame("Some entity [ID: $id] is not found.", $e->getMessage());
    }
}