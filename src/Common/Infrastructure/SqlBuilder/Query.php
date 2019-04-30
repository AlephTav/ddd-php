<?php

namespace AlephTools\DDD\Common\Infrastructure\SqlBuilder;

use Generator;
use RuntimeException;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Traits\FromAware;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Traits\LimitAware;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Traits\JoinAware;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Traits\OrderAware;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Traits\WhereAware;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Expressions\AbstractExpression;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Expressions\FromExpression;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Expressions\GroupExpression;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Expressions\HavingExpression;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Expressions\JoinExpression;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Expressions\OrderExpression;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Expressions\SelectExpression;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Expressions\WhereExpression;

/**
 * Represents the SELECT query.
 */
class Query extends AbstractExpression
{
    use FromAware, JoinAware, WhereAware, OrderAware, LimitAware;

    /**
     * The SELECT expression instance.
     *
     * @var SelectExpression
     */
    private $select;

    /**
     * The GROUP BY expression instance.
     *
     * @var GroupExpression
     */
    private $group;

    /**
     * The HAVING expression instance.
     *
     * @var HavingExpression
     */
    private $having;

    /**
     * @var int
     */
    private $offset;

    /**
     * The list of queries to be combined into the single UNION query.
     *
     * @var array
     */
    private $union;

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
     * @param SelectExpression|null $select
     * @param JoinExpression|null $join
     * @param WhereExpression|null $where
     * @param GroupExpression|null $group
     * @param HavingExpression|null $having
     * @param OrderExpression|null $order
     * @param int|null $limit
     * @param int|null $offset
     */
    public function __construct(
        QueryExecutor $executor = null,
        FromExpression $from = null,
        SelectExpression $select = null,
        JoinExpression $join = null,
        WhereExpression $where = null,
        GroupExpression $group = null,
        HavingExpression $having = null,
        OrderExpression $order = null,
        int $limit = null,
        int $offset = null
    )
    {
        $this->executor = $executor;
        $this->from = $from;
        $this->select = $select;
        $this->where = $where;
        $this->join = $join;
        $this->group = $group;
        $this->having = $having;
        $this->order = $order;
        $this->limit = $limit;
        $this->offset = $offset;
    }

    public function getQueryExecutor(): QueryExecutor
    {
        return $this->executor;
    }

    //region SELECT

    public function select($column, $alias = null): Query
    {
        $this->select = $this->select ?? new SelectExpression();
        $this->select->append($column, $alias);
        $this->built = false;
        return $this;
    }

    //endregion

    //region GROUP BY

    public function groupBy($column, $order = null): Query
    {
        $this->group = $this->group ?? new GroupExpression();
        $this->group->append($column, $order);
        $this->built = false;
        return $this;
    }

    //endregion

    //region HAVING

    public function andHaving($column, $operator = null, $value = null): Query
    {
        return $this->having($column, $operator, $value, 'AND');
    }

    public function orHaving($column, $operator = null, $value = null): Query
    {
        return $this->having($column, $operator, $value, 'OR');
    }

    public function having($column, $operator = null, $value = null, string $connector = 'AND'): Query
    {
        $this->having = $this->having ?? new HavingExpression();
        $this->having->with($column, $operator, $value, $connector);
        $this->built = false;
        return $this;
    }

    //endregion

    //region LIMIT & OFFSET

    public function offset(?int $offset): Query
    {
        $this->offset = $offset;
        $this->built = false;
        return $this;
    }

    public function paginate(int $page, int $size): Query
    {
        $this->offset = $size * $page;
        $this->limit = $size;
        $this->built = false;
        return $this;
    }

    //endregion

    //region UNION

    public function union(Query $query): Query
    {
        return $this->typeUnion('UNION', $query);
    }

    public function unionAll(Query $query): Query
    {
        return $this->typeUnion('UNION ALL', $query);
    }

    public function unionDistinct(Query $query): Query
    {
        return $this->typeUnion('UNION DISTINCT', $query);
    }

    private function typeUnion(string $type, Query $query): Query
    {
        if ($this->union) {
            $this->union[] = [$type, $query];
        } else {
            $self = new self(
                $this->executor,
                $this->from,
                $this->select,
                $this->join,
                $this->where,
                $this->group,
                $this->having,
                $this->order,
                $this->limit,
                $this->offset
            );
            $this->union = [
                [$type, $self],
                [$type, $query],
            ];
            $this->from = null;
            $this->select = null;
            $this->where = null;
            $this->join = null;
            $this->group = null;
            $this->having = null;
            $this->order = null;
            $this->limit = null;
            $this->offset = null;
        }
        $this->built = false;
        return $this;
    }

