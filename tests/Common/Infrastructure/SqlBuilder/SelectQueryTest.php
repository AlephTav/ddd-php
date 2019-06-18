<?php

namespace AlephTools\DDD\Tests\Common\Infrastructure\SqlBuilder;

use RuntimeException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\QueryExecutor;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Expressions\ConditionalExpression;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\SelectQuery;

class SelectQueryTest extends TestCase
{
    use QueryTestAware;

    //region FROM

    public function testFromTableName(): void
    {
        $q = (new SelectQuery())
            ->from('some_table');

        $this->assertSame('SELECT * FROM some_table', $q->toSql());
        $this->assertSame([], $q->getParams());
    }

    public function testFromTableNameWithAlias(): void
    {
        $q = (new SelectQuery())
            ->from('some_table', 't');

        $this->assertSame('SELECT * FROM some_table t', $q->toSql());
        $this->assertSame([], $q->getParams());
    }

    public function testFromListOfTables(): void
    {
        $q = (new SelectQuery())
            ->from([
                'tab1',
                'tab2',
                'tab3'
            ]);

        $this->assertSame('SELECT * FROM tab1, tab2, tab3', $q->toSql());
        $this->assertSame([], $q->getParams());
    }

    public function testFromListOfTablesWithAliases(): void
    {
        $q = (new SelectQuery())
            ->from([
                'tab1' => 't1',
                'tab2' => 't2',
                'tab3' => 't3'
            ]);

        $this->assertSame('SELECT * FROM tab1 t1, tab2 t2, tab3 t3', $q->toSql());
        $this->assertSame([], $q->getParams());
    }

    public function testFromListOfTablesAppend(): void
    {
        $q = (new SelectQuery())
            ->from('t1')
            ->from('t2')
            ->from('t3');

        $this->assertSame('SELECT * FROM t1, t2, t3', $q->toSql());
        $this->assertSame([], $q->getParams());
    }

    public function testFromListOfTablesWithAliasesAppend(): void
    {
        $q = (new SelectQuery())
            ->from('tab1', 't1')
            ->from('tab2', 't2')
            ->from('tab3', 't3');

        $this->assertSame('SELECT * FROM tab1 t1, tab2 t2, tab3 t3', $q->toSql());
        $this->assertSame([], $q->getParams());
    }

    public function testFromRawExpression(): void
    {
        $q = (new SelectQuery())
            ->from(SelectQuery::raw('my_table AS t'));

        $this->assertSame('SELECT * FROM my_table AS t', $q->toSql());
        $this->assertSame([], $q->getParams());
    }

    public function testFromAnotherQuery(): void
    {
        $q = (new SelectQuery())
            ->from((new SelectQuery())->from('my_table'));

        $this->assertSame('SELECT * FROM (SELECT * FROM my_table)', $q->toSql());
        $this->assertSame([], $q->getParams());
    }

    public function testFromAnotherQueryWithAlias(): void
    {
        $q = (new SelectQuery())
            ->from(
                (new SelectQuery())->from('my_table'),
                't'
            );

        $this->assertSame('SELECT * FROM (SELECT * FROM my_table) t', $q->toSql());
        $this->assertSame([], $q->getParams());
    }

    public function testFromListOfQueries(): void
    {
        $q = (new SelectQuery())
            ->from([
                (new SelectQuery())->from('tab1'),
                (new SelectQuery())->from('tab2'),
                (new SelectQuery())->from('tab3')
            ]);

        $this->assertSame(
            'SELECT * FROM (SELECT * FROM tab1), (SELECT * FROM tab2), (SELECT * FROM tab3)',
            $q->toSql()
        );
        $this->assertSame([], $q->getParams());
    }

    public function testFromListOfQueriesWithAliases(): void
    {
        $q = (new SelectQuery())
            ->from([
                [(new SelectQuery())->from('tab1'), 't1'],
                [(new SelectQuery())->from('tab2'), 't2'],
                [(new SelectQuery())->from('tab3'), 't3']
            ]);

        $this->assertSame(
            'SELECT * FROM (SELECT * FROM tab1) t1, (SELECT * FROM tab2) t2, (SELECT * FROM tab3) t3',
            $q->toSql()
        );
        $this->assertSame([], $q->getParams());
    }

    public function testFromMixedSources(): void
    {
        $q = (new SelectQuery())
            ->from([
                [SelectQuery::raw('tab1'), 't1'],
                [SelectQuery::raw('tab1'), 't2'],
                'tab3' => 't3',
                [(new SelectQuery())->from('tab4'), '']
            ]);

        $this->assertSame(
            'SELECT * FROM tab1 t1, tab1 t2, tab3 t3, (SELECT * FROM tab4)',
            $q->toSql()
        );
        $this->assertSame([], $q->getParams());
    }

    //endregion

    //region SELECT

