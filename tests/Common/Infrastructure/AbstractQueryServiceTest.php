<?php

namespace AlephTools\DDD\Tests\Common\Infrastructure;

use DateTime;
use PHPUnit\Framework\TestCase;
use AlephTools\DDD\Tests\Common\Infrastructure\SqlBuilder\QueryTestAware;
use AlephTools\DDD\Common\Infrastructure\AbstractQuery;
use AlephTools\DDD\Common\Infrastructure\AbstractQueryService;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Query;
use AlephTools\DDD\Common\Model\Exceptions\InvalidArgumentException;

/**
 * @property-read DateTime|null $from
 * @property-read DateTime|null $to
 */
class ApplyQueryTestObject extends AbstractQuery {

    private $from;
    private $to;

    private function setFrom($from): void
    {
        $this->from = $this->toDate($from);
    }

    private function setTo($to): void
    {
        $this->to = $this->toDate($to);
    }
}

class QueryServiceTestObject extends AbstractQueryService
{
    public function apply(ApplyQueryTestObject $request)
    {
        $query = new Query();
        $query->from('tb');

        $this->applySelection($query, $request->fields, [
            'f1' => 'field1',
            'f2' => 'field2',
            'f3' => 'field3'
        ]);

        $this->applySorting($query, $request->sort, [
            'f1' => 'field1',
            'f2' => 'field2',
            'f3' => 'field3'
        ]);

        $this->applyPagination($query, $request);

        $this->applyDateRangeFiltering($query, 'dt', $request);

        return $query;
    }
}

class AbstractQueryServiceTest extends TestCase
{
    use QueryTestAware;

    public function testWithoutParameters(): void
    {
        $service = new QueryServiceTestObject();
        $query = $service->apply(new ApplyQueryTestObject());

        $this->assertSame(
            'SELECT field1 f1, field2 f2, field3 f3 FROM tb LIMIT ' . AbstractQuery::DEFAULT_PAGE_SIZE,
            $query->toSql()
        );
    }

    public function testWithFields(): void
    {
        $service = new QueryServiceTestObject();
        $query = $service->apply(new ApplyQueryTestObject(['fields' => 'f1,f3']));

        $this->assertSame(
            'SELECT field1 f1, field3 f3 FROM tb LIMIT ' . AbstractQuery::DEFAULT_PAGE_SIZE,
            $query->toSql()
        );
    }

    public function testWithIncorrectField(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Incorrect field: f4.');

        $service = new QueryServiceTestObject();
        $service->apply(new ApplyQueryTestObject(['fields' => 'f1,f4']));
    }

    public function testWithSorting(): void
    {
        $service = new QueryServiceTestObject();
        $query = $service->apply(new ApplyQueryTestObject(['fields' => 'f1', 'sort' => 'f3,-f2']));

        $this->assertSame(
            'SELECT field1 f1 FROM tb ORDER BY field3 ASC, field2 DESC LIMIT ' . AbstractQuery::DEFAULT_PAGE_SIZE,
            $query->toSql()
        );
    }

    public function testWithIncorrectSortField(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Incorrect sort field: f4.');

        $service = new QueryServiceTestObject();
        $service->apply(new ApplyQueryTestObject(['fields' => 'f1', 'sort' => 'f3,-f4']));
    }

    public function testWithLimit(): void
    {
        $service = new QueryServiceTestObject();
        $query = $service->apply(new ApplyQueryTestObject(['fields' => 'f1', 'sort' => 'f3', 'limit' => 50]));

        $this->assertSame(
            'SELECT field1 f1 FROM tb ORDER BY field3 ASC LIMIT 50',
            $query->toSql()
        );
    }

    public function testWithOffset(): void
    {
        $service = new QueryServiceTestObject();
        $query = $service->apply(new ApplyQueryTestObject([
            'fields' => 'f1',
            'sort' => 'f3',
            'limit' => 50,
            'offset' => 30
        ]));

        $this->assertSame(
            'SELECT field1 f1 FROM tb ORDER BY field3 ASC LIMIT 50 OFFSET 30',
            $query->toSql()
        );
    }

    public function testWithPage(): void
    {
        $service = new QueryServiceTestObject();
        $query = $service->apply(new ApplyQueryTestObject([
            'fields' => 'f1',
            'sort' => 'f3',
            'limit' => 5,
            'page' => 3
        ]));

        $this->assertSame(
            'SELECT field1 f1 FROM tb ORDER BY field3 ASC LIMIT 5 OFFSET 15',
            $query->toSql()
        );
    }

    public function testWithFrom(): void
    {
        $now = '2018-01-01 23:45:12';

        $service = new QueryServiceTestObject();
        $query = $service->apply(new ApplyQueryTestObject([
            'fields' => 'f1',
            'from' => $now
        ]));

        $this->assertEquals(
            'SELECT field1 f1 FROM tb WHERE dt >= :p1 LIMIT 10',
            $query->toSql()
        );
        $this->assertEquals(['p1' => DateTime::createFromFormat('Y-m-d H:i:s', $now)], $query->getParams());
    }

    public function testWithTo(): void
    {
        $now = '2018-01-01 23:45:12';

        $service = new QueryServiceTestObject();
        $query = $service->apply(new ApplyQueryTestObject([
            'fields' => 'f1',
            'to' => $now
        ]));

        $this->assertEquals(
            'SELECT field1 f1 FROM tb WHERE dt <= :p1 LIMIT 10',
            $query->toSql()
        );
        $this->assertEquals(['p1' => DateTime::createFromFormat('Y-m-d H:i:s', $now)], $query->getParams());
    }

    public function testWithFromAndTo(): void
    {
        $from = '2018-01-01 23:45:12';
        $to = '2018-01-04 23:45:12';

        $service = new QueryServiceTestObject();
        $query = $service->apply(new ApplyQueryTestObject([
            'fields' => 'f1',
            'from' => $from,
            'to' => $to
        ]));

        $this->assertEquals(
            'SELECT field1 f1 FROM tb WHERE dt BETWEEN :p1 AND :p2 LIMIT 10',
            $query->toSql()
        );
        $this->assertEquals([
            'p1' => DateTime::createFromFormat('Y-m-d H:i:s', $from),
            'p2' => DateTime::createFromFormat('Y-m-d H:i:s', $to)
        ], $query->getParams());
    }
}