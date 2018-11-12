<?php

namespace AlephTools\DDD\Tests\Common\Infrastructure\SqlBuilder;

use PHPUnit\Framework\TestCase;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Expressions\ConditionalExpression;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Query;

class QueryTest extends TestCase
{
    use QueryTestAware;

    //region FROM

    public function testFromTableName(): void
    {
        $q = (new Query())
            ->from('some_table');

        $this->assertSame('SELECT * FROM some_table', $q->toSql());
    }

    public function testFromTableNameWithAlias(): void
    {
        $q = (new Query())
            ->from('some_table', 't');

        $this->assertSame('SELECT * FROM some_table t', $q->toSql());
    }

    public function testFromListOfTables(): void
    {
        $q = (new Query())
            ->from([
                'tab1',
                'tab2',
                'tab3'
            ]);

        $this->assertSame('SELECT * FROM tab1, tab2, tab3', $q->toSql());
    }

    public function testFromListOfTablesWithAliases(): void
    {
        $q = (new Query())
            ->from([
                'tab1' => 't1',
                'tab2' => 't2',
                'tab3' => 't3'
            ]);

        $this->assertSame('SELECT * FROM tab1 t1, tab2 t2, tab3 t3', $q->toSql());
    }

    public function testFromListOfTablesAppend(): void
    {
        $q = (new Query())
            ->from('t1')
            ->from('t2')
            ->from('t3');

        $this->assertSame('SELECT * FROM t1, t2, t3', $q->toSql());
    }

    public function testFromListOfTablesWithAliasesAppend(): void
    {
        $q = (new Query())
            ->from('tab1', 't1')
            ->from('tab2', 't2')
            ->from('tab3', 't3');

        $this->assertSame('SELECT * FROM tab1 t1, tab2 t2, tab3 t3', $q->toSql());
    }

    public function testFromRawExpression(): void
    {
        $q = (new Query())
            ->from(Query::raw('my_table AS t'));

        $this->assertSame('SELECT * FROM my_table AS t', $q->toSql());
    }

    public function testFromAnotherQuery(): void
    {
        $q = (new Query())
            ->from((new Query())->from('my_table'));

        $this->assertSame('SELECT * FROM (SELECT * FROM my_table)', $q->toSql());
    }

    public function testFromAnotherQueryWithAlias(): void
    {
        $q = (new Query())
            ->from(
                (new Query())->from('my_table'),
                't'
            );

        $this->assertSame('SELECT * FROM (SELECT * FROM my_table) t', $q->toSql());
    }

    public function testFromListOfQueries(): void
    {
        $q = (new Query())
            ->from([
                (new Query())->from('tab1'),
                (new Query())->from('tab2'),
                (new Query())->from('tab3')
            ]);

        $this->assertSame(
            'SELECT * FROM (SELECT * FROM tab1), (SELECT * FROM tab2), (SELECT * FROM tab3)',
            $q->toSql()
        );
    }

    public function testFromListOfQueriesWithAliases(): void
    {
        $q = (new Query())
            ->from([
                [(new Query())->from('tab1'), 't1'],
                [(new Query())->from('tab2'), 't2'],
                [(new Query())->from('tab3'), 't3']
            ]);

        $this->assertSame(
            'SELECT * FROM (SELECT * FROM tab1) t1, (SELECT * FROM tab2) t2, (SELECT * FROM tab3) t3',
            $q->toSql()
        );
    }

    public function testFromMixedSources(): void
    {
        $q = (new Query())
            ->from([
                [Query::raw('tab1'), 't1'],
                [Query::raw('tab1'), 't2'],
                'tab3' => 't3',
                [(new Query())->from('tab4'), '']
            ]);

        $this->assertSame(
            'SELECT * FROM tab1 t1, tab1 t2, tab3 t3, (SELECT * FROM tab4)',
            $q->toSql()
        );
    }

    //endregion

    //region SELECT

    public function testSelectListOfFields(): void
    {
        $q = (new Query())
            ->from('t')
            ->select([
                'f1',
                'f2',
                'f3'
            ]);

        $this->assertSame('SELECT f1, f2, f3 FROM t', $q->toSql());
    }

