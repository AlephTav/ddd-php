<?php

namespace AlephTools\DDD\Common\Infrastructure\SqlBuilder\Expressions;

use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Query;

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
        $this->sql .= $this->convertNameToString($value === null ? $column : [$column => $value]);
        return $this;
    }

    private function convertNameToString($expression): string
    {
        if ($expression instanceof Query) {
            $sql = '(' . $expression->toSql() . ')';
            $this->addParams($expression->getParams());
        } else if ($expression instanceof RawExpression) {
            $sql = $expression->toSql();
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
        } else if ($expression === null) {
            $sql = 'NULL';
        } else {
            $sql = (string)$expression;
        }
        return $sql;
    }

    private function convertValueToString($expression): string
    {
        if ($expression instanceof Query) {
            $sql = '(' . $expression->toSql() . ')';
            $this->addParams($expression->getParams());
        } else if ($expression instanceof RawExpression) {
            $sql = $expression->toSql();
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