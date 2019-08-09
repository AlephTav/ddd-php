<?php

namespace AlephTools\DDD\Common\Model\Events;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use AlephTools\DDD\Common\Infrastructure\ValueObject;

/**
 * The base class for all domain events.
 *
 * @property-read DateTimeImmutable $occurredOn
 */
abstract class DomainEvent extends ValueObject
{
    /**
     * The event creation date and time.
     *
     * @var DateTimeImmutable
     */
    protected $occurredOn;

    /**
     * Constructor.
     *
     * @param array $properties
     */
    public function __construct(array $properties = [])
    {
        if (!isset($properties['occurredOn'])) {
            $properties['occurredOn'] = new DateTimeImmutable();
        }
        parent::__construct($properties);
    }

    protected function setOccurredOn(DateTimeInterface $date): void
    {
        $this->occurredOn = $date instanceof DateTime ? DateTimeImmutable::createFromMutable($date) : $date;
    }
}
