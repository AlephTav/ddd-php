<?php

namespace AlephTools\DDD\Common\Model;

use AlephTools\DDD\Common\Infrastructure\Sanitizer;
use AlephTools\DDD\Common\Infrastructure\ValueObject;

/**
 * @property-read string $number
 */
class Phone extends ValueObject
{
    public const NUMBER_MAX_LENGTH = 20;

    protected string $number = '';

    /**
     * Constructor. Available formats:
     * Phone()
     * Phone(string $number)
     * Phone(array $properties)
     *
     * @param array|string|null $number
     */
    public function __construct($number = null)
    {
        if (is_array($number)) {
            parent::__construct($number);
        } else {
            parent::__construct([
                'number' => $number
            ]);
        }
    }

    protected function setNumber(?string $number): void
    {
        $this->number = Sanitizer::sanitizePhone($number);
    }

    protected function validateNumber(): void
    {
        $this->assertArgumentMaxLength(
            $this->number,
            static::NUMBER_MAX_LENGTH,
            'Phone number must be at most ' . static::NUMBER_MAX_LENGTH . ' characters.'
        );
    }
}
