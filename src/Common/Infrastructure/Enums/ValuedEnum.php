<?php

declare(strict_types=1);

namespace AlephTools\DDD\Common\Infrastructure\Enums;

/**
 * The base class for enums that associated with one numeric value.
 */
class ValuedEnum extends AbstractEnum
{
    /**
     * The numeric value associated with the enum value.
     *
     */
    protected int|float $value;

    protected function __construct(int|float $value)
    {
        parent::__construct();
        $this->value = $value;
    }

    /**
     * Returns the value that associated with the current enum value.
     *
     * @return int|float
     */
    public function getValue(): int|float
    {
        return $this->value;
    }
}
