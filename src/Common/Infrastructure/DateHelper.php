<?php

namespace AlephTools\DDD\Common\Infrastructure;

use DateTime;
use DateTimeInterface;
use AlephTools\DDD\Common\Model\Exceptions\InvalidArgumentException;

class DateHelper
{
    private static $dateFormats = [
        DateTime::ATOM,
        'Y-m-d\TH:i:s.uP',
        'Y-m-d H:i:s',
        'm/d/Y H:i:s',
        'Y-m-d',
        'm/d/Y',
        'm/d/y',
        'H:i:s',
        'Y'
    ];

    public static function getAvailableDateFormats(): array
    {
        return self::$dateFormats;
    }

    public static function parse($date): ?DateTimeInterface
    {
        if ($date === null || $date instanceof DateTimeInterface) {
            return $date;
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
