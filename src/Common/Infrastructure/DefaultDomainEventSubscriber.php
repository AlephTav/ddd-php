<?php

namespace AlephTools\DDD\Common\Infrastructure;

class DefaultDomainEventSubscriber implements DomainEventSubscriber
{
    /**
     * @var EventStore
     */
    private $eventStore;

    /**
     * Constructor.
     *
     * @param EventStore $eventStore
     */
    public function __construct(EventStore $eventStore)
    {
        $this->eventStore = $eventStore;
    }

    /**
     * @param DomainEvent $event
     * @return void
     */
    public function handle($event): void
    {
        $this->eventStore->append($event);
    }

    /**
     * @return string
     */
    public function subscribedToEventType(): string
    {
        return DomainEvent::class;
    }
}