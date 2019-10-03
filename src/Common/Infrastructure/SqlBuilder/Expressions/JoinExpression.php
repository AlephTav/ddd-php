<?php

namespace AlephTools\DDD\Common\Infrastructure\SqlBuilder\Expressions;

class JoinExpression extends AbstractExpression
{
    public function append(string $type, $table, $conditions = null): JoinExpression
    {
        if ($this->sql !== '') {
            $this->sql .= ' ';
        }
        $this->sql .= $type . ' ';

        $tb = (new ListExpression())->append($table);
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
                $conditions = (new ConditionalExpression())->with($conditions);
                $this->sql .= ' ON ' . $conditions->toSql();
            } else {
                $conditions = (new ListExpression())->append($conditions);
                $this->sql .= ' USING (' . $conditions->toSql() . ')';
            }
            $this->addParams($conditions->getParams());
        }

        return $this;
    }
}
