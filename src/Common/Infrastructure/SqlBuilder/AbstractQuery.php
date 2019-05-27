<?php

namespace AlephTools\DDD\Common\Infrastructure\SqlBuilder;

use RuntimeException;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Expressions\AbstractExpression;

abstract class AbstractQuery extends AbstractExpression
{
    /**
     * The query executor instance.
     *
     * @var QueryExecutor
     */
    protected $db;

    /**
     * Contains TRUE if the query has built.
     *
     * @var bool
     */
    protected $built = false;

    public function getQueryExecutor(): QueryExecutor
    {
        return $this->db;
    }

    /**
     * @return AbstractQuery
     */
    abstract public function build();

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

    /**
     * @return void
     * @throws RuntimeException
     */
    protected function validateAndBuild(): void
    {
        if ($this->db === null) {
            throw new RuntimeException('The query executor instance must not be null.');
        }
        $this->build();
    }
}
