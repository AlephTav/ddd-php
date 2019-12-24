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
            'amount' => 111.555,
            'currency' => Currency::RUB()
        ]);

        $this->assertSame('111.555', $money->amount);
        $this->assertSame(Currency::RUB(), $money->currency);
        $this->assertSame('111.555', $money->toString());

        $money = new Money($money, Currency::USD());

        $this->assertSame('111.555', $money->amount);
        $this->assertSame(Currency::RUB(), $money->currency);
        $this->assertSame('111.555', $money->toString());
    }

    /**
     * @dataProvider invalidMoneyTypes
     * @param mixed $value
     * @param string $error
     */
    public function testInvalidAmountType($value, string $error): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($error);

        new Money(['amount' => $value]);
    }

    public function invalidMoneyTypes(): array
    {
        return [
            [
                ['value'],
                'Money amount must be a scalar, array given.'
            ],
            [
                new \stdClass(),
                'Money amount must be a scalar, object given.'
            ]
        ];
    }

    /**
     * @dataProvider  invalidMoneyValues
     * @param mixed $value
     */
    public function testInvalidMoneyFormat($value): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid money format.');

        new Money(['amount' => '0,10']);
    }

    public function invalidMoneyValues(): array
    {
        return [
            [''],
            ['0.'],
            ['.0'],
            ['.'],
            ['+'],
            ['-'],
            ['+.'],
            ['+0.'],
            ['-.0']
        ];
    }

    public function testCurrencyCodeValidation(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Currency must not be null.');

        new Money([
            'amount' => 500
        ]);
    }

    public function testCurrency(): void
    {
        $money = Currency::RUB();

        $this->assertSame('Russian ruble', $money->getName());
        $this->assertSame('643', $money->getNumericCode());
        $this->assertSame(2, $money->getSubunits());
    }

    public function testAdd(): void
    {
        $money = (new Money('13.67'))->add('12.56');

        $this->assertSame('26.230000000000', $money->amount);
        $this->assertSame(Currency::USD(), $money->currency);
    }

    public function testSub(): void
    {
        $money = (new Money('11.04'))->sub('12.96');

        $this->assertSame('-1.920000000000', $money->amount);
        $this->assertSame(Currency::USD(), $money->currency);
    }

    public function testMul(): void
    {
        $money = (new Money('7.53'))->mul('17.79');

        $this->assertSame('133.958700000000', $money->amount);
        $this->assertSame(Currency::USD(), $money->currency);
    }

    public function testDiv(): void
    {
        $money = (new Money('34.67'))->div('5.01');

        $this->assertSame('6.920159680638', $money->amount);
        $this->assertSame(Currency::USD(), $money->currency);
    }

    public function testSqrt(): void
    {
        $money = (new Money('2.25'))->sqrt();
        $this->assertSame('1.500000000000', $money->amount);
        $this->assertSame(Currency::USD(), $money->currency);
    }

    public function testCmp(): void
    {
        $money = new Money('3.33');
        $this->assertSame(1, $money->cmp('1.57'));
        $this->assertSame(-1, $money->cmp('5.45'));
        $this->assertSame(0, $money->cmp('3.33'));
    }

    public function testIsZero(): void
    {
        $money = new Money('0');
        $this->assertTrue($money->isZero());

        $money = new Money('0.000');
        $this->assertTrue($money->isZero());

        $money = new Money('0.00001');
        $this->assertFalse($money->isZero());
    }

    public function testIsNegative(): void
    {
        $money = new Money('0');
        $this->assertFalse($money->isNegative());

        $money = new Money('-0.0003');
        $this->assertTrue($money->isNegative());

        $money = new Money('0.00001');
        $this->assertFalse($money->isNegative());
    }

    public function testIsPositive(): void
    {
        $money = new Money('0');
        $this->assertFalse($money->isPositive());

        $money = new Money('-0.0003');
        $this->assertFalse($money->isPositive());

        $money = new Money('0.00001');
        $this->assertTrue($money->isPositive());
    }

    public function testIsLessThan(): void
    {
        $money = new Money('0');
        $this->assertTrue($money->isLessThan('5.67'));

        $money = new Money('-0.0003333');
        $this->assertTrue($money->isLessThan('-0.00009'));

        $money = new Money('0.123');
        $this->assertFalse($money->isLessThan('-1'));

        $money = new Money('-0.123');
        $this->assertFalse($money->isLessThan('-0.123'));
    }

    public function testIsGreaterThan(): void
    {
        $money = new Money('0');
        $this->assertFalse($money->isGreaterThan('5.67'));

        $money = new Money('-0.0003333');
        $this->assertFalse($money->isGreaterThan('-0.00009'));

        $money = new Money('0.123');
        $this->assertTrue($money->isGreaterThan('-1'));

        $money = new Money('-0.123');
        $this->assertFalse($money->isGreaterThan('-0.123'));
    }

    public function testIsLessOrEqualThan(): void
    {
        $money = new Money('0');
        $this->assertTrue($money->isLessOrEqualThan('5.67'));

        $money = new Money('-0.0003333');
        $this->assertTrue($money->isLessOrEqualThan('-0.00009'));

        $money = new Money('0.123');
        $this->assertFalse($money->isLessOrEqualThan('-1'));

        $money = new Money('-0.123');
        $this->assertTrue($money->isLessOrEqualThan('-0.123'));
    }

    public function testIsGreaterOrEqualThan(): void
    {
        $money = new Money('0');
        $this->assertFalse($money->isGreaterOrEqualThan('5.67'));

        $money = new Money('-0.0003333');
        $this->assertFalse($money->isGreaterOrEqualThan('-0.00009'));

        $money = new Money('0.123');
        $this->assertTrue($money->isGreaterOrEqualThan('-1'));

        $money = new Money('-0.123');
        $this->assertTrue($money->isGreaterOrEqualThan('-0.123'));
    }

    public function testEquals(): void
    {
        $money = new Money('0');
        $this->assertTrue($money->equals('0.00000'));
        $this->assertFalse($money->equals('0.00001'));

        $money = new Money('-1.234', Currency::RUB());
        $this->assertTrue($money->equals('-1.234000'));
        $this->assertFalse($money->equals('-1.23399999'));

        $this->assertTrue($money->equals(new Money('-1.234000', Currency::RUB())));
        $this->assertFalse($money->equals(new Money('-1.234000', Currency::ZAR())));
        $this->assertFalse($money->equals(new Money('-1.233999', Currency::RUB())));
        $this->assertFalse($money->equals(new \DateTime()));
    }

    public function testAbs(): void
    {
        $money = new Money('0');
        $this->assertTrue($money->equals($money->abs()));

        $money = new Money('123');
        $this->assertTrue($money->equals($money->abs()));

        $money = new Money('-1.23');
        $this->assertFalse($money->equals($money->abs()));
        $this->assertSame(0, $money->abs()->cmp('1.23'));
    }

    public function testToRoundAmount(): void
    {
        $money = new Money('0.005');
        $this->assertSame('0.01', $money->toRoundAmount());

        $money = new Money('-0.005');
        $this->assertSame('-0.01', $money->toRoundAmount());

        $money = new Money('-0.004');
        $this->assertSame('-0.00', $money->toRoundAmount());

        $money = new Money('0');
        $this->assertSame('0.00', $money->toRoundAmount());
    }

    public function testToFloorAmount(): void
    {
        $money = new Money('0.005');
        $this->assertSame('0.00', $money->toFloorAmount());

        $money = new Money('-0.005');
        $this->assertSame('-0.01', $money->toFloorAmount());

        $money = new Money('-0.004');
        $this->assertSame('-0.01', $money->toFloorAmount());

        $money = new Money('0');
        $this->assertSame('0.00', $money->toFloorAmount());
    }

    public function testToCeilAmount(): void
    {
        $money = new Money('0.005');
        $this->assertSame('0.01', $money->toCeilAmount());

        $money = new Money('-0.005');
        $this->assertSame('-0.00', $money->toCeilAmount());

        $money = new Money('-0.004');
        $this->assertSame('-0.00', $money->toCeilAmount());

        $money = new Money('0');
        $this->assertSame('0.00', $money->toCeilAmount());
    }

    public function testToScaledAmountWithInvalidScaleFunction(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Unexpected scale function: foo.');

        (new Money('0.005'))->toScaledAmount('foo');
    }
}
