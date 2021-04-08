<?php

namespace AlephTools\DDD\Common\Model;

use AlephTools\DDD\Common\Infrastructure\Sanitizer;
use AlephTools\DDD\Common\Infrastructure\Scalarable;
use AlephTools\DDD\Common\Infrastructure\ValueObject;

/**
 * @property-read string $number
 */
class Phone extends ValueObject implements Scalarable
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

    public function toString(): string
    {
        return $this->number;
    }

    public function toScalar()
    {
        return $this->number;
    }

    protected function setNumber(?string $number): void
    {
        if ($number === null) {
            $number = '';
        } else {
            $number = preg_replace('/[^0-9]/', '', $number);
            if (strlen($number) === 10) {
                $number = '7' . $number;
            }
        }
        $this->number = $number;
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
