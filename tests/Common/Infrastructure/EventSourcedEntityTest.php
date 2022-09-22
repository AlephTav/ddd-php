<?php

declare(strict_types=1);

namespace Tests\AlephTools\DDD\Common\Infrastructure;

use AlephTools\DDD\Common\Infrastructure\EventSourcedEntity;
use AlephTools\DDD\Common\Model\Events\EntityCreated;
use AlephTools\DDD\Common\Model\Events\EntityDeleted;
use AlephTools\DDD\Common\Model\Events\EntityUpdated;
use AlephTools\DDD\Common\Model\Identity\LocalId;
use PHPUnit\Framework\TestCase;

/**
 * @property mixed $prop1
 * @property mixed $prop2
 * @property mixed $prop3
 */
class EventSourcedEntityTestObject extends EventSourcedEntity
{
    private $prop1;
    private $prop2;
    private $prop3 = true;

    public function assign(array $properties): void
    {
        $this->applyChangesAndValidate($properties);
    }

    public function delete(): void
    {
        $this->publishEntityDeletedEvent();
    }
}

/**
 * @internal
 */
class EventSourcedEntityTest extends TestCase
{
    use DomainEventPublisherAware;

    public function testCreationWithEvent(): void
    {
        $id = new class (1) extends LocalId {};
        $properties = [
            'id' => $id,
            'prop1' => 'a',
            'prop2' => true,
        ];
        new EventSourcedEntityTestObject($properties, false);
        $properties['prop3'] = true;

        $events = $this->publisher->getEvents();

        self::assertCount(1, $events);
        self::assertInstanceOf(EntityCreated::class, $events[0]);
        self::assertTrue($events[0]->id->equals($id));
        self::assertSame(EventSourcedEntityTestObject::class, $events[0]->entity);
        self::assertSame($properties, $events[0]->properties);
    }

    public function testCreationWithoutEvent(): void
    {
        new EventSourcedEntityTestObject([], true);

        self::assertCount(0, $this->publisher->getEvents());
    }

    public function testDeleteEntity(): void
    {
        $id = new class (1) extends LocalId {};
        $entity = new EventSourcedEntityTestObject(['id' => $id], true);
        $entity->delete();

        $events = $this->publisher->getEvents();

        self::assertCount(1, $events);
        self::assertInstanceOf(EntityDeleted::class, $events[0]);
        self::assertTrue($events[0]->id->equals($id));
        self::assertSame(EventSourcedEntityTestObject::class, $events[0]->entity);
    }

    public function testUpdateScalarProperties(): void
    {
        $id = new class (1) extends LocalId {};
        $properties = [
            'id' => $id,
            'prop1' => 'a',
            'prop2' => true,
        ];
        $entity = new EventSourcedEntityTestObject($properties, true);

        $entity->assign([
            'prop1' => 123,
            'prop2' => 'b',
        ]);
        $entity->assign([
            'prop1' => 345,
            'prop3' => 'abc',
        ]);

        $events = $this->publisher->getEvents();

        self::assertCount(1, $events);
        self::assertInstanceOf(EntityUpdated::class, $events[0]);
        self::assertTrue($events[0]->id->equals($id));
        self::assertSame(EventSourcedEntityTestObject::class, $events[0]->entity);
        self::assertSame(['prop1' => 'a', 'prop2' => true, 'prop3' => true], $events[0]->oldProperties);
        self::assertSame(['prop1' => 345, 'prop2' => 'b', 'prop3' => 'abc'], $events[0]->newProperties);
    }

    public function testUpdateNestedProperties(): void
    {
        $properties = [
            'prop1' => 'a',
            'prop2' => true,
            'prop3' => 123,
        ];

        // to generate EntityCreated event
        new EventSourcedEntityTestObject($properties, false);

        $entity1 = new EventSourcedEntityTestObject($properties, true);

        $properties = [
            'prop1' => false,
            'prop2' => 321,
            'prop3' => 'b',
        ];
        $entity2 = new EventSourcedEntityTestObject($properties, true);

        $properties = [
            'prop1' => 'a',
            'prop2' => false,
            'prop3' => 'foo',
        ];
        $entity3 = new EventSourcedEntityTestObject($properties, true);

        $id = new class (10) extends LocalId {};
        $properties = [
            'id' => $id,
            'prop1' => $entity1,
            'prop2' => $entity2,
            'prop3' => 'test',
        ];
        $entity = new EventSourcedEntityTestObject($properties, true);

        $entity->assign([
            'prop1' => $entity3,
            'prop2' => $entity2,
            'prop3' => $entity1,
        ]);
        $entity->assign([
            'prop1' => $entity2,
            'prop2' => $entity2,
            'prop3' => $entity3,
        ]);

        $events = $this->publisher->getEvents();

        self::assertCount(2, $events);
        self::assertInstanceOf(EntityCreated::class, $events[0]);
        self::assertSame(EventSourcedEntityTestObject::class, $events[0]->entity);
        self::assertSame(
            [
                'id' => null,
                'prop1' => 'a',
                'prop2' => true,
                'prop3' => 123,
            ],
            $events[0]->properties
        );

        self::assertInstanceOf(EntityUpdated::class, $events[1]);
        self::assertTrue($events[1]->id->equals($id));
        self::assertSame(EventSourcedEntityTestObject::class, $events[1]->entity);

        self::assertEquals([
            'prop1' => [
                'id' => null,
                'prop1' => 'a',
                'prop2' => true,
                'prop3' => 123,
            ],
            'prop3' => 'test',
        ], $events[1]->oldProperties);

        self::assertEquals([
            'prop1' => [
                'prop1' => false,
                'prop2' => 321,
                'prop3' => 'b',
            ],
            'prop3' => [
                'id' => null,
                'prop1' => 'a',
                'prop2' => false,
                'prop3' => 'foo',
            ],
        ], $events[1]->newProperties);
    }
}
