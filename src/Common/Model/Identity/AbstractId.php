<?php

declare(strict_types=1);

namespace AlephTools\DDD\Common\Model\Identity;

use AlephTools\DDD\Common\Infrastructure\Scalarable;
use AlephTools\DDD\Common\Infrastructure\ValueObject;
use AlephTools\DDD\Common\Model\Exceptions\InvalidArgumentException;

/**
 * The base class for all identifiers.
 *
 * @property-read mixed $identity
 */
abstract class AbstractId extends ValueObject implements Scalarable
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
     */
    public function toString(): string
    {
        return (string)$this->identity;
    }

    public function toScalar(): mixed
    {
        return $this->identity;
    }

    /**
     * Parsing of raw value
     *
     * @param mixed $identity
     * @return mixed
     */
    abstract protected function parse(mixed $identity);

    /**
     * Validates the identity.
     */
    protected function validateIdentity(): void
    {
        if ($this->identity === null) {
            $domainName = $this->domainName();
            throw new InvalidArgumentException("Identity of $domainName must not be null.");
        }
    }
}
