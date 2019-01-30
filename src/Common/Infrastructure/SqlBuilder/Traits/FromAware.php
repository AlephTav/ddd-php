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

    public function from($table, $alias = null): self
    {
        $this->from = $this->from ?? new FromExpression();
        $this->from->append($table, $alias);
        $this->built = false;
        return $this;
    }
}
