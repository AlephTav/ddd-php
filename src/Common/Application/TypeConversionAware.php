<?php

namespace AlephTools\DDD\Common\Application;

use DateTime;
use DateTimeImmutable;
use AlephTools\DDD\Common\Infrastructure\DateHelper;

trait TypeConversionAware
{
    protected function toBoolean($value): bool
    {
        if (is_scalar($value)) {
            $value = strtolower(trim($value));
            return $value === 'true' || $value === '1' || $value === 'on';
        }

        return false;
    }

    protected function toDate($value): ?DateTime
    {
        return DateHelper::parse($value);
    }

    protected function toImmutableDate($value): ?DateTimeImmutable
    {
        return DateHelper::parseImmutable($value);
    }
}
