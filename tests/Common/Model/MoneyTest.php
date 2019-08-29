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
        $money = new Money(123);

        $this->assertSame('123', $money->amount);
        $this->assertSame(Currency::USD(), $money->currency);
        $this->assertSame('123', $money->toString());

        $money = new Money(null, Currency::EUR());

        $this->assertSame('0', $money->amount);
        $this->assertSame(Currency::EUR(), $money->currency);
        $this->assertSame('0', $money->toString());

        $money = new Money([
            'amount' => 111,
            'currency' => Currency::RUB()
        ]);

        $this->assertSame('111', $money->amount);
        $this->assertSame(Currency::RUB(), $money->currency);
        $this->assertSame('111', $money->toString());

        $money = new Money($money, Currency::USD());

        $this->assertSame('111', $money->amount);
        $this->assertSame(Currency::RUB(), $money->currency);
        $this->assertSame('111', $money->toString());
    }

    public function testInvalidAmount(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Money amount must be a scalar, array given.');

        new Money(['amount' => ['value']]);
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
        $money = (new Money('13.67'))->add('12.56');

        $this->assertSame('26.23', $money->amount);
    }

    public function testSub(): void
    {
        $money = (new Money('11.04'))->sub('12.96');

        $this->assertSame('-1.92', $money->amount);
    }

    public function testMul(): void
    {
        $money = (new Money('7.53'))->mul('17.79');

        $this->assertSame('133.95', $money->amount);
    }

    public function testDiv(): void
    {
        $money = (new Money('34.67'))->div('5.01');

        $this->assertSame('6.92', $money->amount);
    }

    public function testCmp(): void
    {
        $money = new Money('3.33');
        $this->assertSame(1, $money->cmp('1.57'));
        $this->assertSame(-1, $money->cmp('5.45'));
        $this->assertSame(0, $money->cmp('3.33'));
    }

    public function testCurrency(): void
    {
        $money = Currency::RUB();

        $this->assertSame('Russian ruble', $money->getName());
        $this->assertSame('643', $money->getNumericCode());
        $this->assertSame(2, $money->getSubunits());
    }
}
