<?php

declare(strict_types=1);

namespace AlephTools\DDD\Tests\Common\Infrastructure;

use AlephTools\DDD\Common\Infrastructure\ApplicationContext;
use AlephTools\DDD\Common\Infrastructure\DomainEventPublisher;
use AlephTools\DDD\Common\Infrastructure\EventDispatcher;
use PHPUnit\Framework\MockObject\MockBuilder;

trait DomainEventPublisherAware
{
    /**
     * @var DomainEventPublisher
     */
    protected $publisher;

    public function setUp(): void
    {
        /** @var MockBuilder $dispatcher */
        $dispatcher = $this->getMockBuilder(EventDispatcher::class);
        $dispatcher = $dispatcher->setMethods(['dispatch'])->getMock();

        /**
         * @var EventDispatcher $dispatcher
         */
        $this->publisher = new DomainEventPublisher($dispatcher);
        $this->publisher->queued(true);
        $this->publisher->cleanAll();

        ApplicationContext::set(fn () => $this->publisher);
    }
}
