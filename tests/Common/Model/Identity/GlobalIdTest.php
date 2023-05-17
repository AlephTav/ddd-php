<?php

declare(strict_types=1);

namespace Tests\AlephTools\DDD\Common\Model\Identity;

use AlephTools\DDD\Common\Model\Exceptions\InvalidArgumentException;
use AlephTools\DDD\Common\Model\Identity\GlobalId;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * @internal
 */
class GlobalIdTest extends TestCase
{
    public function testNullId(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Identity of GlobalId must not be null.');

        new GlobalId(null);
    }

    public function testToString(): void
    {
        $identity = 'bd2cbad1-6ccf-48e3-bb92-bc9961bc011e';
        $id = new GlobalId($identity);

        self::assertSame($identity, $id->toString());
        self::assertSame($identity, (string)$id);
    }

    public function testNewId(): void
    {
        $id = GlobalId::create();

        self::assertInstanceOf(GlobalId::class, $id);
        self::assertTrue(GlobalId::canBeId($id->identity));
    }

    public function testCanBeId(): void
    {
        $identity = 'b5e2cf01-8bb6-4fcd-ad88-0efb611195da';

        self::assertTrue(GlobalId::canBeId($identity));
        self::assertTrue(GlobalId::canBeId(GlobalId::create()));

        self::assertFalse(GlobalId::canBeId($identity . '0'));
        self::assertFalse(GlobalId::canBeId('123'));
        self::assertFalse(GlobalId::canBeId([]));
        self::assertFalse(GlobalId::canBeId(new stdClass()));
        self::assertFalse(GlobalId::canBeId(null));
    }

    public function testParseGlobalId(): void
    {
        $id = GlobalId::create();
        $copy = new GlobalId($id);

        self::assertSame($id->identity, $copy->identity);
    }

    /**
     * @dataProvider invalidIdentityProvider
     * @param string $error
     * @param mixed $identity
     */
    public function testParseInvalidValue(string $error, mixed $identity): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($error);

        new GlobalId($identity);
    }

    public static function invalidIdentityProvider(): array
    {
        $invalidIdentity = 'b11c9be1-b619-4ef5-be1b-a1cd9ef265b7' . '0';
        return [
            [
                'Invalid UUID: identity must be a string.',
                [],
            ],
            [
                'Invalid UUID: identity must be a string.',
                new stdClass(),
            ],
            [
                'Invalid UUID: identity must be a string.',
                123,
            ],
            [
                'Invalid UUID: ' . $invalidIdentity,
                $invalidIdentity,
            ],
        ];
    }

    public function testToScalar(): void
    {
        $id = GlobalId::create();

        self::assertSame($id->identity, $id->toString());
        self::assertSame($id->identity, $id->toScalar());
    }

    public function testFrom(): void
    {
        $id = GlobalId::create();

        self::assertSame($id->identity, GlobalId::from($id->identity)->identity);
    }

    public function testFromNullable(): void
    {
        $id = GlobalId::create();

        self::assertSame($id->identity, GlobalId::fromNullable($id->identity)?->identity);
        self::assertNull(GlobalId::fromNullable(null));
    }
}