    public function testSelectListOfFields(): void
    {
        $q = (new SelectQuery())
            ->from('t')
            ->select([
                'f1',
                'f2',
                'f3'
            ]);

        $this->assertSame('SELECT f1, f2, f3 FROM t', $q->toSql());
        $this->assertSame([], $q->getParams());
    }

    public function testSelectListOfFieldsWithAlias(): void
    {
        $q = (new SelectQuery())
            ->from('t')
            ->select([
                'field1' => 'f1',
                'field2' => 'f2',
                'field3' => 'f3'
            ]);

        $this->assertSame('SELECT field1 f1, field2 f2, field3 f3 FROM t', $q->toSql());
        $this->assertSame([], $q->getParams());
    }

    public function testSelectListOfFieldsAppend(): void
    {
        $q = (new SelectQuery())
            ->from('t')
            ->select('field1')
            ->select('field2')
            ->select('field3');

        $this->assertSame('SELECT field1, field2, field3 FROM t', $q->toSql());
        $this->assertSame([], $q->getParams());
    }

    public function testSelectListOfFieldsWithAliasesAppend(): void
    {
        $q = (new SelectQuery())
            ->from('t')
            ->select('field1','t1')
            ->select('field2', 't2')
            ->select('field3', 't3');

        $this->assertSame('SELECT field1 t1, field2 t2, field3 t3 FROM t', $q->toSql());
        $this->assertSame([], $q->getParams());
    }

    public function testSelectStringExpression(): void
    {
        $q = (new SelectQuery())
            ->from('t')
            ->select('f1, f2, f3');

        $this->assertSame('SELECT f1, f2, f3 FROM t', $q->toSql());
        $this->assertSame([], $q->getParams());
    }

    public function testSelectRawExpression(): void
    {
        $q = (new SelectQuery())
            ->from('t')
            ->select(SelectQuery::raw('f1, f2, f3'));

        $this->assertSame('SELECT f1, f2, f3 FROM t', $q->toSql());
        $this->assertSame([], $q->getParams());
    }

    public function testSelectQuery(): void
    {
        $q = (new SelectQuery())
            ->from('t1')
            ->select((new SelectQuery())->from('t2'));

        $this->assertSame('SELECT (SELECT * FROM t2) FROM t1', $q->toSql());
        $this->assertSame([], $q->getParams());
    }

    public function testSelectQueryWithAlias(): void
    {
        $q = (new SelectQuery())
            ->from('tab1', 't1')
            ->select(
                (new SelectQuery())->from('tab2'),
                'f1'
            );

        $this->assertSame('SELECT (SELECT * FROM tab2) f1 FROM tab1 t1', $q->toSql());
        $this->assertSame([], $q->getParams());
    }

    public function testSelectMixedSources(): void
    {
        $q = (new SelectQuery())
            ->from('t1')
            ->select([
                [(new SelectQuery())->from('tab2'), 'f1'],
                [null, 'f2'],
                'field3' => 'f3',
                [SelectQuery::raw('COUNT(*)'), 'f4']
            ]);

        $this->assertSame(
            'SELECT (SELECT * FROM tab2) f1, NULL f2, field3 f3, COUNT(*) f4 FROM t1',
            $q->toSql()
        );
        $this->assertSame([], $q->getParams());
    }

    //endregion

    //region JOIN

    public function testJoinSingleTable(): void
    {
        $q = (new SelectQuery())
            ->from('tab1 t1')
            ->join('tab2 t2', 't2.id = t1.tab1_id');

        $this->assertSame('SELECT * FROM tab1 t1 JOIN tab2 t2 ON t2.id = t1.tab1_id', $q->toSql());
        $this->assertSame([], $q->getParams());
    }

    public function testJoinListOfTables(): void
    {
        $q = (new SelectQuery())
            ->from('tab1')
            ->join(['tab2', 'tab3'], 'tab2.id = tab3.id AND tab1.id = tab3.id');

        $this->assertSame(
            'SELECT * FROM tab1 JOIN (tab2, tab3) ON tab2.id = tab3.id AND tab1.id = tab3.id',
            $q->toSql()
        );
        $this->assertSame([], $q->getParams());
    }

    public function testJoinListOfTablesAppend(): void
    {
        $q = (new SelectQuery())
            ->from('t1')
            ->join('t2', 't2.id = t1.id')
            ->join('t3', 't3.id = t2.id')
            ->join('t4', ['t4.id', 't3.id']);

        $this->assertSame(
            'SELECT * FROM t1 JOIN t2 ON t2.id = t1.id JOIN t3 ON t3.id = t2.id JOIN t4 USING (t4.id, t3.id)',
            $q->toSql()
        );
        $this->assertSame([], $q->getParams());
    }

