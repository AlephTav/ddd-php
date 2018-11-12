<?php

namespace AlephTools\DDD\Common\Infrastructure;

interface EventStore
{
    public function append(DomainEvent $event): void;
}