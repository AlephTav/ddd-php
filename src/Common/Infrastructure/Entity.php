<?php

declare(strict_types=1);

namespace AlephTools\DDD\Common\Infrastructure;

use AlephTools\DDD\Common\Model\Events\DomainEvent;

abstract class Entity extends IdentifiedDomainObject
{
    protected bool $isEntityInstantiated = false;

    /**
     * @param array<string,mixed> $properties
     */
    public function __construct(array $properties = [])
    {
        parent::__construct($properties);
        $this->isEntityInstantiated = true;
    }

    protected function publishEvent(DomainEvent $event): void
    {
        if ($this->isEntityInstantiated) {
            $this->eventPublisher()->publish($event);
        }
    }

    protected function eventPublisher(): DomainEventPublisher
    {
        return ApplicationContext::get(DomainEventPublisher::class);
    }

    /**
     * Creates a copy of this domain object with the given property values.
     *
     * @param array<string,mixed> $properties
     * @return static
     */
    public function copyWith(array $properties = [])
    {
        $instance = parent::copyWith($properties);
        $instance->isEntityInstantiated = true;
        return $instance;
    }
}
