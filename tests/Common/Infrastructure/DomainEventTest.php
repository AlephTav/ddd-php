<?php

namespace AlephTools\DDD\Tests\Common\Infrastructure;

use PHPUnit\Framework\TestCase;
use AlephTools\DDD\Common\Infrastructure\DomainEvent;

/**
 * @property mixed $prop
 */
class DomainEventTestObject extends DomainEvent
{
    private $prop;
}

class DomainEventTest extends TestCase
{
    public function testCreation(): void
    {
        $now = new \DateTime();
        $event = new DomainEventTestObject([
            'prop' => 'foo',
            'occurredOn' => $now
        ]);

        $this->assertEquals('foo', $event->prop);
        $this->assertEquals($now->format('Y-m-d H:i:s'), $event->occurredOn->format('Y-m-d H:i:s'));
    }
}