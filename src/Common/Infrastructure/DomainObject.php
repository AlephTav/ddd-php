<?php

namespace AlephTools\DDD\Common\Infrastructure;

use ReflectionClass;

/**
 * The base class for all domain objects (entities, value objects, domain events).
 */
abstract class DomainObject extends StrictDto implements Hashable
{
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
        return $this->hash() === $other->hash();
    }

    /**
     * Generates a hash value for this domain object.
     *
     * @return string
     */
    public function hash(): string
    {
        $hash = new \stdClass();
        $hash->type = get_class($this);
        $hash->properties = $this->toArray();
        return Hash::of($hash);
    }

    /**
     * Creates a copy of this domain object.
     *
     * @return static
     */
    public function copy()
    {
        return new static($this->toArray());
    }

    /**
     * Creates a copy of this domain object with the given property values.
     *
     * @param array $properties
     * @return static
     */
    public function copyWith(array $properties = [])
    {
        /** @var static $instance */
        $instance = (new ReflectionClass($this))->newInstanceWithoutConstructor();
        $instance->__wakeup();
        $instance->assignPropertiesAndValidate(array_merge($this->toArray(), $properties));
        return $instance;
    }

    /**
     * Returns the domain name associated with this object.
     * By default it's the short class name.
     *
     * @return string
     */
    public function domainName(): string
    {
        return (new ReflectionClass($this))->getShortName();
    }
}
