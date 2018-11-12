<?php

namespace AlephTools\DDD\Common\Infrastructure\SqlBuilder\Expressions;

class RawExpression extends AbstractExpression
{
    public function __construct(string $expression)
    {
        $this->sql .= $expression;
    }
}