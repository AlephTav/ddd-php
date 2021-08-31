<?php

declare(strict_types=1);

namespace AlephTools\DDD\Common\Application\Subscriber;

use AlephTools\DDD\Common\Model\Events\DomainEvent;

interface DomainEventSubscriber
{
    /**
     * Handles the appropriate event.
     *
     * @param DomainEvent $event
     */
    public function handle($event): void;

    /**
     * Returns the class of an event to be handled.
     *
     * @psalm-return class-string<DomainEvent>
     */
    public function subscribedToEventType(): string;
}
