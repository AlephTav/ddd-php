<?php

declare(strict_types=1);

namespace AlephTools\DDD\Common\Infrastructure;

use AlephTools\DDD\Common\Model\Events\EntityCreated;
use AlephTools\DDD\Common\Model\Events\EntityDeleted;
use AlephTools\DDD\Common\Model\Events\EntityUpdated;

abstract class EventSourcedEntity extends Entity
{
    /**
     * @param array<string,mixed> $properties
     */
    public function __construct(array $properties = [], bool $suppressEntityCreatedEvent = true)
    {
        parent::__construct($properties);
        if (!$suppressEntityCreatedEvent) {
            $this->publishEntityCreatedEvent($this->toArray());
        }
    }

    /**
     * Sets new properties and generates EntityUpdated event.
     *
     * @param array<string,mixed> $newProperties
     * @param bool $strict Determines whether to throw exception for non-existing properties (TRUE).
     */
    protected function applyChanges(array $newProperties, bool $strict = true): void
    {
        $oldProperties = $this->getOldProperties($newProperties);
        [$oldNestedProperties, $newNestedProperties] = $this->computeNestedChanges($oldProperties, $newProperties);
        if ($newNestedProperties) {
            $this->assignProperties($newProperties, $strict);
            $this->publishEntityUpdatedEvent($oldNestedProperties, $newNestedProperties);
        }
    }

    /**
     * Sets new properties, generates EntityUpdated event and validates those properties.
     *
     * @param array<string,mixed> $newProperties
     */
    protected function applyChangesAndValidate(array $newProperties, bool $strict = true): void
    {
        $this->applyChanges($newProperties, $strict);
        $this->validate();
    }

    /**
     * @param array<string,mixed> $newProperties
     * @return array<string,mixed>
     */
    private function getOldProperties(array $newProperties): array
    {
        $oldProperties = [];
        foreach ($newProperties as $property => $_) {
            $oldProperties[$property] = $this->__get($property);
        }
        return $oldProperties;
    }

    /**
     * @param array<string,mixed> $properties1
     * @param array<string,mixed> $properties2
     * @psalm-return array{array<string,mixed>,array<string,mixed>}
     */
    private function computeNestedChanges(array $properties1, array $properties2): array
    {
        $oldProperties = [];
        $newProperties = [];
        foreach ($properties2 as $property => $value2) {
            $value1 = $properties1[$property];
            if ($value2 instanceof DomainObject) {
                if (!$value2->equals($value1)) {
                    if ($value1 instanceof DomainObject) {
                        $old = $value1->toNestedArray();
                        $oldProperties[$property] = $old;
                        $newProperties[$property] = $this->computeNestedChanges($old, $value2->toNestedArray())[1];
                    } else {
                        $oldProperties[$property] = $value1;
                        $newProperties[$property] = $value2->toNestedArray();
                    }
                }
            } elseif ($value2 !== $value1) {
                $oldProperties[$property] = $value1 instanceof DomainObject ? $value1->toNestedArray() : $value1;
                $newProperties[$property] = $value2;
            }
        }
        return [$oldProperties, $newProperties];
    }

    protected function publishEntityCreatedEvent(array $properties): void
    {
        $this->publishEvent(new EntityCreated(static::class, $this->id, $properties));
    }

    protected function publishEntityUpdatedEvent(array $oldProperties, array $newProperties): void
    {
        $newUpdatedEvent = new EntityUpdated(static::class, $this->id, $oldProperties, $newProperties);

        $eventPublisher = $this->eventPublisher();
        $events = $eventPublisher->getEvents();

        foreach ($events as &$event) {
            if ($event instanceof EntityUpdated && $event->entity === static::class &&
                $event->id && $event->id->equals($this->id)
            ) {
                $event = $event->merge($newUpdatedEvent);
                $eventPublisher->cleanEvents();
                $eventPublisher->publishAll($events);
                return;
            }
        }

        $eventPublisher->publish($newUpdatedEvent);
    }

    protected function publishEntityDeletedEvent(): void
    {
        $this->publishEvent(new EntityDeleted(static::class, $this->id));
    }
}
