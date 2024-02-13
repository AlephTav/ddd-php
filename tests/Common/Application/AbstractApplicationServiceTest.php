<?php

declare(strict_types=1);

namespace Tests\AlephTools\DDD\Common\Application;

use AlephTools\DDD\Common\Application\AbstractApplicationService;
use AlephTools\DDD\Common\Application\UnitOfWork;
use AlephTools\DDD\Common\Infrastructure\ApplicationContext;
use AlephTools\DDD\Common\Infrastructure\Async;
use AlephTools\DDD\Common\Infrastructure\DomainEventPublisher;
use AlephTools\DDD\Common\Infrastructure\EventDispatcher;
use AlephTools\DDD\Common\Model\Events\DomainEvent;
use PHPUnit\Framework\TestCase;

class ApplicationServiceTestObject extends AbstractApplicationService
{
    public function execAtomically(callable $callback)
    {
        return $this->executeAtomically($callback);
    }

    public function execAsync(callable $callback, array $params = []): void
    {
        $this->runAsync($callback, $params);
    }

    public function getEventPublisher(): DomainEventPublisher
    {
        return $this->eventPublisher();
    }
}

/**
 * @internal
 */
class AbstractApplicationServiceTest extends TestCase
{
    protected function setUp(): void
    {
        ApplicationContext::set(function (string $abstract = null, array $parameters = []) {
            if ($abstract === UnitOfWork::class) {
                return new class () implements UnitOfWork {
                    public function execute(callable $callback)
                    {
                        return $callback();
                    }
                };
            }
            if ($abstract === DomainEventPublisher::class) {
                return new DomainEventPublisher(new class () implements EventDispatcher {
                    public function dispatch(string $subscriber, DomainEvent $event, bool $async): void
                    {
                    }
                });
            }
            return new class () implements Async {
                public function run(mixed $callback, array $params = [], int $delay = 0): void
                {
                    $callback($params);
                }

                public function runBatch(mixed $callback, array $params = [], int $delay = 0): void
                {
                    $callback($params);
                }
            };
        });
    }

    public function testExecuteAtomically(): void
    {
        $service = new ApplicationServiceTestObject();

        $result = $service->execAtomically(fn () => 'executed atomically');

        self::assertSame('executed atomically', $result);
    }

    public function testRunAsync(): void
    {
        $service = new ApplicationServiceTestObject();

        $result = null;
        $service->execAsync(function (array $params) use (&$result): void {
            $result = $params;
        }, ['a', 'b', 'c']);

        self::assertSame(['a', 'b', 'c'], $result);
    }

    public function testEventPublisher(): void
    {
        $service = new ApplicationServiceTestObject();

        self::assertInstanceOf(DomainEventPublisher::class, $service->getEventPublisher());
    }
}
