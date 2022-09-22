<?php

declare(strict_types=1);

namespace Tests\AlephTools\DDD\Common\Infrastructure;

use AlephTools\DDD\Common\Infrastructure\AssertionConcern;
use AlephTools\DDD\Common\Model\Exceptions\InvalidArgumentException;
use AlephTools\DDD\Common\Model\Exceptions\InvalidStateException;
use DateTime;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use stdClass;

class AssertionConcernTestObject
{
    use AssertionConcern;

    public function __call(string $method, array $params)
    {
        return $this->{$method}(...$params);
    }
}

/**
 * @internal
 */
class AssertionConcernTest extends TestCase
{
    /**
     * Some object that uses AssertionConcern trait.
     *
     * @var AssertionConcernTestObject
     */
    private static $obj;

    public static function setUpBeforeClass(): void
    {
        self::$obj = new AssertionConcernTestObject();
    }

    //region Attribute Assertions

    public function testAssertArgumentSameSuccess(): void
    {
        $obj = new stdClass();
        self::$obj->assertArgumentSame('', '', '');
        self::$obj->assertArgumentSame(1, 1, '');
        self::$obj->assertArgumentSame(true, true, '');
        self::$obj->assertArgumentSame([1, 2, 3], [1, 2, 3], '');
        self::$obj->assertArgumentSame($obj, $obj, '');

        self::assertTrue(true);
    }

    public function testAssertArgumentSameFailure(): void
    {
        $pairs = [
            ['1', '0'],
            [1, 0],
            [true, false],
            [[1, 2, 3], [2, 3, 1]],
            [new stdClass(), new stdClass()],
        ];
        foreach ($pairs as $n => [$value1, $value2]) {
            $error = 'error ' . $n;
            try {
                self::$obj->assertArgumentSame($value1, $value2, $error);
            } catch (InvalidArgumentException $e) {
                self::assertEquals('error ' . $n, $error);
            }
        }
    }

    public function testAssertArgumentNotSameSuccess(): void
    {
        self::$obj->assertArgumentNotSame('1', '0', '');
        self::$obj->assertArgumentNotSame(1, 0, '');
        self::$obj->assertArgumentNotSame(false, true, '');
        self::$obj->assertArgumentNotSame([1, 2, 3], [3, 1, 3], '');
        self::$obj->assertArgumentNotSame(new stdClass(), new stdClass(), '');

        self::assertTrue(true);
    }

    public function testAssertArgumentNotSameFailure(): void
    {
        $obj = new stdClass();
        $pairs = [
            ['1', '1'],
            [0, 0],
            [true, true],
            [[1, 2, 3], [1, 2, 3]],
            [$obj, $obj],
        ];
        foreach ($pairs as $n => [$value1, $value2]) {
            $error = 'error ' . $n;
            try {
                self::$obj->assertArgumentNotSame($value1, $value2, $error);
            } catch (InvalidArgumentException $e) {
                self::assertEquals('error ' . $n, $error);
            }
        }
    }

    public function testAssertArgumentEqualsSuccess(): void
    {
        self::$obj->assertArgumentEquals(1, 1, '');
        self::$obj->assertArgumentEquals('abc', 'abc', '');
        self::$obj->assertArgumentEquals(true, true, '');

        self::assertTrue(true);
    }

    public function testAssertArgumentEqualsFailure(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('error message');

        self::$obj->assertArgumentEquals(3, 4, 'error message');
    }

    public function testAssertArgumentNotEqualsSuccess(): void
    {
        self::$obj->assertArgumentNotEquals(1, 6, '');
        self::$obj->assertArgumentNotEquals('abc', 4, '');
        self::$obj->assertArgumentNotEquals(5.6, false, '');

        self::assertTrue(true);
    }

    public function testAssertArgumentNotEqualsFailure(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('error message');

        self::$obj->assertArgumentNotEquals('a', 'a', 'error message');
    }

    public function testAssertArgumentNotNullSuccess(): void
    {
        self::$obj->assertArgumentNotNull(0, 'error message');
        self::$obj->assertArgumentNotNull('', 'error message');
        self::$obj->assertArgumentNotNull([], 'error message');
        self::$obj->assertArgumentNotNull(new stdClass(), 'error message');

        self::assertTrue(true);
    }

    public function testAssertArgumentNotNullFailure(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('error message');

        self::$obj->assertArgumentNotNull(null, 'error message');
    }

