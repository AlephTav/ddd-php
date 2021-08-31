<?php

declare(strict_types=1);

namespace AlephTools\DDD\Common\Application\Subscriber;

use AlephTools\DDD\Common\Application\EventStore;
use AlephTools\DDD\Common\Model\Events\DomainEvent;

class DefaultDomainEventSubscriber implements DomainEventSubscriber
{
    private EventStore $eventStore;

    /**
     * Constructor.
     *
     */
    public function __construct(EventStore $eventStore)
    {
        $this->eventStore = $eventStore;
    }

    /**
     * @param DomainEvent $event
     */
    public function handle($event): void
    {
        $this->eventStore->append($event);
    }

    /**
     */
    public function subscribedToEventType(): string
    {
        return DomainEvent::class;
    }
}
