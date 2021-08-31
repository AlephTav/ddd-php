<?php

declare(strict_types=1);

namespace AlephTools\DDD\Tests\Common\Model\Exceptions;

use AlephTools\DDD\Common\Model\Exceptions\EntityNotFoundException;
use AlephTools\DDD\Common\Model\Identity\GlobalId;
use PHPUnit\Framework\TestCase;

class SomeEntityId extends GlobalId
{
}

/**
 * @internal
 */
class EntityNotFoundExceptionTest extends TestCase
{
    public function testGenerateMessageFromId(): void
    {
        $id = SomeEntityId::create();
        $e = new EntityNotFoundException($id);

        self::assertSame("Some entity [ID: $id] is not found.", $e->getMessage());
    }
}
