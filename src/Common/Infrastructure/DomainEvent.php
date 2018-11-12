<?php

namespace AlephTools\DDD\Common\Infrastructure;

use DateTime;

/**
 * The base class for all domain events.
 *
 * @property-read DateTime $occurredOn
 */
abstract class DomainEvent extends ValueObject
{
    /**
     * The event creation date and time.
     *
     * @var DateTime
     */
    protected $occurredOn;

    /**
     * Constructor.
     *
     * @param array $properties
     */
    public function __construct(array $properties = [])
    {
        $properties['occurredOn'] = new DateTime();
        parent::__construct($properties);
    }
}