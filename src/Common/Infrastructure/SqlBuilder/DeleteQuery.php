<?php

namespace AlephTools\DDD\Common\Infrastructure\SqlBuilder;

use RuntimeException;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Traits\FromAware;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Traits\LimitAware;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Traits\JoinAware;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Traits\OrderAware;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Traits\WhereAware;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Traits\ReturningAware;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Expressions\WithExpression;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Expressions\FromExpression;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Expressions\JoinExpression;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Expressions\OrderExpression;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Expressions\WhereExpression;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Expressions\ReturningExpression;

/**
 * Represents the DELETE query.
 */
class DeleteQuery extends AbstractQuery
{
    use FromAware, JoinAware, WhereAware, OrderAware, LimitAware, ReturningAware;

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
     * @param ReturningExpression|null $returning
     * @param WithExpression|null $with
     * @param int|null $limit
     */
    public function __construct(
        QueryExecutor $db = null,
        FromExpression $from = null,
        FromExpression $using = null,
        JoinExpression $join = null,
        WhereExpression $where = null,
        OrderExpression $order = null,
        ReturningExpression $returning = null,
        WithExpression $with = null,
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
        $this->returning = $returning;
        $this->with = $with;
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
        $this->buildWith();
        $this->buildFrom();
        $this->buildUsing();
        $this->buildJoin();
        $this->buildWhere();
        $this->buildOrderBy();
        $this->buildLimit();
        $this->buildReturning();
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

    //endregion
}
