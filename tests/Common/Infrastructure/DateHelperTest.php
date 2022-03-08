<?php

declare(strict_types=1);

namespace AlephTools\DDD\Tests\Common\Infrastructure;

use stdClass;
use DateTime;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use AlephTools\DDD\Common\Infrastructure\DateHelper;
use AlephTools\DDD\Common\Model\Exceptions\InvalidArgumentException;

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
     * @param $value
     */
    public function testParseDate($value, ?string $format, bool $expectException): void
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

    public function dateDataProvider(): array
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

            $this->assertNotNull($date);
            $this->assertEquals(0, $date->format('H'));
            $this->assertEquals(0, $date->format('i'));
            $this->assertEquals(0, $date->format('s'));
        }

        $date = DateHelper::parse(date('H:i:s'));

        $this->assertNotNull($date);
        $this->assertEquals(1970, $date->format('Y'));
        $this->assertEquals(1, $date->format('m'));
        $this->assertEquals(1, $date->format('d'));

        $date = DateHelper::parse(date('Y'));

        $this->assertNotNull($date);
        $this->assertEquals(1, $date->format('m'));
        $this->assertEquals(1, $date->format('d'));
        $this->assertEquals(0, $date->format('H'));
        $this->assertEquals(0, $date->format('i'));
        $this->assertEquals(0, $date->format('s'));
    }
}
