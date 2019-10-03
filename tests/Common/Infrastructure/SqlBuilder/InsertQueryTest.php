<?php

namespace AlephTools\DDD\Tests\Common\Infrastructure\SqlBuilder;

use AlephTools\DDD\Common\Infrastructure\SqlBuilder\InsertQuery;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\SelectQuery;
use PHPUnit\Framework\TestCase;

class InsertQueryTest extends TestCase
{
    use QueryTestAware;

    //region INTO

    public function testIntoTableName(): void
    {
        $q = (new InsertQuery())
            ->into('t')
            ->values(['f' => 'val']);

        $this->assertSame('INSERT INTO t (f) VALUES (:p1)', $q->toSql());
        $this->assertSame(['p1' => 'val'], $q->getParams());
    }

    public function testIntoTableNameWithAlias(): void
    {
        $q = (new InsertQuery())
            ->into('some_table', 't')
            ->values(['f' => 'val']);

        $this->assertSame('INSERT INTO some_table t (f) VALUES (:p1)', $q->toSql());
        $this->assertSame(['p1' => 'val'], $q->getParams());
    }

    public function testFromRawExpression(): void
    {
        $q = (new InsertQuery())
            ->from(InsertQuery::raw('my_table AS t'))
            ->values(['f' => 'val']);

        $this->assertSame('INSERT INTO my_table AS t (f) VALUES (:p1)', $q->toSql());
        $this->assertSame(['p1' => 'val'], $q->getParams());
    }

    //endregion

    //region COLUMNS & VALUES

    public function testColumnsSeparatelyFromValues(): void
    {
        $q = (new InsertQuery())
            ->into('t')
            ->columns(['f1', 'f2', 'f3'])
            ->values(['val1', 'val2', 'val3']);

        $this->assertSame('INSERT INTO t (f1, f2, f3) VALUES (:p1, :p2, :p3)', $q->toSql());
        $this->assertSame(['p1' => 'val1', 'p2' => 'val2', 'p3' => 'val3'], $q->getParams());
    }

    public function testColumnsSeparatelyFromSetOfValues(): void
    {
        $q = (new InsertQuery())
            ->into('t')
            ->columns(['f1', 'f2', 'f3'])
            ->values([
                ['val1', 'val2', 'val3'],
                ['val4', 'val5', 'val6'],
                ['val7', 'val8', 'val9']
            ]);

        $this->assertSame(
            'INSERT INTO t (f1, f2, f3) VALUES (:p1, :p2, :p3), (:p4, :p5, :p6), (:p7, :p8, :p9)',
            $q->toSql()
        );
        $this->assertSame([
            'p1' => 'val1', 'p2' => 'val2', 'p3' => 'val3',
            'p4' => 'val4', 'p5' => 'val5', 'p6' => 'val6',
            'p7' => 'val7', 'p8' => 'val8', 'p9' => 'val9'
        ], $q->getParams());
    }

    public function testColumnsWithValues(): void
    {
        $q = (new InsertQuery())
            ->into('t')
            ->values(['val1', 'val2', 'val3'], ['f1', 'f2', 'f3']);

        $this->assertSame('INSERT INTO t (f1, f2, f3) VALUES (:p1, :p2, :p3)', $q->toSql());
        $this->assertSame(['p1' => 'val1', 'p2' => 'val2', 'p3' => 'val3'], $q->getParams());
    }

    public function testColumnsWithSetOfValues(): void
    {
        $q = (new InsertQuery())
            ->into('t')
            ->values([
                ['val1', 'val2', 'val3'],
                ['val4', 'val5', 'val6'],
                ['val7', 'val8', 'val9']
            ], ['f1', 'f2', 'f3']);

        $this->assertSame(
            'INSERT INTO t (f1, f2, f3) VALUES (:p1, :p2, :p3), (:p4, :p5, :p6), (:p7, :p8, :p9)',
            $q->toSql()
        );
        $this->assertSame([
            'p1' => 'val1', 'p2' => 'val2', 'p3' => 'val3',
            'p4' => 'val4', 'p5' => 'val5', 'p6' => 'val6',
            'p7' => 'val7', 'p8' => 'val8', 'p9' => 'val9'
        ], $q->getParams());
    }

    public function testColumnsAndValuesAsSingleParameter(): void
    {
        $q = (new InsertQuery())
            ->into('t')
            ->values(['f1' => 'val1', 'f2' => 'val2', 'f3' => 'val3']);

        $this->assertSame('INSERT INTO t (f1, f2, f3) VALUES (:p1, :p2, :p3)', $q->toSql());
        $this->assertSame(['p1' => 'val1', 'p2' => 'val2', 'p3' => 'val3'], $q->getParams());
    }

    public function testColumnsAndSetOfValuesAsSingleParameter(): void
    {
        $q = (new InsertQuery())
            ->into('t')
            ->values([
                ['f1' => 'val1', 'f2' => 'val2', 'f3' => 'val3'],
                ['f1' => 'val4', 'f2' => 'val5', 'f3' => 'val6'],
                ['f1' => 'val7', 'f2' => null, 'f3' => InsertQuery::raw('DEFAULT')]
            ]);

        $this->assertSame(
            'INSERT INTO t (f1, f2, f3) VALUES (:p1, :p2, :p3), (:p4, :p5, :p6), (:p7, NULL, DEFAULT)',
            $q->toSql()
        );
        $this->assertSame([
            'p1' => 'val1', 'p2' => 'val2', 'p3' => 'val3',
            'p4' => 'val4', 'p5' => 'val5', 'p6' => 'val6',
            'p7' => 'val7'
        ], $q->getParams());
    }

