<?php

namespace AlephTools\DDD\Tests\Common\Infrastructure;

use PHPUnit\Framework\TestCase;
use AlephTools\DDD\Common\Infrastructure\ApplicationContext;
use AlephTools\DDD\Common\Infrastructure\DomainEvent;
use AlephTools\DDD\Common\Infrastructure\DomainEventPublisher;
use AlephTools\DDD\Common\Infrastructure\Entity;
use AlephTools\DDD\Common\Infrastructure\EventDispatcher;

class EntityTestObject extends Entity
{
    public function setIsEntityInstantiated(bool $flag): void
    {
        $this->isEntityInstantiated = $flag;
    }

    public function isEntityInstantiated(): bool
    {
        return $this->isEntityInstantiated;
    }

    public function publish(DomainEvent $event)
    {
        $this->publishEvent($event);
    }
}

class EntityTest extends TestCase
{
    use DomainEventPublisherAware;

    public function testConstructor(): void
    {
        $entity = new EntityTestObject();

        $this->assertTrue($entity->isEntityInstantiated());
    }

    public function testPublishEventAfterInstantiation(): void
    {
        $event = new class extends DomainEvent {};

        $entity = new EntityTestObject();
        $entity->publish($event);

        $this->assertSame([$event], $this->publisher->getEvents());
    }

    public function testPublishEventBeforeInstantiation(): void
    {
        $event = new class extends DomainEvent {};

        $entity = new EntityTestObject();
        $entity->setIsEntityInstantiated(false);
        $entity->publish($event);

        $this->assertSame([], $this->publisher->getEvents());
    }
}