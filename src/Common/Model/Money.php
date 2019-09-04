<?php

namespace AlephTools\DDD\Common\Model;

use AlephTools\DDD\Common\Infrastructure\ValueObject;

/**
 * @property-read string $amount
 * @property-read Currency $currency
 */
class Money extends ValueObject
{
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
        return parent::equals($other);
    }

    public function abs(): Money
    {
        if ($this->isNegative()) {
            return $this->mul('-1');
        }
        return $this;
    }

    public function cmp(string $amount): int
    {
        return bccomp($this->amount, $amount, static::PRECISION);
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

    public function sqrt(string $amount): Money
    {
        return $this->op($amount, 'bcsqrt');
    }

    private function op(string $amount, string $operation): Money
    {
        $amount = $operation($this->amount, $amount, static::PRECISION);
        return new Money($amount, $this->currency);
    }

    public function asScaledString(): string
    {
        return $this->bcround($this->amount, $this->currency->getSubunits());
    }

    private function bcround($number, $precision = 0)
    {
        if (strpos($number, '.') !== false) {
            if ($number[0] != '-') {
                return bcadd($number, '0.' . str_repeat('0', $precision) . '5', $precision);
            }
            return bcsub($number, '0.' . str_repeat('0', $precision) . '5', $precision);
        }
        return $number . '.' . str_repeat('0', $precision);
    }

    public function toString(): string
    {
        return $this->amount;
    }

    //region Setters and Validators

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

    private function validateCurrency(): void
    {
        $this->assertArgumentNotNull($this->currency, 'Currency must not be null.');
    }

    //endregion
}
