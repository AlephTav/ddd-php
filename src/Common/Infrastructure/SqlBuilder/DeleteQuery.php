<?php

namespace AlephTools\DDD\Common\Infrastructure\SqlBuilder;

use RuntimeException;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Traits\FromAware;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Traits\LimitAware;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Traits\JoinAware;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Traits\OrderAware;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Traits\WhereAware;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Expressions\FromExpression;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Expressions\JoinExpression;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Expressions\OrderExpression;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Expressions\WhereExpression;

/**
 * Represents the DELETE query.
 */
class DeleteQuery extends AbstractQuery
{
    use FromAware, JoinAware, WhereAware, OrderAware, LimitAware;

    /**
     * The USING expression instance.
     *
     * @var FromExpression
     */
    private $using;

    /**
     * Constructor.
     *
     * @param QueryExecutor|null $db
     * @param FromExpression|null $from
     * @param FromExpression|null $using
     * @param JoinExpression|null $join
     * @param WhereExpression|null $where
     * @param OrderExpression|null $order
     * @param int|null $limit
     */
    public function __construct(
        QueryExecutor $db = null,
        FromExpression $from = null,
        FromExpression $using = null,
        JoinExpression $join = null,
        WhereExpression $where = null,
        OrderExpression $order = null,
        int $limit = null
    )
    {
        $this->db = $db;
        $this->from = $from;
        $this->using = $using;
        $this->where = $where;
        $this->join = $join;
        $this->order = $order;
        $this->limit = $limit;
    }

    //region USING

    public function using($table, $alias = null): DeleteQuery
    {
        $this->using = $this->using ?? new FromExpression();
        $this->using->append($table, $alias);
        $this->built = false;
        return $this;
    }

    //endregion

    //region Execution

    /**
     * Executes this delete query.
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
}
