<?php

namespace AlephTools\DDD\Common\Infrastructure\SqlBuilder\Expressions;

use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Query;

class ListExpression extends AbstractExpression
{
    public function __construct($column = null, $order = null)
    {
        if ($column !== null) {
            $this->append($column, $order);
        }
    }

    public function append($column, $order = null): ListExpression
    {
        if (strlen($this->sql) > 0) {
            $this->sql .= ', ';
        }
        if ($order === null) {
            $expression = $column;
        } else if (is_object($column)) {
            $expression = [[$column, $order]];
        } else {
            $expression = [$column => $order];
        }
        $this->sql .= $this->convertNameToString($expression);
        return $this;
    }

    private function convertNameToString($expression): string
    {
        if ($expression instanceof Query) {
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
                $list[] = $this->convertNameToString($key) . ($alias === '' ? '' : ' ' . $alias);
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