    public function testValuesWithoutColumns(): void
    {
        $q = (new InsertQuery())
            ->into('t')
            ->values(['val1', 'val2', 'val3']);

        $this->assertSame('INSERT INTO t VALUES (:p1, :p2, :p3)', $q->toSql());
        $this->assertSame(['p1' => 'val1', 'p2' => 'val2', 'p3' => 'val3'], $q->getParams());
    }

    //endregion

    //region SELECT

    public function testSelect(): void
    {
        $q = (new InsertQuery())
            ->into('t1')
            ->columns(['t1.f1', 't1.f2', 't1.f3'])
            ->select((new SelectQuery())
                ->from('t2')
                ->select(['t2.f1', 't2.f2', 't3.f3'])
                ->where('t2.f1', '=', 123)
            );

        $this->assertSame(
            'INSERT INTO t1 (t1.f1, t1.f2, t1.f3) SELECT t2.f1, t2.f2, t3.f3 FROM t2 WHERE t2.f1 = :p1',
            $q->toSql()
        );
        $this->assertSame(['p1' => 123], $q->getParams());
    }

    //endregion

    //region ON DUPLICATE KEY UPDATE (MySQL)

    public function testOnDuplicateKeyUpdateWithSingleColumn(): void
    {
        $q = (new InsertQuery())
            ->into('t')
            ->values(['f1' => 'v1', 'f2' => 'v2'])
            ->onDuplicateKeyUpdate('f1', 'v3');

        $this->assertSame(
            'INSERT INTO t (f1, f2) VALUES (:p1, :p2) ON DUPLICATE KEY UPDATE f1 = :p3',
            $q->toSql()
        );
        $this->assertSame(['p1' => 'v1', 'p2' => 'v2', 'p3' => 'v3'], $q->getParams());
    }

    public function testOnDuplicateKeyUpdateWithMultipleColumns(): void
    {
        $q = (new InsertQuery())
            ->into('t')
            ->values(['f1' => 'v1', 'f2' => 'v2'])
            ->onDuplicateKeyUpdate(['f1' => 'v3', 'f2' => 'v4']);

        $this->assertSame(
            'INSERT INTO t (f1, f2) VALUES (:p1, :p2) ON DUPLICATE KEY UPDATE f1 = :p3, f2 = :p4',
            $q->toSql()
        );
        $this->assertSame(['p1' => 'v1', 'p2' => 'v2', 'p3' => 'v3', 'p4' => 'v4'], $q->getParams());
    }

    //endregion

    //region ON CONFLICT DO UPDATE (PostgreSQL)

    public function testOnConflictDoNothing(): void
    {
        $q = (new InsertQuery())
            ->into('t')
            ->values('v1', 'f1')
            ->onConflictDoUpdate('f1');

        $this->assertSame('INSERT INTO t (f1) VALUES (:p1) ON CONFLICT (f1) DO NOTHING', $q->toSql());
        $this->assertSame(['p1' => 'v1'], $q->getParams());
    }

    public function testOnConflictDoUpdateSingleColumn(): void
    {
        $q = (new InsertQuery())
            ->into('t')
            ->values(['f1' => 'v1', 'f2' => 'v2'])
            ->onConflictDoUpdate('f1', 'f1', 'v3');

        $this->assertSame(
            'INSERT INTO t (f1, f2) VALUES (:p1, :p2) ON CONFLICT (f1) DO UPDATE SET f1 = :p3',
            $q->toSql()
        );
        $this->assertSame(['p1' => 'v1', 'p2' => 'v2', 'p3' => 'v3'], $q->getParams());
    }

    public function testOnConflictDoUpdateMultipleColumns(): void
    {
        $q = (new InsertQuery())
            ->into('t')
            ->values(['f1' => 'v1', 'f2' => 'v2'])
            ->onConflictDoUpdate('f1', ['f1' => 'v3', 'f2' => 'v4']);

        $this->assertSame(
            'INSERT INTO t (f1, f2) VALUES (:p1, :p2) ON CONFLICT (f1) DO UPDATE SET f1 = :p3, f2 = :p4',
            $q->toSql()
        );
        $this->assertSame(['p1' => 'v1', 'p2' => 'v2', 'p3' => 'v3', 'p4' => 'v4'], $q->getParams());
    }

    //endregion

    //region RETURNING

    public function testReturningAllColumns(): void
    {
        $q = (new InsertQuery())
            ->into('t')
            ->values(['f1' => 'v1'])
            ->returning();

        $this->assertSame('INSERT INTO t (f1) VALUES (:p1) RETURNING *', $q->toSql());
        $this->assertSame(['p1' => 'v1'], $q->getParams());
    }

    public function testReturningSpecificColumns(): void
    {
        $q = (new InsertQuery())
            ->into('t')
            ->values(['f1' => 'v1'])
            ->returning([
                'f1',
                'f2' => 'ff'
            ]);

        $this->assertSame('INSERT INTO t (f1) VALUES (:p1) RETURNING f1, f2 ff', $q->toSql());
        $this->assertSame(['p1' => 'v1'], $q->getParams());
    }

    //endregion

    //region Query Execution

    public function testExec(): void
    {
        $executor = $this->getMockQueryExecutor('insert');

        $id = (new InsertQuery($executor))
            ->into('t')
            ->values('v1')
            ->exec('id');

        $this->assertSame(1, $id);
        $this->assertSame(['INSERT INTO t VALUES (:p1)', ['p1' => 'v1'], 'id'], $this->queryExecutionResult);
    }

    //endregion
}
