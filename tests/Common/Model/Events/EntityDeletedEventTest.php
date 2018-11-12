<?php

namespace AlephTools\DDD\Tests\Common\Model\Events;

use PHPUnit\Framework\TestCase;
use AlephTools\DDD\Common\Model\Events\EntityDeleted;
use AlephTools\DDD\Common\Model\Identity\GlobalId;

class EntityDeletedEventTest extends TestCase
{
    public function testEntityDeletedEventCreation(): void
    {
        $id = GlobalId::create();
        $event = new EntityDeleted(\stdClass::class, $id);

        $this->assertSame($id, $event->id);
        $this->assertSame(\stdClass::class, $event->entity);
    }
}