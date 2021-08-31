<?php

declare(strict_types=1);

namespace AlephTools\DDD\Tests\Common\Model\Events;

use AlephTools\DDD\Common\Model\Events\DomainEvent;
use DateTime;
use PHPUnit\Framework\TestCase;

/**
 * @property mixed $prop
 */
class DomainEventTestObject extends DomainEvent
{
    private $prop;
}

/**
 * @internal
 */
class DomainEventTest extends TestCase
{
    public function testCreation(): void
    {
        $now = new DateTime();
        $event = new DomainEventTestObject([
            'prop' => 'foo',
            'occurredOn' => $now,
        ]);

        self::assertEquals('foo', $event->prop);
        self::assertEquals($now->format('Y-m-d H:i:s'), $event->occurredOn->format('Y-m-d H:i:s'));
    }
}
