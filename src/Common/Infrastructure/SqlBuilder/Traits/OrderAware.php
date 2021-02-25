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

    /**
     * @param mixed $column
     * @param mixed $order
     * @return static
     */
    public function orderBy($column, $order = null)
    {
        $this->order = $this->order ?? new OrderExpression();
        $this->order->append($column, $order);
        $this->built = false;
        return $this;
    }

    private function buildOrderBy(): void
    {
        if ($this->order) {
            $this->sql .= ' ORDER BY ' . $this->order->toSql();
            $this->addParams($this->order->getParams());
        }
    }
}
