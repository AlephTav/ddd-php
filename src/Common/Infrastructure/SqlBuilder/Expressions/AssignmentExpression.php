<?php

namespace AlephTools\DDD\Common\Infrastructure\SqlBuilder\Expressions;

use AlephTools\DDD\Common\Infrastructure\SqlBuilder\SelectQuery;

class AssignmentExpression extends AbstractExpression
{
    public function __construct($column = null, $value = null)
    {
        if ($column !== null) {
            $this->append($column, $value);
        }
    }

    public function append($column, $value = null): AssignmentExpression
    {
        if ($this->sql !== '') {
            $this->sql .= ', ';
        }
        if ($value === null) {
            $this->sql .= $this->convertNameToString($column);
        } else {
            if (is_object($column)) {
                $column = $this->convertNameToString($column);
            }
            $this->sql .= $this->convertNameToString([$column => $value]);
        }
        return $this;
    }

    private function convertNameToString($expression): string
    {
        if ($expression instanceof RawExpression) {
            $sql = $expression->toSql();
            $this->addParams($expression->getParams());
        } else if (is_array($expression)) {
            $list = [];
            foreach ($expression as $key => $value) {
                if (is_numeric($key)) {
                    $list[] = $this->convertNameToString($value);
                } else {
                    $value = $this->convertValueToString($value);
                    $list[] = $this->convertNameToString($key) . ' = ' . ($value === '' ? 'DEFAULT' : $value);
                }
            }
            $sql = implode(', ', $list);
        } else {
            $sql = (string)$expression;
        }
        return $sql;
    }

    private function convertValueToString($expression): string
    {
        if ($expression instanceof SelectQuery) {
            $sql = '(' . $expression->toSql() . ')';
            $this->addParams($expression->getParams());
        } else if ($expression instanceof RawExpression) {
            $sql = $expression->toSql();
            $this->addParams($expression->getParams());
        } else if ($expression === null) {
            $sql = 'NULL';
        } else {
            $param = self::nextParameterName();
            $sql = ':' . $param;
            $this->params[$param] = $expression;
        }
        return $sql;
    }
}
