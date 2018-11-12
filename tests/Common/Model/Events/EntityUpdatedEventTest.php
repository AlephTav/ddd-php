<?php

namespace AlephTools\DDD\Tests\Common\Model\Events;

use PHPUnit\Framework\TestCase;
use AlephTools\DDD\Common\Model\Events\EntityUpdated;
use AlephTools\DDD\Common\Model\Identity\GlobalId;

class EntityUpdatedEventTest extends TestCase
{
    public function testEntityUpdatedEventCreation(): void
    {
        $id = GlobalId::create();
        $oldProperties = ['a' => 1, 'b' => 2];
        $newProperties = ['a' => 2, 'b' => 3];
        $event = new EntityUpdated(\stdClass::class, $id, $oldProperties, $newProperties);

        $this->assertSame($id, $event->id);
        $this->assertSame(\stdClass::class, $event->entity);
        $this->assertSame($oldProperties, $event->oldProperties);
        $this->assertSame($newProperties, $event->newProperties);
    }
}