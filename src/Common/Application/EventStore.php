<?php

declare(strict_types=1);

namespace AlephTools\DDD\Common\Application;

use AlephTools\DDD\Common\Model\Events\DomainEvent;

interface EventStore
{
    public function append(DomainEvent $event): void;
}
