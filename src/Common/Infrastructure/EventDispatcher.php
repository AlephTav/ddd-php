<?php

namespace AlephTools\DDD\Common\Infrastructure;

interface EventDispatcher
{
    public function dispatch(string $subscriber, DomainEvent $event): void;
}