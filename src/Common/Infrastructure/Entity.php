<?php

namespace AlephTools\DDD\Common\Infrastructure;

abstract class Entity extends IdentifiedDomainObject
{
    protected $isEntityInstantiated = false;

    public function __construct(array $properties = [])
    {
        parent::__construct($properties);
        $this->isEntityInstantiated = true;
    }

    protected function publishEvent(DomainEvent $event): void
    {
        if ($this->isEntityInstantiated) {
            ApplicationContext::get(DomainEventPublisher::class)->publish($event);
        }
    }
}