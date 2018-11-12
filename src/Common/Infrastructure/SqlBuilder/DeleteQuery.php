<?php

namespace AlephTools\DDD\Common\Infrastructure\SqlBuilder;

use RuntimeException;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Expressions\AbstractExpression;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Expressions\FromExpression;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Expressions\JoinExpression;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Expressions\OrderExpression;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Expressions\WhereExpression;

/**
 * Represents the DELETE query.
 */
class DeleteQuery extends AbstractExpression
{
    /**
     * The FROM expression instance.
     *
     * @var FromExpression
     */
    private $from;

    /**
     * The USING expression instance.
     *
     * @var FromExpression
     */
    private $using;

    /**
     * The JOIN expression instance.
     *
     * @var JoinExpression
     */
    private $join;

    /**
     * The WHERE clause.
     *
     * @var WhereExpression
     */
    private $where;

    /**
     * The ORDER clause.
     *
     * @var OrderExpression
     */
    private $order;

    /**
     * @var int
     */
    private $limit;

    /**
     * The query executor instance.
     *
     * @var QueryExecutor
     */
    private $executor;

    /**
     * Contains TRUE if the query has built.
     *
     * @var bool
     */
    private $built = false;

    /**
     * Constructor.
     *
     * @param QueryExecutor|null $executor
     * @param FromExpression|null $from
     * @param FromExpression|null $using
     * @param JoinExpression|null $join
     * @param WhereExpression|null $where
     * @param OrderExpression|null $order
     * @param int|null $limit
     */
    public function __construct(
        QueryExecutor $executor = null,
        FromExpression $from = null,
        FromExpression $using = null,
        JoinExpression $join = null,
        WhereExpression $where = null,
        OrderExpression $order = null,
        int $limit = null
    )
    {
        $this->executor = $executor;
        $this->from = $from;
        $this->using = $using;
        $this->where = $where;
        $this->join = $join;
        $this->order = $order;
        $this->limit = $limit;
    }

    public function getQueryExecutor(): QueryExecutor
    {
        return $this->executor;
    }

    //region FROM

    public function from($table, $alias = null): DeleteQuery
    {
        $this->from = $this->from ?? new FromExpression();
        $this->from->append($table, $alias);
        $this->built = false;
        return $this;
    }

    //endregion

    //region USING

    public function using($table, $alias = null): DeleteQuery
    {
        $this->using = $this->using ?? new FromExpression();
        $this->using->append($table, $alias);
        $this->built = false;
        return $this;
    }

    //endregion

    //region JOIN

    public function join($table, $conditions = null): DeleteQuery
    {
        return $this->typeJoin('JOIN', $table, $conditions);
    }

    public function innerJoin($table, $conditions = null): DeleteQuery
    {
        return $this->typeJoin('INNER JOIN', $table, $conditions);
    }

    public function crossJoin($table, $conditions = null): DeleteQuery
    {
        return $this->typeJoin('CROSS JOIN', $table, $conditions);
    }

    public function leftJoin($table, $conditions = null): DeleteQuery
    {
        return $this->typeJoin('LEFT JOIN', $table, $conditions);
    }

    public function rightJoin($table, $conditions = null): DeleteQuery
    {
        return $this->typeJoin('RIGHT JOIN', $table, $conditions);
    }

    public function leftOuterJoin($table, $conditions = null): DeleteQuery
    {
        return $this->typeJoin('LEFT OUTER JOIN', $table, $conditions);
    }

    public function rightOuterJoin($table, $conditions = null): DeleteQuery
    {
        return $this->typeJoin('RIGHT OUTER JOIN', $table, $conditions);
    }

    public function naturalLeftJoin($table, $conditions = null): DeleteQuery
    {
        return $this->typeJoin('NATURAL LEFT JOIN', $table, $conditions);
    }

    public function naturalRightJoin($table, $conditions = null): DeleteQuery
    {
        return $this->typeJoin('NATURAL RIGHT JOIN', $table, $conditions);
    }

