<?php

declare(strict_types=1);

namespace AlephTools\DDD\Common\Model;

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
     * @param array<string,mixed>|string|null $number
     */
    public function __construct(null|array|string $number = null)
    {
        if (is_array($number)) {
            parent::__construct($number);
        } else {
            parent::__construct([
                'number' => $number,
            ]);
        }
    }

    public function toString(): string
    {
        return $this->number;
    }

    public function toScalar(): mixed
    {
        return $this->number;
    }

    protected function setNumber(?string $number): void
    {
        if ($number === null) {
            $number = '';
        } else {
            $number = preg_replace('/[^0-9]/', '', $number);
            // Adjust phone number for RU zone by default.
            $length = strlen($number);
            if ($length === 10) {
                $number = '7' . $number;
            } elseif ($length === 11 && $number[0] === '8') {
                $number = '7' . substr($number, 1);
            }
        }
        $this->number = $number;
    }

    protected function validateNumber(): void
    {
        $this->assertArgumentMaxLength(
            $this->number,
            (int)static::NUMBER_MAX_LENGTH,
            'Phone number must be at most ' . (string)static::NUMBER_MAX_LENGTH . ' characters.'
        );
    }
}
