<?php

declare(strict_types=1);

namespace AlephTools\DDD\Common\Model;

use InvalidArgumentException;
use UnexpectedValueException;
use AlephTools\DDD\Common\Infrastructure\Scalarable;
use AlephTools\DDD\Common\Infrastructure\ValueObject;

/**
 * @property-read numeric-string $amount
 * @property-read Currency $currency
 */
class Money extends ValueObject implements Scalarable
{
    public const SCALE_FUNC_ROUND = 'round';
    public const SCALE_FUNC_FLOOR = 'floor';
    public const SCALE_FUNC_CEIL = 'ceil';

    protected const PRECISION = 12;

    /** @var numeric-string */
    protected string $amount = '0';
    protected ?Currency $currency = null;

    /**
     * Constructor.
     *
     * @param array<string,mixed>|string|int|float|Money $amount
     */
    public function __construct(null|array|string|int|float|Money $amount, Currency $currency = null)
    {
        if (is_array($amount)) {
            parent::__construct($amount);
        } elseif ($amount instanceof Money) {
            parent::__construct([
                'amount' => $amount->amount,
                'currency' => $amount->currency,
            ]);
        } else {
            parent::__construct([
                'amount' => $amount,
                'currency' => $currency ?? Currency::USD(),
            ]);
        }
    }

    public function toString(): string
    {
        return $this->amount;
    }

    public function toScalar(): mixed
    {
        return $this->amount;
    }

    //region Comparison

    public function isPositive(): bool
    {
        return $this->isGreaterThan('0');
    }

    public function isZero(): bool
    {
        return $this->equals('0');
    }

    public function isNegative(): bool
    {
        return $this->isLessThan('0');
    }

    /**
     * @param numeric-string $amount
     */
    public function isLessThan(string $amount): bool
    {
        return $this->cmp($amount) < 0;
    }

    /**
     * @param numeric-string $amount
     */
    public function isLessOrEqualThan(string $amount): bool
    {
        return $this->cmp($amount) <= 0;
    }

    /**
     * @param numeric-string $amount
     */
    public function isGreaterThan(string $amount): bool
    {
        return $this->cmp($amount) > 0;
    }

    /**
     * @param numeric-string $amount
     */
    public function isGreaterOrEqualThan(string $amount): bool
    {
        return $this->cmp($amount) >= 0;
    }

    public function equals(mixed $other): bool
    {
        if (is_numeric($other)) {
            return $this->cmp((string)$other) === 0;
        }
        if ($other instanceof Money) {
            return $this->cmp($other->amount) === 0 && $this->currency->equals($other->currency);
        }
        return false;
    }

    /**
     * @param numeric-string $amount
     */
    public function cmp(string $amount): int
    {
        return bccomp($this->amount, $this->normalizeAmount($amount), self::PRECISION);
    }

    //endregion

    //region Basic Operation

    public function abs(): Money
    {
        if ($this->isNegative()) {
            return $this->mul('-1');
        }
        return $this;
    }

    /**
     * @param numeric-string $amount
     */
    public function add(string $amount): Money
    {
        return $this->op($amount, 'bcadd');
    }

    /**
     * @param numeric-string $amount
     */
    public function sub(string $amount): Money
    {
        return $this->op($amount, 'bcsub');
    }

    /**
     * @param numeric-string $amount
     */
    public function mul(string $amount): Money
    {
        return $this->op($amount, 'bcmul');
    }

    /**
     * @param numeric-string $amount
     */
    public function div(string $amount): Money
    {
        return $this->op($amount, 'bcdiv');
    }

    public function sqrt(): Money
    {
        if ($this->isNegative()) {
            throw new InvalidArgumentException('Cannot take the square root of a negative number.');
        }
        $amount = (string)bcsqrt($this->amount, self::PRECISION);
        return new Money($amount, $this->currency);
    }

    /**
     * @param numeric-string $amount
     */
    private function op(string $amount, string $operation): Money
    {
        $amount = $this->normalizeAmount($amount);
        /** @var numeric-string $amount */
        $amount = $operation($this->amount, $amount, static::PRECISION);
        return new Money($amount, $this->currency);
    }

    //endregion

    //region Scaling

    public function toRoundAmount(): string
    {
        return $this->toScaledAmount();
    }

    public function toFloorAmount(): string
    {
        return $this->toScaledAmount(self::SCALE_FUNC_FLOOR);
    }

    public function toCeilAmount(): string
    {
        return $this->toScaledAmount(self::SCALE_FUNC_CEIL);
    }

    public function toScaledAmount(string $scaleFunction = self::SCALE_FUNC_ROUND): string
    {
        switch ($scaleFunction) {
            case self::SCALE_FUNC_CEIL:
                return $this->ceil($this->amount, $this->currency->getSubunits());
            case self::SCALE_FUNC_FLOOR:
                return $this->floor($this->amount, $this->currency->getSubunits());
            case self::SCALE_FUNC_ROUND:
                return $this->round($this->amount, $this->currency->getSubunits());
        }
        throw new UnexpectedValueException("Unexpected scale function: $scaleFunction.");
    }

    /**
     * @param numeric-string $number
     */
    private function round(string $number, int $precision): string
    {
        if (str_contains($number, '.')) {
            /** @psalm-var numeric-string $addend */
            $addend = '0.' . str_repeat('0', $precision) . '5';
            if ($number[0] != '-') {
                return bcadd($number, $addend, $precision);
            }
            return bcsub($number, $addend, $precision);
        }
        return $number . '.' . str_repeat('0', $precision);
    }

    /**
     * @param numeric-string $number
     */
    private function floor(string $number, int $precision): string
    {
        if (str_contains($number, '.')) {
            if ($number[0] != '-') {
                return bcadd($number, '0', $precision);
            }
            /** @psalm-var numeric-string $addend */
            $addend = '0.' . str_repeat('0', $precision) . '9';
            return bcsub($number, $addend, $precision);
        }
        return $number . '.' . str_repeat('0', $precision);
    }

    /**
     * @param numeric-string $number
     */
    private function ceil(string $number, int $precision): string
    {
        if (str_contains($number, '.')) {
            if ($number[0] != '-') {
                /** @psalm-var numeric-string $addend */
                $addend = '0.' . str_repeat('0', $precision) . '9';
                return bcadd($number, $addend, $precision);
            }
            return bcsub($number, '0', $precision);
        }
        return $number . '.' . str_repeat('0', $precision);
    }

    //endregion

    /**
     * @return numeric-string
     */
    protected function normalizeAmount(string $amount): string
    {
        $amount = str_replace(',', '.', $amount);
        $this->assertArgumentTrue(is_numeric($amount), 'Amount must be a number.');
        return $amount;
    }

    //region Setters

    protected function setAmount(mixed $amount): void
    {
        $this->assertArgumentFalse(
            $amount !== null && !is_scalar($amount),
            'Money amount must be a scalar, ' . gettype($amount) . ' given.'
        );
        /** @psalm-var numeric-string $amount */
        $amount = $amount ? str_replace(',', '.', (string)$amount) : '0';
        $this->amount = $amount;
    }

    //endregion

    //region Validators

    protected function validateCurrency(): void
    {
        $this->assertArgumentNotNull($this->currency, 'Currency must not be null.');
    }

    protected function validateAmount(): void
    {
        $this->assertArgumentPatternMatch(
            $this->amount,
            '/^[+-]?[0-9]+(\.[0-9]+)?$/',
            'Invalid money format.'
        );
    }

    //endregion
}
