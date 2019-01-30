<?php

namespace AlephTools\DDD\Common\Infrastructure\SqlBuilder\Expressions;

class RawExpression extends AbstractExpression
{
    public function __construct(string $expression, array $params = [])
    {
        $this->sql .= $expression;
        $this->params = $params;
    }
}