    public function testJoinListOfTablesWithAliases(): void
    {
        $q = (new SelectQuery())
            ->from('tab1', 't1')
            ->join(['tab2' => 't2', 'tab3' => 't3'], 't2.id = t3.id AND t1.id = t3.id');

        $this->assertSame(
            'SELECT * FROM tab1 t1 JOIN (tab2 t2, tab3 t3) ON t2.id = t3.id AND t1.id = t3.id',
            $q->toSql()
        );
        $this->assertSame([], $q->getParams());
    }

    public function testJoinTableWithColumnList(): void
    {
        $q = (new SelectQuery())
            ->from('t1')
            ->join('t2', ['f1', 'f2', 'f3']);

        $this->assertSame('SELECT * FROM t1 JOIN t2 USING (f1, f2, f3)', $q->toSql());
        $this->assertSame([], $q->getParams());
    }

    public function testJoinSubquery(): void
    {
        $q = (new SelectQuery())
            ->from('t1')
            ->join((new SelectQuery())->from('t2'), 't2.id = t1.id');

        $this->assertSame('SELECT * FROM t1 JOIN (SELECT * FROM t2) ON t2.id = t1.id', $q->toSql());
        $this->assertSame([], $q->getParams());
    }

    public function testJoinSubqueryWithAlias(): void
    {
        $q = (new SelectQuery())
            ->from('tab1', 't1')
            ->join([[(new SelectQuery())->from('tab2'), 't2']], 't2.id = t1.id');

        $this->assertSame(
            'SELECT * FROM tab1 t1 JOIN (SELECT * FROM tab2) t2 ON t2.id = t1.id',
            $q->toSql()
        );
        $this->assertSame([], $q->getParams());
    }

    public function testJoinTableWithNestedConditionsClosure(): void
    {
        $q = (new SelectQuery())
            ->from('t1')
            ->join('t2', function(ConditionalExpression $conditions) { $conditions
                ->with('t2.id', '=', SelectQuery::raw('t1.id'))
                ->and('t1.f1', '>', SelectQuery::raw('t2.f2'))
                ->or('t2.f3', '<>', SelectQuery::raw('t1.f3'));
            });

        $this->assertSame(
            'SELECT * FROM t1 JOIN t2 ON (t2.id = t1.id AND t1.f1 > t2.f2 OR t2.f3 <> t1.f3)',
            $q->toSql()
        );
        $this->assertSame([], $q->getParams());
    }

    public function testJoinTableWithNestedConditionsConditionalExpression(): void
    {
        $q = (new SelectQuery())
            ->from('t1')
            ->join('t2', SelectQuery::condition()
                ->with('t2.id', '=', SelectQuery::raw('t1.id'))
                ->and('t1.f1', '>', SelectQuery::raw('t2.f2'))
                ->or('t2.f3', '<>', SelectQuery::raw('t1.f3'))
            );

        $this->assertSame(
            'SELECT * FROM t1 JOIN t2 ON (t2.id = t1.id AND t1.f1 > t2.f2 OR t2.f3 <> t1.f3)',
            $q->toSql()
        );
        $this->assertSame([], $q->getParams());
    }

    public function testJoinOfDifferentTypes(): void
    {
        $q = (new SelectQuery())
            ->from('t1')
            ->innerJoin('t2')
            ->leftJoin('t3')
            ->leftOuterJoin('t4')
            ->naturalLeftJoin('t5')
            ->naturalLeftOuterJoin('t6')
            ->rightJoin('t7')
            ->rightOuterJoin('t8')
            ->naturalRightJoin('t9')
            ->naturalRightOuterJoin('t10')
            ->crossJoin('t11')
            ->straightJoin('t12');

        $this->assertSame(
            'SELECT * FROM t1 INNER JOIN t2 LEFT JOIN t3 LEFT OUTER JOIN t4 NATURAL LEFT JOIN t5 ' .
            'NATURAL LEFT OUTER JOIN t6 RIGHT JOIN t7 RIGHT OUTER JOIN t8 NATURAL RIGHT JOIN t9 ' .
            'NATURAL RIGHT OUTER JOIN t10 CROSS JOIN t11 STRAIGHT_JOIN t12',
            $q->toSql()
        );
        $this->assertSame([], $q->getParams());
    }

    //endregion

    //region WHERE

    public function testWhereColumnOpValue(): void
    {
        $q = (new SelectQuery())
            ->from('t')
            ->where('f', '=', 1);

        $this->assertSame('SELECT * FROM t WHERE f = :p1', $q->toSql());
        $this->assertEquals(['p1' => 1], $q->getParams());
    }

    public function testWhereColumnOpValueAppend(): void
    {
        $q = (new SelectQuery())
            ->from('t')
            ->where('f1', '>', 1)
            ->where('f2', '<', 2);

        $this->assertSame('SELECT * FROM t WHERE f1 > :p1 AND f2 < :p2', $q->toSql());
        $this->assertEquals(['p1' => 1, 'p2' => 2], $q->getParams());
    }

