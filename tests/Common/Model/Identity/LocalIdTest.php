<?php

namespace AlephTools\DDD\Tests\Common\Model\Identity;

use PHPUnit\Framework\TestCase;
use AlephTools\DDD\Common\Model\Exceptions\InvalidArgumentException;
use AlephTools\DDD\Common\Model\Identity\LocalId;

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

        $this->assertSame('12345', $id->toString());
        $this->assertSame('12345', (string)$id);
    }

    public function testCanBeId(): void
    {
        $this->assertTrue(LocalId::canBeId('54321'));
        $this->assertTrue(LocalId::canBeId(123));

        $this->assertFalse(LocalId::canBeId([]));
        $this->assertFalse(LocalId::canBeId(new \stdClass()));
        $this->assertFalse(LocalId::canBeId(null));
    }

    public function testParseLocalId(): void
    {
        $id = new LocalId(new LocalId(123));

        $this->assertSame(123, $id->identity);
    }

    public function testParseString(): void
    {
        $id = new LocalId('123');

        $this->assertSame(123, $id->identity);
    }

    public function testParseFloat(): void
    {
        $id = new LocalId(123.6);

        $this->assertSame(123, $id->identity);
    }

    public function testParseInteger(): void
    {
        $id = new LocalId(123);

        $this->assertSame(123, $id->identity);
    }

    /**
     * @dataProvider invalidIdentityProvider
     * @param string $error
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
                []
            ],
            [
                'Invalid identifier: identity must be an integer.',
                new \stdClass()
            ],
            [
                'Invalid identifier: foo',
                'foo'
            ]
        ];
    }
}