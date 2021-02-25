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

    /**
     * @param mixed $table
     * @param mixed $conditions
     * @return static
     */
    public function join($table, $conditions = null)
    {
        return $this->typeJoin('JOIN', $table, $conditions);
    }

    /**
     * @param mixed $table
     * @param mixed $conditions
     * @return static
     */
    public function innerJoin($table, $conditions = null)
    {
        return $this->typeJoin('INNER JOIN', $table, $conditions);
    }

    /**
     * @param mixed $table
     * @param mixed $conditions
     * @return static
     */
    public function crossJoin($table, $conditions = null)
    {
        return $this->typeJoin('CROSS JOIN', $table, $conditions);
    }

    /**
     * @param mixed $table
     * @param mixed $conditions
     * @return static
     */
    public function leftJoin($table, $conditions = null)
    {
        return $this->typeJoin('LEFT JOIN', $table, $conditions);
    }

    /**
     * @param mixed $table
     * @param mixed $conditions
     * @return static
     */
    public function rightJoin($table, $conditions = null)
    {
        return $this->typeJoin('RIGHT JOIN', $table, $conditions);
    }

    /**
     * @param mixed $table
     * @param mixed $conditions
     * @return static
     */
    public function leftOuterJoin($table, $conditions = null)
    {
        return $this->typeJoin('LEFT OUTER JOIN', $table, $conditions);
    }

    /**
     * @param mixed $table
     * @param mixed $conditions
     * @return static
     */
    public function rightOuterJoin($table, $conditions = null)
    {
        return $this->typeJoin('RIGHT OUTER JOIN', $table, $conditions);
    }

    /**
     * @param mixed $table
     * @param mixed $conditions
     * @return static
     */
    public function naturalLeftJoin($table, $conditions = null)
    {
        return $this->typeJoin('NATURAL LEFT JOIN', $table, $conditions);
    }

    /**
     * @param mixed $table
     * @param mixed $conditions
     * @return static
     */
    public function naturalRightJoin($table, $conditions = null)
    {
        return $this->typeJoin('NATURAL RIGHT JOIN', $table, $conditions);
    }

    /**
     * @param mixed $table
     * @param mixed $conditions
     * @return static
     */
    public function naturalLeftOuterJoin($table, $conditions = null)
    {
        return $this->typeJoin('NATURAL LEFT OUTER JOIN', $table, $conditions);
    }

    /**
     * @param mixed $table
     * @param mixed $conditions
     * @return static
     */
    public function naturalRightOuterJoin($table, $conditions = null)
    {
        return $this->typeJoin('NATURAL RIGHT OUTER JOIN', $table, $conditions);
    }

    /**
     * @param mixed $table
     * @param mixed $conditions
     * @return static
     */
    public function straightJoin($table, $conditions = null)
    {
        return $this->typeJoin('STRAIGHT_JOIN', $table, $conditions);
    }

    /**
     * @param string $type
     * @param mixed $table
     * @param mixed $conditions
     * @return static
     */
    private function typeJoin(string $type, $table, $conditions = null)
    {
        $this->join = $this->join ?? new JoinExpression();
        $this->join->append($type, $table, $conditions);
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
