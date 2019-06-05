<?php

namespace AlephTools\DDD\Common\Application\Subscriber;

use AlephTools\DDD\Common\Model\Events\DomainEvent;

interface DomainEventSubscriber
{
    /**
     * Handles the appropriate event.
     *
     * @param DomainEvent $event
     * @return void
     */
    public function handle($event): void;

    /**
     * Returns the class of an event to be handled.
     *
     * @return string
     */
    public function subscribedToEventType(): string;
}
