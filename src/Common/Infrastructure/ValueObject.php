<?php

namespace AlephTools\DDD\Common\Infrastructure;

/**
 * The base class for all value objects.
 */
abstract class ValueObject extends DomainObject
{
    /**
     * The cached hash value.
     *
     * @var string|null
     */
    private ?string $computedHash = null;

    /**
     * Generates a hash value for this domain object.
     *
     * @return string
     */
    public function hash(): string
    {
        if ($this->computedHash !== null) {
            return $this->computedHash;
        }
        return $this->computedHash = parent::hash();
    }
}