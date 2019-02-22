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

    public function testMiddleNameIsTooLong(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Middle name must be at most ' . FullName::LAST_NAME_MAX_LENGTH . ' characters.');

        new FullName(
            'first',
            'last',
            str_repeat('*', FullName::MIDDLE_NAME_MAX_LENGTH + 1)
        );
    }

    public function testSanitizationOfSpaces(): void
    {
        $name = new FullName('  first', 'last  ', ' middle  ');

        $this->assertSame('first', $name->firstName);
        $this->assertSame('last', $name->lastName);
        $this->assertSame('middle', $name->middleName);
    }

    public function testSanitizationOfControlCharacters(): void
    {
        $name = new FullName("\n first ", " \r last \t", "\rmiddle\n\n\t");

        $this->assertSame('first', $name->firstName);
        $this->assertSame('last', $name->lastName);
        $this->assertSame('middle', $name->middleName);
    }

    /**
     * @dataProvider nameFormattingDataProvider
     * @param string $firstName
     * @param string $lastName
     * @param string $middleName
     * @param string $format
     * @param string $formattedName
     */
    public function testFullNameFormatting(
        string $firstName,
        string $lastName,
        string $middleName,
        string $format,
        string $formattedName
    ): void
    {
        $name = new FullName($firstName, $lastName, $middleName);

        $this->assertSame($formattedName, $name->asFormattedName($format));
    }

    public function nameFormattingDataProvider(): array
    {
        return [
            [
                ' first ',
                ' last ',
                ' ',
                'f',
                'first'
            ],
            [
                ' first ',
                ' last ',
                ' ',
                'l',
                'last'
            ],
            [
                ' first ',
                ' last ',
                ' ',
                'm',
                ''
            ],
            [
                ' first ',
                ' last ',
                ' middle ',
                'm',
                'middle'
            ],
            [
                ' first',
                ' last',
                ' middle ',
                'fl',
                'first last'
            ],
            [
                ' first',
                ' last',
                ' middle ',
                'lf',
                'last first'
            ],
            [
                'first ',
                ' last',
                ' middle ',
                'fm',
                'first middle'
            ],
            [
                'first ',
                ' last',
                ' middle ',
                'mf',
                'middle first'
            ],
            [
                'first ',
                ' last',
                ' middle ',
                'lm',
                'last middle'
            ],
            [
                'first ',
                ' last',
                ' middle ',
                'ml',
                'middle last'
            ],
            [
                'first ',
                ' last',
                ' middle ',
                'flm',
                'first last middle'
            ],
            [
                'first ',
                ' last',
                ' middle ',
                'lfm',
                'last first middle'
            ],
            [
                'first ',
                ' last',
                ' middle ',
                'mfl',
                'middle first last'
            ],
            [
                'first ',
                ' last',
                ' middle ',
                'fml',
                'first middle last'
            ],
            [
                'first ',
                ' last',
                ' middle ',
                'lmf',
                'last middle first'
            ],
            [
                ' ',
                ' ',
                '  ',
                'flm',
                ''
            ],
            [
                '',
                '',
                '',
                'fml',
                ''
            ]
        ];
    }

    /**
     * @dataProvider nameParsingDataProvider
     * @param null|string $fullName
     * @param string $firstName
     * @param string $lastName
     * @param string $middleName
     * @param string $format
     */
    public function testParseName(
        ?string $fullName,
        string $firstName,
        string $lastName,
        string $middleName,
        string $format
    ): void
    {
        $name = FullName::parse($fullName, $format);

        $this->assertSame($firstName, $name->firstName);
        $this->assertSame($lastName, $name->lastName);
        $this->assertSame($middleName, $name->middleName);
    }

    public function nameParsingDataProvider(): array
    {
        return [
            [
                '  first last middle   ',
                'first',
                'last',
                'middle',
                'flm'
            ],
            [
                'last first middle   ',
                'first',
                'last',
                'middle',
                'lfm'
            ],
            [
                '  first middle last',
                'first',
                'last',
                'middle',
                'fml'
            ],
            [
                'last middle first',
                'first',
                'last',
                'middle',
                'lmf'
            ],
            [
                'middle first last  ',
                'first',
                'last',
                'middle',
                'mfl'
            ],
            [
                '  middle last first',
                'first',
                'last',
                'middle',
                'mlf'
            ],
            [
                '  first last middle',
                'first',
                'last',
                '',
                'fl'
            ],
            [
                '  last first middle',
                'first',
                'last',
                '',
                'lf'
            ],
            [
                '  first last middle',
                'first',
                'last',
                '',
                'fl'
            ],
            [
                '',
                '',
                '',
                '',
                'flm',
                ''
            ],
            [
                null,
                '',
                '',
                '',
                '',
                ''
            ],
        ];
    }
}
