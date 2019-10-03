<?php

namespace AlephTools\DDD\Common\Infrastructure\SqlBuilder\Expressions;

use AlephTools\DDD\Common\Infrastructure\SqlBuilder\AbstractQuery;

class WithExpression extends AbstractExpression
{
    public function __construct($query = null, $alias = null, bool $recursive = false)
    {
        if ($query !== null) {
            $this->append($query, $alias, $recursive);
        }
    }

    public function append($query, $alias = null, bool $recursive = false): WithExpression
    {
        if (strlen($this->sql) > 0) {
            $this->sql .= ', ';
        }
        if ($alias === null) {
            $expression = $query;
        } else if (is_object($query)) {
            $expression = [[$query, $alias]];
        } else {
            if (!is_scalar($query)) {
                $query = $this->convertNameToString($query);
            }
            $expression = [$query => $alias];
        }
        $this->sql .= ($recursive ? 'RECURSIVE ' : '' ) . $this->convertNameToString($expression);
        return $this;
    }

    private function convertNameToString($expression): string
    {
        if ($expression instanceof AbstractQuery) {
            $sql = '(' . $expression->toSql() . ')';
            $this->addParams($expression->getParams());
        } else if ($expression instanceof RawExpression) {
            $sql = $expression->toSql();
            $this->addParams($expression->getParams());
        } else if (is_array($expression)) {
            $list = [];
            foreach ($expression as $key => $value) {
                if (is_numeric($key)) {
                    if (is_array($value) && \count($value) === 2) {
                        list($key, $value) = $value;
                    } else {
                        $key = $value;
                        $value = '';
                    }
                }
                $alias = $this->convertNameToString($value);
                $list[] = ($alias === '' ? '' : $alias . ' AS ') . $this->convertNameToString($key);
            }
            $sql = implode(', ', $list);
        } else if ($expression === null) {
            $sql = 'NULL';
        } else {
            $sql = (string)$expression;
        }
        return $sql;
    }
}