    public function naturalLeftOuterJoin($table, $conditions = null): DeleteQuery
    {
        return $this->typeJoin('NATURAL LEFT OUTER JOIN', $table, $conditions);
    }

    public function naturalRightOuterJoin($table, $conditions = null): DeleteQuery
    {
        return $this->typeJoin('NATURAL RIGHT OUTER JOIN', $table, $conditions);
    }

    public function straightJoin($table, $conditions = null): DeleteQuery
    {
        return $this->typeJoin('STRAIGHT_JOIN', $table, $conditions);
    }

    private function typeJoin(string $type, $table, $conditions = null): DeleteQuery
    {
        $this->join = $this->join ?? new JoinExpression();
        $this->join->append($type, $table, $conditions);
        $this->built = false;
        return $this;
    }

    //endregion

    //region WHERE

    public function andWhere($column, $operator = null, $value = null): DeleteQuery
    {
        return $this->where($column, $operator, $value, 'AND');
    }

    public function orWhere($column, $operator = null, $value = null): DeleteQuery
    {
        return $this->where($column, $operator, $value, 'OR');
    }

    public function where($column, $operator = null, $value = null, string $connector = 'AND'): DeleteQuery
    {
        $this->where = $this->where ?? new WhereExpression();
        $this->where->with($column, $operator, $value, $connector);
        $this->built = false;
        return $this;
    }

    //endregion

    //region ORDER BY

    public function orderBy($column, $order = null): DeleteQuery
    {
        $this->order = $this->order ?? new OrderExpression();
        $this->order->append($column, $order);
        $this->built = false;
        return $this;
    }

    //endregion

    //region LIMIT

    public function limit(?int $limit): DeleteQuery
    {
        $this->limit = $limit;
        $this->built = false;
        return $this;
    }

    //endregion

    //region Execution

    public function exec(): int
    {
        $this->validateAndBuild();
        return $this->executor->execute($this->toSql(), $this->getParams());
    }

    /**
     * @return void
     * @throws RuntimeException
     */
    private function validateAndBuild(): void
    {
        if ($this->executor === null) {
            throw new RuntimeException('The query executor instance must not be null.');
        }
        $this->build();
    }

    //endregion

    //region Query Building

    public function build(): DeleteQuery
    {
        if ($this->built) {
            return $this;
        }
        $this->sql = '';
        $this->params = [];
        $this->buildFrom();
        $this->buildUsing();
        $this->buildJoin();
        $this->buildWhere();
        $this->buildOrderBy();
        $this->buildLimit();
        $this->built = true;
        return $this;
    }

    private function buildFrom(): void
    {
        $this->sql .= 'DELETE FROM ';
        if ($this->from) {
            $this->sql .= $this->from->toSql();
            $this->addParams($this->from->getParams());
        }
    }

    private function buildUsing(): void
    {
        if ($this->using) {
            $this->sql .= ' USING ' . $this->from->toSql();
            $this->addParams($this->from->getParams());
        }
    }

    private function buildJoin(): void
    {
        if ($this->join) {
            $this->sql .= ' ' . $this->join->toSql();
            $this->addParams($this->join->getParams());
        }
    }

    private function buildWhere(): void
    {
        if ($this->where) {
            $this->sql .= ' WHERE ' . $this->where->toSql();
            $this->addParams($this->where->getParams());
        }
    }

    private function buildOrderBy(): void
    {
        if ($this->order) {
            $this->sql .= ' ORDER BY ' . $this->order->toSql();
            $this->addParams($this->order->getParams());
        }
    }

    private function buildLimit(): void
    {
        if ($this->limit !== null) {
            $this->sql .= ' LIMIT ' . $this->limit;
        }
    }

    //endregion

    public function toSql(): string
    {
        $this->build();
        return parent::toSql();
    }

    public function getParams(): array
    {
        $this->build();
        return parent::getParams();
    }
}