<?php

namespace AlephTools\DDD\Common\Infrastructure\SqlBuilder;

use RuntimeException;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Traits\FromAware;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Traits\JoinAware;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Traits\LimitAware;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Traits\OrderAware;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Traits\WhereAware;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Expressions\FromExpression;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Expressions\JoinExpression;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Expressions\AssignmentExpression;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Expressions\OrderExpression;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Expressions\WhereExpression;

/**
 * Represents the UPDATE query.
 */
class UpdateQuery extends AbstractQuery
{
    use FromAware, JoinAware, WhereAware, OrderAware, LimitAware;

    /**
     * The SET expression instance.
     *
     * @var AssignmentExpression
     */
    private $assignment;

    public function __construct(
        QueryExecutor $db = null,
        FromExpression $from = null,
        JoinExpression $join = null,
        AssignmentExpression $assignment = null,
        WhereExpression $where = null,
        OrderExpression $order = null,
        ?int $limit = null
    )
    {
        $this->db = $db;
        $this->from = $from;
        $this->where = $where;
        $this->join = $join;
        $this->assignment = $assignment;
        $this->order = $order;
        $this->limit = $limit;
    }

    //region FROM

    public function table($table, $alias = null): UpdateQuery
    {
        return $this->from($table, $alias);
    }

    //endregion

    //region SET (Assignment List)

    public function assign($column, $value = null): UpdateQuery
    {
        $this->assignment = $this->assignment ?? new AssignmentExpression();
        $this->assignment->append($column, $value);
        $this->built = false;
        return $this;
    }

    //endregion

    //region Execution

    /**
     * Executes this update query.
     *
     * @return int
     * @throws RuntimeException
     */
    public function exec(): int
    {
        $this->validateAndBuild();
        return $this->db->execute($this->toSql(), $this->getParams());
    }

    //endregion

    //region Query Building

    public function build(): UpdateQuery
    {
        if ($this->built) {
            return $this;
        }
        $this->sql = '';
        $this->params = [];
        $this->buildFrom();
        $this->buildJoin();
        $this->buildAssignment();
        $this->buildWhere();
        $this->buildOrderBy();
        $this->buildLimit();
        $this->built = true;
        return $this;
    }

    private function buildFrom(): void
    {
        $this->sql .= 'UPDATE ';
        if ($this->from) {
            $this->sql .= $this->from->toSql();
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

    private function buildAssignment(): void
    {
        if ($this->assignment) {
            $this->sql .= ' SET ';
            $this->sql .= $this->assignment->toSql();
            $this->addParams($this->assignment->getParams());
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
}