    public function testAssertArgumentNullSuccess(): void
    {
        self::$obj->assertArgumentNull(null, 'error message');

        self::assertTrue(true);
    }

    public function testAssertArgumentNullFailure(): void
    {
        foreach ([0, '', [], new stdClass()] as $n => $value) {
            $error = 'error ' . $n;
            try {
                self::$obj->assertArgumentNull($value, $error);
            } catch (InvalidArgumentException $e) {
                self::assertEquals('error ' . $n, $error);
            }
        }
    }

    public function testAssertArgumentFalseSuccess(): void
    {
        self::$obj->assertArgumentFalse(false, 'error message');
        self::$obj->assertArgumentFalse(0, 'error message');
        self::$obj->assertArgumentFalse('', 'error message');
        self::$obj->assertArgumentFalse('0', 'error message');
        self::$obj->assertArgumentFalse([], 'error message');

        self::assertTrue(true);
    }

    public function testAssertArgumentFalseFailure(): void
    {
        foreach ([true, 1, '1', [null], new stdClass()] as $n => $value) {
            $error = 'error ' . $n;
            try {
                self::$obj->assertArgumentFalse($value, $error);
            } catch (InvalidArgumentException $e) {
                self::assertEquals('error ' . $n, $error);
            }
        }
    }

    public function testAssertArgumentTrueSuccess(): void
    {
        self::$obj->assertArgumentTrue(true, 'error message');
        self::$obj->assertArgumentTrue(1, 'error message');
        self::$obj->assertArgumentTrue('1', 'error message');
        self::$obj->assertArgumentTrue([null], 'error message');
        self::$obj->assertArgumentTrue(new stdClass(), 'error message');

        self::assertTrue(true);
    }

    public function testAssertArgumentTrueFailure(): void
    {
        foreach ([false, 0, '', '0', []] as $n => $value) {
            $error = 'error ' . $n;
            try {
                self::$obj->assertArgumentTrue($value, $error);
            } catch (InvalidArgumentException $e) {
                self::assertEquals('error ' . $n, $error);
            }
        }
    }

    public function testAssertArgumentNotEmptySuccess(): void
    {
        self::$obj->assertArgumentNotEmpty('some text', 'error message');

        self::assertTrue(true);
    }

    public function testAssertArgumentNotEmptyFailure(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('error message');

        self::$obj->assertArgumentNotEmpty('', 'error message');
    }

    public function testAssertArgumentLengthSuccess(): void
    {
        self::$obj->assertArgumentLength('some text', 3, 10, 'error message');
        self::$obj->assertArgumentLength('', 0, 10, 'error message');
        self::$obj->assertArgumentLength(null, 0, 10, 'error message');

        self::assertTrue(true);
    }

    public function testAssertArgumentLengthFailureMax(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('error message');

        self::$obj->assertArgumentLength('some text', 3, 5, 'error message');
    }

    public function testAssertArgumentLengthFailureMin(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('error message');

        self::$obj->assertArgumentLength("some text", 10, 15, 'error message');
    }

    public function testAssertArgumentMinLengthSuccess(): void
    {
        self::$obj->assertArgumentMinLength('some text', 5, 'error message');
        self::$obj->assertArgumentMinLength('', 0, 'error message');
        self::$obj->assertArgumentMinLength(null, 0, 'error message');

        self::assertTrue(true);
    }

    public function testAssertArgumentMinLengthFailure(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('error message');

        self::$obj->assertArgumentMinLength("some text", 10, 'error message');
    }

    public function testAssertArgumentMaxLengthSuccess(): void
    {
        self::$obj->assertArgumentMaxLength('some text', 9, 'error message');
        self::$obj->assertArgumentMaxLength('', 3, 'error message');
        self::$obj->assertArgumentMaxLength(null, 3, 'error message');

        self::assertTrue(true);
    }

    public function testAssertArgumentMaxLengthFailure(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('error message');

        self::$obj->assertArgumentMaxLength("some text", 5, 'error message');
    }

    public function testAssertArgumentRangeSuccess(): void
    {
        self::$obj->assertArgumentRange(10, 5, 15, 'error message');

        self::assertTrue(true);
    }

    public function testAssertArgumentRangeFailureMax(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('error message');

        self::$obj->assertArgumentRange(20, 5, 15, 'error message');
    }

    public function testAssertArgumentRangeFailureMin(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('error message');

        self::$obj->assertArgumentRange(3, 5, 15, 'error message');
    }

