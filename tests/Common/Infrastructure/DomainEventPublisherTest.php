<?php

declare(strict_types=1);

namespace Tests\AlephTools\DDD\Common\Infrastructure;

use AlephTools\DDD\Common\Application\Subscriber\DefaultDomainEventSubscriber;
use AlephTools\DDD\Common\Application\Subscriber\DomainEventSubscriber;
use AlephTools\DDD\Common\Infrastructure\DomainEventPublisher;
use AlephTools\DDD\Common\Infrastructure\EventDispatcher;
use AlephTools\DDD\Common\Model\Events\DomainEvent;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class Event1TestObject extends DomainEvent
{
}
class Event2TestObject extends DomainEvent
{
}
class Event3TestObject extends Event2TestObject
{
}

class Event1SubscriberTestObject implements DomainEventSubscriber
{
    public function handle($event): void
    {
    }

    public function subscribedToEventType(): string
    {
        return Event1TestObject::class;
    }
}

class Event2SubscriberTestObject implements DomainEventSubscriber
{
    public function handle($event): void
    {
    }

    public function subscribedToEventType(): string
    {
        return Event2TestObject::class;
    }
}

/**
 * @internal
 */
class DomainEventPublisherTest extends TestCase
{
    /**
     * @var MockObject&EventDispatcher
     */
    private $eventDispatcher;

    /**
     * @var array
     */
    private $result;

    protected function setUp(): void
    {
        $this->result = [];

        $this->eventDispatcher = $this->getMockBuilder(EventDispatcher::class)
            ->setMethods(['dispatch'])
            ->getMock();
    }

    public function testAsyncMode(): void
    {
        $publisher = new DomainEventPublisher($this->eventDispatcher);

        self::assertTrue($publisher->inAsyncMode());
        $publisher->async(false);
        self::assertFalse($publisher->inAsyncMode());
        $publisher->async(true);
        self::assertTrue($publisher->inAsyncMode());
    }

    public function testInstantEventPublish(): void
    {
        $this->eventDispatcher->method('dispatch')
            ->willReturnCallback(function (string $subscriber, DomainEvent $event, bool $async): void {
                $this->result[] = [$subscriber, $event, $async];
            });

        $publisher = new DomainEventPublisher($this->eventDispatcher);
        $publisher->subscribe(DefaultDomainEventSubscriber::class);
        $publisher->queued(false);

        $publisher->subscribeAll([
            Event1SubscriberTestObject::class,
            Event2SubscriberTestObject::class,
        ]);

        $subscribers = [
            DefaultDomainEventSubscriber::class => DomainEvent::class,
            Event1SubscriberTestObject::class => Event1TestObject::class,
            Event2SubscriberTestObject::class => Event2TestObject::class,
        ];
        self::assertSame($subscribers, $publisher->getSubscribers());

        $publisher->publishAll([
            $event1 = new Event1TestObject(),
            $event2 = new Event2TestObject(),
        ]);

        $publisher->async(false);
        $publisher->publish($event3 = new Event3TestObject());

        self::assertCount(6, $this->result);

        self::assertSame([
            [DefaultDomainEventSubscriber::class, $event1, true],
            [Event1SubscriberTestObject::class, $event1, true],
            [DefaultDomainEventSubscriber::class, $event2, true],
            [Event2SubscriberTestObject::class, $event2, true],
            [DefaultDomainEventSubscriber::class, $event3, false],
            [Event2SubscriberTestObject::class, $event3, false],
        ], $this->result);

        self::assertSame($subscribers, $publisher->getSubscribers());

        self::assertSame([], $publisher->getEvents());
    }

    public function testQueuedEventPublish(): void
    {
        $event4 = new Event1TestObject();
        $publisher = new DomainEventPublisher($this->eventDispatcher);
        $publisher->subscribe(DefaultDomainEventSubscriber::class);

        $this->eventDispatcher->method('dispatch')
            ->willReturnCallback(function (string $subscriber, DomainEvent $event) use ($publisher, $event4): void {
                ;
                $this->result[] = [$subscriber, $event];

                if ($publisher->inQueueMode() &&
                    $event instanceof Event3TestObject &&
                    !$publisher->getEvents()
                ) {
                    $publisher->publish($event4);
                }
            });

        $publisher->queued(true);

        $publisher->subscribeAll([
            Event1SubscriberTestObject::class,
            Event2SubscriberTestObject::class,
        ]);

        $publisher->publish($event1 = new Event1TestObject());

        $publisher->publishAll([
            $event2 = new Event2TestObject(),
            $event3 = new Event3TestObject(),
        ]);

        self::assertCount(0, $this->result);

        $subscribers = [
            DefaultDomainEventSubscriber::class => DomainEvent::class,
            Event1SubscriberTestObject::class => Event1TestObject::class,
            Event2SubscriberTestObject::class => Event2TestObject::class,
        ];
        self::assertSame($subscribers, $publisher->getSubscribers());

        $publisher->release();

        self::assertCount(8, $this->result);

        self::assertSame([
            [DefaultDomainEventSubscriber::class, $event1],
            [Event1SubscriberTestObject::class, $event1],
            [DefaultDomainEventSubscriber::class, $event2],
            [Event2SubscriberTestObject::class, $event2],
            [DefaultDomainEventSubscriber::class, $event3],
            [Event2SubscriberTestObject::class, $event3],
            [DefaultDomainEventSubscriber::class, $event4],
            [Event1SubscriberTestObject::class, $event4],
        ], $this->result);

        self::assertSame($subscribers, $publisher->getSubscribers());

        self::assertSame([], $publisher->getEvents());
    }
}
