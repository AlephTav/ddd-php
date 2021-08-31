<?php

declare(strict_types=1);

namespace AlephTools\DDD\Common\Infrastructure;

use AlephTools\DDD\Common\Model\Exceptions\InvalidArgumentException;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;

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
     * @var array<array<string,list<mixed>>>
     */
    private static array $dateComponents = [
        ['tm' => ['H', 'i', 's', 0]],
        ['tm' => ['H', 'i', 's', 0]],
        ['tm' => ['H', 'i', 's', 0]],
        [],
        [],
        ['tm' => ['H', 'i', 's', 0]],
        ['tm' => [0, 0, 0, 0]],
        ['tm' => [0, 0, 0, 0]],
        ['tm' => [0, 0, 0, 0]],
        ['tm' => [0, 0, 0, 0]],
        ['dt' => [1970, 1, 1], 'tm' => ['H', 'i', 's', 0]],
        ['dt' => ['Y', 1, 1], 'tm' => [0, 0, 0, 0]],
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
        self::$dateComponents = self::parseFormatList($formats);
    }

    /**
     * @param string[] $formats
     * @return array<array<string,list<mixed>>>
     */
    private static function parseFormatList(array $formats): array
    {
        $dateComponents = [];
        foreach ($formats as $format) {
            $dateComponents[] = self::parseFormat($format);
        }
        return $dateComponents;
    }

    /**
     * @return array<string,list<mixed>>
     */
    private static function parseFormat(string $format): array
    {
        $components = [
            'dt' => [1970, 1, 1],
            'tm' => [0, 0, 0, 0],
        ];
        $dateComponent = 0;
        $timeComponent = 0;

        $prevChar = '';
        for ($len = strlen($format), $i = 0; $i < $len; ++$i) {
            $char = $format[$i];
            if ($prevChar === '\\') {
                $prevChar = $char === '\\' ? '' : $char;
            } elseif ($char === 'd' || $char === 'j' || $char === 'D' || $char === 'l' ||
                $char === 'N' || $char === 'w' || $char === 'W' || $char === 'z'
            ) {
                $components['dt'][2] = 'd';
                $dateComponent |= 1;
            } elseif ($char === 'm' || $char === 'n' || $char === 'F' || $char === 'M') {
                $components['dt'][1] = 'm';
                $dateComponent |= 2;
            } elseif ($char === 'Y' || $char === 'y' || $char === 'o') {
                $components['dt'][0] = 'Y';
                $dateComponent |= 4;
            } elseif ($char === 'H' || $char === 'h' || $char === 'G' || $char === 'g') {
                $components['tm'][0] = 'H';
                $timeComponent |= 8;
            } elseif ($char === 'i') {
                $components['tm'][1] = 'i';
                $timeComponent |= 4;
            } elseif ($char === 's') {
                $components['tm'][2] = 's';
                $timeComponent |= 2;
            } elseif ($char === 'u' || $char === 'v') {
                $components['tm'][3] = 'u';
                $timeComponent |= 1;
            } elseif ($char === 'B') {
                $components['tm'][0] = 'H';
                $components['tm'][1] = 'i';
                $components['tm'][2] = 's';
                $timeComponent |= 8 | 4 | 2;
            } elseif ($char === 'c' || $char === 'r' || $char === 'U') {
                $components['dt'][0] = 'Y';
                $components['dt'][1] = 'm';
                $components['dt'][2] = 'd';
                $components['tm'][0] = 'H';
                $components['tm'][1] = 'i';
                $components['tm'][2] = 's';
                $dateComponent = 7;
                $timeComponent |= 8 | 4 | 2;
            } else {
                $prevChar = $char;
            }
        }

        if ($dateComponent === 7) {
            unset($components['dt']);
        }
        if ($timeComponent === 15) {
            unset($components['tm']);
        }

        return $components;
    }

    /**
     * @param mixed $date
     */
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

    /**
     * @param mixed $date
     */
    public static function parse($date): ?DateTime
    {
        if ($date === null || $date instanceof DateTime) {
            return $date;
        }
        if ($date instanceof DateTimeImmutable) {
            if (static::dateTimeHasCreateFromImmutable()) {
                return DateTime::createFromImmutable($date);
            }
            return (new DateTime($date->format('c')))
                ->setTime(
                    (int)$date->format('H'),
                    (int)$date->format('i'),
                    (int)$date->format('s'),
                    (int)$date->format('u')
                );
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
    private static function parseInternal($date, string $class)
    {
        if (is_scalar($date)) {
            $date = preg_replace('/[[:cntrl:]]/', '', trim((string)$date));

            foreach (static::getAvailableDateFormats() as $index => $format) {
                /** @var DateTime|DateTimeImmutable|false $d */
                $d = $class::createFromFormat($format, $date);
                if ($d !== false) {
                    return self::normalizeDate($d, $index);
                }
            }
        }

        throw new InvalidArgumentException('Invalid date format.');
    }

    /**
     * @template T as DateTime|DateTimeImmutable
     * @param T $date
     * @return T
     */
    private static function normalizeDate($date, int $index)
    {
        $components = self::$dateComponents[$index];
        if (!$components) {
            return $date;
        }

        if (isset($components['tm'])) {
            $timeComponents = self::formDateTimeComponent($date, $components['tm']);
            if ($timeComponents) {
                $date = $date->setTime(...$timeComponents);
            }
        }

        if (isset($components['dt'])) {
            $dateComponents = self::formDateTimeComponent($date, $components['dt']);
            if ($dateComponents) {
                $date = $date->setDate(...$dateComponents);
            }
        }

        return $date;
    }

    /**
     * @param list<mixed> $components
     * @return list<int>
     */
    private static function formDateTimeComponent(DateTimeInterface $date, array $components): array
    {
        $dt = [];
        foreach ($components as $component) {
            if ($component === 0) {
                $dt[] = 0;
            } else {
                $dt[] = (int)$date->format((string)$component);
            }
        }
        return $dt;
    }
}
