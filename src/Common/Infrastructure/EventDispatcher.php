<?php

namespace AlephTools\DDD\Common\Infrastructure;

use AlephTools\DDD\Common\Model\Events\DomainEvent;

interface EventDispatcher
{
    /**
     * @param string $subscriber
     * @param DomainEvent $event
     * @param bool $async Determines whether the subscriber should be invoked in asynchronous way.
     * @return void
     */
    public function dispatch(string $subscriber, DomainEvent $event, bool $async): void;
}
