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
        return Hash::of($this->toString());
    }

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
