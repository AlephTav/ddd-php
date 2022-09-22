<?php

declare(strict_types=1);

namespace Tests\AlephTools\DDD\Common\Application\Query;

use AlephTools\DDD\Common\Application\Query\AbstractQuery;
use AlephTools\DDD\Common\Model\Language;
use DateTime;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use stdClass;

class TestQueryTestObject extends AbstractQuery
{
    public function castToBoolean($value): bool
    {
        return $this->toBoolean($value);
    }

    public function castToDate($value): ?DateTime
    {
        return $this->toDate($value);
    }

    public function castToImmutableDate($value): ?DateTimeImmutable
    {
        return $this->toImmutableDate($value);
    }
}

/**
 * @internal
 */
class AbstractQueryTest extends TestCase
{
    public function testDefaultValues(): void
    {
        $query = new TestQueryTestObject(['a' => 1, 'b' => 2, 'c' => 3]);

        self::assertSame([
            'keyword' => null,
            'limit' => TestQueryTestObject::DEFAULT_PAGE_SIZE,
            'offset' => null,
            'page' => null,
            'sort' => null,
            'group' => null,
            'fields' => null,
            'timezone' => null,
            'language' => null,
            'withoutCount' => false,
            'withoutItems' => false,
            'offsetField' => null,
            'offsetValue' => null,
        ], $query->toArray());
    }

    /**
     * @dataProvider booleanDataProvider
     * @param mixed $value
     */
    public function testToBoolean($value, bool $result): void
    {
        $query = new TestQueryTestObject();
        self::assertEquals($result, $query->castToBoolean($value));
    }

    public function booleanDataProvider(): array
    {
        return [
            [' tRue', true],
            ['on ', true],
            [' 1 ', true],
            [1, true],
            [true, true],
            [false, false],
            [0, false],
            [' 0', false],
            ['oFf', false],
            ['  faLse', false],
            [[], false],
            [new stdClass(), false],
            [null, false],
        ];
    }

    public function testToDate(): void
    {
        $query = new TestQueryTestObject();
        $date = $query->castToDate('2018-10-01 21:30:00');

        self::assertInstanceOf(DateTime::class, $date);
        self::assertSame($date->format('Y-m-d H:i:s'), $date->format('Y-m-d H:i:s'));

        $date = $query->castToImmutableDate('2018-10-01 21:30:00');

        self::assertInstanceOf(DateTimeImmutable::class, $date);
        self::assertSame($date->format('Y-m-d H:i:s'), $date->format('Y-m-d H:i:s'));
    }

    /**
     * @dataProvider keywordDataProvider
     * @param mixed $value
     */
    public function testSetKeyword($value, ?string $keyword): void
    {
        $query = new TestQueryTestObject(['keyword' => $value]);
        self::assertSame($keyword, $query->keyword);
    }

    public function keywordDataProvider(): array
    {
        return [
            ['test', 'test'],
            [123, '123'],
            [12.5, '12.5'],
            [true, '1'],
            [false, ''],
            [[], null],
            [new stdClass(), null],
            [null, null],
        ];
    }

    /**
     * @dataProvider limitDataProvider
     * @param mixed $value
     */
    public function testSetLimit($value, int $limit): void
    {
        $query = new TestQueryTestObject(['limit' => $value]);
        self::assertSame($limit, $query->limit);
    }

    public function limitDataProvider(): array
    {
        return [
            [10, 10],
            ['5', 5],
            [3.5, 3],
            ['6.7', 6],
            [AbstractQuery::DEFAULT_PAGE_MAX_SIZE + 1, AbstractQuery::DEFAULT_PAGE_MAX_SIZE],
            ['test', AbstractQuery::DEFAULT_PAGE_SIZE],
            [[], AbstractQuery::DEFAULT_PAGE_SIZE],
            [new stdClass(), AbstractQuery::DEFAULT_PAGE_SIZE],
            [null, AbstractQuery::DEFAULT_PAGE_SIZE],
        ];
    }

    /**
     * @dataProvider offsetDataProvider
     * @param mixed $value
     */
    public function testSetOffset($value, ?int $offset): void
    {
        $query = new TestQueryTestObject(['offset' => $value]);
        self::assertSame($offset, $query->offset);
    }

    public function offsetDataProvider(): array
    {
        return [
            [10, 10],
            ['5', 5],
            [3.5, 3],
            ['6.7', 6],
            ['test', null],
            [[], null],
            [new stdClass(), null],
            [null, null],
        ];
    }

    /**
     * @dataProvider pageDataProvider
     * @param mixed $value
     */
    public function testSetPage($value, ?int $page): void
    {
        $query = new TestQueryTestObject(['page' => $value]);
        self::assertSame($page, $query->page);
    }

    public function pageDataProvider(): array
    {
        return [
            [10, 10],
            ['5', 5],
            [3.5, 3],
            ['6.7', 6],
            ['test', null],
            [[], null],
            [new stdClass(), null],
            [null, null],
        ];
    }