    //endregion

    //region Data Fetching

    /**
     * @return array
     * @throws RuntimeException
     */
    public function rows(): array
    {
        $this->validateAndBuild();
        return $this->executor->rows($this->toSql(), $this->getParams());
    }

    /**
     * @return array
     * @throws RuntimeException
     */
    public function row(): array
    {
        $this->validateAndBuild();
        return $this->executor->row($this->toSql(), $this->getParams());
    }

    /**
     * @param mixed $column
     * @return array
     * @throws RuntimeException
     */
    public function column($column = ''): array
    {
        if ($column !== '') {
            $prevSelect = $this->select;
            $this->select = new SelectExpression($column);
            $this->built = false;
            $result = $this->column();
            $this->select = $prevSelect;
            $this->built = false;
            return $result;
        }
        $this->validateAndBuild();
        return $this->executor->column($this->toSql(), $this->getParams());
    }

    /**
     * @param mixed $column
     * @return mixed
     * @throws RuntimeException
     */
    public function scalar($column = '')
    {
        if ($column !== '') {
            $prevSelect = $this->select;
            $this->select = new SelectExpression($column);
            $this->built = false;
            $result = $this->scalar();
            $this->select = $prevSelect;
            $this->built = false;
            return $result;
        }
        $this->validateAndBuild();
        return $this->executor->scalar($this->toSql(), $this->getParams());
    }

    /**
     * @param string $column
     * @return int
     */
    public function count(string $column = '*'): int
    {
        $prevLimit = $this->limit;
        $prevOffset = $this->offset;
        $prevOrder = $this->order;
        $this->limit = $this->offset = $this->order = null;
        $this->built = false;
        $total = (int)$this->scalar("COUNT($column)");
        $this->order = $prevOrder;
        $this->limit = $prevLimit;
        $this->offset = $prevOffset;
        $this->built = false;
        return $total;
    }

    /**
     * @param int $size
     * @param int $page
     * @return Generator|array[]
     */
    public function pages(int $size = 1000, int $page = 0): Generator
    {
        while (true) {
            $rows = $this
                ->paginate($page, $size)
                ->rows();

            $count = count($rows);
            if ($count > 0) {
                yield from $rows;
            }
            if ($count < $size) {
                break;
            }

            ++$page;
        }
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

    public function build(): Query
    {
        if ($this->built) {
            return $this;
        }
        $this->sql = '';
        $this->params = [];
        if ($this->union) {
            $this->buildUnion();
        } else {
            $this->buildSelect();
            $this->buildFrom();
            $this->buildJoin();
            $this->buildWhere();
            $this->buildHaving();
            $this->buildGroupBy();
        }
        $this->buildOrderBy();
        $this->buildLimit();
        $this->buildOffset();
        $this->built = true;
        return $this;
    }

    private function buildUnion(): void
    {
        $first = true;
        foreach ($this->union as [$unionType, $query]) {
            if (!$first) {
                $this->sql .= ' ' . $unionType . ' ';
            }
            $this->sql .= '(' . $query->toSql() . ')';
            $this->addParams($query->getParams());
            $first = false;
        }
    }

    private function buildSelect(): void
    {
        if ($this->select) {
            $this->sql .= 'SELECT ' . $this->select->toSql();
            $this->addParams($this->select->getParams());
        } else {
            $this->sql .= 'SELECT *';
        }
    }

    private function buildFrom(): void
    {
        if ($this->from) {
            $this->sql .= ' FROM ' . $this->from->toSql();
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

    private function buildHaving(): void
    {
        if ($this->having) {
            $this->sql .= ' HAVING ' . $this->having->toSql();
            $this->addParams($this->having->getParams());
        }
    }

    private function buildGroupBy(): void
    {
        if ($this->group) {
            $this->sql .= ' GROUP BY ' . $this->group->toSql();
            $this->addParams($this->group->getParams());
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

    private function buildOffset(): void
    {
        if ($this->offset !== null) {
            $this->sql .= ' OFFSET ' . $this->offset;
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
