<?php

namespace AlephTools\DDD\Tests\Common\Infrastructure;

use DateTime;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use AlephTools\DDD\Common\Infrastructure\DateHelper;
use AlephTools\DDD\Common\Model\Exceptions\InvalidArgumentException;

class DateHelperTest extends TestCase
{
    /**
     * @dataProvider dateDataProvider
     * @param $value
     * @param string|null $format
     * @param bool $expectException
     */
    public function testParseDate($value, ?string $format, bool $expectException): void
    {
        if ($expectException) {
            $this->expectException(InvalidArgumentException::class);
        }

        $date = DateHelper::parse($value);
        if ($format) {
            $this->assertInstanceOf(DateTime::class, $date);
            $this->assertEquals($value, $date->format($format));
        } else {
            $this->assertEquals($value, $date);
        }

        $date = DateHelper::parseImmutable($value);
        if ($format) {
            $this->assertInstanceOf(DateTimeImmutable::class, $date);
            $this->assertEquals($value, $date->format($format));
        } else {
            $this->assertEquals($value, $date);
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
                false
            ];
        }
        $data = array_merge($data, [
            [
                new DateTime(),
                null,
                false
            ],
            [
                new DateTimeImmutable(),
                null,
                false
            ],
            [
                null,
                null,
                false,
            ],
            [
                [],
                null,
                true
            ],
            [
                new \stdClass(),
                null,
                true
            ]
        ]);

        return $data;
    }
}
