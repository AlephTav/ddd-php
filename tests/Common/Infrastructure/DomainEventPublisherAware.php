<?php

namespace AlephTools\DDD\Tests\Common\Infrastructure;

use PHPUnit\Framework\MockObject\MockBuilder;
use AlephTools\DDD\Common\Infrastructure\ApplicationContext;
use AlephTools\DDD\Common\Infrastructure\DomainEventPublisher;
use AlephTools\DDD\Common\Infrastructure\EventDispatcher;

trait DomainEventPublisherAware
{
    /**
     * @var DomainEventPublisher
     */
    protected $publisher;

    public function setUp()
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

        ApplicationContext::set(function() {
            return $this->publisher;
        });
    }
}
