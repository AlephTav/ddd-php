<?php

declare(strict_types=1);

namespace Tests\AlephTools\DDD\Common\Model;

use AlephTools\DDD\Common\Model\Currency;
use AlephTools\DDD\Common\Model\Exceptions\InvalidArgumentException;
use AlephTools\DDD\Common\Model\Money;
use DateTime;
use PHPUnit\Framework\TestCase;
use stdClass;
use UnexpectedValueException;

/**
 * @internal
 */
class MoneyTest extends TestCase
{
    /**
     * @dataProvider validMoneyTypes
     */
    public function testCreation(mixed $value, Currency $currency, string $expected): void
    {
        $money = new Money($value, $currency);

        self::assertSame($expected, $money->amount);
        self::assertSame($currency, $money->currency);
        self::assertSame($expected, $money->toString());
    }

    public static function validMoneyTypes(): array
    {
        return [
            [
                0, Currency::ALL(), '0',
            ],
            [
                '123', Currency::USD(), '123',
            ],
            [
                125.3467, Currency::RUB(), '125.3467',
            ],
            [
                1234567890, Currency::ARS(), '1234567890',
            ],
            [
                '1.6E-7', Currency::RUB(), '0.00000016',
            ],
            [
                '0.006E3', Currency::RUB(), '6',
            ],
            [
                5.67e8, Currency::CLF(), '567000000',
            ],
            [
                '001.0001e-2', Currency::BAM(), '0.010001',
            ]
        ];
    }

    /**
     * @dataProvider invalidMoneyTypes
     */
    public function testInvalidAmountType(mixed $value, string $error): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($error);

