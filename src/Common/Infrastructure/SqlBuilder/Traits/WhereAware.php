<?php

namespace AlephTools\DDD\Common\Infrastructure\SqlBuilder\Traits;

use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Expressions\WhereExpression;

trait WhereAware
{
    /**
     * The WHERE clause.
     *
     * @var WhereExpression
     */
    private $where;

    public function andWhere($column, $operator = null, $value = null): self
    {
        return $this->where($column, $operator, $value, 'AND');
    }

    public function orWhere($column, $operator = null, $value = null): self
    {
        return $this->where($column, $operator, $value, 'OR');
    }

    public function where($column, $operator = null, $value = null, string $connector = 'AND'): self
    {
        $this->where = $this->where ?? new WhereExpression();
        $this->where->with($column, $operator, $value, $connector);
        $this->built = false;
        return $this;
    }

    private function buildWhere(): void
    {
        if ($this->where) {
            $this->sql .= ' WHERE ' . $this->where->toSql();
            $this->addParams($this->where->getParams());
        }
    }
}
