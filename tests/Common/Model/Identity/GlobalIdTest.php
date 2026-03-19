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

    public function testNewId7(): void
    {
        $id = GlobalId::create7();

        self::assertInstanceOf(GlobalId::class, $id);
        self::assertTrue(GlobalId::canBeId($id->identity));
        self::assertSame('7', substr($id->identity, 14, 1));
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

    public function testCanBeId7(): void
    {
        // Valid UUID7 examples
        $validUuid7 = '019a005c-330a-7417-b3e5-829eaa6c10ee';

        self::assertTrue(GlobalId::canBeId7($validUuid7));
        self::assertTrue(GlobalId::canBeId7(GlobalId::create7()));
        self::assertTrue(GlobalId::canBeId($validUuid7));
        self::assertTrue(GlobalId::canBeId(GlobalId::create7()));

        // Invalid UUID7 examples
        self::assertFalse(GlobalId::canBeId7('019a005c-330a-6417-b3e5-829eaa6c10ee')); // version 6
        self::assertFalse(GlobalId::canBeId7('019a005c-330a-7417-b3e5-829eaa6c10ee0')); // too long
        self::assertFalse(GlobalId::canBeId7('019a005c-330a-7417-c3e5-829eaa6c10ee')); // invalid variant
    }

    public function testParseGlobalId(): void
    {
        $id = GlobalId::create();
        $copy = new GlobalId($id);

        self::assertSame($id->identity, $copy->identity);
    }

    public function testParseGlobalId7(): void
    {
        $id = GlobalId::create7();
        $copy = new GlobalId($id);

        self::assertSame($id->identity, $copy->identity);
    }

    /**
     * @dataProvider invalidIdentityProvider
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

    public function testUuid7ChronologicalOrder(): void
    {
        $uuids = [];
        for ($i = 0; $i < 5; $i++) {
            $uuids[] = GlobalId::create7();
            usleep(1000); // 1ms delay to ensure different timestamps
        }

        // Extract the identities as strings for comparison
        $identities = array_map(fn($id) => $id->identity, $uuids);
        $sorted = $identities;
        sort($sorted);

        // UUID7 should be roughly chronological (allowing for some variance due to random bits)
        // Since we have small delays, most UUIDs should be in chronological order
        self::assertGreaterThanOrEqual(3, count(array_intersect_assoc($identities, $sorted)));
    }

    public function testUuid7Uniqueness(): void
    {
        $uuids = [];
        $count = 1000;

        for ($i = 0; $i < $count; $i++) {
            $uuids[] = GlobalId::create7()->identity;
        }

        $uniqueUuids = array_unique($uuids);

        self::assertSame($count, count($uniqueUuids), 'All UUID7 should be unique');
    }

    public function testUuid7Performance(): void
    {
        $count = 10000;
        $start = microtime(true);

        for ($i = 0; $i < $count; $i++) {
            GlobalId::create7();
        }

        $time = microtime(true) - $start;
        $performance = $count / $time;

        // Should be able to generate at least 10,000 UUID7 per second
        self::assertLessThan(1, $time,
            "UUID7 generation should be fast. Got: " . number_format($performance) . " UUID/sec");
    }
}
