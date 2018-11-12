<?php

namespace AlephTools\DDD\Tests\Common\Infrastructure;

use PHPUnit\Framework\TestCase;
use AlephTools\DDD\Common\Infrastructure\DefaultDomainEventSubscriber;
use AlephTools\DDD\Common\Infrastructure\DomainEvent;
use AlephTools\DDD\Common\Infrastructure\EventStore;

class DefaultEventTestObject extends DomainEvent {}

class DefaultDomainEventSubscriberTest extends TestCase
{
    public function testSubscribedEventType(): void
    {
        /** @var EventStore $eventStore */
        $eventStore = $this->getEventStoreMock();
        $subscriber = new DefaultDomainEventSubscriber($eventStore);

        $this->assertSame(DomainEvent::class, $subscriber->subscribedToEventType());
    }

    public function testEventHandling(): void
    {
        $eventStore = $this->getEventStoreMock();
        $eventStore->method('append')
            ->willReturnCallback(function($event) {
                $this->assertInstanceOf(DefaultEventTestObject::class, $event);
            });

        /** @var EventStore $eventStore */
        $subscriber = new DefaultDomainEventSubscriber($eventStore);
        $subscriber->handle(new DefaultEventTestObject());
    }

    private function getEventStoreMock()
    {
        return $this->getMockBuilder(EventStore::class)
            ->setMethods(['append'])
            ->getMock();
    }
}