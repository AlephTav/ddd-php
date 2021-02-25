<?php

namespace AlephTools\DDD\Common\Infrastructure\SqlBuilder\Traits;

use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Expressions\FromExpression;

trait FromAware
{
    /**
     * The FROM expression instance.
     *
     * @var FromExpression
     */
    private $from;

    /**
     * @param mixed $table
     * @param mixed $alias
     * @return static
     */
    public function from($table, $alias = null)
    {
        $this->from = $this->from ?? new FromExpression();
        $this->from->append($table, $alias);
        $this->built = false;
        return $this;
    }
}