    public function testWhereOpValue(): void
    {
        $q = (new SelectQuery())
            ->from('t')
            ->where('NOT', SelectQuery::raw('(t.f1 == t.f2)'));

        $this->assertSame('SELECT * FROM t WHERE NOT (t.f1 == t.f2)', $q->toSql());
        $this->assertSame([], $q->getParams());
    }

    public function testWhereWithDifferentConnectors(): void
    {
        $q = (new SelectQuery())
            ->from('t')
            ->where('f1', '=', 1)
            ->andWhere('NOT', true)
            ->orWhere('f2', 'IS', null)
            ->andWhere('NOT false')
            ->andWhere('f2', '=', 4)
            ->orWhere('!5')
            ->orWhere('NOT', false);

        $this->assertSame(
            'SELECT * FROM t WHERE f1 = :p1 AND NOT :p2 OR f2 IS NULL AND NOT false ' .
            'AND f2 = :p3 OR !5 OR NOT :p4',
            $q->toSql()
        );
        $this->assertEquals(['p1' => 1, 'p2' => true, 'p3' => 4, 'p4' => false], $q->getParams());
    }

    public function testWhereWithNestedConditionsClosure(): void
    {
        $q = (new SelectQuery())
            ->from('t')
            ->where('f1', 'BETWEEN', [1, 2])
            ->where(function(ConditionalExpression $cond) { $cond
                ->with('f2', '=', 1)
                ->or(function(ConditionalExpression $cond) { $cond
                    ->with('f3', 'IN', [1, 2])
                    ->and('f4', '<', 5)
                    ->or('f5', '>', 6);
                });
            });

        $this->assertSame(
            'SELECT * FROM t WHERE f1 BETWEEN :p1 AND :p2 AND ' .
            '(f2 = :p3 OR (f3 IN (:p4, :p5) AND f4 < :p6 OR f5 > :p7))',
            $q->toSql()
        );
        $this->assertEquals(
            ['p1' => 1, 'p2' => 2, 'p3' => 1, 'p4' => 1, 'p5' => 2, 'p6' => 5, 'p7' => 6],
            $q->getParams()
        );
    }

    public function testWhereWithNestedConditionsObject(): void
    {
        $q = (new SelectQuery())
            ->from('t')
            ->where('f1', 'BETWEEN', [1, 2])
            ->where(SelectQuery::condition()
                ->with('f2', '=', 1)
                ->and('NOT', true)
                ->or('NOT', false)
                ->or(SelectQuery::condition()
                    ->with('f3', 'IN', [1, 2])
                    ->and('f4', '<', 5)
                    ->or('f5', '>', 6)
                    ->and('1 = 1')
                )
            );

        $this->assertSame(
            'SELECT * FROM t WHERE f1 BETWEEN :p1 AND :p2 AND ' .
            '(f2 = :p3 AND NOT :p4 OR NOT :p5 OR (f3 IN (:p6, :p7) AND f4 < :p8 OR f5 > :p9 AND 1 = 1))',
            $q->toSql()
        );
        $this->assertEquals(
            ['p1' => 1, 'p2' => 2, 'p3' => 1, 'p4' => true, 'p5' => false, 'p6' => 1, 'p7' => 2, 'p8' => 5, 'p9' => 6],
            $q->getParams()
        );
    }

    public function testWhereWithSubquery(): void
    {
        $q = (new SelectQuery())
            ->from('t1')
            ->where((new SelectQuery())
                ->from('t2')
                ->select('t2.id')
                ->where('t2.f1', '=', SelectQuery::raw('t1.f2'))
                ->orWhere('t2.f3', 'IS NOT', null),
                'IN',
                (new SelectQuery())
                    ->from('t2')
                    ->select('t2.f1')
            );

        $this->assertSame(
            'SELECT * FROM t1 WHERE (SELECT t2.id FROM t2 WHERE t2.f1 = t1.f2 OR t2.f3 IS NOT NULL) ' .
            'IN (SELECT t2.f1 FROM t2)',
            $q->toSql()
        );
        $this->assertSame([], $q->getParams());
    }

    public function testWhereRawExpressionAndString(): void
    {
        $q = (new SelectQuery())
            ->from('t')
            ->where('t.f1 = 5')
            ->where(SelectQuery::raw('t.f2 = 6'));

        $this->assertSame('SELECT * FROM t WHERE t.f1 = 5 AND t.f2 = 6', $q->toSql());
        $this->assertSame([], $q->getParams());
    }

    public function testWhereOperandIsNull(): void
    {
        $q = (new SelectQuery())
            ->from('t')
            ->where(null, '<>', SelectQuery::raw('t.f'));

        $this->assertSame('SELECT * FROM t WHERE NULL <> t.f', $q->toSql());
        $this->assertSame([], $q->getParams());
    }

