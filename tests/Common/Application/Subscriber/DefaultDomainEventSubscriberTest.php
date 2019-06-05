<?php

namespace AlephTools\DDD\Tests\Common\Application\Subscriber;

use AlephTools\DDD\Common\Application\EventStore;
use AlephTools\DDD\Common\Application\Subscriber\DefaultDomainEventSubscriber;
use AlephTools\DDD\Common\Model\Events\DomainEvent;
use PHPUnit\Framework\TestCase;

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
