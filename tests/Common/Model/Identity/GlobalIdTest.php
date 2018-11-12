<?php

namespace AlephTools\DDD\Tests\Common\Model\Identity;

use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
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
        $identity = Uuid::uuid4()->toString();
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
        $identity = Uuid::uuid4()->toString();

        $this->assertTrue(GlobalId::canBeId($identity));
        $this->assertTrue(GlobalId::canBeId(Uuid::uuid4()));
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

    public function testParseUuid(): void
    {
        $identity = Uuid::uuid4();
        $id = new GlobalId($identity);

        $this->assertSame($identity, $id->identity);
    }

    public function testParseBytes(): void
    {
        $identity = Uuid::uuid4();
        $id = new GlobalId($identity->getBytes());

        $this->assertEquals($identity->toString(), $id->toString());
    }

    public function testParseString(): void
    {
        $identity = Uuid::uuid4()->toString();
        $id = new GlobalId($identity);

        $this->assertEquals($identity, $id->toString());
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
        $invalidIdentity = Uuid::uuid4()->toString() . '0';
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
                'Invalid UUID: 123',
                123
            ],
            [
                'Invalid UUID: ' . $invalidIdentity,
                $invalidIdentity
            ]
        ];
    }
}