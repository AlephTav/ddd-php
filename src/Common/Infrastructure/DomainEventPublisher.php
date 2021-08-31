<?php

declare(strict_types=1);

namespace AlephTools\DDD\Common\Infrastructure;

use AlephTools\DDD\Common\Application\Subscriber\DomainEventSubscriber;
use AlephTools\DDD\Common\Model\Events\DomainEvent;
use ReflectionClass;
use ReflectionException;

class DomainEventPublisher
{
    private EventDispatcher $dispatcher;

    /**
     * @var array<class-string<DomainEventSubscriber>,class-string<DomainEvent>>
     */
    private array $subscribers = [];

    /**
     * @var DomainEvent[]
     */
    private array $events = [];

    private bool $queued = true;
    private bool $async = true;
    private bool $isPublishing = false;

    public function __construct(EventDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @return DomainEvent[]
     */
    public function getEvents(): array
    {
        return $this->events;
    }

    public function getSubscribers(): array
    {
        return $this->subscribers;
    }

    public function cleanEvents(): void
    {
        $this->events = [];
    }

    public function cleanSubscribers(): void
    {
        $this->subscribers = [];
    }

    public function cleanAll(): void
    {
        $this->cleanEvents();
        $this->cleanSubscribers();
    }

    public function inAsyncMode(): bool
    {
        return $this->async;
    }

    public function async(bool $flag): void
    {
        $this->async = $flag;
    }

    public function inQueueMode(): bool
    {
        return $this->queued;
    }

    public function queued(bool $flag): void
    {
        $this->queued = $flag;
    }

    public function release(): void
    {
        if ($this->queued && !$this->isPublishing) {
            $this->isPublishing = true;
            $this->dispatchAll();
            $this->isPublishing = false;
        }
    }

    /**
     * @param list<class-string<DomainEventSubscriber>> $subscribers
     * @throws ReflectionException
     */
    public function subscribeAll(array $subscribers): void
    {
        foreach ($subscribers as $subscriber) {
            $this->subscribe($subscriber);
        }
    }

    /**
     * @param class-string<DomainEventSubscriber> $subscriber
     * @throws ReflectionException
     */
    public function subscribe(string $subscriber): void
    {
        if (!isset($this->subscribers[$subscriber])) {
            $subscriberInstance = (new ReflectionClass($subscriber))
                ->newInstanceWithoutConstructor();
            $this->subscribers[$subscriber] = $subscriberInstance->subscribedToEventType();
        }
    }

    /**
     * @param DomainEvent[] $events
     */
    public function publishAll(array $events): void
    {
        if ($this->queued) {
            $this->events = array_merge($this->events, $events);
        } else {
            foreach ($events as $event) {
                $this->dispatch($event);
            }
        }
    }

    public function publish(DomainEvent $event): void
    {
        if ($this->queued) {
            $this->events[] = $event;
        } else {
            $this->dispatch($event);
        }
    }

    private function dispatchAll(): void
    {
        try {
            while ($this->events) {
                $currentEvents = $this->events;
                $this->events = [];
                foreach ($currentEvents as $event) {
                    $this->dispatch($event);
                }
            }
        } finally {
            $this->events = [];
        }
    }

    private function dispatch(DomainEvent $event): void
    {
        foreach ($this->findMatchedSubscribers($event) as $subscriber) {
            $this->dispatcher->dispatch($subscriber, $event, $this->async);
        }
    }

    /**
     * @psalm-return list<class-string<DomainEventSubscriber>>
     */
    private function findMatchedSubscribers(DomainEvent $event): array
    {
        $subscribers = [];
        foreach ($this->subscribers as $subscriber => $eventType) {
            if (is_a($event, $eventType)) {
                $subscribers[] = $subscriber;
            }
        }
        return $subscribers;
    }
}
