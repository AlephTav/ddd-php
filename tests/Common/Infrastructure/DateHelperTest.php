<?php

declare(strict_types=1);

namespace Tests\AlephTools\DDD\Common\Infrastructure;

use AlephTools\DDD\Common\Infrastructure\DateHelper;
use AlephTools\DDD\Common\Model\Exceptions\InvalidArgumentException;
use DateTime;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * @internal
 */
class DateHelperTest extends TestCase
{
    public function testParseDateFormats(): void
    {
        $defaultFormats = DateHelper::getAvailableDateFormats();
        DateHelper::setAvailableDateFormats($defaultFormats);

        self::assertSame($defaultFormats, DateHelper::getAvailableDateFormats());
    }

    /**
     * @depends testParseDateFormats
     * @dataProvider dateDataProvider
     */
    public function testParseDate(mixed $value, ?string $format, bool $expectException): void
    {
        if ($expectException) {
            $this->expectException(InvalidArgumentException::class);
        }

        $date = DateHelper::parse($value);
        if ($format) {
            self::assertInstanceOf(DateTime::class, $date);
            self::assertEquals($value, $date->format($format));
        } else {
            self::assertEquals($value, $date);
        }

        $date = DateHelper::parseImmutable($value);
        if ($format) {
            self::assertInstanceOf(DateTimeImmutable::class, $date);
            self::assertEquals($value, $date->format($format));
        } else {
            self::assertEquals($value, $date);
        }
    }

    public static function dateDataProvider(): array
    {
        $data = [];
        $now = new DateTime();
        foreach (DateHelper::getAvailableDateFormats() as $format) {
            $date = $now->format($format);

            $data[] = [
                $date,
                $format,
                false,
            ];
        }
        return array_merge($data, [
            [
                new DateTime(),
                null,
                false,
            ],
            [
                new DateTimeImmutable(),
                null,
                false,
            ],
            [
                null,
                null,
                false,
            ],
            [
                [],
                null,
                true,
            ],
            [
                new stdClass(),
                null,
                true,
            ],
        ]);
    }

    public function testParsePartlyDefinedDates(): void
    {
        $formats = [
            'Y-m-d',
            'Y-m-dP',
            'm/d/Y',
            'm/d/y',
        ];

        foreach ($formats as $format) {
            $date = DateHelper::parse(date($format));

            self::assertNotNull($date);
            self::assertEquals(0, $date->format('H'));
            self::assertEquals(0, $date->format('i'));
            self::assertEquals(0, $date->format('s'));
        }

        $date = DateHelper::parse(date('H:i:s'));

        self::assertNotNull($date);
        self::assertEquals(1970, $date->format('Y'));
        self::assertEquals(1, $date->format('m'));
        self::assertEquals(1, $date->format('d'));

        $date = DateHelper::parse(date('Y'));

        self::assertNotNull($date);
        self::assertEquals(1, $date->format('m'));
        self::assertEquals(1, $date->format('d'));
        self::assertEquals(0, $date->format('H'));
        self::assertEquals(0, $date->format('i'));
        self::assertEquals(0, $date->format('s'));
    }
}
