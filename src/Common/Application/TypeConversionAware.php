<?php

declare(strict_types=1);

namespace AlephTools\DDD\Common\Application;

use AlephTools\DDD\Common\Infrastructure\DateHelper;
use DateTime;
use DateTimeImmutable;

trait TypeConversionAware
{
    protected function toBoolean(mixed $value): bool
    {
        if (is_scalar($value)) {
            $value = strtolower(trim((string)$value));
            return $value === 'true' || $value === '1' || $value === 'on';
        }

        return false;
    }

    protected function toDate(mixed $value): ?DateTime
    {
        return DateHelper::parse($value);
    }

    protected function toImmutableDate(mixed $value): ?DateTimeImmutable
    {
        return DateHelper::parseImmutable($value);
    }
}
