<?php

declare(strict_types=1);

namespace AlephTools\DDD\Tests\Common\Infrastructure;

use AlephTools\DDD\Common\Infrastructure\Entity;
use AlephTools\DDD\Common\Model\Events\DomainEvent;
use PHPUnit\Framework\TestCase;

/**
 * @property mixed $prop1
 * @property mixed $prop2
 */
class EntityTestObject extends Entity
{
    private $prop1;
    private $prop2;

    public function __construct($prop1 = null, $prop2 = null)
    {
        parent::__construct([
            'prop1' => $prop1,
            'prop2' => $prop2,
        ]);
    }

    public function setIsEntityInstantiated(bool $flag): void
    {
        $this->isEntityInstantiated = $flag;
    }

    public function isEntityInstantiated(): bool
    {
        return $this->isEntityInstantiated;
    }

    public function publish(DomainEvent $event): void
    {
        $this->publishEvent($event);
    }
}

/**
 * @internal
 */
class EntityTest extends TestCase
{
    use DomainEventPublisherAware;

    public function testConstructor(): void
    {
        $entity = new EntityTestObject();

        self::assertTrue($entity->isEntityInstantiated());
    }

    public function testPublishEventAfterInstantiation(): void
    {
        $event = new class() extends DomainEvent {};

        $entity = new EntityTestObject();
        $entity->publish($event);

        self::assertSame([$event], $this->publisher->getEvents());
    }

    public function testPublishEventBeforeInstantiation(): void
    {
        $event = new class() extends DomainEvent {};

        $entity = new EntityTestObject();
        $entity->setIsEntityInstantiated(false);
        $entity->publish($event);

        self::assertSame([], $this->publisher->getEvents());
    }

    public function testCopyEntity(): void
    {
        $entity = new EntityTestObject('abc', 123);
        $copy = $entity->copyWith(['prop2' => '@@@']);

        self::assertSame($entity->prop1, $copy->prop1);
        self::assertSame('@@@', $copy->prop2);
        self::assertTrue($copy->isEntityInstantiated());
    }
}
