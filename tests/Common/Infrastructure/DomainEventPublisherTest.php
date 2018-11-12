<?php

namespace AlephTools\DDD\Tests\Common\Infrastructure;

use PHPUnit\Framework\TestCase;
use AlephTools\DDD\Common\Infrastructure\DefaultDomainEventSubscriber;
use AlephTools\DDD\Common\Infrastructure\DomainEvent;
use AlephTools\DDD\Common\Infrastructure\DomainEventPublisher;
use AlephTools\DDD\Common\Infrastructure\DomainEventSubscriber;
use AlephTools\DDD\Common\Infrastructure\EventDispatcher;

class Event1TestObject extends DomainEvent {}
class Event2TestObject extends DomainEvent {}
class Event3TestObject extends Event2TestObject {}

class Event1SubscriberTestObject implements DomainEventSubscriber
{
    public function handle($event): void {}

    public function subscribedToEventType(): string
    {
        return Event1TestObject::class;
    }
}

class Event2SubscriberTestObject implements DomainEventSubscriber
{
    public function handle($event): void {}

    public function subscribedToEventType(): string
    {
        return Event2TestObject::class;
    }
}

class DomainEventPublisherTest extends TestCase
{
    /**
     * @var mixed
     */
    private $eventDispatcher;

    /**
     * @var array
     */
    private $result;

    public function setUp()
    {
        $this->result = [];

        $this->eventDispatcher = $this->getMockBuilder(EventDispatcher::class)
            ->setMethods(['dispatch'])
            ->getMock();
    }

    public function testInstantEventPublish(): void
    {
        $this->eventDispatcher->method('dispatch')
            ->willReturnCallback(function($subscriber, $event) {
                $this->result[] = [$subscriber, $event];
            });

        $publisher = new DomainEventPublisher($this->eventDispatcher);
        $publisher->turnOffTransactionMode();

        $publisher->subscribeAll([
            Event1SubscriberTestObject::class,
            Event2SubscriberTestObject::class
        ]);

        $subscribers = [
            DefaultDomainEventSubscriber::class => DomainEvent::class,
            Event1SubscriberTestObject::class => Event1TestObject::class,
            Event2SubscriberTestObject::class => Event2TestObject::class
        ];
        $this->assertSame($subscribers, $publisher->getSubscribers());

        $publisher->publishAll([
            $event1 = new Event1TestObject(),
            $event2 = new Event2TestObject(),
            $event3 = new Event3TestObject()
        ]);

        $this->assertCount(6, $this->result);

        $this->assertSame([
            [DefaultDomainEventSubscriber::class, $event1],
            [Event1SubscriberTestObject::class, $event1],
            [DefaultDomainEventSubscriber::class, $event2],
            [Event2SubscriberTestObject::class, $event2],
            [DefaultDomainEventSubscriber::class, $event3],
            [Event2SubscriberTestObject::class, $event3],
        ], $this->result);

        $this->assertSame($subscribers, $publisher->getSubscribers());

        $this->assertSame([], $publisher->getEvents());
    }

    public function testTransactionEventPublish(): void
    {
        $event4 = new Event1TestObject();
        $publisher = new DomainEventPublisher($this->eventDispatcher);

        $this->eventDispatcher->method('dispatch')
            ->willReturnCallback(function($subscriber, $event) use($publisher, $event4) {
                $this->result[] = [$subscriber, $event];

                if ($publisher->inTransactionMode() &&
                    $event instanceof Event3TestObject &&
                    !$publisher->getEvents()
                ) {
                    $publisher->publish($event4);
                }
            });

        $publisher->turnOnTransactionMode();

        $publisher->subscribeAll([
            Event1SubscriberTestObject::class,
            Event2SubscriberTestObject::class
        ]);

        $publisher->publishAll([
            $event1 = new Event1TestObject(),
            $event2 = new Event2TestObject(),
            $event3 = new Event3TestObject()
        ]);

        $this->assertCount(0, $this->result);

        $subscribers = [
            DefaultDomainEventSubscriber::class => DomainEvent::class,
            Event1SubscriberTestObject::class => Event1TestObject::class,
            Event2SubscriberTestObject::class => Event2TestObject::class
        ];
        $this->assertSame($subscribers, $publisher->getSubscribers());

        $publisher->turnOffTransactionMode();

        $this->assertCount(8, $this->result);

        $this->assertSame([
            [DefaultDomainEventSubscriber::class, $event1],
            [Event1SubscriberTestObject::class, $event1],
            [DefaultDomainEventSubscriber::class, $event2],
            [Event2SubscriberTestObject::class, $event2],
            [DefaultDomainEventSubscriber::class, $event3],
            [Event2SubscriberTestObject::class, $event3],
            [DefaultDomainEventSubscriber::class, $event4],
            [Event1SubscriberTestObject::class, $event4],
        ], $this->result);

        $this->assertSame($subscribers, $publisher->getSubscribers());

        $this->assertSame([], $publisher->getEvents());
    }
}