    public function testAssertArgumentMinSuccess(): void
    {
        self::$obj->assertArgumentMin(10, 5, 'error message');

        self::assertTrue(true);
    }

    public function testAssertArgumentMinFailure(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('error message');

        self::$obj->assertArgumentMin(3, 5, 'error message');
    }

    public function testAssertArgumentMaxSuccess(): void
    {
        self::$obj->assertArgumentMax(10, 15, 'error message');

        self::assertTrue(true);
    }

    public function testAssertArgumentMaxFailure(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('error message');

        self::$obj->assertArgumentMax(10, 5, 'error message');
    }

    public function testAssertArgumentPatternSuccess(): void
    {
        self::$obj->assertArgumentPatternMatch('abcdefg', '/^[a-z]+$/', 'error message');

        self::assertTrue(true);
    }

    public function testAssertArgumentPatternFailure(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('error message');

        self::$obj->assertArgumentPatternMatch('abcdefg', '/^[0-9]+$/', 'error message');
    }

    public function testAssertArgumentWithoutExceptionSuccess(): void
    {
        self::$obj->assertArgumentWithoutException(
            fn () => 'some code without exception goes here',
            'error message'
        );

        self::assertTrue(true);
    }

    public function testAssertArgumentWithoutExceptionFailure(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('error message');

        self::$obj->assertArgumentWithoutException(
            function (): void {
                throw new RuntimeException('error message');
            },
            'error message'
        );
    }

    public function testAssertArgumentNotInFutureSuccess(): void
    {
        self::$obj->assertArgumentNotInFuture(new DateTime(), 'error message 1');
        self::$obj->assertArgumentNotInFuture(new DateTime('-1 second'), 'error message 2');

        self::assertTrue(true);
    }

    public function testAssertArgumentNotInFutureFailure(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('error message');

        self::$obj->assertArgumentNotInFuture(new DateTime('+1 second'), 'error message');
    }

    public function testAssertArgumentNotInPastSuccess(): void
    {
        self::$obj->assertArgumentNotInPast(new DateTime(), 'error message 1');
        self::$obj->assertArgumentNotInPast(new DateTime('+1 second'), 'error message 2');

        self::assertTrue(true);
    }

    public function testAssertArgumentNotInPastFailure(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('error message');

        self::$obj->assertArgumentNotInPast(new DateTime('-1 second'), 'error message');
    }

    public function testAssertArgumentInstanceOfSuccess(): void
    {
        self::$obj->assertArgumentInstanceOf(new stdClass(), stdClass::class, 'error message 1');
        self::$obj->assertArgumentInstanceOf(new DateTime(), DateTime::class, 'error message 2');
        self::$obj->assertArgumentInstanceOf($this, TestCase::class, 'error message 3');

        self::assertTrue(true);
    }

    public function testAssertArgumentInstanceOfFailure(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('error message');

        self::$obj->assertArgumentInstanceOf(new DateTime(), stdClass::class, 'error message');
    }

    //endregion

    //region State Assertions

    public function testAssertStateFalseSuccess(): void
    {
        self::$obj->assertStateFalse(false, 'error message');
        self::$obj->assertStateFalse(0, 'error message');
        self::$obj->assertStateFalse('', 'error message');
        self::$obj->assertStateFalse('0', 'error message');
        self::$obj->assertStateFalse([], 'error message');

        self::assertTrue(true);
    }

    public function testAssertStateFalseFailure(): void
    {
        foreach ([true, 1, '1', [null], new stdClass()] as $n => $value) {
            $error = 'error ' . $n;
            try {
                self::$obj->assertStateFalse($value, $error);
            } catch (InvalidStateException $e) {
                self::assertEquals('error ' . $n, $error);
            }
        }
    }

    public function testAssertStateTrueSuccess(): void
    {
        self::$obj->assertStateTrue(true, 'error message');
        self::$obj->assertStateTrue(1, 'error message');
        self::$obj->assertStateTrue('1', 'error message');
        self::$obj->assertStateTrue([null], 'error message');
        self::$obj->assertStateTrue(new stdClass(), 'error message');

        self::assertTrue(true);
    }

    public function testAssertStateTrueFailure(): void
    {
        foreach ([false, 0, '', '0', []] as $n => $value) {
            $error = 'error ' . $n;
            try {
                self::$obj->assertStateTrue($value, $error);
            } catch (InvalidStateException $e) {
                self::assertEquals('error ' . $n, $error);
            }
        }
    }

    //endregion
}
