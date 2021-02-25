<?php

namespace AlephTools\DDD\Common\Infrastructure\SqlBuilder\Traits;

trait LimitAware
{
    /**
     * @var int
     */
    private $limit;

    /**
     * @param int|null $limit
     * @return static
     */
    public function limit(?int $limit)
    {
        $this->limit = $limit;
        $this->built = false;
        return $this;
    }

    private function buildLimit(): void
    {
        if ($this->limit !== null) {
            $this->sql .= ' LIMIT ' . $this->limit;
        }
    }
}
