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
    /**
     * @param mixed $value1
     * @param mixed $value2
     */
    protected function assertArgumentSame($value1, $value2, string $msg): void
    {
        if ($value1 !== $value2) {
            throw new InvalidArgumentException($msg);
        }
    }

    /**
     * @param mixed $value1
     * @param mixed $value2
     */
    protected function assertArgumentNotSame($value1, $value2, string $msg): void
    {
        if ($value1 === $value2) {
            throw new InvalidArgumentException($msg);
        }
    }

    /**
     * @param mixed $value1
     * @param mixed $value2
     */
    protected function assertArgumentEquals($value1, $value2, string $msg): void
    {
        if ($value1 != $value2) {
            throw new InvalidArgumentException($msg);
        }
    }

    /**
     * @param mixed $value1
     * @param mixed $value2
     */
    protected function assertArgumentNotEquals($value1, $value2, string $msg): void
    {
        if ($value1 == $value2) {
            throw new InvalidArgumentException($msg);
        }
    }

    /**
     * @param mixed $value
     */
    protected function assertArgumentNotNull($value, string $msg): void
    {
        if ($value === null) {
            throw new InvalidArgumentException($msg);
        }
    }

    /**
     * @param mixed $value
     */
    protected function assertArgumentNull($value, string $msg): void
    {
        if ($value !== null) {
            throw new InvalidArgumentException($msg);
        }
    }

    /**
     * @param mixed $value
     */
    protected function assertArgumentFalse($value, string $msg): void
    {
        if ($value) {
            throw new InvalidArgumentException($msg);
        }
    }

    /**
     * @param mixed $value
     */
    protected function assertArgumentTrue($value, string $msg): void
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

    /**
     * @param mixed $value
     * @param mixed $min
     * @param mixed $max
     */
    protected function assertArgumentRange($value, $min, $max, string $msg): void
    {
        if ($value < $min || $value > $max) {
            throw new InvalidArgumentException($msg);
        }
    }

    /**
     * @param mixed $value
     * @param mixed $min
     */
    protected function assertArgumentMin($value, $min, string $msg): void
    {
        if ($value < $min) {
            throw new InvalidArgumentException($msg);
        }
    }

    /**
     * @param mixed $value
     * @param mixed $max
     */
    protected function assertArgumentMax($value, $max, string $msg): void
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
        } catch (Throwable $ignore) {
            throw new InvalidArgumentException($msg);
        }
    }

    /**
     * @param mixed $value
     */
    protected function assertArgumentInstanceOf($value, string $class, string $msg): void
    {
        if (!is_a($value, $class)) {
            throw new InvalidArgumentException($msg);
        }
    }

    /**
     * @param mixed $value
     */
    protected function assertStateFalse($value, string $msg): void
    {
        if ($value) {
            throw new InvalidStateException($msg);
        }
    }

    /**
     * @param mixed $value
     */
    protected function assertStateTrue($value, string $msg): void
    {
        if (!$value) {
            throw new InvalidStateException($msg);
        }
    }
}
