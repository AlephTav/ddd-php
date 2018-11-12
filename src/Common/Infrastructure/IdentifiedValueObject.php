<?php

namespace AlephTools\DDD\Common\Infrastructure;

class IdentifiedValueObject extends IdentifiedDomainObject
{
    /**
     * The cached hash value.
     *
     * @var string
     */
    private $computedHash;

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