    public function testWhereOperandIsList(): void
    {
        $q = (new SelectQuery())
            ->from('t')
            ->where(['t.f1 = t.f2', 't.f2 = t.f3']);

        $this->assertSame('SELECT * FROM t WHERE t.f1 = t.f2 AND t.f2 = t.f3', $q->toSql());
        $this->assertSame([], $q->getParams());
    }

    public function testWhereOperandIsMap(): void
    {
        $q = (new SelectQuery())
            ->from('t')
            ->where(['f1' => 1, 'f2' => 2]);

        $this->assertSame('SELECT * FROM t WHERE f1 = :p1 AND f2 = :p2', $q->toSql());
        $this->assertEquals(['p1' => 1, 'p2' => 2], $q->getParams());
    }

    //endregion

    //region HAVING

    public function testHavingColumnOpValue(): void
    {
        $q = (new SelectQuery())
            ->from('t')
            ->having('f', '=', 1);

        $this->assertSame('SELECT * FROM t HAVING f = :p1', $q->toSql());
        $this->assertEquals(['p1' => 1], $q->getParams());
    }

    public function testHavingColumnOpValueAppend(): void
    {
        $q = (new SelectQuery())
            ->from('t')
            ->having('f1', '>', 1)
            ->having('f2', '<', 2);

        $this->assertSame('SELECT * FROM t HAVING f1 > :p1 AND f2 < :p2', $q->toSql());
        $this->assertEquals(['p1' => 1, 'p2' => 2], $q->getParams());
    }

    public function testHavingOpValue(): void
    {
        $q = (new SelectQuery())
            ->from('t')
            ->having('NOT', SelectQuery::raw('(t.f1 == t.f2)'));

        $this->assertSame('SELECT * FROM t HAVING NOT (t.f1 == t.f2)', $q->toSql());
        $this->assertSame([], $q->getParams());
    }

    public function testHavingWithDifferentConnector(): void
    {
        $q = (new SelectQuery())
            ->from('t')
            ->having('f1', '=', 1)
            ->andHaving('NOT', true)
            ->orHaving('f2', 'IS', null)
            ->andHaving('NOT false')
            ->andHaving('f2', '=', 4)
            ->orHaving('!5')
            ->orHaving('NOT', false);

        $this->assertSame(
            'SELECT * FROM t HAVING f1 = :p1 AND NOT :p2 OR f2 IS NULL AND NOT false ' .
            'AND f2 = :p3 OR !5 OR NOT :p4',
            $q->toSql()
        );
        $this->assertEquals(['p1' => 1, 'p2' => true, 'p3' => 4, 'p4' => false], $q->getParams());
    }

    public function testHavingWithNestedConditionsClosure(): void
    {
        $q = (new SelectQuery())
            ->from('t')
            ->having('f1', 'BETWEEN', [1, 2])
            ->having(function(ConditionalExpression $cond) { $cond
                ->with('f2', '=', 1)
                ->or(function(ConditionalExpression $cond) { $cond
                    ->with('f3', 'IN', [1, 2])
                    ->and('f4', '<', 5)
                    ->or('f5', '>', 6);
                });
            });

        $this->assertSame(
            'SELECT * FROM t HAVING f1 BETWEEN :p1 AND :p2 AND (f2 = :p3 OR (f3 IN (:p4, :p5) AND ' .
            'f4 < :p6 OR f5 > :p7))',
            $q->toSql()
        );
        $this->assertEquals(
            ['p1' => 1, 'p2' => 2, 'p3' => 1, 'p4' => 1, 'p5' => 2, 'p6' => 5, 'p7' => 6],
            $q->getParams()
        );
    }

    public function testHavingWithSubquery(): void
    {
        $q = (new SelectQuery())
            ->from('t1')
            ->having((new SelectQuery())
                ->from('t2')
                ->select('t2.id')
                ->having('t2.f1', '=', SelectQuery::raw('t1.f2'))
                ->orHaving('t2.f3', 'IS NOT', null),
                'IN',
                [1, 2]
            );

        $this->assertSame(
            'SELECT * FROM t1 HAVING (SELECT t2.id FROM t2 HAVING t2.f1 = t1.f2 OR t2.f3 IS NOT NULL) ' .
            'IN (:p1, :p2)',
            $q->toSql()
        );
        $this->assertEquals(['p1' => 1, 'p2' => 2], $q->getParams());
    }

    public function testHavingRawExpressionAndString(): void
    {
        $q = (new SelectQuery())
            ->from('t')
            ->having('t.f1 = 5')
            ->having(SelectQuery::raw('t.f2 = 6'));

        $this->assertSame('SELECT * FROM t HAVING t.f1 = 5 AND t.f2 = 6', $q->toSql());
        $this->assertSame([], $q->getParams());
    }

    //endregion

    //region GROUP BY

