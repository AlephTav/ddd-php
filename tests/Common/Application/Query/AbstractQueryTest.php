<?php

namespace AlephTools\DDD\Tests\Common\Application\Query;

use DateTime;
use DateTimeImmutable;
use AlephTools\DDD\Common\Application\Query\AbstractQuery;
use PHPUnit\Framework\TestCase;

class TestQueryTestObject extends AbstractQuery
{
    public function castToBoolean($value): bool
    {
        return $this->toBoolean($value);
    }

    public function castToDate($value): DateTime
    {
        return $this->toDate($value);
    }

    public function castToImmutableDate($value): DateTimeImmutable
    {
        return $this->toImmutableDate($value);
    }
}

class AbstractQueryTest extends TestCase
{
    public function testDefaultValues(): void
    {
        $query = new TestQueryTestObject(['a' => 1, 'b' => 2, 'c' => 3]);

        $this->assertSame([
            'keyword' => null,
            'limit' => TestQueryTestObject::DEFAULT_PAGE_SIZE,
            'offset' => null,
            'page' => null,
            'sort' => null,
            'fields' => null,
            'withoutCount' => false,
            'withoutItems' => false
        ], $query->toArray());
    }

    /**
     * @dataProvider booleanDataProvider
     * @param mixed $value
     * @param bool $result
     */
    public function testToBoolean($value, bool $result): void
    {
        $query = new TestQueryTestObject();
        $this->assertEquals($result, $query->castToBoolean($value));
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
            [new \stdClass(), false],
            [null, false]
        ];
    }

    public function testToDate(): void
    {
        $query = new TestQueryTestObject();
        $date = $query->castToDate('2018-10-01 21:30:00');

        $this->assertInstanceOf(DateTime::class, $date);
        $this->assertSame($date->format('Y-m-d H:i:s'), $date->format('Y-m-d H:i:s'));

        $date = $query->castToImmutableDate('2018-10-01 21:30:00');

        $this->assertInstanceOf(DateTimeImmutable::class, $date);
        $this->assertSame($date->format('Y-m-d H:i:s'), $date->format('Y-m-d H:i:s'));
    }

    /**
     * @dataProvider keywordDataProvider
     * @param mixed $value
     * @param null|string $keyword
     */
    public function testSetKeyword($value, ?string $keyword): void
    {
        $query = new TestQueryTestObject(['keyword' => $value]);
        $this->assertSame($keyword, $query->keyword);
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
            [new \stdClass(), null],
            [null, null]
        ];
    }

    /**
     * @dataProvider limitDataProvider
     * @param mixed $value
     * @param int $limit
     */
    public function testSetLimit($value, int $limit): void
    {
        $query = new TestQueryTestObject(['limit' => $value]);
        $this->assertSame($limit, $query->limit);
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
            [new \stdClass(), AbstractQuery::DEFAULT_PAGE_SIZE],
            [null, AbstractQuery::DEFAULT_PAGE_SIZE]
        ];
    }

    /**
     * @dataProvider offsetDataProvider
     * @param $value
     * @param int|null $offset
     */
    public function testSetOffset($value, ?int $offset): void
    {
        $query = new TestQueryTestObject(['offset' => $value]);
        $this->assertSame($offset, $query->offset);
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
            [new \stdClass(), null],
            [null, null]
        ];
    }

    /**
     * @dataProvider pageDataProvider
     * @param $value
     * @param int|null $page
     */
    public function testSetPage($value, ?int $page): void
    {
        $query = new TestQueryTestObject(['page' => $value]);
        $this->assertSame($page, $query->page);
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
            [new \stdClass(), null],
            [null, null]
        ];
    }

    /**
     * @dataProvider booleanDataProvider
     * @param mixed $value
     * @param bool $result
     */
    public function testSetWithoutCount($value, bool $result): void
    {
        $query = new TestQueryTestObject(['withoutCount' => $value]);
        $this->assertEquals($result, $query->withoutCount);
    }

    /**
     * @dataProvider booleanDataProvider
     * @param mixed $value
     * @param bool $result
     */
    public function testSetWithoutItems($value, bool $result): void
    {
        $query = new TestQueryTestObject(['withoutItems' => $value]);
        $this->assertEquals($result, $query->withoutItems);
    }

    /**
     * @dataProvider sortDataPRovider
     * @param mixed $value
     * @param array|null $sort
     */
    public function testSetSort($value, ?array $sort): void
    {
        $query = new TestQueryTestObject(['sort' => $value]);
        $this->assertSame($sort, $query->sort);
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
            [new \stdClass(), null],
            [null, null]
        ];
    }

    /**
     * @dataProvider fieldsDataProvider
     * @param mixed $value
     * @param array|null $fields
     */
    public function testSetFields($value, ?array $fields): void
    {
        $query = new TestQueryTestObject(['fields' => $value]);
        $this->assertSame($fields, $query->fields);
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
            [new \stdClass(), null],
            [null, null]
        ];
    }

    public function testContainsField(): void
    {
        $query = new TestQueryTestObject(['fields' => 'f1,f2,f3']);

        $this->assertTrue($query->containsField('f1'));
        $this->assertTrue($query->containsField('f2'));
        $this->assertTrue($query->containsField('f3'));
        $this->assertFalse($query->containsField('f4'));

        $query = new TestQueryTestObject();
        $this->assertTrue($query->containsField('f1'));
        $this->assertTrue($query->containsField('f4'));
    }

    public function testSetPageSize(): void
    {
        AbstractQuery::setMaxPageSize(10000);
        $query1 = new TestQueryTestObject(['limit' => 50000]);
        $query2 = new TestQueryTestObject(['limit' => 35000]);

        $this->assertSame(AbstractQuery::getMaxPageSize(), $query1->limit);
        $this->assertSame(AbstractQuery::getMaxPageSize(), $query2->limit);
    }
}
