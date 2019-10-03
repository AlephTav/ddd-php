<?php

namespace AlephTools\DDD\Tests\Common\Infrastructure\SqlBuilder;

use AlephTools\DDD\Common\Infrastructure\SqlBuilder\DeleteQuery;
use PHPUnit\Framework\TestCase;

class DeleteQueryTest extends TestCase
{
    use QueryTestAware;

    //region Using

    public function testUsingSingleTable(): void
    {
        $q = (new DeleteQuery())
            ->from('t1')
            ->using('t2')
            ->where('t2.f1 = t1.f2');

        $this->assertSame('DELETE FROM t1 USING t2 WHERE t2.f1 = t1.f2', $q->toSql());
        $this->assertSame([], $q->getParams());
    }

    public function testUsingMultipleTables(): void
    {
        $q = (new DeleteQuery())
            ->from('t1')
            ->using(['t2', 't3'])
            ->where('t2.f1 = t1.f2')
            ->where('t3.f2 = t2.f1');

        $this->assertSame('DELETE FROM t1 USING t2, t3 WHERE t2.f1 = t1.f2 AND t3.f2 = t2.f1', $q->toSql());
        $this->assertSame([], $q->getParams());
    }

    //endregion

    //region Query Execution

    public function testExec(): void
    {
        $executor = $this->getMockQueryExecutor('execute');

        $affectedRows = (new DeleteQuery($executor))
            ->from('t')
            ->where('f1', '=', 10)
            ->exec();

        $this->assertSame(
            ['DELETE FROM t WHERE f1 = :p1', ['p1' => 10]],
            $this->queryExecutionResult
        );
        $this->assertSame(1, $affectedRows);
    }

    //endregion
}
