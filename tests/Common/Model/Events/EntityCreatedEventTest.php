<?php

declare(strict_types=1);

namespace AlephTools\DDD\Tests\Common\Model\Events;

use AlephTools\DDD\Common\Model\Events\EntityCreated;
use AlephTools\DDD\Common\Model\Identity\GlobalId;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * @internal
 */
class EntityCreatedEventTest extends TestCase
{
    public function testEntityCreatedEventCreation(): void
    {
        $id = GlobalId::create();
        $properties = ['a' => 1, 'b' => 2];
        $event = new EntityCreated(stdClass::class, $id, $properties);

        self::assertSame($id, $event->id);
        self::assertSame(stdClass::class, $event->entity);
        self::assertSame($properties, $event->properties);
    }

    public function testCopyWith(): void
    {
        $id = GlobalId::create();
        $event = new EntityCreated(stdClass::class, $id, ['c' => 3]);

        $copy = $event->copyWith([
            'properties' => $properties = ['a' => 1, 'b' => 2],
        ]);

        self::assertSame($event->id, $copy->id);
        self::assertSame($event->entity, $copy->entity);
        self::assertSame($properties, $copy->properties);
    }
}
