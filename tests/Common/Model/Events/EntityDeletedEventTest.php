<?php

declare(strict_types=1);

namespace Tests\AlephTools\DDD\Common\Model\Events;

use AlephTools\DDD\Common\Model\Events\EntityDeleted;
use AlephTools\DDD\Common\Model\Identity\GlobalId;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * @internal
 */
class EntityDeletedEventTest extends TestCase
{
    public function testEntityDeletedEventCreation(): void
    {
        $id = GlobalId::create();
        $event = new EntityDeleted(stdClass::class, $id);

        self::assertSame($id, $event->id);
        self::assertSame(stdClass::class, $event->entity);
    }
}
