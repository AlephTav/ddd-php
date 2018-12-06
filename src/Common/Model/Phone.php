<?php

namespace AlephTools\DDD\Common\Model;

use AlephTools\DDD\Common\Infrastructure\Sanitizer;
use AlephTools\DDD\Common\Infrastructure\ValueObject;

/**
 * @property-read string $number
 */
class Phone extends ValueObject
{
    //region Constants

    public const NUMBER_MAX_LENGTH = 20;

    //endregion

    //region Properties

    protected $number;

    //endregion

    //region Constructors

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

    //endregion

    //region Setters

    protected function setNumber(?string $number): void
    {
        $this->number = Sanitizer::sanitizePhone($number);
    }

    //endregion

    //region Property Validators

    protected function validateNumber(): void
    {
        $this->assertArgumentMaxLength(
            $this->number,
            static::NUMBER_MAX_LENGTH,
            'Phone number must be at most ' . static::NUMBER_MAX_LENGTH . ' characters.'
        );
    }

    //endregion
}