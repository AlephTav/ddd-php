<?php

namespace AlephTools\DDD\Tests\Common\Infrastructure\SqlBuilder;

use AlephTools\DDD\Common\Infrastructure\SqlBuilder\SelectQuery;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\UpdateQuery;
use PHPUnit\Framework\TestCase;

class UpdateQueryTest extends TestCase
{
    use QueryTestAware;

    //region TABLE

    public function testTableName(): void
    {
        $q = (new UpdateQuery())
            ->table('tb');

        $this->assertSame('UPDATE tb', $q->toSql());
        $this->assertSame([], $q->getParams());
    }

    public function testTableNameWithAlias(): void
    {
        $q = (new UpdateQuery())
            ->table('tb', 't1');

        $this->assertSame('UPDATE tb t1', $q->toSql());
        $this->assertSame([], $q->getParams());
    }

    public function testFromRawExpression(): void
    {
        $q = (new UpdateQuery())
            ->table(UpdateQuery::raw('my_table AS t'));

        $this->assertSame('UPDATE my_table AS t', $q->toSql());
        $this->assertSame([], $q->getParams());
    }

    //endregion

    //region SET (Assignment List)

    public function testAssignSingleColumn(): void
    {
        $q = (new UpdateQuery())
            ->table('t')
            ->assign('f1', 'v1');

        $this->assertSame('UPDATE t SET f1 = :p1', $q->toSql());
        $this->assertSame(['p1' => 'v1'], $q->getParams());
    }

    public function testAssignMultipleColumns(): void
    {
        $q = (new UpdateQuery())
            ->table('t1')
            ->assign('f1', 'v1')
            ->assign('f2', UpdateQuery::raw('DEFAULT'))
            ->assign(['f3' => NULL])
            ->assign(
                UpdateQuery::raw('(f4, f5)'),
                (new SelectQuery())->from('t2')->select('t2.f1, t2.f2')
            )
            ->assign(['f6 = 5']);

        $this->assertSame(
            'UPDATE t1 SET f1 = :p1, f2 = DEFAULT, f3 = NULL, ' .
            '(f4, f5) = (SELECT t2.f1, t2.f2 FROM t2), f6 = 5',
            $q->toSql()
        );
        $this->assertSame(['p1' => 'v1'], $q->getParams());
    }

    //endregion

    //region RETURNING

    public function testReturningAllColumns(): void
    {
        $q = (new UpdateQuery())
            ->table('tb')
            ->assign('name', 'test')
            ->returning();

        $this->assertSame('UPDATE tb SET name = :p1 RETURNING *', $q->toSql());
        $this->assertSame(['p1' => 'test'], $q->getParams());
    }

    public function testReturningSpecificColumns(): void
    {
        $q = (new UpdateQuery())
            ->table('tb')
            ->assign('name', 'test')
            ->returning([
                'c1',
                'c2',
                'c3' => 'col'
            ]);

        $this->assertSame('UPDATE tb SET name = :p1 RETURNING c1, c2, c3 col', $q->toSql());
        $this->assertSame(['p1' => 'test'], $q->getParams());
    }

    //endregion

    //region Query Execution

    public function testExec(): void
    {
        $executor = $this->getMockQueryExecutor('execute');

        $affectedRows = (new UpdateQuery($executor))->table('t')->assign([
            'f1' => 'v1',
            'f2' => 'v2',
            'f3' => 'v3'
        ])->where('f1', '=', 10)->exec();

        $this->assertSame(
            [
                'UPDATE t SET f1 = :p1, f2 = :p2, f3 = :p3 WHERE f1 = :p4',
                [
                    'p1' => 'v1',
                    'p2' => 'v2',
                    'p3' => 'v3',
                    'p4' => 10
                ]
            ],
            $this->queryExecutionResult
        );
        $this->assertSame(1, $affectedRows);
    }

    //endregion
}
