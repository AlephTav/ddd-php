<?php

declare(strict_types=1);

namespace AlephTools\DDD\Common\Model\Events;

use AlephTools\DDD\Common\Infrastructure\ValueObject;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;

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
     */
    protected ?DateTimeImmutable $occurredOn = null;

    /**
     * Constructor.
     *
     * @param array<string,mixed> $properties
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
