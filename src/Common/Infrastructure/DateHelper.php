<?php

namespace AlephTools\DDD\Common\Infrastructure;

use DateTime;
use AlephTools\DDD\Common\Model\Exceptions\InvalidArgumentException;

class DateHelper
{
    private static $dateFormats = [
        'Y-m-d H:i:s',
        'm/d/Y H:i:s',
        'Y-m-d',
        'm/d/Y',
        'm/d/y',
        'Y'
    ];

    public static function getAvailableDateFormats(): array
    {
        return self::$dateFormats;
    }

    public static function parse($date): ?DateTime
    {
        if ($date === null) {
            return null;
        }

        if (is_scalar($date)) {
            $date = Sanitizer::sanitizeName($date);

            foreach (static::getAvailableDateFormats() as $format) {
                $d = DateTime::createFromFormat($format, $date);
                if ($d !== false) {
                    return $d;
                }
            }
        }

        throw new InvalidArgumentException('Invalid date format.');
    }
}