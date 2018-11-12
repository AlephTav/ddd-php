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
     * @param string $value
     * @return mixed
     * @throws InvalidArgumentException
     */
    public static function toEnum(string $enum, string $value)
    {
        try {
            return $enum::$value();
        } catch (UnexpectedValueException $e) {
            throw new InvalidArgumentException($e->getMessage(), $e->getCode(), $e);
        }
    }
}