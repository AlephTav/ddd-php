<?php

declare(strict_types=1);

namespace AlephTools\DDD\Tests\Common\Infrastructure;

use AlephTools\DDD\Common\Infrastructure\DateHelper;
use AlephTools\DDD\Common\Model\Exceptions\InvalidArgumentException;
use DateTime;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use stdClass;

class DateHelperTestObject extends DateHelper
{
    protected static function dateTimeHasCreateFromImmutable(): bool
    {
        return false;
    }
}

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
        $data = array_merge($data, [
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

        return $data;
    }

    public function testParseDateTimeImmutableForOldPhpVersions(): void
    {
        $date = new DateTimeImmutable();

        $parsedDate = DateHelperTestObject::parse($date);

        self::assertSame($date->format('Y-m-d H:i:s.u'), $parsedDate->format('Y-m-d H:i:s.u'));
    }
}
