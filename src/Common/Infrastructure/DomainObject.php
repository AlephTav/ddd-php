<?php

declare(strict_types=1);

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
     */
    public function equals(mixed $other): bool
    {
        if ($other instanceof static) {
            return $this->hash() === $other->hash();
        }
        return false;
    }

    /**
     * Generates a hash value for this domain object.
     *
     */
    public function hash(): string
    {
        return Hash::of(get_class($this) . Hash::of($this->toArray()));
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
     * @param array<string,mixed> $properties
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
     * By default, it's the short class name.
     *
     */
    public function domainName(): string
    {
        return (new ReflectionClass($this))->getShortName();
    }
}
