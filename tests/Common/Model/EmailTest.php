<?php

declare(strict_types=1);

namespace Tests\AlephTools\DDD\Common\Model;

use AlephTools\DDD\Common\Model\Email;
use AlephTools\DDD\Common\Model\Exceptions\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class EmailTest extends TestCase
{
    public function testCreationWithNoArguments(): void
    {
        $email = new Email();

        self::assertSame('', $email->address);
    }

    public function testCreationWithArguments(): void
    {
        $email1 = new Email('my@email1.com');
        $email2 = new Email(['address' => 'my@email2.com']);

        self::assertSame('my@email1.com', $email1->address);
        self::assertSame('my@email2.com', $email2->address);
    }

    public function testAddressTooLong(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Email address must be at most ' . Email::ADDRESS_MAX_LENGTH . ' characters.');

        new Email(str_repeat('*', Email::ADDRESS_MAX_LENGTH + 1));
    }

    public function testInvalidAddressFormat(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid email format.');

        new Email('invalid_email.com');
    }

    /**
     * @dataProvider emailDataProvider
     * @param mixed $email
     */
    public function testEmailSanitization($email, string $sanitizedEmail): void
    {
        $email = new Email($email);

        self::assertSame($sanitizedEmail, $email->address);
    }

    public function emailDataProvider(): array
    {
        return [
            [null, ''],
            ['', ''],
            ['some@email.com', 'some@email.com'],
            ['  abc@abc.com', 'abc@abc.com'],
            ['abc@abc.com    ', 'abc@abc.com'],
            [' abc@abc.com ', 'abc@abc.com'],
        ];
    }

    public function testToScalar(): void
    {
        $email = new Email('test@gmail.com');

        self::assertSame('test@gmail.com', $email->toString());
        self::assertSame('test@gmail.com', $email->toScalar());
    }
}
