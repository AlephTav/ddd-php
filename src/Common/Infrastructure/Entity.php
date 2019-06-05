<?php

namespace AlephTools\DDD\Common\Infrastructure;

use AlephTools\DDD\Common\Model\Events\DomainEvent;
use ReflectionClass;

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

    /**
     * Creates a copy of this domain object with the given property values.
     *
     * @param array $properties
     * @return static
     */
    public function copyWith(array $properties = [])
    {
        /** @var static $instance */
        $instance = (new ReflectionClass($this))->newInstanceWithoutConstructor();
        $instance->init();
        $instance->assignPropertiesAndValidate(array_merge($this->toArray(), $properties));
        $instance->isEntityInstantiated = true;
        return $instance;
    }
}
