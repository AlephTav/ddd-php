<?php

declare(strict_types=1);

namespace AlephTools\DDD\Tests\Common\Model\Identity;

use AlephTools\DDD\Common\Model\Exceptions\InvalidArgumentException;
use AlephTools\DDD\Common\Model\Identity\LocalId;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * @internal
 */
class LocalIdTest extends TestCase
{
    public function testNullId(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Identity of LocalId must not be null.');

        new LocalId(null);
    }

    public function testToString(): void
    {
        $id = new LocalId(12345);

        self::assertSame('12345', $id->toString());
        self::assertSame('12345', (string)$id);
    }

    public function testCanBeId(): void
    {
        self::assertTrue(LocalId::canBeId('54321'));
        self::assertTrue(LocalId::canBeId(123));

        self::assertFalse(LocalId::canBeId([]));
        self::assertFalse(LocalId::canBeId(new stdClass()));
        self::assertFalse(LocalId::canBeId(null));
    }

    public function testParseLocalId(): void
    {
        $id = new LocalId(new LocalId(123));

        self::assertSame(123, $id->identity);
    }

    public function testParseString(): void
    {
        $id = new LocalId('123');

        self::assertSame(123, $id->identity);
    }

    public function testParseFloat(): void
    {
        $id = new LocalId(123.6);

        self::assertSame(123, $id->identity);
    }

    public function testParseInteger(): void
    {
        $id = new LocalId(123);

        self::assertSame(123, $id->identity);
    }

    /**
     * @dataProvider invalidIdentityProvider
     * @param mixed $identity
     */
    public function testParseInvalidValue(string $error, $identity): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($error);

        new LocalId($identity);
    }

    public function invalidIdentityProvider(): array
    {
        return [
            [
                'Invalid identifier: identity must be an integer.',
                [],
            ],
            [
                'Invalid identifier: identity must be an integer.',
                new stdClass(),
            ],
            [
                'Invalid identifier: foo',
                'foo',
            ],
        ];
    }

    public function testToScalar(): void
    {
        $id = new LocalId(123);

        self::assertSame('123', $id->toString());
        self::assertSame(123, $id->toScalar());
    }
}