    public function testGroupByColumn(): void
    {
        $q = (new SelectQuery())
            ->from('t')
            ->groupBy('t.f1');

        $this->assertSame('SELECT * FROM t GROUP BY t.f1', $q->toSql());
    }

    public function testGroupByColumnWithDirection(): void
    {
        $q = (new SelectQuery())
            ->from('t')
            ->groupBy('t.f1', 'DESC');

        $this->assertSame('SELECT * FROM t GROUP BY t.f1 DESC', $q->toSql());
    }

    public function testGroupByColumns(): void
    {
        $q = (new SelectQuery())
            ->from('t')
            ->groupBy([
                'f1',
                'f2',
                'f3'
            ]);

        $this->assertSame('SELECT * FROM t GROUP BY f1, f2, f3', $q->toSql());
    }

    public function testGroupByColumnsWithDirections(): void
    {
        $q = (new SelectQuery())
            ->from('t')
            ->groupBy([
                'f1' => 'ASC',
                'f2' => 'DESC',
                'f3' => ''
            ]);

        $this->assertSame('SELECT * FROM t GROUP BY f1 ASC, f2 DESC, f3', $q->toSql());
    }

    public function testGroupByColumnsAppend(): void
    {
        $q = (new SelectQuery())
            ->from('t')
            ->groupBy('f1')
            ->groupBy('f2')
            ->groupBy('f3');

        $this->assertSame('SELECT * FROM t GROUP BY f1, f2, f3', $q->toSql());
    }

    public function testGroupByColumnsWithDirectionsAppend(): void
    {
        $q = (new SelectQuery())
            ->from('t')
            ->groupBy('f1', 'DESC')
            ->groupBy('f2', '')
            ->groupBy('f3', 'ASC');

        $this->assertSame('SELECT * FROM t GROUP BY f1 DESC, f2, f3 ASC', $q->toSql());
    }

    public function testGroupByQuery(): void
    {
        $q = (new SelectQuery())
            ->from('t1')
            ->groupBy((new SelectQuery())
                ->from('t2')
                ->select('t2.id'),
                'DESC'
            );

        $this->assertSame('SELECT * FROM t1 GROUP BY (SELECT t2.id FROM t2) DESC', $q->toSql());
    }

    public function testGroupByMixedSources(): void
    {
        $q = (new SelectQuery())
            ->from('t1')
            ->groupBy('f1 ASC')
            ->groupBy(SelectQuery::raw('f2 DESC'))
            ->groupBy(['f3', 'f4'])
            ->groupBy(['f5' => 'DESC'])
            ->groupBy((new SelectQuery())->from('t2')->select('t2.id'));

        $this->assertSame(
            'SELECT * FROM t1 GROUP BY f1 ASC, f2 DESC, f3, f4, f5 DESC, (SELECT t2.id FROM t2)',
            $q->toSql()
        );
    }

    //endregion

    //region ORDER BY

    public function testOrderByColumn(): void
    {
        $q = (new SelectQuery())
            ->from('t')
            ->orderBy('t.f1');

        $this->assertSame('SELECT * FROM t ORDER BY t.f1', $q->toSql());
    }

    public function testOrderByColumnWithDirection(): void
    {
        $q = (new SelectQuery())
            ->from('t')
            ->orderBy('t.f1', 'DESC');

        $this->assertSame('SELECT * FROM t ORDER BY t.f1 DESC', $q->toSql());
    }

    public function testOrderByColumns(): void
    {
        $q = (new SelectQuery())
            ->from('t')
            ->orderBy([
                'f1',
                'f2',
                'f3'
            ]);

        $this->assertSame('SELECT * FROM t ORDER BY f1, f2, f3', $q->toSql());
    }

    public function testOrderByColumnsWithDirections(): void
    {
        $q = (new SelectQuery())
            ->from('t')
            ->orderBy([
                'f1' => 'ASC',
                'f2' => 'DESC',
                'f3' => ''
            ]);

        $this->assertSame('SELECT * FROM t ORDER BY f1 ASC, f2 DESC, f3', $q->toSql());
    }

    public function testOrderByColumnsAppend(): void
    {
        $q = (new SelectQuery())
            ->from('t')
            ->orderBy('f1')
            ->orderBy('f2')
            ->orderBy('f3');

        $this->assertSame('SELECT * FROM t ORDER BY f1, f2, f3', $q->toSql());
    }

    public function testOrderByColumnsWithDirectionsAppend(): void
    {
        $q = (new SelectQuery())
            ->from('t')
            ->orderBy('f1', 'DESC')
            ->orderBy('f2', '')
            ->orderBy('f3', 'ASC');

        $this->assertSame('SELECT * FROM t ORDER BY f1 DESC, f2, f3 ASC', $q->toSql());
    }

