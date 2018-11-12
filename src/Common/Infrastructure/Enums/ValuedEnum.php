<?php

namespace AlephTools\DDD\Common\Infrastructure\Enums;

use InvalidArgumentException;

/**
 * The base class for enums that associated with one numeric value.
 */
class ValuedEnum extends AbstractEnum
{
    /**
     * The numeric value associated with the enum value.
     *
     * @var int|float
     */
    protected $value;

    /**
     * Constructor.
     *
     * @param int|float $value
     * @throws InvalidArgumentException
     */
    protected function __construct($value)
    {
        if (!is_numeric($value)) {
            throw new InvalidArgumentException('The enum value must be a numeric type.');
        }
        $this->value = $value;
    }

    /**
     * Returns the value that associated with the current enum value.
     *
     * @return int|float
     */
    public function getValue()
    {
        return $this->value;
    }
}