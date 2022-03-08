<?php

declare(strict_types=1);

namespace AlephTools\DDD\Common\Infrastructure;

use DateTime;
use DateTimeImmutable;
use AlephTools\DDD\Common\Model\Exceptions\InvalidArgumentException;

class DateHelper
{
    /**
     * @var list<string>
     */
    private static array $dateFormats = [
        'Y-m-d\TH:i:sP',
        'Y-m-d H:i:s',
        'Y-m-d H:i:sP',
        'Y-m-d H:i:s.uP',
        'Y-m-d\TH:i:s.uP',
        'm/d/Y H:i:s',
        'Y-m-d',
        'Y-m-dP',
        'm/d/Y',
        'm/d/y',
        'H:i:s',
        'Y',
    ];

    /**
     * @return list<string>
     */
    public static function getAvailableDateFormats(): array
    {
        return self::$dateFormats;
    }

    /**
     * @param list<string> $formats
     */
    public static function setAvailableDateFormats(array $formats): void
    {
        self::$dateFormats = $formats;
    }

    public static function parseImmutable(mixed $date): ?DateTimeImmutable
    {
        if ($date === null || $date instanceof DateTimeImmutable) {
            return $date;
        }
        if ($date instanceof DateTime) {
            return DateTimeImmutable::createFromMutable($date);
        }
        return self::parseInternal($date, DateTimeImmutable::class);
    }

    public static function parse(mixed $date): ?DateTime
    {
        if ($date === null || $date instanceof DateTime) {
            return $date;
        }
        if ($date instanceof DateTimeImmutable) {
            return DateTime::createFromImmutable($date);
        }
        return self::parseInternal($date, DateTime::class);
    }

    protected static function dateTimeHasCreateFromImmutable(): bool
    {
        return method_exists(DateTime::class, 'createFromImmutable');
    }

    /**
     * @template T as class-string<DateTime>|class-string<DateTimeImmutable>
     * @param mixed $date
     * @param T $class
     * @psalm-return (T is class-string<DateTime> ? DateTime : DateTimeImmutable)
     */
    private static function parseInternal(mixed $date, string $class)
    {
        if (is_scalar($date)) {
            $date = preg_replace('/[[:cntrl:]]/', '', trim((string)$date));

            foreach (static::getAvailableDateFormats() as $format) {
                /** @var DateTime|DateTimeImmutable|false $d */
                $d = $class::createFromFormat("!$format", $date);
                if ($d) {
                    return $d;
                }
            }
        }

        throw new InvalidArgumentException('Invalid date format.');
    }
}
