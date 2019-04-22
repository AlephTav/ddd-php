<?php

namespace AlephTools\DDD\Tests\Common\Model;

use AlephTools\DDD\Common\Model\Currency;
use AlephTools\DDD\Common\Model\CurrencyCode;
use AlephTools\DDD\Common\Model\Exceptions\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class CurrencyTest extends TestCase
{
    public function testCreation(): void
    {
        $currency = new Currency(123);

        $this->assertSame('123', $currency->value);
        $this->assertSame(CurrencyCode::USD(), $currency->code);
        $this->assertSame('123', $currency->toString());

        $currency = new Currency(null, CurrencyCode::EUR());

        $this->assertSame('0', $currency->value);
        $this->assertSame(CurrencyCode::EUR(), $currency->code);
        $this->assertSame('0', $currency->toString());

        $currency = new Currency([
            'value' => 111,
            'code' => CurrencyCode::RUB()
        ]);

        $this->assertSame('111', $currency->value);
        $this->assertSame(CurrencyCode::RUB(), $currency->code);
        $this->assertSame('111', $currency->toString());
    }

    public function testCurrencyCodeValidation(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Currency code must not be null.');

        new Currency([
            'value' => 500
        ]);
    }

    public function testAdd(): void
    {
        $currency = (new Currency('13.67'))->add('12.56');

        $this->assertSame('26.23', $currency->value);
    }

    public function testSub(): void
    {
        $currency = (new Currency('11.04'))->sub('12.96');

        $this->assertSame('-1.92', $currency->value);
    }

    public function testMul(): void
    {
        $currency = (new Currency('7.53'))->mul('17.79');

        $this->assertSame('133.95', $currency->value);
    }

    public function testDiv(): void
    {
        $currency = (new Currency('34.67'))->div('5.01');

        $this->assertSame('6.92', $currency->value);
    }

    public function testCmp(): void
    {
        $currency = new Currency('3.33');
        $this->assertSame(1, $currency->cmp('1.57'));
        $this->assertSame(-1, $currency->cmp('5.45'));
        $this->assertSame(0, $currency->cmp('3.33'));
    }
}
