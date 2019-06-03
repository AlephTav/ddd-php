<?php

namespace AlephTools\DDD\Tests\Common\Model;

use AlephTools\DDD\Common\Model\Money;
use AlephTools\DDD\Common\Model\Currency;
use AlephTools\DDD\Common\Model\Exceptions\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class MoneyTest extends TestCase
{
    public function testCreation(): void
    {
        $currency = new Money(123);

        $this->assertSame('123', $currency->amount);
        $this->assertSame(Currency::USD(), $currency->currency);
        $this->assertSame('123', $currency->toString());

        $currency = new Money(null, Currency::EUR());

        $this->assertSame('0', $currency->amount);
        $this->assertSame(Currency::EUR(), $currency->currency);
        $this->assertSame('0', $currency->toString());

        $currency = new Money([
            'amount' => 111,
            'currency' => Currency::RUB()
        ]);

        $this->assertSame('111', $currency->amount);
        $this->assertSame(Currency::RUB(), $currency->currency);
        $this->assertSame('111', $currency->toString());
    }

    public function testCurrencyCodeValidation(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Currency must not be null.');

        new Money([
            'amount' => 500
        ]);
    }

    public function testAdd(): void
    {
        $currency = (new Money('13.67'))->add('12.56');

        $this->assertSame('26.23', $currency->amount);
    }

    public function testSub(): void
    {
        $currency = (new Money('11.04'))->sub('12.96');

        $this->assertSame('-1.92', $currency->amount);
    }

    public function testMul(): void
    {
        $currency = (new Money('7.53'))->mul('17.79');

        $this->assertSame('133.95', $currency->amount);
    }

    public function testDiv(): void
    {
        $currency = (new Money('34.67'))->div('5.01');

        $this->assertSame('6.92', $currency->amount);
    }

    public function testCmp(): void
    {
        $currency = new Money('3.33');
        $this->assertSame(1, $currency->cmp('1.57'));
        $this->assertSame(-1, $currency->cmp('5.45'));
        $this->assertSame(0, $currency->cmp('3.33'));
    }

    public function testCurrency(): void
    {
        $currency = Currency::RUB();

        $this->assertSame('Russian ruble', $currency->getName());
        $this->assertSame('643', $currency->getNumericCode());
        $this->assertSame(2, $currency->getSubunits());
    }
}
