<?php

namespace AlephTools\DDD\Common\Infrastructure\SqlBuilder\Traits;

use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Expressions\OrderExpression;

trait OrderAware
{
    /**
     * The ORDER BY expression instance.
     *
     * @var OrderExpression
     */
    private $order;

    public function orderBy($column, $order = null): self
    {
        $this->order = $this->order ?? new OrderExpression();
        $this->order->append($column, $order);
        $this->built = false;
        return $this;
    }
}
