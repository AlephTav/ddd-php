<?php

namespace AlephTools\DDD\Common\Infrastructure\SqlBuilder;

use RuntimeException;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Traits\FromAware;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Traits\LimitAware;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Traits\JoinAware;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Traits\OrderAware;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Traits\WhereAware;
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
    use FromAware, JoinAware, WhereAware, OrderAware, LimitAware;

    /**
     * The USING expression instance.
     *
     * @var FromExpression
     */
    private $using;

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
