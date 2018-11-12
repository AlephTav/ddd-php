<?php

namespace AlephTools\DDD\Common\Infrastructure\SqlBuilder\Expressions;

use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Query;

class ConditionalExpression extends AbstractExpression
{
    public function __construct($column = null, $operator = null, $value = null, string $connector = 'AND')
    {
        if ($column !== null) {
            $this->with($column, $operator, $value, $connector);
        }
    }

    public function and($column, $operator = null, $value = null): ConditionalExpression
    {
        return $this->with($column, $operator, $value, 'AND');
    }

    public function or($column, $operator = null, $value = null): ConditionalExpression
    {
        return $this->with($column, $operator, $value, 'OR');
    }

    public function with($column, $operator = null, $value = null, string $connector = 'AND'): ConditionalExpression
    {
        if ($this->sql !== '') {
            $this->sql .= ' ' . $connector . ' ';
        }

        if ($operator !== null) {
            if (is_string($operator)) {
                $this->sql .= $this->convertOperandToString($column) . ' ' .
                    $operator . ' ' . $this->convertValueToString($value, $operator);
            } else {
                $this->sql .= $column . ' ' . $this->convertValueToString($operator, $column);
            }
        } else {
            $this->sql .= $this->convertOperandToString($column);
        }

        return $this;
    }

    private function convertOperandToString($expression): string
    {
        if ($expression instanceof Query || $expression instanceof self) {
            $sql = '(' . $expression->toSql() . ')';
            $this->addParams($expression->getParams());
        } else if ($expression instanceof RawExpression) {
            $sql = $expression->toSql();
        } else if (is_array($expression)) {
            $list = [];
            foreach ($expression as $key => $value) {
                if (is_numeric($key)) {
                    $list[] = $this->convertOperandToString($value);
                } else {
                    $list[] = $this->convertOperandToString($key) . ' = ' . $this->convertValueToString($value, '=');
                }
            }
            $sql = implode(' AND ', $list);
        } else if ($expression instanceof \Closure) {
            $conditions = new ConditionalExpression();
            $expression($conditions);
            $sql = $this->convertOperandToString($conditions);
        } else if ($expression === null) {
            $sql = 'NULL';
        } else {
            $sql = (string)$expression;
        }
        return $sql;
    }

    private function convertValueToString($expression, string $operator): string
    {
        if ($expression instanceof Query) {
            $sql = '(' . $expression->toSql() . ')';
            $this->addParams($expression->getParams());
        } else if ($expression instanceof RawExpression) {
            $sql = $expression->toSql();
        } else if (is_array($expression)) {
            $isBetween = $this->isBetween($operator);
            $list = [];
            foreach ($expression as $value) {
                $list[] = $this->convertValueToString($value, $operator);
            }
            $sql = implode($isBetween ? ' AND ' : ', ', $list);
            if (!$isBetween) {
                $sql = '(' . $sql . ')';
            }
        } else if ($expression === null) {
            $sql = 'NULL';
        } else {
            $param = self::nextParameterName();
            $sql = ':' . $param;
            $this->params[$param] = $expression;
        }
        return $sql;
    }

    private function isBetween(string $operator): bool
    {
        $op = strtolower($operator);
        return $op === 'between' || $op === 'not between';
    }
}