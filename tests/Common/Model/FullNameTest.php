<?php

namespace AlephTools\DDD\Tests\Common\Model;

use PHPUnit\Framework\TestCase;
use AlephTools\DDD\Common\Model\Exceptions\InvalidArgumentException;
use AlephTools\DDD\Common\Model\FullName;

class FullNameTest extends TestCase
{
    public function testCreationWithEmptyArguments(): void
    {
        $name = new FullName();

        $this->assertSame('', $name->firstName);
        $this->assertSame('', $name->lastName);
    }

    public function testCreationWithArguments(): void
    {
        $name = new FullName('first', 'last');

        $this->assertSame('first', $name->firstName);
        $this->assertSame('last', $name->lastName);

        $name = new FullName(['firstName' => 'first', 'lastName' => 'last']);

        $this->assertSame('first', $name->firstName);
        $this->assertSame('last', $name->lastName);
    }

    public function creationWithMixedArguments(): void
    {
        $name = new FullName('first');

        $this->assertSame('first', $name->firstName);
        $this->assertSame('', $name->lastName);

        $name = new FullName(null, 'last');

        $this->assertSame('', $name->firstName);
        $this->assertSame('last', $name->lastName);
    }

    public function testFirstNameIsTooLong(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('First name must be at most ' . FullName::FIRST_NAME_MAX_LENGTH . ' characters.');

        new FullName( str_repeat('*', FullName::FIRST_NAME_MAX_LENGTH + 1), 'last');
    }

    public function testLastNameIsTooLong(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Last name must be at most ' . FullName::LAST_NAME_MAX_LENGTH . ' characters.');

        new FullName('first', str_repeat('*', FullName::LAST_NAME_MAX_LENGTH + 1));
    }

    public function testSanitizationOfSpaces(): void
    {
        $name = new FullName('  first', 'last  ');

        $this->assertSame('first', $name->firstName);
        $this->assertSame('last', $name->lastName);
    }

    public function testSanitizationOfControlCharacters(): void
    {
        $name = new FullName("\n first ", " \r last \t");

        $this->assertSame('first', $name->firstName);
        $this->assertSame('last', $name->lastName);
    }

    /**
     * @dataProvider nameFormattingDataProvider
     * @param string $firstName
     * @param string $lastName
     * @param string $formattedName
     */
    public function testFullNameFormatting(string $firstName, string $lastName, string $formattedName): void
    {
        $name = new FullName($firstName, $lastName);

        $this->assertSame($formattedName, $name->asFormattedName());
    }

    public function nameFormattingDataProvider(): array
    {
        return [
            [
                ' first ',
                ' last ',
                'first last'
            ],
            [
                ' first',
                '',
                'first'
            ],
            [
                '',
                'last  ',
                'last'
            ],
            [
                '',
                '',
                ''
            ]
        ];
    }

    /**
     * @dataProvider nameParsingDataProvider
     * @param string $firstName
     * @param string $lastName
     * @param null|string $fullName
     */
    public function testParseName(?string $fullName, string $firstName, string $lastName): void
    {
        $name = FullName::parse($fullName);

        $this->assertSame($firstName, $name->firstName);
        $this->assertSame($lastName, $name->lastName);
    }

    public function nameParsingDataProvider(): array
    {
        return [
            [
                'first last',
                'first',
                'last'
            ],
            [
                ' first last  ',
                'first',
                'last'
            ],
            [
                'first middle last',
                'first',
                'middle last'
            ],
            [
                'first  ',
                'first',
                ''
            ],
            [
                ' last',
                'last',
                ''
            ],
            [
                '',
                '',
                ''
            ],
            [
                null,
                '',
                ''
            ],
        ];
    }
}