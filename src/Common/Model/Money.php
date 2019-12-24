<?php

namespace AlephTools\DDD\Common\Model;

use UnexpectedValueException;
use AlephTools\DDD\Common\Infrastructure\ValueObject;

/**
 * @property-read string $amount
 * @property-read Currency $currency
 */
class Money extends ValueObject
{
    public const SCALE_FUNC_ROUND = 'round';
    public const SCALE_FUNC_FLOOR = 'floor';
    public const SCALE_FUNC_CEIL = 'ceil';

    protected const PRECISION = 12;

    private $amount;
    private $currency;

    /**
     * Constructor.
     *
     * @param array|string|int|float|Money $amount
     * @param Currency|null $currency
     */
    public function __construct($amount, Currency $currency = null)
    {
        if (is_array($amount)) {
            parent::__construct($amount);
        } else if ($amount instanceof Money) {
            parent::__construct([
                'amount' => $amount->amount,
                'currency' => $amount->currency
            ]);
        } else {
            parent::__construct([
                'amount' => $amount,
                'currency' => $currency ?? Currency::USD()
            ]);
        }
    }

    public function toString(): string
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

    public function isLessThan(string $amount): bool
    {
        return $this->cmp($amount) < 0;
    }

    public function isLessOrEqualThan(string $amount): bool
    {
        return $this->cmp($amount) <= 0;
    }

    public function isGreaterThan(string $amount): bool
    {
        return $this->cmp($amount) > 0;
    }

    public function isGreaterOrEqualThan(string $amount): bool
    {
        return $this->cmp($amount) >= 0;
    }

    public function equals($other): bool
    {
        if (is_scalar($other)) {
            return $this->cmp($other) === 0;
        }
        if ($other instanceof Money) {
            return $this->cmp($other->amount) === 0 && $this->currency->is($other->currency);
        }
        return false;
    }

    public function cmp(string $amount): int
    {
        return bccomp($this->amount, $amount, static::PRECISION);
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

    public function add(string $amount): Money
    {
        return $this->op($amount, 'bcadd');
    }

    public function sub(string $amount): Money
    {
        return $this->op($amount, 'bcsub');
    }

    public function mul(string $amount): Money
    {
        return $this->op($amount, 'bcmul');
    }

    public function div(string $amount): Money
    {
        return $this->op($amount, 'bcdiv');
    }

    public function sqrt(): Money
    {
        return new Money(bcsqrt($this->amount, static::PRECISION), $this->currency);
    }

    private function op(string $amount, string $operation): Money
    {
        $amount = $operation($this->amount, $amount, static::PRECISION);
        return new Money($amount, $this->currency);
    }

    //endregion

    //region Scaling

    public function toRoundAmount(): string
    {
        return $this->toScaledAmount(self::SCALE_FUNC_ROUND);
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
        if ($scaleFunction !== self::SCALE_FUNC_ROUND &&
            $scaleFunction !== self::SCALE_FUNC_FLOOR &&
            $scaleFunction !== self::SCALE_FUNC_CEIL
        ) {
            throw new UnexpectedValueException("Unexpected scale function: $scaleFunction.");
        }
        return $this->{$scaleFunction}($this->amount, $this->currency->getSubunits());
    }

    private function round(string $number, int $precision): string
    {
        if (strpos($number, '.') !== false) {
            if ($number[0] != '-') {
                return bcadd($number, '0.' . str_repeat('0', $precision) . '5', $precision);
            }
            return bcsub($number, '0.' . str_repeat('0', $precision) . '5', $precision);
        }
        return $number . '.' . str_repeat('0', $precision);
    }

    private function floor(string $number, int $precision): string
    {
        if (strpos($number, '.') !== false) {
            if ($number[0] != '-') {
                return bcadd($number, '0', $precision);
            }
            return bcsub($number, '0.' . str_repeat('0', $precision) . '9', $precision);
        }
        return $number . '.' . str_repeat('0', $precision);
    }

    private function ceil(string $number, int $precision): string
    {
        if (strpos($number, '.') !== false) {
            if ($number[0] != '-') {
                return bcadd($number, '0.' . str_repeat('0', $precision) . '9', $precision);
            }
            return bcsub($number, '0', $precision);
        }
        return $number . '.' . str_repeat('0', $precision);
    }

    //endregion

    //region Setters

    private function setAmount($amount): void
    {
        $this->assertArgumentFalse(
            $amount !== null && !is_scalar($amount),
            'Money amount must be a scalar, ' . gettype($amount) . ' given.'
        );
        $this->amount = $amount ? (string)$amount : '0';
    }

    private function setCurrency(?Currency $currency): void
    {
        $this->currency = $currency;
    }

    //endregion

    //region Validators

    private function validateCurrency(): void
    {
        $this->assertArgumentNotNull($this->currency, 'Currency must not be null.');
    }

    private function validateAmount(): void
    {
        $this->assertArgumentPatternMatch(
            $this->amount,
            '/^[+-]?[0-9]+(\.[0-9]+)?$/',
            'Invalid money format.'
        );
    }

    //endregion
}
