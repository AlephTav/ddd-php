<?php

declare(strict_types=1);

namespace AlephTools\DDD\Common\Infrastructure;

use stdClass;
use AlephTools\DDD\Common\Model\Identity\AbstractId;

/**
 * @property-read AbstractId|null $id
 */
abstract class IdentifiedDomainObject extends DomainObject implements Identifiable
{
    /**
     * The object identifier.
     *
     */
    protected ?AbstractId $id = null;

    /**
     * Converts an object to its identity.
     *
     */
    public function toIdentity(): mixed
    {
        return $this->id?->identity;
    }

    /**
     * Converts an object to its identity string.
     *
     */
    public function toIdentityString(): ?string
    {
        return $this->id?->toString();
    }

    /**
     * Generates a hash value for this domain object.
     *
     */
    public function hash(): string
    {
        if (!$this->id) {
            return parent::hash();
        }
        $hash = new stdClass();
        $hash->type = get_class($this);
        $hash->properties = ['id' => $this->id];
        return Hash::of($hash);
    }
}