    public function testSelectListOfFieldsWithAlias(): void
    {
        $q = (new Query())
            ->from('t')
            ->select([
                'field1' => 'f1',
                'field2' => 'f2',
                'field3' => 'f3'
            ]);

        $this->assertSame('SELECT field1 f1, field2 f2, field3 f3 FROM t', $q->toSql());
    }

    public function testSelectListOfFieldsAppend(): void
    {
        $q = (new Query())
            ->from('t')
            ->select('field1')
            ->select('field2')
            ->select('field3');

        $this->assertSame('SELECT field1, field2, field3 FROM t', $q->toSql());
    }

    public function testSelectListOfFieldsWithAliasesAppend(): void
    {
        $q = (new Query())
            ->from('t')
            ->select('field1','t1')
            ->select('field2', 't2')
            ->select('field3', 't3');

        $this->assertSame('SELECT field1 t1, field2 t2, field3 t3 FROM t', $q->toSql());
    }

    public function testSelectStringExpression(): void
    {
        $q = (new Query())
            ->from('t')
            ->select('f1, f2, f3');

        $this->assertSame('SELECT f1, f2, f3 FROM t', $q->toSql());
    }

    public function testSelectRawExpression(): void
    {
        $q = (new Query())
            ->from('t')
            ->select(Query::raw('f1, f2, f3'));

        $this->assertSame('SELECT f1, f2, f3 FROM t', $q->toSql());
    }

    public function testSelectQuery(): void
    {
        $q = (new Query())
            ->from('t1')
            ->select((new Query())->from('t2'));

        $this->assertSame('SELECT (SELECT * FROM t2) FROM t1', $q->toSql());
    }

    public function testSelectQueryWithAlias(): void
    {
        $q = (new Query())
            ->from('tab1', 't1')
            ->select(
                (new Query())->from('tab2'),
                'f1'
            );

        $this->assertSame('SELECT (SELECT * FROM tab2) f1 FROM tab1 t1', $q->toSql());
    }

    public function testSelectMixedSources(): void
    {
        $q = (new Query())
            ->from('t1')
            ->select([
                [(new Query())->from('tab2'), 'f1'],
                [null, 'f2'],
                'field3' => 'f3',
                [Query::raw('COUNT(*)'), 'f4']
            ]);

        $this->assertSame(
            'SELECT (SELECT * FROM tab2) f1, NULL f2, field3 f3, COUNT(*) f4 FROM t1',
            $q->toSql()
        );
    }

    //endregion

    //region JOIN

    public function testJoinSingleTable(): void
    {
        $q = (new Query())
            ->from('tab1 t1')
            ->join('tab2 t2', 't2.id = t1.tab1_id');

        $this->assertSame('SELECT * FROM tab1 t1 JOIN tab2 t2 ON t2.id = t1.tab1_id', $q->toSql());
    }

    public function testJoinListOfTables(): void
    {
        $q = (new Query())
            ->from('tab1')
            ->join(['tab2', 'tab3'], 'tab2.id = tab3.id AND tab1.id = tab3.id');

        $this->assertSame(
            'SELECT * FROM tab1 JOIN (tab2, tab3) ON tab2.id = tab3.id AND tab1.id = tab3.id',
            $q->toSql()
        );
    }

    public function testJoinListOfTablesAppend(): void
    {
        $q = (new Query())
            ->from('t1')
            ->join('t2', 't2.id = t1.id')
            ->join('t3', 't3.id = t2.id')
            ->join('t4', ['t4.id', 't3.id']);

        $this->assertSame(
            'SELECT * FROM t1 JOIN t2 ON t2.id = t1.id JOIN t3 ON t3.id = t2.id JOIN t4 USING (t4.id, t3.id)',
            $q->toSql()
        );
    }

    public function testJoinListOfTablesWithAliases(): void
    {
        $q = (new Query())
            ->from('tab1', 't1')
            ->join(['tab2' => 't2', 'tab3' => 't3'], 't2.id = t3.id AND t1.id = t3.id');

        $this->assertSame(
            'SELECT * FROM tab1 t1 JOIN (tab2 t2, tab3 t3) ON t2.id = t3.id AND t1.id = t3.id',
            $q->toSql()
        );
    }

