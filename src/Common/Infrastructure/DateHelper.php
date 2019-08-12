<?php

namespace AlephTools\DDD\Common\Infrastructure;

use DateTime;
use DateTimeImmutable;
use AlephTools\DDD\Common\Model\Exceptions\InvalidArgumentException;

class DateHelper
{
    private static $dateFormats = [
        DateTime::ATOM,
        'Y-m-d H:i:s',
        'Y-m-d H:i:sP',
        'Y-m-d\TH:i:s.uP',
        'm/d/Y H:i:s',
        'Y-m-d',
        'Y-m-dP',
        'm/d/Y',
        'm/d/y',
        'H:i:s',
        'Y'
    ];

    public static function getAvailableDateFormats(): array
    {
        return self::$dateFormats;
    }

    public static function parseImmutable($date): ?DateTimeImmutable
    {
        if ($date === null || $date instanceof DateTimeImmutable) {
            return $date;
        }
        if ($date instanceof DateTime) {
            return DateTimeImmutable::createFromMutable($date);
        }
        return self::parseInternal($date, DateTimeImmutable::class);
    }

    public static function parse($date): ?DateTime
    {
        if ($date === null || $date instanceof DateTime) {
            return $date;
        }
        if ($date instanceof DateTimeImmutable) {
            return DateTime::createFromImmutable($date);
        }
        return self::parseInternal($date, DateTime::class);
    }

    /**
     * @param $date
     * @param string $class
     * @return DateTime|DateTimeImmutable
     */
    private static function parseInternal($date, string $class)
    {
        if (is_scalar($date)) {
            $date = Sanitizer::sanitizeName($date);

            foreach (static::getAvailableDateFormats() as $format) {
                /** @noinspection PhpUndefinedMethodInspection */
                $d = $class::createFromFormat($format, $date);
                if ($d !== false) {
                    return $d;
                }
            }
        }

        throw new InvalidArgumentException('Invalid date format.');
    }
}
