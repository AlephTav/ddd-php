<?php

namespace AlephTools\DDD\Common\Application\Subscriber;

use AlephTools\DDD\Common\Application\EventStore;
use AlephTools\DDD\Common\Model\Events\DomainEvent;

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
