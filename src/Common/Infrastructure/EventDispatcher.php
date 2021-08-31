<?php

declare(strict_types=1);

namespace AlephTools\DDD\Common\Infrastructure;

use AlephTools\DDD\Common\Application\Subscriber\DomainEventSubscriber;
use AlephTools\DDD\Common\Model\Events\DomainEvent;

interface EventDispatcher
{
    /**
     * @param class-string<DomainEventSubscriber> $subscriber
     * @param bool $async Determines whether the subscriber should be invoked in asynchronous way.
     */
    public function dispatch(string $subscriber, DomainEvent $event, bool $async): void;
}
