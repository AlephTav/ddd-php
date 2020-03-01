<?php

namespace AlephTools\DDD\Common\Model\Identity;

use AlephTools\DDD\Common\Infrastructure\Hash;
use ReflectionException;
use AlephTools\DDD\Common\Infrastructure\ValueObject;

/**
 * The base class for all identifiers.
 *
 * @property-read mixed $identity
 */
abstract class AbstractId extends ValueObject
{
    /**
     * The identity (unique identifier).
     *
     * @var mixed
     */
    protected $identity;

    /**
     * Converts this identifier to a string.
     *
     * @return string
     */
    public function toString(): string
    {
        return (string)$this->identity;
    }

    /**
     * Generates a hash value for the id.
     *
     * @return string
     */
    public function hash(): string
    {
        return Hash::of(get_class($this) . $this->toString());
    }

    /**
     * Compact serialization of AbstractId
     *
     * @return array
     */
    public function __serialize()
    {
        return ["\0*\0identity" => (string)$this->identity];
    }

    /**
     * Unserialization of AbstractId
     *
     * @param $data
     */
    public function __unserialize($data)
    {
        if(is_string($data["\0*\0identity"])) {
            $data["\0*\0identity"] = $this->parse($data["\0*\0identity"]);
        }
        $this->identity = $data["\0*\0identity"];
        $this->__wakeup();
    }

    /**
     * Parsing of raw value
     *
     * @param $identity
     * @return mixed
     */
    abstract protected function parse($identity);

    /**
     * Validates the identity.
     *
     * @return void
     * @throws ReflectionException
     */
    protected function validateIdentity(): void
    {
        $domainName = $this->domainName();
        $this->assertArgumentNotNull($this->identity, "Identity of $domainName must not be null.");
    }
}
