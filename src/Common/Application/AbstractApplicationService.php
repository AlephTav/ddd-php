<?php

declare(strict_types=1);

namespace AlephTools\DDD\Common\Application;

use AlephTools\DDD\Common\Infrastructure\ApplicationContext;
use AlephTools\DDD\Common\Infrastructure\Async;
use AlephTools\DDD\Common\Infrastructure\DomainEventPublisher;

abstract class AbstractApplicationService
{
    protected function runAsync(mixed $callback, array $params = []): void
    {
        $this->async()->run($callback, $params);
    }

    protected function executeAtomically(callable $callback): mixed
    {
        return $this->unitOfWork()->execute($callback);
    }

    protected function async(): Async
    {
        return ApplicationContext::get(Async::class);
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
