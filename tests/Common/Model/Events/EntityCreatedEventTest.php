<?php

namespace AlephTools\DDD\Tests\Common\Model\Events;

use PHPUnit\Framework\TestCase;
use AlephTools\DDD\Common\Model\Events\EntityCreated;
use AlephTools\DDD\Common\Model\Identity\GlobalId;

class EntityCreatedEventTest extends TestCase
{
    public function testEntityCreatedEventCreation(): void
    {
        $id = GlobalId::create();
        $properties = ['a' => 1, 'b' => 2];
        $event = new EntityCreated(\stdClass::class, $id, $properties);

        $this->assertSame($id, $event->id);
        $this->assertSame(\stdClass::class, $event->entity);
        $this->assertSame($properties, $event->properties);
    }
}