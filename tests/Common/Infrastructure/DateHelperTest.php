<?php

namespace AlephTools\DDD\Tests\Common\Infrastructure;

use DateTime;
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
        $this->assertEquals($value, $format ? $date->format($format) : $date);
    }

    public function dateDataProvider(): array
    {
        $data = [];
        $now = new DateTime();
        foreach (DateHelper::getAvailableDateFormats() as $format) {
            $date = $now->format($format);
echo $date . PHP_EOL;
            $data[] = [
                $date,
                $format,
                false
            ];
        }
        $data = array_merge($data, [
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