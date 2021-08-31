<?php

declare(strict_types=1);

namespace AlephTools\DDD\Common\Infrastructure;

abstract class IdentifiedValueObject extends IdentifiedDomainObject
{
    /**
     * The cached hash value.
     *
     */
    private ?string $computedHash = null;

    /**
     * Generates a hash value for this domain object.
     *
     */
    public function hash(): string
    {
        if ($this->computedHash !== null) {
            return $this->computedHash;
        }
        return $this->computedHash = parent::hash();
    }
}
