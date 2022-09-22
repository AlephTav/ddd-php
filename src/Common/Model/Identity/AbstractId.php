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
     */
    protected mixed $identity;

    /**
     * Returns an instance of the id.
     *
     */
    public static function from(mixed $identity): static
    {
        return new static($identity);
    }

    /**
     * Returns id instance if the identity is not null, null otherwise.
     *
     */
    public static function fromNullable(mixed $identity): ?static
    {
        return $identity !== null ? new static($identity) : null;
    }

    /**
     * Constructor.
     *
     */
    public function __construct(mixed $identity)
    {
        parent::__construct(['identity' => $this->parse($identity)]);
    }

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
     */
    abstract protected function parse(mixed $identity): mixed;

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
