<?php

namespace AlephTools\DDD\Common\Model\Identity;

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
     * Converts this identifier to a string.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->toString();
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