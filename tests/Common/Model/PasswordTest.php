<?php

namespace AlephTools\DDD\Tests\Common\Model;

use AlephTools\DDD\Common\Model\Exceptions\InvalidArgumentException;
use AlephTools\DDD\Common\Model\Password;
use PHPUnit\Framework\TestCase;

class PasswordTest extends TestCase
{
    public function testCreationFromScalar(): void
    {
        $password = new Password('12345678');

        $this->assertSame('12345678', $password->password);
        $this->assertTrue(password_verify('12345678', $password->hash));
    }

    public function testCreationFromArray(): void
    {
        $password = new Password([
            'password' => 'abcdefgh',
            'hash' => '1234'
        ]);

        $this->assertSame('abcdefgh', $password->password);
        $this->assertSame('1234', $password->hash);

        $password = new Password([
            'hash' => 'abcd'
        ]);

        $this->assertSame('abcd', $password->hash);
    }

    /**
     * @dataProvider propertyDataProvider
     * @param array $properties
     * @param string $error
     */
    public function testValidation(array $properties, string $error): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($error);

        new Password($properties);
    }

    public function propertyDataProvider(): array
    {
        return [
            [
                [
                    'hash' => '',
                    'password' => '12345678'
                ],
                'Password hash must not be empty.'
            ],
            [
                [
                    'hash' => str_repeat('*', Password::HASH_MAX_LENGTH + 1),
                    'password' => '12345678'
                ],
                'Password hash must be at most ' . Password::HASH_MAX_LENGTH . ' characters.'
            ],
            [
                [
                    'hash' => 'abcd',
                    'password' => ''
                ],
                'Password must be at least ' . Password::PASSWORD_MIN_LENGTH .
                ' and at most ' . Password::PASSWORD_MAX_LENGTH . ' characters.'
            ],
            [
                [
                    'hash' => 'abcd',
                    'password' => str_repeat('*', Password::PASSWORD_MAX_LENGTH + 1)
                ],
                'Password must be at least ' . Password::PASSWORD_MIN_LENGTH .
                ' and at most ' . Password::PASSWORD_MAX_LENGTH . ' characters.'
            ],
        ];
    }

    public function testRandomPassword(): void
    {
        $password = Password::random();

        $this->assertEquals(Password::RANDOM_PASSWORD_LENGTH, strlen($password->password));
        $this->assertNotNull($password->hash);
        $this->assertTrue(password_verify($password->password, $password->hash));
    }

    public function testPasswordHash(): void
    {
        $password = new Password('12345678');

        $hash1 = $password->hash();
        $this->assertSame($hash1, $password->hash());

        $password = new Password([
            'password' => '12345678',
            'hash' => 'abcd'
        ]);

        $hash2 = $password->hash();
        $this->assertSame($hash2, $password->hash());
        $this->assertSame($hash1, $hash2);

        $password = new Password([
            'hash' => 'abcd'
        ]);

        $this->assertNotSame($hash1, $password->hash());
    }

    public function testPasswordCompareDifferentTypes(): void
    {
        $password = new Password('12345678');

        $this->assertFalse($password->equals(new \stdClass()));
        $this->assertFalse($password->equals('abc'));
        $this->assertFalse($password->equals([1, 2, 3]));
        $this->assertFalse($password->equals(123));
        $this->assertFalse($password->equals(null));
        $this->assertTrue($password->equals($password));
    }

    /**
     * @dataProvider compareDataProvider
     * @param array $properties1
     * @param array $properties2
     * @param bool $result
     */
    public function testPasswordCompare(array $properties1, array $properties2, bool $result): void
    {
        $password1 = new Password($properties1);
        $password2 = new Password($properties2);

        $this->assertEquals($result, $password1->equals($password2));
    }

    public function compareDataProvider(): array
    {
        return [
            [
                [
                    'password' => '12345678',
                    'hash' => 'abcde'
                ],
                [
                    'password' => '12345678',
                    'hash' => '12345'
                ],
                true
            ],
            [
                [
                    'password' => '123456789',
                    'hash' => 'abcde'
                ],
                [
                    'password' => '12345678',
                    'hash' => '12345'
                ],
                false
            ],
            [
                [
                    'hash' => password_hash('12345678', PASSWORD_DEFAULT)
                ],
                [
                    'password' => '12345678',
                    'hash' => 'abcd'
                ],
                true
            ],
            [
                [
                    'password' => '12345678',
                    'hash' => 'abcd'
                ],
                [
                    'hash' => password_hash('12345678', PASSWORD_DEFAULT)
                ],
                true
            ],
            [
                [
                    'hash' => '1234'
                ],
                [
                    'hash' => '1234'
                ],
                true
            ],
            [
                [
                    'hash' => '1234'
                ],
                [
                    'hash' => 'abcd'
                ],
                false
            ]
        ];
    }
}
