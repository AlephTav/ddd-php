<?php

declare(strict_types=1);

namespace Tests\AlephTools\DDD\Common\Application\Subscriber;

use AlephTools\DDD\Common\Application\EventStore;
use AlephTools\DDD\Common\Application\Subscriber\DefaultDomainEventSubscriber;
use AlephTools\DDD\Common\Model\Events\DomainEvent;
use PHPUnit\Framework\TestCase;

class DefaultEventTestObject extends DomainEvent
{
}

/**
 * @internal
 */
class DefaultDomainEventSubscriberTest extends TestCase
{
    public function testSubscribedEventType(): void
    {
        /** @var EventStore $eventStore */
        $eventStore = $this->getEventStoreMock();
        $subscriber = new DefaultDomainEventSubscriber($eventStore);

        self::assertSame(DomainEvent::class, $subscriber->subscribedToEventType());
    }

    public function testEventHandling(): void
    {
        $eventStore = $this->getEventStoreMock();
        $eventStore->method('append')
            ->willReturnCallback(function ($event): void {
                $this->assertInstanceOf(DefaultEventTestObject::class, $event);
            });

        /** @var EventStore $eventStore */
        $subscriber = new DefaultDomainEventSubscriber($eventStore);
        $subscriber->handle(new DefaultEventTestObject());
    }

    private function getEventStoreMock()
    {
        return $this->getMockBuilder(EventStore::class)
            ->onlyMethods(['append'])
            ->getMock();
    }
}
