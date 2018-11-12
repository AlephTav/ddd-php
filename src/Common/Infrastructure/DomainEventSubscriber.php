<?php

namespace AlephTools\DDD\Common\Infrastructure;

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