    public function testJoinTableWithColumnList(): void
    {
        $q = (new Query())
            ->from('t1')
            ->join('t2', ['f1', 'f2', 'f3']);

        $this->assertSame('SELECT * FROM t1 JOIN t2 USING (f1, f2, f3)', $q->toSql());
    }

    public function testJoinSubquery(): void
    {
        $q = (new Query())
            ->from('t1')
            ->join((new Query())->from('t2'), 't2.id = t1.id');

        $this->assertSame('SELECT * FROM t1 JOIN (SELECT * FROM t2) ON t2.id = t1.id', $q->toSql());
    }

    public function testJoinSubqueryWithAlias(): void
    {
        $q = (new Query())
            ->from('tab1', 't1')
            ->join([[(new Query())->from('tab2'), 't2']], 't2.id = t1.id');

        $this->assertSame(
            'SELECT * FROM tab1 t1 JOIN (SELECT * FROM tab2) t2 ON t2.id = t1.id',
            $q->toSql()
        );
    }

    public function testJoinTableWithNestedConditions(): void
    {
        $q = (new Query())
            ->from('t1')
            ->join('t2', function(ConditionalExpression $conditions) { $conditions
                ->with('t2.id', '=', Query::raw('t1.id'))
                ->and('t1.f1', '>', Query::raw('t2.f2'))
                ->or('t2.f3', '<>', Query::raw('t1.f3'));
            });

        $this->assertSame(
            'SELECT * FROM t1 JOIN t2 ON (t2.id = t1.id AND t1.f1 > t2.f2 OR t2.f3 <> t1.f3)',
            $q->toSql()
        );
    }

    public function testJoinOfDifferentTypes(): void
    {
        $q = (new Query())
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
    }

    //endregion

    //region WHERE

    public function testWhereColumnOpValue(): void
    {
        $q = (new Query())
            ->from('t')
            ->where('f', '=', 1);

        $this->assertSame('SELECT * FROM t WHERE f = :p1', $q->toSql());
        $this->assertEquals(['p1' => 1], $q->getParams());
    }

    public function testWhereColumnOpValueAppend(): void
    {
        $q = (new Query())
            ->from('t')
            ->where('f1', '>', 1)
            ->where('f2', '<', 2);

        $this->assertSame('SELECT * FROM t WHERE f1 > :p1 AND f2 < :p2', $q->toSql());
        $this->assertEquals(['p1' => 1, 'p2' => 2], $q->getParams());
    }

    public function testWhereOpValue(): void
    {
        $q = (new Query())
            ->from('t')
            ->where('NOT', Query::raw('(t.f1 == t.f2)'));

        $this->assertSame('SELECT * FROM t WHERE NOT (t.f1 == t.f2)', $q->toSql());
        $this->assertSame([], $q->getParams());
    }

