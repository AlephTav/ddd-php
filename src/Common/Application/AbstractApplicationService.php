<?php

namespace AlephTools\DDD\Common\Application;

use AlephTools\DDD\Common\Infrastructure\ApplicationContext;
use AlephTools\DDD\Common\Infrastructure\DomainEventPublisher;

abstract class AbstractApplicationService
{
    protected function executeAtomically(callable $callback)
    {
        return $this->unitOfWork()->execute($callback);
    }

    protected function unitOfWork(): UnitOfWork
    {
        return ApplicationContext::get(UnitOfWork::class);
    }

    protected function eventPublisher(): DomainEventPublisher
    {
        return ApplicationContext::get(DomainEventPublisher::class);
    }
}
