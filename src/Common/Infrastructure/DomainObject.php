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

        $properties1 = $this->toArray();
        $properties2 = $other->toArray();

        foreach ($properties1 as $property => $value1) {
            $value2 = $properties2[$property];
            if ($value1 instanceof DomainObject) {
                return $value1->equals($value2);
            }
            if ($value1 != $value2) {
                return false;
            }
        }

        return true;
    }

    /**
     * Generates a hash value for this domain object.
     *
     * @return string
     */
    public function hash(): string
    {
        return Hash::of($this->toArray());
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
        $instance->init();
        $instance->assignPropertiesAndValidate(array_merge($this->toArray(), $properties));
        return $instance;
    }

    /**
     * Returns the domain name associated with this object.
     * By default it's the short class name.
     *
     * @return string
     * @throws \ReflectionException
     */
    public function domainName(): string
    {
        return (new ReflectionClass($this))->getShortName();
    }
}
