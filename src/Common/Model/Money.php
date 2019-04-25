<?php

namespace AlephTools\DDD\Common\Model;

use AlephTools\DDD\Common\Infrastructure\ValueObject;

/**
 * @property-read string $amount
 * @property-read Currency $currency
 */
class Money extends ValueObject
{
    private $amount;
    private $currency;

    /**
     * Constructor.
     *
     * @param array|string|int|float $amount
     * @param Currency|null $currency
     */
    public function __construct($amount, Currency $currency = null)
    {
        if (is_array($amount)) {
            parent::__construct($amount);
        } else {
            parent::__construct([
                'amount' => $amount,
                'currency' => $currency ?? Currency::USD()
            ]);
        }
    }

    public function cmp(string $amount): int
    {
        return bccomp($this->amount, $amount, $this->currency->getSubunits());
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

    private function op(string $amount, string $operation): Money
    {
        $amount = $operation($this->amount, $amount, $this->currency->getSubunits());
        return new Money($amount, $this->currency);
    }

    public function toString(): string
    {
        return $this->amount;
    }

    //region Setters and Validators

    private function setAmount(?string $amount): void
    {
        $this->amount = $amount ?? '0';
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
