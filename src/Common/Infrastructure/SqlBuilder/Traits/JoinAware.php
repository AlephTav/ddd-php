<?php

namespace AlephTools\DDD\Common\Infrastructure\SqlBuilder\Traits;

use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Expressions\JoinExpression;

trait JoinAware
{
    /**
     * The JOIN expression instance.
     *
     * @var JoinExpression
     */
    private $join;

    public function join($table, $conditions = null): self
    {
        return $this->typeJoin('JOIN', $table, $conditions);
    }

    public function innerJoin($table, $conditions = null): self
    {
        return $this->typeJoin('INNER JOIN', $table, $conditions);
    }

    public function crossJoin($table, $conditions = null): self
    {
        return $this->typeJoin('CROSS JOIN', $table, $conditions);
    }

    public function leftJoin($table, $conditions = null): self
    {
        return $this->typeJoin('LEFT JOIN', $table, $conditions);
    }

    public function rightJoin($table, $conditions = null): self
    {
        return $this->typeJoin('RIGHT JOIN', $table, $conditions);
    }

    public function leftOuterJoin($table, $conditions = null): self
    {
        return $this->typeJoin('LEFT OUTER JOIN', $table, $conditions);
    }

    public function rightOuterJoin($table, $conditions = null): self
    {
        return $this->typeJoin('RIGHT OUTER JOIN', $table, $conditions);
    }

    public function naturalLeftJoin($table, $conditions = null): self
    {
        return $this->typeJoin('NATURAL LEFT JOIN', $table, $conditions);
    }

    public function naturalRightJoin($table, $conditions = null): self
    {
        return $this->typeJoin('NATURAL RIGHT JOIN', $table, $conditions);
    }

    public function naturalLeftOuterJoin($table, $conditions = null): self
    {
        return $this->typeJoin('NATURAL LEFT OUTER JOIN', $table, $conditions);
    }

    public function naturalRightOuterJoin($table, $conditions = null): self
    {
        return $this->typeJoin('NATURAL RIGHT OUTER JOIN', $table, $conditions);
    }

    public function straightJoin($table, $conditions = null): self
    {
        return $this->typeJoin('STRAIGHT_JOIN', $table, $conditions);
    }

    private function typeJoin(string $type, $table, $conditions = null): self
    {
        if ($this->join === null) {
            $this->join = new JoinExpression($type, $table, $conditions);
        } else {
            $this->join->append($type, $table, $conditions);
        }
        $this->built = false;
        return $this;
    }

    private function buildJoin(): void
    {
        if ($this->join) {
            $this->sql .= ' ' . $this->join->toSql();
            $this->addParams($this->join->getParams());
        }
    }
}