    public function testOrderByQuery(): void
    {
        $q = (new SelectQuery())
            ->from('t1')
            ->orderBy(
                (new SelectQuery())
                    ->from('t2')
                    ->select('t2.id'),
                'DESC'
            );

        $this->assertSame('SELECT * FROM t1 ORDER BY (SELECT t2.id FROM t2) DESC', $q->toSql());
    }

    public function testOrderByMixedSources(): void
    {
        $q = (new SelectQuery())
            ->from('t1')
            ->orderBy('f1 ASC')
            ->orderBy(SelectQuery::raw('f2 DESC'))
            ->orderBy(['f3', 'f4'])
            ->orderBy(['f5' => 'DESC'])
            ->orderBy((new SelectQuery())->from('t2')->select('t2.id'));

        $this->assertSame(
            'SELECT * FROM t1 ORDER BY f1 ASC, f2 DESC, f3, f4, f5 DESC, (SELECT t2.id FROM t2)',
            $q->toSql()
        );
    }

    //endregion

    //region LIMIT & OFFSET

    public function testLimit(): void
    {
        $q = (new SelectQuery())
            ->from('t')
            ->limit(10);

        $this->assertSame('SELECT * FROM t LIMIT 10', $q->toSql());
    }

    public function testOffset(): void
    {
        $q = (new SelectQuery())
            ->from('t')
            ->offset(12);

        $this->assertSame('SELECT * FROM t OFFSET 12', $q->toSql());
    }

    public function testLimitAndOffset(): void
    {
        $q = (new SelectQuery())
            ->from('t')
            ->limit(5)
            ->offset(12);

        $this->assertSame('SELECT * FROM t LIMIT 5 OFFSET 12', $q->toSql());
    }

    public function testPage(): void
    {
        $q = (new SelectQuery())
            ->from('t')
            ->paginate(3, 7);

        $this->assertSame('SELECT * FROM t LIMIT 7 OFFSET 21', $q->toSql());
    }

    //endregion

    //region UNION

    public function testUnionOfTwoQueries(): void
    {
        $s = (new SelectQuery())
            ->from('t2');

        $q = (new SelectQuery())
            ->from('t1')
            ->union($s);

        $this->assertSame('(SELECT * FROM t1) UNION (SELECT * FROM t2)', $q->toSql());
    }

    public function testUnionOfQueriesWithSorting(): void
    {
        $q = (new SelectQuery())
            ->from('t1')
            ->union((new SelectQuery())
                ->from('t2')
                ->orderBy('t2.id', 'ASC')
            )
            ->union((new SelectQuery())
                ->from('t3')
                ->orderBy('t3.id', 'DESC')
            )
            ->orderBy('id', 'DESC');

        $this->assertSame(
            '(SELECT * FROM t1) UNION (SELECT * FROM t2 ORDER BY t2.id ASC) UNION ' .
            '(SELECT * FROM t3 ORDER BY t3.id DESC) ORDER BY id DESC',
            $q->toSql()
        );
    }

    public function testUnionOfDifferentTypes(): void
    {
        $q = (new SelectQuery())
            ->from('t1')
            ->unionAll((new SelectQuery())->from('t2'))
            ->unionDistinct((new SelectQuery())->from('t3'))
            ->paginate(10, 5);

        $this->assertSame(
            '(SELECT * FROM t1) UNION ALL (SELECT * FROM t2) UNION DISTINCT ' .
            '(SELECT * FROM t3) LIMIT 5 OFFSET 50',
            $q->toSql()
        );
    }

    //endregion

    //region Query Execution

    public function testQueryExecutorValidationForRows(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('The query executor instance must not be null.');

        (new SelectQuery())->rows();
    }

    public function testQueryExecutorValidationForRow(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('The query executor instance must not be null.');

        (new SelectQuery())->row();
    }

    public function testQueryExecutorValidationForColumn(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('The query executor instance must not be null.');

        (new SelectQuery())->column();
    }

    public function testQueryExecutorValidationForScalar(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('The query executor instance must not be null.');

        (new SelectQuery())->scalar();
    }

    public function testQueryExecutorValidationForCount(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('The query executor instance must not be null.');

        (new SelectQuery())->count();
    }

    public function testRows(): void
    {
        $q = (new SelectQuery($this->getMockQueryExecutor('rows')))
            ->from('tb')
            ->where('c1', '=', 123);

        $this->assertSame([$q->toSql(), $q->getParams()], $q->rows());
    }

    public function testRow(): void
    {
        $q = (new SelectQuery($this->getMockQueryExecutor('row')))
            ->from('tb')
            ->where('c1', '=', 123)
            ->limit(1);

        $this->assertSame([$q->toSql(), $q->getParams()], $q->row());
    }

