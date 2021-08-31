<?php

declare(strict_types=1);

namespace AlephTools\DDD\Common\Application;

use AlephTools\DDD\Common\Infrastructure\DateHelper;
use DateTime;
use DateTimeImmutable;

trait TypeConversionAware
{
    /**
     * @param mixed $value
     */
    protected function toBoolean($value): bool
    {
        if (is_scalar($value)) {
            $value = strtolower(trim((string)$value));
            return $value === 'true' || $value === '1' || $value === 'on';
        }

        return false;
    }

    /**
     * @param mixed $value
     */
    protected function toDate($value): ?DateTime
    {
        return DateHelper::parse($value);
    }

    /**
     * @param mixed $value
     */
    protected function toImmutableDate($value): ?DateTimeImmutable
    {
        return DateHelper::parseImmutable($value);
    }
}
