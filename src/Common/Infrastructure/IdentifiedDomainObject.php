<?php

namespace AlephTools\DDD\Common\Infrastructure;

use AlephTools\DDD\Common\Model\Identity\AbstractId;

/**
 * @property-read AbstractId $id
 */
abstract class IdentifiedDomainObject extends DomainObject implements Identifiable
{
    /**
     * The object identifier.
     *
     * @var AbstractId
     */
    protected ?AbstractId $id = null;

    /**
     * Converts an object to its identity.
     *
     * @return mixed
     */
    public function toIdentity()
    {
        return $this->id ? $this->id->identity : null;
    }

    /**
     * Converts an object to its identity string.
     *
     * @return null|string
     */
    public function toIdentityString(): ?string
    {
        return $this->id ? $this->id->toString() : null;
    }

    /**
     * Compares two domain objects.
     *
     * @param mixed $other
     * @return bool
     */
    public function equals($other): bool
    {
        if ($other === null || !($other instanceof static)) {
            return false;
        }

        return $this->id || $other->id ? $this->id->equals($other->id) : parent::equals($other);
    }

    /**
     * Generates a hash value for this domain object.
     *
     * @return string
     */
    public function hash(): string
    {
        return $this->id ? $this->id->hash() : parent::hash();
    }
}