    /**
     * @dataProvider timezoneDataProvider
     * @param mixed $value
     */
    public function testSetTimezone($value, ?int $timezone): void
    {
        $query = new TestQueryTestObject(['timezone' => $value]);
        self::assertSame($timezone, $query->timezone);
    }

    public function timezoneDataProvider(): array
    {
        return [
            [10, 10],
            ['5', 5],
            [-3.5, -3],
            ['-6.7', -6],
            ['test', null],
            [[], null],
            [new stdClass(), null],
            [null, null],
        ];
    }

    /**
     * @dataProvider languageDataProvider
     */
    public function testSetLanguage(?string $value, ?Language $language): void
    {
        $query = new TestQueryTestObject(['language' => $value]);
        self::assertSame($language, $query->language);
    }

    public function languageDataProvider(): array
    {
        return [
            ['ru', Language::RU()],
            ['En', Language::EN()],
            [null, null],
        ];
    }

    /**
     * @dataProvider booleanDataProvider
     * @param mixed $value
     */
    public function testSetWithoutCount($value, bool $result): void
    {
        $query = new TestQueryTestObject(['withoutCount' => $value]);
        self::assertEquals($result, $query->withoutCount);
    }

    /**
     * @dataProvider booleanDataProvider
     * @param mixed $value
     */
    public function testSetWithoutItems($value, bool $result): void
    {
        $query = new TestQueryTestObject(['withoutItems' => $value]);
        self::assertEquals($result, $query->withoutItems);
    }

    /**
     * @dataProvider sortDataPRovider
     * @param mixed $value
     */
    public function testSetSort($value, ?array $sort): void
    {
        $query = new TestQueryTestObject(['sort' => $value]);
        self::assertSame($sort, $query->sort);
    }

    public function sortDataProvider(): array
    {
        return [
            ['column', ['column' => 'ASC']],
            ['+column', ['column' => 'ASC']],
            ['-column', ['column' => 'DESC']],
            ['+ c1, - c2,, c3,-c4', ['c1' => 'ASC', 'c2' => 'DESC', 'c3' => 'ASC', 'c4' => 'DESC']],
            [',,,', null],
            [123, null],
            [12.56, null],
            [true, null],
            [false, null],
            [[], null],
            [new stdClass(), null],
            [null, null],
        ];
    }

    /**
     * @dataProvider fieldsDataProvider
     * @param mixed $value
     */
    public function testSetFields($value, ?array $fields): void
    {
        $query = new TestQueryTestObject(['fields' => $value]);
        self::assertSame($fields, $query->fields);
    }

    /**
     * @dataProvider fieldsDataProvider
     * @param mixed $value
     */
    public function testSetGroup($value, ?array $fields): void
    {
        $query = new TestQueryTestObject(['group' => $value]);
        self::assertSame($fields, $query->group);
    }

    public function fieldsDataProvider(): array
    {
        return [
            ['field', ['field']],
            ['  f1, f2,, f3', ['f1', 'f2', 'f3']],
            [',,,', null],
            [',,,', null],
            [123, null],
            [12.56, null],
            [true, null],
            [false, null],
            [[], null],
            [new stdClass(), null],
            [null, null],
        ];
    }

    public function testContainsField(): void
    {
        $query = new TestQueryTestObject(['fields' => 'f1,f2,f3']);

        self::assertTrue($query->containsField('f1'));
        self::assertTrue($query->containsField('f2'));
        self::assertTrue($query->containsField('f3'));
        self::assertFalse($query->containsField('f4'));

        $query = new TestQueryTestObject();
        self::assertTrue($query->containsField('f1'));
        self::assertTrue($query->containsField('f4'));
    }

    public function testContainingSortField(): void
    {
        $query = new TestQueryTestObject(['sort' => '+f1,-f2, f3']);

        self::assertTrue($query->containsSortField('f1'));
        self::assertTrue($query->containsSortField('f2'));
        self::assertTrue($query->containsSortField('f3'));
        self::assertFalse($query->containsSortField('f5'));

        $query = new TestQueryTestObject();
        self::assertFalse($query->containsSortField('f1'));
        self::assertFalse($query->containsSortField('f4'));
    }

    public function testUseField(): void
    {
        $query = new TestQueryTestObject([
            'sort' => '+f1,-f2',
            'fields' => 'f1,f3',
        ]);

        self::assertTrue($query->usesField('f1'));
        self::assertTrue($query->usesField('f2'));
        self::assertTrue($query->usesField('f3'));
        self::assertFalse($query->usesField('f5'));

        $query = new TestQueryTestObject();
        self::assertTrue($query->usesField('f1'));
        self::assertTrue($query->usesField('f4'));
    }

    public function testSetPageSize(): void
    {
        AbstractQuery::setPageMaxSize(10000);
        $query1 = new TestQueryTestObject(['limit' => 50000]);
        $query2 = new TestQueryTestObject(['limit' => 35000]);

        self::assertSame(AbstractQuery::getPageMaxSize(), $query1->limit);
        self::assertSame(AbstractQuery::getPageMaxSize(), $query2->limit);
    }
}
