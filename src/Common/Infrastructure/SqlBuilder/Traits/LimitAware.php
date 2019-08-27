<?php

namespace AlephTools\DDD\Common\Infrastructure\SqlBuilder\Traits;

trait LimitAware
{
    /**
     * @var int
     */
    private $limit;

    public function limit(?int $limit): self
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