        new Money(['amount' => $value]);
    }

    public static function invalidMoneyTypes(): array
    {
        return [
            [
                ['value'],
                'Money amount must be a scalar, array given.',
            ],
            [
                new stdClass(),
                'Money amount must be a scalar, object given.',
            ],
        ];
    }

    /**
     * @dataProvider invalidMoneyValues
     */
    public function testInvalidMoneyFormat(mixed $value): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid money format.');

        new Money($value);
    }

    public static function invalidMoneyValues(): array
    {
        return [
            ['.'],
            ['+'],
            ['-'],
            ['+.'],
            ['-.'],
            ['.e'],
            ['e'],
        ];
    }

    public function testCurrencyCodeValidation(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Currency must not be null.');

        new Money([
            'amount' => 500,
        ]);
    }

    public function testCurrency(): void
    {
        $money = Currency::RUB();

        self::assertSame('Russian ruble', $money->getName());
        self::assertSame('643', $money->getNumericCode());
        self::assertSame(2, $money->getSubunits());
    }

    public function testAdd(): void
    {
        $money = (new Money('13.67'))->add('12.56');

        self::assertSame('26.23', $money->amount);
        self::assertSame(Currency::USD(), $money->currency);
    }

    public function testSub(): void
    {
        $money = (new Money('11.04'))->sub('12.96');

        self::assertSame('-1.92', $money->amount);
        self::assertSame(Currency::USD(), $money->currency);
    }

    public function testMul(): void
    {
        $money = (new Money('7.53'))->mul('17.79');

        self::assertSame('133.9587', $money->amount);
        self::assertSame(Currency::USD(), $money->currency);
    }

    public function testDiv(): void
    {
        $money = (new Money('34.67'))->div('5.01');

        self::assertSame('6.920159680638', $money->amount);
        self::assertSame(Currency::USD(), $money->currency);
    }

    public function testSqrt(): void
    {
        $money = (new Money('2.25'))->sqrt();
        self::assertSame('1.5', $money->amount);
        self::assertSame(Currency::USD(), $money->currency);
    }

    public function testSqrtFromNegativeNumber(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new Money('-2.25'))->sqrt();
    }

    public function testCmp(): void
    {
        $money = new Money('3.33');
        self::assertSame(1, $money->cmp('1.57'));
        self::assertSame(-1, $money->cmp('5.45'));
        self::assertSame(0, $money->cmp('3.33'));
    }

    public function testIsZero(): void
    {
        $money = new Money('0');
        self::assertTrue($money->isZero());

        $money = new Money('0.000');
        self::assertTrue($money->isZero());

        $money = new Money('0.00001');
        self::assertFalse($money->isZero());
    }

    public function testIsNegative(): void
    {
        $money = new Money('0');
        self::assertFalse($money->isNegative());

        $money = new Money('-0.0003');
        self::assertTrue($money->isNegative());

        $money = new Money('0.00001');
        self::assertFalse($money->isNegative());
    }

    public function testIsPositive(): void
    {
        $money = new Money('0');
        self::assertFalse($money->isPositive());

        $money = new Money('-0.0003');
        self::assertFalse($money->isPositive());

        $money = new Money('0.00001');
        self::assertTrue($money->isPositive());
    }

    public function testIsLessThan(): void
    {
        $money = new Money('0');
        self::assertTrue($money->isLessThan('5.67'));

        $money = new Money('-0.0003333');
        self::assertTrue($money->isLessThan('-0.00009'));

        $money = new Money('0.123');
        self::assertFalse($money->isLessThan('-1'));

        $money = new Money('-0.123');
        self::assertFalse($money->isLessThan('-0.123'));
    }

    public function testIsGreaterThan(): void
    {
        $money = new Money('0');
        self::assertFalse($money->isGreaterThan('5.67'));

        $money = new Money('-0.0003333');
        self::assertFalse($money->isGreaterThan('-0.00009'));

        $money = new Money('0.123');
        self::assertTrue($money->isGreaterThan('-1'));

        $money = new Money('-0.123');
        self::assertFalse($money->isGreaterThan('-0.123'));
    }

    public function testIsLessOrEqualThan(): void
    {
        $money = new Money('0');
        self::assertTrue($money->isLessOrEqualThan('5.67'));

        $money = new Money('-0.0003333');
        self::assertTrue($money->isLessOrEqualThan('-0.00009'));

        $money = new Money('0.123');
        self::assertFalse($money->isLessOrEqualThan('-1'));

        $money = new Money('-0.123');
        self::assertTrue($money->isLessOrEqualThan('-0.123'));
    }

    public function testIsGreaterOrEqualThan(): void
    {
        $money = new Money('0');
        self::assertFalse($money->isGreaterOrEqualThan('5.67'));

        $money = new Money('-0.0003333');
        self::assertFalse($money->isGreaterOrEqualThan('-0.00009'));

        $money = new Money('0.123');
        self::assertTrue($money->isGreaterOrEqualThan('-1'));

        $money = new Money('-0.123');
        self::assertTrue($money->isGreaterOrEqualThan('-0.123'));
    }

    public function testEquals(): void
    {
        $money = new Money('0');
        self::assertTrue($money->equals('0.00000'));
        self::assertFalse($money->equals('0.00001'));

        $money = new Money('-1.234', Currency::RUB());
        self::assertTrue($money->equals('-1.234000'));
        self::assertFalse($money->equals('-1.23399999'));

        self::assertTrue($money->equals(new Money('-1.234000', Currency::RUB())));
        self::assertFalse($money->equals(new Money('-1.234000', Currency::ZAR())));
        self::assertFalse($money->equals(new Money('-1.233999', Currency::RUB())));
        self::assertFalse($money->equals(new DateTime()));
    }

    public function testAbs(): void
    {
        $money = new Money('0');
        self::assertTrue($money->equals($money->abs()));

        $money = new Money('123');
        self::assertTrue($money->equals($money->abs()));

        $money = new Money('-1.23');
        self::assertFalse($money->equals($money->abs()));
        self::assertSame(0, $money->abs()->cmp('1.23'));
    }

    public function testToRoundAmount(): void
    {
        $money = new Money('0.005');
        self::assertSame('0.01', $money->toRoundAmount());

        $money = new Money('-0.005');
        self::assertSame('-0.01', $money->toRoundAmount());

        $money = new Money('-0.004');
        self::assertSame('0', $money->toRoundAmount());

        $money = new Money('0');
        self::assertSame('0', $money->toRoundAmount());
    }

    public function testToFloorAmount(): void
    {
        $money = new Money('0.005');
        self::assertSame('0', $money->toFloorAmount());

        $money = new Money('-0.005');
        self::assertSame('-0.01', $money->toFloorAmount());

        $money = new Money('-0.004');
        self::assertSame('-0.01', $money->toFloorAmount());

        $money = new Money('0');
        self::assertSame('0', $money->toFloorAmount());
    }

    public function testToCeilAmount(): void
    {
        $money = new Money('0.005');
        self::assertSame('0.01', $money->toCeilAmount());

        $money = new Money('-0.005');
        self::assertSame('0', $money->toCeilAmount());

        $money = new Money('-0.004');
        self::assertSame('0', $money->toCeilAmount());

        $money = new Money('0');
        self::assertSame('0', $money->toCeilAmount());
    }

    public function testToScaledAmountWithInvalidScaleFunction(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Unexpected scale function: foo.');

        (new Money('0.005'))->toScaledAmount('foo');
    }

    public function testToScalar(): void
    {
        $money = new Money(34.56);

        self::assertSame('34.56', $money->toString());
        self::assertSame('34.56', $money->toScalar());
    }

    public function testParseAmountWithComma(): void
    {
        $money = new Money('67,345');

        self::assertSame('67.345', $money->toString());
    }
}
