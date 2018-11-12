<?php

namespace AlephTools\DDD\Common\Infrastructure\SqlBuilder\Expressions;

class JoinExpression extends AbstractExpression
{
    public function __construct(?string $type = null, $table = null, $conditions = null)
    {
        if ($type !== null && $table !== null) {
            $this->append($type, $table, $conditions);
        }
    }

    public function append(string $type, $table, $conditions = null): JoinExpression
    {
        if ($this->sql !== '') {
            $this->sql .= ' ';
        }
        $this->sql .= $type . ' ';

        $tb = new ListExpression($table);
        if (is_array($table) && \count($table) > 1) {
            $this->sql .= '(' . $tb->toSql() . ')';
        } else {
            $this->sql .= $tb->toSql();
        }
        $this->addParams($tb->getParams());

        if ($conditions !== null) {
            if ($conditions instanceof RawExpression ||
                $conditions instanceof \Closure ||
                $conditions instanceof ConditionalExpression ||
                is_string($conditions)
            ) {
                $conditions = new ConditionalExpression($conditions);
                $this->sql .= ' ON ' . $conditions->toSql();
            } else {
                $conditions = new ListExpression($conditions);
                $this->sql .= ' USING (' . $conditions->toSql() . ')';
            }
            $this->addParams($conditions->getParams());
        }

        return $this;
    }
}