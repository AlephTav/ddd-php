<?php

namespace AlephTools\DDD\Common\Infrastructure;

use AlephTools\DDD\Common\Model\Exceptions\InvalidArgumentException;
use UnexpectedValueException;

class EnumHelper
{
    /**
     * Converts the given enum constant to the appropriate enum instance.
     *
     * @param string $enum
     * @param mixed $value
     * @return mixed
     * @throws InvalidArgumentException
     */
    public static function toEnum(string $enum, $value)
    {
        if (is_object($value) && ($value instanceof $enum)) {
            return $value;
        }
        if (!is_string($value)) {
            throw new InvalidArgumentException("Constant of $enum must be a string, " . gettype($value) . ' given.');
        }
        if (strlen($value) === 0) {
            throw new InvalidArgumentException("Constant of $enum must not be empty string.");
        }

        try {
            return $enum::$value();
        } catch (UnexpectedValueException $e) {
            throw new InvalidArgumentException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
