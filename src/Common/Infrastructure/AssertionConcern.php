<?php

namespace AlephTools\DDD\Common\Infrastructure;

use DateTime;
use Throwable;
use AlephTools\DDD\Common\Model\Exceptions\InvalidArgumentException;
use AlephTools\DDD\Common\Model\Exceptions\InvalidStateException;

/**
 * Use this class to validate input parameters or states of some domain object.
 */
trait AssertionConcern
{
    protected function assertArgumentSame($value1, $value2, string $msg): void
    {
        if ($value1 !== $value2) {
            throw new InvalidArgumentException($msg);
        }
    }

    protected function assertArgumentNotSame($value1, $value2, string $msg): void
    {
        if ($value1 === $value2) {
            throw new InvalidArgumentException($msg);
        }
    }

    protected function assertArgumentEquals($value1, $value2, string $msg): void
    {
        if ($value1 != $value2) {
            throw new InvalidArgumentException($msg);
        }
    }

    protected function assertArgumentNotEquals($value1, $value2, string $msg): void
    {
        if ($value1 == $value2) {
            throw new InvalidArgumentException($msg);
        }
    }

    protected function assertArgumentNotNull($value, string $msg): void
    {
        if ($value === null) {
            throw new InvalidArgumentException($msg);
        }
    }

    protected function assertArgumentNull($value, string $msg): void
    {
        if ($value !== null) {
            throw new InvalidArgumentException($msg);
        }
    }

    protected function assertArgumentFalse($value, string $msg): void
    {
        if ($value) {
            throw new InvalidArgumentException($msg);
        }
    }

    protected function assertArgumentTrue($value, string $msg): void
    {
        if (!$value) {
            throw new InvalidArgumentException($msg);
        }
    }

    protected function assertArgumentNotEmpty(?string $value, string $msg): void
    {
        if (mb_strlen($value) === 0) {
            throw new InvalidArgumentException($msg);
        }
    }

    protected function assertArgumentLength(?string $value, int $min, int $max, string $msg): void
    {
        $length = mb_strlen($value);
        if ($length > $max || $length < $min) {
            throw new InvalidArgumentException($msg);
        }
    }

    protected function assertArgumentMinLength(?string $value, int $min, string $msg): void
    {
        if (mb_strlen($value) < $min) {
            throw new InvalidArgumentException($msg);
        }
    }

    protected function assertArgumentMaxLength(?string $value, int $max, string $msg): void
    {
        if (mb_strlen($value) > $max) {
            throw new InvalidArgumentException($msg);
        }
    }

    protected function assertArgumentRange($value, $min, $max, string $msg): void
    {
        if ($value < $min || $value > $max) {
            throw new InvalidArgumentException($msg);
        }
    }

    protected function assertArgumentMin($value, $min, string $msg): void
    {
        if ($value < $min) {
            throw new InvalidArgumentException($msg);
        }
    }

    protected function assertArgumentMax($value, $max, string $msg): void
    {
        if ($value > $max) {
            throw new InvalidArgumentException($msg);
        }
    }

    protected function assertArgumentNotInFuture(DateTime $date, string $msg): void
    {
        if ($date > new DateTime()) {
            throw new InvalidArgumentException($msg);
        }
    }

    protected function assertArgumentNotInPast(DateTime $date, string $msg): void
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
        } catch (Throwable $ignore) {
            throw new InvalidArgumentException($msg);
        }
    }

    protected function assertArgumentInstanceOf($value, string $class, string $msg): void
    {
        if (!is_a($value, $class)) {
            throw new InvalidArgumentException($msg);
        }
    }

    protected function assertStateFalse($value, string $msg): void
    {
        if ($value) {
            throw new InvalidStateException($msg);
        }
    }

    protected function assertStateTrue($value, string $msg): void
    {
        if (!$value) {
            throw new InvalidStateException($msg);
        }
    }
}
