<?php

namespace AlephTools\DDD\Common\Infrastructure\SqlBuilder;

use Generator;
use RuntimeException;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Traits\FromAware;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Traits\LimitAware;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Traits\JoinAware;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Traits\OrderAware;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Traits\WhereAware;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Expressions\FromExpression;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Expressions\GroupExpression;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Expressions\HavingExpression;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Expressions\JoinExpression;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Expressions\OrderExpression;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Expressions\SelectExpression;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Expressions\WhereExpression;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Expressions\ValueListExpression;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Expressions\WithExpression;

/**
 * Represents the SELECT query.
 */
class SelectQuery extends AbstractQuery
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
     * The VALUES expression instance.
     *
     * @var ValueListExpression
     */
    private $values;

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
     * Constructor.
     *
     * @param QueryExecutor|null $db
     * @param FromExpression|null $from
     * @param SelectExpression|null $select
     * @param JoinExpression|null $join
     * @param WhereExpression|null $where
     * @param GroupExpression|null $group
     * @param HavingExpression|null $having
     * @param OrderExpression|null $order
     * @param ValueListExpression|null $values
     * @param WithExpression|null $with
     * @param int|null $limit
     * @param int|null $offset
     */
    public function __construct(
        QueryExecutor $db = null,
        FromExpression $from = null,
        SelectExpression $select = null,
        JoinExpression $join = null,
        WhereExpression $where = null,
        GroupExpression $group = null,
        HavingExpression $having = null,
        OrderExpression $order = null,
        ValueListExpression $values = null,
        WithExpression $with = null,
        int $limit = null,
        int $offset = null
    )
    {
        $this->db = $db;
        $this->from = $from;
        $this->select = $select;
        $this->where = $where;
        $this->join = $join;
        $this->group = $group;
        $this->having = $having;
        $this->order = $order;
        $this->values = $values;
        $this->with = $with;
        $this->limit = $limit;
        $this->offset = $offset;
    }

    //region SELECT

    public function select($column, $alias = null): SelectQuery
    {
        $this->select = $this->select ?? new SelectExpression();
        $this->select->append($column, $alias);
        $this->built = false;
        return $this;
    }

    //endregion

    //region GROUP BY

    public function groupBy($column, $order = null): SelectQuery
    {
        $this->group = $this->group ?? new GroupExpression();
        $this->group->append($column, $order);
        $this->built = false;
        return $this;
    }

    //endregion

    //region HAVING

    public function andHaving($column, $operator = null, $value = null): SelectQuery
    {
        return $this->having($column, $operator, $value, 'AND');
    }

    public function orHaving($column, $operator = null, $value = null): SelectQuery
    {
        return $this->having($column, $operator, $value, 'OR');
    }

    public function having($column, $operator = null, $value = null, string $connector = 'AND'): SelectQuery
    {
        $this->having = $this->having ?? new HavingExpression();
        $this->having->with($column, $operator, $value, $connector);
        $this->built = false;
        return $this;
    }

    //endregion

    //region VALUES

    public function values($values, string $alias = ''): SelectQuery
    {
        $this->values = $this->values ?? new ValueListExpression();
        $this->values->append($values, $alias);
        $this->built = false;
        return $this;
    }

    //endregion

    //region LIMIT & OFFSET

    public function offset(?int $offset): SelectQuery
    {
        $this->offset = $offset;
        $this->built = false;
        return $this;
    }

    public function paginate(int $page, int $size): SelectQuery
    {
        $this->offset = $size * $page;
        $this->limit = $size;
        $this->built = false;
        return $this;
    }

    //endregion

    //region UNION

    public function union(SelectQuery $query): SelectQuery
    {
        return $this->typeUnion('UNION', $query);
    }

    public function unionAll(SelectQuery $query): SelectQuery
    {
        return $this->typeUnion('UNION ALL', $query);
    }

    public function unionDistinct(SelectQuery $query): SelectQuery
    {
        return $this->typeUnion('UNION DISTINCT', $query);
    }

    public function intersect(SelectQuery $query): SelectQuery
    {
        return $this->typeUnion('INTERSECT', $query);
    }

    public function intersectAll(SelectQuery $query): SelectQuery
    {
        return $this->typeUnion('INTERSECT ALL', $query);
    }

    public function except(SelectQuery $query): SelectQuery
    {
        return $this->typeUnion('EXCEPT', $query);
    }

    public function exceptAll(SelectQuery $query): SelectQuery
    {
        return $this->typeUnion('EXCEPT ALL', $query);
    }

    private function typeUnion(string $type, SelectQuery $query): SelectQuery
    {
        if ($this->union) {
            $this->union[] = [$type, $query];
        } else {
            $self = new self(
                $this->db,
                $this->from,
                $this->select,
                $this->join,
                $this->where,
                $this->group,
                $this->having,
                $this->order,
                $this->values,
                $this->with,
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
            $this->values = null;
            $this->with = null;
            $this->limit = null;
            $this->offset = null;
        }
        $this->built = false;
        return $this;
    }

    //endregion

    //region Data Fetching

    /**
     * @param mixed $column
     * @return array
     * @throws RuntimeException
     */
    public function column($column = ''): array
    {
        if ($column !== '') {
            $prevSelect = $this->select;
            $this->select = (new SelectExpression())->append($column);
            $this->built = false;
            $result = $this->column();
            $this->select = $prevSelect;
            $this->built = false;
            return $result;
        }
        $this->validateAndBuild();
        return $this->db->column($this->toSql(), $this->getParams());
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
            $this->select = (new SelectExpression())->append($column);
            $this->built = false;
            $result = $this->scalar();
            $this->select = $prevSelect;
            $this->built = false;
            return $result;
        }
        $this->validateAndBuild();
        return $this->db->scalar($this->toSql(), $this->getParams());
    }

    /**
     * @param string $column
     * @param bool $clearNonConditionalClauses
     * @return int
     */
    public function count(string $column = '*', bool $clearNonConditionalClauses = true): int
    {
        $this->built = false;
        if ($clearNonConditionalClauses) {
            $prevLimit = $this->limit;
            $prevOffset = $this->offset;
            $prevOrder = $this->order;
            $prevGroup = $this->group;
            $this->limit = $this->offset = $this->order = $this->group = null;
            $total = $this->scalar("COUNT($column)");
            $this->order = $prevOrder;
            $this->limit = $prevLimit;
            $this->offset = $prevOffset;
            $this->group = $prevGroup;
        } else {
            $total = $this->scalar("COUNT($column)");
        }
        $this->built = false;
        return (int)$total;
    }

    /**
     * @param int $size
     * @param int $page
     * @return Generator|array[]
     */
    public function pages(int $size = 1000, int $page = 0): Generator
    {
        if ($size <= 0) {
            return;
        }
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
     * @param int $size
     * @param int $page
     * @return Generator|array[]
     */
    public function batches(int $size = 1000, int $page = 0): Generator
    {
        if ($size <= 0) {
            return;
        }
        while (true) {
            $rows = $this
                ->paginate($page, $size)
                ->rows();

            $count = count($rows);
            if ($count > 0) {
                yield $rows;
            }
            if ($count < $size) {
                break;
            }

            ++$page;
        }
    }

    //endregion

    //region Query Building

    public function build(): SelectQuery
    {
        if ($this->built) {
            return $this;
        }
        $this->sql = '';
        $this->params = [];
        if ($this->union) {
            $this->buildUnion();
        } else {
            $this->buildWith();
            if ($this->values) {
                $this->buildValues();
            } else {
                $this->buildSelect();
            }
            $this->buildFrom();
            $this->buildJoin();
            $this->buildWhere();
            $this->buildGroupBy();
            $this->buildHaving();
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

    private function buildValues(): void
    {
        $this->sql .= 'VALUES ';
        if ($this->values) {
            $this->sql .= $this->values->toSql();
            $this->addParams($this->values->getParams());
        }
    }

    private function buildFrom(): void
    {
        if ($this->from) {
            $this->sql .= ' FROM ' . $this->from->toSql();
            $this->addParams($this->from->getParams());
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

    private function buildOffset(): void
    {
        if ($this->offset !== null) {
            $this->sql .= ' OFFSET ' . $this->offset;
        }
    }

    //endregion
}
