<?php

namespace AlephTools\DDD\Common\Infrastructure\SqlBuilder\Expressions;

class ValueListExpression extends AbstractExpression
{
    public function __construct($values = null, string $alias = '')
    {
        if ($values !== null) {
            $this->append($values, $alias);
        }
    }

    public function append($values, string $alias = ''): ValueListExpression
    {
        if ($this->sql !== '') {
            $this->sql .= ', ';
        }
        if (is_array($values) && is_array(reset($values))) {
            foreach ($values as $value) {
                $this->append($value);
            }
        } else {
            $this->sql .= '(';
            $this->sql .= $this->convertValueToString($values);
            $this->sql .= ')';
        }
        if ($alias !== '') {
            $this->sql .= ' ' . $alias;
        }
        return $this;
    }

    private function convertValueToString($expression): string
    {
        if (is_array($expression)) {
            $sql = [];
            foreach ($expression as $value) {
                $sql[] = $this->convertValueToString($value);
            }
            $sql = implode(', ', $sql);
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