    public function testWhereWithDifferentConnectors(): void
    {
        $q = (new Query())
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
        $q = (new Query())
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
        $q = (new Query())
            ->from('t')
            ->where('f1', 'BETWEEN', [1, 2])
            ->where(Query::condition()
                ->with('f2', '=', 1)
                ->and('NOT', true)
                ->or('NOT', false)
                ->or(Query::condition()
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
        $q = (new Query())
            ->from('t1')
            ->where((new Query())
                ->from('t2')
                ->select('t2.id')
                ->where('t2.f1', '=', Query::raw('t1.f2'))
                ->orWhere('t2.f3', 'IS NOT', null),
                'IN',
                (new Query())
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
        $q = (new Query())
            ->from('t')
            ->where('t.f1 = 5')
            ->where(Query::raw('t.f2 = 6'));

        $this->assertSame('SELECT * FROM t WHERE t.f1 = 5 AND t.f2 = 6', $q->toSql());
        $this->assertSame([], $q->getParams());
    }

    public function testWhereOperandIsNull(): void
    {
        $q = (new Query())
            ->from('t')
            ->where(null, '<>', Query::raw('t.f'));

        $this->assertSame('SELECT * FROM t WHERE NULL <> t.f', $q->toSql());
        $this->assertSame([], $q->getParams());
    }

    public function testWhereOperandIsList(): void
    {
        $q = (new Query())
            ->from('t')
            ->where(['t.f1 = t.f2', 't.f2 = t.f3']);

        $this->assertSame('SELECT * FROM t WHERE t.f1 = t.f2 AND t.f2 = t.f3', $q->toSql());
        $this->assertSame([], $q->getParams());
    }

    public function testWhereOperandIsMap(): void
    {
        $q = (new Query())
            ->from('t')
            ->where(['f1' => 1, 'f2' => 2]);

        $this->assertSame('SELECT * FROM t WHERE f1 = :p1 AND f2 = :p2', $q->toSql());
        $this->assertEquals(['p1' => 1, 'p2' => 2], $q->getParams());
    }

    //endregion

    //region HAVING

    public function testHavingColumnOpValue(): void
    {
        $q = (new Query())
            ->from('t')
            ->having('f', '=', 1);

        $this->assertSame('SELECT * FROM t HAVING f = :p1', $q->toSql());
        $this->assertEquals(['p1' => 1], $q->getParams());
    }

    public function testHavingColumnOpValueAppend(): void
    {
        $q = (new Query())
            ->from('t')
            ->having('f1', '>', 1)
            ->having('f2', '<', 2);

        $this->assertSame('SELECT * FROM t HAVING f1 > :p1 AND f2 < :p2', $q->toSql());
        $this->assertEquals(['p1' => 1, 'p2' => 2], $q->getParams());
    }

    public function testHavingOpValue(): void
    {
        $q = (new Query())
            ->from('t')
            ->having('NOT', Query::raw('(t.f1 == t.f2)'));

        $this->assertSame('SELECT * FROM t HAVING NOT (t.f1 == t.f2)', $q->toSql());
        $this->assertSame([], $q->getParams());
    }

    public function testHavingWithDifferentConnector(): void
    {
        $q = (new Query())
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
        $q = (new Query())
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
        $q = (new Query())
            ->from('t1')
            ->having((new Query())
                ->from('t2')
                ->select('t2.id')
                ->having('t2.f1', '=', Query::raw('t1.f2'))
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
        $q = (new Query())
            ->from('t')
            ->having('t.f1 = 5')
            ->having(Query::raw('t.f2 = 6'));

        $this->assertSame('SELECT * FROM t HAVING t.f1 = 5 AND t.f2 = 6', $q->toSql());
        $this->assertSame([], $q->getParams());
    }

    //endregion

    //region GROUP BY

    public function testGroupByColumn(): void
    {
        $q = (new Query())
            ->from('t')
            ->groupBy('t.f1');

        $this->assertSame('SELECT * FROM t GROUP BY t.f1', $q->toSql());
    }

    public function testGroupByColumnWithDirection(): void
    {
        $q = (new Query())
            ->from('t')
            ->groupBy('t.f1', 'DESC');

        $this->assertSame('SELECT * FROM t GROUP BY t.f1 DESC', $q->toSql());
    }

    public function testGroupByColumns(): void
    {
        $q = (new Query())
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
        $q = (new Query())
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
        $q = (new Query())
            ->from('t')
            ->groupBy('f1')
            ->groupBy('f2')
            ->groupBy('f3');

        $this->assertSame('SELECT * FROM t GROUP BY f1, f2, f3', $q->toSql());
    }

    public function testGroupByColumnsWithDirectionsAppend(): void
    {
        $q = (new Query())
            ->from('t')
            ->groupBy('f1', 'DESC')
            ->groupBy('f2', '')
            ->groupBy('f3', 'ASC');

        $this->assertSame('SELECT * FROM t GROUP BY f1 DESC, f2, f3 ASC', $q->toSql());
    }

    public function testGroupByQuery(): void
    {
        $q = (new Query())
            ->from('t1')
            ->groupBy((new Query())
                ->from('t2')
                ->select('t2.id'),
                'DESC'
            );

        $this->assertSame('SELECT * FROM t1 GROUP BY (SELECT t2.id FROM t2) DESC', $q->toSql());
    }

    public function testGroupByMixedSources(): void
    {
        $q = (new Query())
            ->from('t1')
            ->groupBy('f1 ASC')
            ->groupBy(Query::raw('f2 DESC'))
            ->groupBy(['f3', 'f4'])
            ->groupBy(['f5' => 'DESC'])
            ->groupBy((new Query())->from('t2')->select('t2.id'));

        $this->assertSame(
            'SELECT * FROM t1 GROUP BY f1 ASC, f2 DESC, f3, f4, f5 DESC, (SELECT t2.id FROM t2)',
            $q->toSql()
        );
    }

    //endregion

    //region ORDER BY

    public function testOrderByColumn(): void
    {
        $q = (new Query())
            ->from('t')
            ->orderBy('t.f1');

        $this->assertSame('SELECT * FROM t ORDER BY t.f1', $q->toSql());
    }

    public function testOrderByColumnWithDirection(): void
    {
        $q = (new Query())
            ->from('t')
            ->orderBy('t.f1', 'DESC');

        $this->assertSame('SELECT * FROM t ORDER BY t.f1 DESC', $q->toSql());
    }

    public function testOrderByColumns(): void
    {
        $q = (new Query())
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
        $q = (new Query())
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
        $q = (new Query())
            ->from('t')
            ->orderBy('f1')
            ->orderBy('f2')
            ->orderBy('f3');

        $this->assertSame('SELECT * FROM t ORDER BY f1, f2, f3', $q->toSql());
    }

    public function testOrderByColumnsWithDirectionsAppend(): void
    {
        $q = (new Query())
            ->from('t')
            ->orderBy('f1', 'DESC')
            ->orderBy('f2', '')
            ->orderBy('f3', 'ASC');

        $this->assertSame('SELECT * FROM t ORDER BY f1 DESC, f2, f3 ASC', $q->toSql());
    }

    public function testOrderByQuery(): void
    {
        $q = (new Query())
            ->from('t1')
            ->orderBy(
                (new Query())
                    ->from('t2')
                    ->select('t2.id'),
                'DESC'
            );

        $this->assertSame('SELECT * FROM t1 ORDER BY (SELECT t2.id FROM t2) DESC', $q->toSql());
    }

    public function testOrderByMixedSources(): void
    {
        $q = (new Query())
            ->from('t1')
            ->orderBy('f1 ASC')
            ->orderBy(Query::raw('f2 DESC'))
            ->orderBy(['f3', 'f4'])
            ->orderBy(['f5' => 'DESC'])
            ->orderBy((new Query())->from('t2')->select('t2.id'));

        $this->assertSame(
            'SELECT * FROM t1 ORDER BY f1 ASC, f2 DESC, f3, f4, f5 DESC, (SELECT t2.id FROM t2)',
            $q->toSql()
        );
    }

    //endregion

    //region LIMIT & OFFSET

    public function testLimit(): void
    {
        $q = (new Query())
            ->from('t')
            ->limit(10);

        $this->assertSame('SELECT * FROM t LIMIT 10', $q->toSql());
    }

    public function testOffset(): void
    {
        $q = (new Query())
            ->from('t')
            ->offset(12);

        $this->assertSame('SELECT * FROM t OFFSET 12', $q->toSql());
    }

    public function testLimitAndOffset(): void
    {
        $q = (new Query())
            ->from('t')
            ->limit(5)
            ->offset(12);

        $this->assertSame('SELECT * FROM t LIMIT 5 OFFSET 12', $q->toSql());
    }

    public function testPage(): void
    {
        $q = (new Query())
            ->from('t')
            ->paginate(3, 7);

        $this->assertSame('SELECT * FROM t LIMIT 7 OFFSET 21', $q->toSql());
    }

    //endregion

    //region UNION

    public function testUnionOfTwoQueries(): void
    {
        $s = (new Query())
            ->from('t2');

        $q = (new Query())
            ->from('t1')
            ->union($s);

        $this->assertSame('(SELECT * FROM t1) UNION (SELECT * FROM t2)', $q->toSql());
    }

    public function testUnionOfQueriesWithSorting(): void
    {
        $q = (new Query())
            ->from('t1')
            ->union((new Query())
                ->from('t2')
                ->orderBy('t2.id', 'ASC')
            )
            ->union((new Query())
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
        $q = (new Query())
            ->from('t1')
            ->unionAll((new Query())->from('t2'))
            ->unionDistinct((new Query())->from('t3'))
            ->paginate(10, 5);

        $this->assertSame(
            '(SELECT * FROM t1) UNION ALL (SELECT * FROM t2) UNION DISTINCT ' .
            '(SELECT * FROM t3) LIMIT 5 OFFSET 50',
            $q->toSql()
        );
    }

    //endregion
}