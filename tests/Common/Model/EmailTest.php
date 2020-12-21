<?php

namespace AlephTools\DDD\Tests\Common\Model;

use PHPUnit\Framework\TestCase;
use AlephTools\DDD\Common\Model\Email;
use AlephTools\DDD\Common\Model\Exceptions\InvalidArgumentException;

class EmailTest extends TestCase
{
    public function testCreationWithNoArguments(): void
    {
        $email = new Email();

        $this->assertSame('', $email->address);
    }

    public function testCreationWithArguments(): void
    {
        $email1 = new Email('my@email1.com');
        $email2 = new Email(['address' => 'my@email2.com']);

        $this->assertSame('my@email1.com', $email1->address);
        $this->assertSame('my@email2.com', $email2->address);
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
     * @param string $sanitizedEmail
     */
    public function testEmailSanitization($email, string $sanitizedEmail): void
    {
        $email = new Email($email);

        $this->assertSame($sanitizedEmail, $email->address);
    }

    public function emailDataProvider(): array
    {
        return [
            [null, ''],
            ['', ''],
            ['some@email.com', 'some@email.com'],
            ['  abc@abc.com', 'abc@abc.com'],
            ['abc@abc.com    ', 'abc@abc.com'],
            [' abc@abc.com ', 'abc@abc.com']
        ];
    }

    public function testToScalar(): void
    {
        $email = new Email('test@gmail.com');

        $this->assertSame('test@gmail.com', $email->toString());
        $this->assertSame('test@gmail.com', $email->toScalar());
    }
}
