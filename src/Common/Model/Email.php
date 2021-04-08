<?php

namespace AlephTools\DDD\Common\Model;

use AlephTools\DDD\Common\Infrastructure\Sanitizer;
use AlephTools\DDD\Common\Infrastructure\Scalarable;
use AlephTools\DDD\Common\Infrastructure\ValueObject;

/**
 * @property-read string $address
 */
class Email extends ValueObject implements Scalarable
{
    public const ADDRESS_MAX_LENGTH = 255;

    protected string $address = '';

    /**
     * Constructor. Available formats:
     * Email()
     * Email(string $address)
     * Email(array $properties)
     *
     * @param array|string|null $address
     */
    public function __construct($address = null)
    {
        if (is_array($address)) {
            parent::__construct($address);
        } else {
            parent::__construct(['address' => $address]);
        }
    }

    public function toString(): string
    {
        return $this->address;
    }

    public function toScalar()
    {
        return $this->address;
    }

    protected function setAddress(?string $address): void
    {
        $this->address = trim($address);
    }

    protected function validateAddress(): void
    {
        $this->assertArgumentMaxLength(
            $this->address,
            static::ADDRESS_MAX_LENGTH,
            'Email address must be at most ' . static::ADDRESS_MAX_LENGTH . ' characters.'
        );
        if (strlen($this->address) > 0) {
            $this->assertArgumentTrue(filter_var($this->address, FILTER_VALIDATE_EMAIL), 'Invalid email format.');
        }
    }
}
