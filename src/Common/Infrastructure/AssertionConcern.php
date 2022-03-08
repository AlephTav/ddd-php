<?php

declare(strict_types=1);

namespace AlephTools\DDD\Common\Infrastructure;

use AlephTools\DDD\Common\Model\Exceptions\InvalidArgumentException;
use AlephTools\DDD\Common\Model\Exceptions\InvalidStateException;
use DateTime;
use DateTimeInterface;
use Throwable;

/**
 * Use this class to validate input parameters or states of some domain object.
 */
trait AssertionConcern
{
    protected function assertArgumentSame(mixed $value1, mixed $value2, string $msg): void
    {
        if ($value1 !== $value2) {
            throw new InvalidArgumentException($msg);
        }
    }

    protected function assertArgumentNotSame(mixed $value1, mixed $value2, string $msg): void
    {
        if ($value1 === $value2) {
            throw new InvalidArgumentException($msg);
        }
    }

    protected function assertArgumentEquals(mixed $value1, mixed $value2, string $msg): void
    {
        if ($value1 != $value2) {
            throw new InvalidArgumentException($msg);
        }
    }

    protected function assertArgumentNotEquals(mixed $value1, mixed $value2, string $msg): void
    {
        if ($value1 == $value2) {
            throw new InvalidArgumentException($msg);
        }
    }

    protected function assertArgumentNotNull(mixed $value, string $msg): void
    {
        if ($value === null) {
            throw new InvalidArgumentException($msg);
        }
    }

    protected function assertArgumentNull(mixed $value, string $msg): void
    {
        if ($value !== null) {
            throw new InvalidArgumentException($msg);
        }
    }

    protected function assertArgumentFalse(mixed $value, string $msg): void
    {
        if ($value) {
            throw new InvalidArgumentException($msg);
        }
    }

    protected function assertArgumentTrue(mixed $value, string $msg): void
    {
        if (!$value) {
            throw new InvalidArgumentException($msg);
        }
    }

    protected function assertArgumentNotEmpty(?string $value, string $msg): void
    {
        if (mb_strlen((string)$value) === 0) {
            throw new InvalidArgumentException($msg);
        }
    }

    protected function assertArgumentLength(?string $value, int $min, int $max, string $msg): void
    {
        $length = mb_strlen((string)$value);
        if ($length > $max || $length < $min) {
            throw new InvalidArgumentException($msg);
        }
    }

    protected function assertArgumentMinLength(?string $value, int $min, string $msg): void
    {
        if (mb_strlen((string)$value) < $min) {
            throw new InvalidArgumentException($msg);
        }
    }

    protected function assertArgumentMaxLength(?string $value, int $max, string $msg): void
    {
        if (mb_strlen((string)$value) > $max) {
            throw new InvalidArgumentException($msg);
        }
    }

    protected function assertArgumentRange(int|float|null $value, int|float $min, int|float $max, string $msg): void
    {
        if ($value < $min || $value > $max) {
            throw new InvalidArgumentException($msg);
        }
    }

    protected function assertArgumentMin(int|float|null $value, int|float $min, string $msg): void
    {
        if ($value < $min) {
            throw new InvalidArgumentException($msg);
        }
    }

    protected function assertArgumentMax(int|float|null $value, int|float $max, string $msg): void
    {
        if ($value > $max) {
            throw new InvalidArgumentException($msg);
        }
    }

    protected function assertArgumentNotInFuture(DateTimeInterface $date, string $msg): void
    {
        if ($date > new DateTime()) {
            throw new InvalidArgumentException($msg);
        }
    }

    protected function assertArgumentNotInPast(DateTimeInterface $date, string $msg): void
    {
        if ($date < new DateTime('-1 second')) {
            throw new InvalidArgumentException($msg);
        }
    }

    protected function assertArgumentPatternMatch(string $value, string $pattern, string $msg): void
    {
        if (!preg_match($pattern, $value)) {
            throw new InvalidArgumentException($msg);
        }
    }

    protected function assertArgumentWithoutException(callable $callback, string $msg): void
    {
        try {
            $callback();
        } catch (Throwable $_) {
            throw new InvalidArgumentException($msg);
        }
    }

    /**
     * @psalm-param class-string $class
     */
    protected function assertArgumentInstanceOf(mixed $value, string $class, string $msg): void
    {
        if (!is_a($value, $class)) {
            throw new InvalidArgumentException($msg);
        }
    }

    protected function assertStateFalse(mixed $value, string $msg): void
    {
        if ($value) {
            throw new InvalidStateException($msg);
        }
    }

    protected function assertStateTrue(mixed $value, string $msg): void
    {
        if (!$value) {
            throw new InvalidStateException($msg);
        }
    }
}
