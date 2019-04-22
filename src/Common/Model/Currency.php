<?php

namespace AlephTools\DDD\Common\Model;

use AlephTools\DDD\Common\Infrastructure\ValueObject;

/**
 * @property-read string $value
 * @property-read CurrencyCode $code
 */
class Currency extends ValueObject
{
    private $value;
    private $code;

    /**
     * Constructor.
     *
     * @param array|string|int|float $value
     * @param CurrencyCode|null $code
     */
    public function __construct($value, CurrencyCode $code = null)
    {
        if (is_array($value)) {
            parent::__construct($value);
        } else {
            parent::__construct([
                'value' => $value,
                'code' => $code ?? CurrencyCode::USD()
            ]);
        }
    }

    public function cmp(string $value): int
    {
        return bccomp($this->value, $value, $this->code->getSubunits());
    }

    public function add(string $value): Currency
    {
        return $this->op($value, 'bcadd');
    }

    public function sub(string $value): Currency
    {
        return $this->op($value, 'bcsub');
    }

    public function mul(string $value): Currency
    {
        return $this->op($value, 'bcmul');
    }

    public function div(string $value): Currency
    {
        return $this->op($value, 'bcdiv');
    }

    private function op(string $value, string $operation): Currency
    {
        $value = $operation($this->value, $value, $this->code->getSubunits());
        return new Currency($value, $this->code);
    }

    public function toString(): string
    {
        return $this->value;
    }

    //region Setters and Validators

    private function setValue(?string $value): void
    {
        $this->value = $value ?? '0';
    }

    private function setCode(?CurrencyCode $code): void
    {
        $this->code = $code;
    }

    private function validateCode(): void
    {
        $this->assertArgumentNotNull($this->code, 'Currency code must not be null.');
    }

    //endregion
}
