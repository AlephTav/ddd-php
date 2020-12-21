<?php

namespace AlephTools\DDD\Tests\Common\Model\Identity;

use PHPUnit\Framework\TestCase;
use AlephTools\DDD\Common\Model\Exceptions\InvalidArgumentException;
use AlephTools\DDD\Common\Model\Identity\GlobalId;

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

        $this->assertSame($identity, $id->toString());
        $this->assertSame($identity, (string)$id);
    }

    public function testNewId(): void
    {
        $id = GlobalId::create();

        $this->assertInstanceOf(GlobalId::class, $id);
        $this->assertTrue(GlobalId::canBeId($id->identity));
    }

    public function testCanBeId(): void
    {
        $identity = 'b5e2cf01-8bb6-4fcd-ad88-0efb611195da';

        $this->assertTrue(GlobalId::canBeId($identity));
        $this->assertTrue(GlobalId::canBeId(GlobalId::create()));

        $this->assertFalse(GlobalId::canBeId($identity . '0'));
        $this->assertFalse(GlobalId::canBeId('123'));
        $this->assertFalse(GlobalId::canBeId([]));
        $this->assertFalse(GlobalId::canBeId(new \stdClass()));
        $this->assertFalse(GlobalId::canBeId(null));
    }

    public function testParseGlobalId(): void
    {
        $id = GlobalId::create();
        $copy = new GlobalId($id);

        $this->assertSame($id->identity, $copy->identity);
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

        new GlobalId($identity);
    }

    public function invalidIdentityProvider(): array
    {
        $invalidIdentity = 'b11c9be1-b619-4ef5-be1b-a1cd9ef265b7' . '0';
        return [
            [
                'Invalid UUID: identity must be a string.',
                []
            ],
            [
                'Invalid UUID: identity must be a string.',
                new \stdClass()
            ],
            [
                'Invalid UUID: identity must be a string.',
                123
            ],
            [
                'Invalid UUID: ' . $invalidIdentity,
                $invalidIdentity
            ]
        ];
    }

    public function testToScalar(): void
    {
        $id = GlobalId::create();

        $this->assertSame($id->identity, $id->toString());
        $this->assertSame($id->identity, $id->toScalar());
    }
}