    public function testColumn(): void
    {
        $executor = $this->getMockQueryExecutor('column');

        $q = (new SelectQuery($executor))
            ->from('tb')
            ->where('c1', '=', 123);

        $this->assertSame([$q->toSql(), $q->getParams()], $q->column());

        $column = $q->column('c2');
        $this->assertSame('SELECT * FROM tb WHERE c1 = :p1', $q->toSql());
        $this->assertSame(['p1' => 123], $q->getParams());

        $q->select('c2');
        $this->assertSame([$q->toSql(), $q->getParams()], $column);
    }

    public function testScalar(): void
    {
        $executor = $this->getMockQueryExecutor('scalar');

        $q = (new SelectQuery($executor))
            ->from('tb')
            ->where('c1', '=', 123);

        $this->assertSame([$q->toSql(), $q->getParams()], $q->scalar());

        $column = $q->scalar('c2');
        $this->assertSame('SELECT * FROM tb WHERE c1 = :p1', $q->toSql());
        $this->assertSame(['p1' => 123], $q->getParams());

        $q->select('c2');
        $this->assertSame([$q->toSql(), $q->getParams()], $column);
    }

    public function testCount(): void
    {
        $executor = $this->getMockQueryExecutor();

        $result = null;
        $executor->method('scalar')
            ->willReturnCallback(function(string $sql, array $params) use(&$result) {
                $result = [$sql, $params];
                return 5;
            });

        $q = (new SelectQuery($executor))
            ->from('tb')
            ->where('c1', '=', 123)
            ->orderBy('c3')
            ->limit(10)
            ->offset(25);

        $count = $q->count();
        $this->assertSame(5, $count);
        $this->assertSame(['SELECT COUNT(*) FROM tb WHERE c1 = :p1', $q->getParams()], $result);

        $count = $q->count('c2');
        $this->assertSame(5, $count);
        $this->assertSame(['SELECT COUNT(c2) FROM tb WHERE c1 = :p1', $q->getParams()], $result);

        $this->assertSame('SELECT * FROM tb WHERE c1 = :p1 ORDER BY c3 LIMIT 10 OFFSET 25', $q->toSql());
        $this->assertSame(['p1' => 123], $q->getParams());
    }

    public function testGetQueryExecutor(): void
    {
        $executor = $this->getMockQueryExecutor();
        $q = new SelectQuery($executor);

        $this->assertSame($executor, $q->getQueryExecutor());
    }

    public function testRowsByKey(): void
    {
        $executor = $this->getMockQueryExecutor();

        $executor->method('rows')
            ->willReturnCallback(function(string $sql, array $params) {
                return [
                    ['key' => 1, 'data' => 'a'],
                    ['key' => 2, 'data' => 'b'],
                    ['key' => 3, 'data' => 'c']
                ];
            });

        $rows = (new SelectQuery($executor))
            ->from('table')
            ->rowsByKey('key');

        $this->assertSame([
            1 => ['key' => 1, 'data' => 'a'],
            2 => ['key' => 2, 'data' => 'b'],
            3 => ['key' => 3, 'data' => 'c']
        ], $rows);

        $rows = (new SelectQuery($executor))
            ->from('table')
            ->rowsByKey('key', true);

        $this->assertSame([
            1 => ['data' => 'a'],
            2 => ['data' => 'b'],
            3 => ['data' => 'c']
        ], $rows);

        $this->expectException(RuntimeException::class);

        (new SelectQuery($executor))
            ->from('table')
            ->rowsByKey('foo');
    }

    public function testRowsByGroup(): void
    {
        $executor = $this->getMockQueryExecutor();

        $executor->method('rows')
            ->willReturnCallback(function(string $sql, array $params) {
                return [
                    ['key' => 1, 'data' => 'a'],
                    ['key' => 1, 'data' => 'b'],
                    ['key' => 2, 'data' => 'c']
                ];
            });

        $rows = (new SelectQuery($executor))
            ->from('table')
            ->rowsByGroup('key');

        $this->assertSame([
            1 => [
                ['key' => 1, 'data' => 'a'],
                ['key' => 1, 'data' => 'b']
            ],
            2 => [
                ['key' => 2, 'data' => 'c']
            ]
        ], $rows);

        $rows = (new SelectQuery($executor))
            ->from('table')
            ->rowsByGroup('key', true);

        $this->assertSame([
            1 => [
                ['data' => 'a'],
                ['data' => 'b']
            ],
            2 => [
                ['data' => 'c']
            ]
        ], $rows);

        $this->expectException(RuntimeException::class);

        (new SelectQuery($executor))
            ->from('table')
            ->rowsByGroup('foo');
    }

    private function getMockQueryExecutor(string $method = null): MockObject
    {
        $executor = $this->getMockBuilder(QueryExecutor::class)
            ->setMethods(['rows', 'row', 'column', 'scalar', 'insert', 'execute'])
            ->getMock();

        if ($method) {
            $executor->method($method)
                ->willReturnCallback(function (string $sql, array $params) {
                    return [$sql, $params];
                });
        }

        return $executor;
    }

    //endregion
}
