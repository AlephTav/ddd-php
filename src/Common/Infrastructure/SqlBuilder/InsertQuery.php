<?php

namespace AlephTools\DDD\Common\Infrastructure\SqlBuilder;

use RuntimeException;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Traits\FromAware;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Expressions\AssignmentExpression;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Expressions\FromExpression;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Expressions\ListExpression;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Expressions\ValueListExpression;

/**
 * Represents the INSERT query.
 */
class InsertQuery extends AbstractQuery
{
    use FromAware;

    /**
     * The list of columns.
     *
     * @var ListExpression
     */
    private $columns;

    /**
     * The values of columns.
     *
     * @var ValueListExpression
     */
    private $values;

    /**
     * The SELECT query.
     *
     * @var SelectQuery
     */
    private $query;

    /**
     * The conflict target (PostgreSQL).
     *
     * @var ListExpression
     */
    private $indexColumns;

    /**
     * The assignment for update on duplication record.
     *
     * @var AssignmentExpression
     */
    private $assignment;

    /**
     * Constructor.
     *
     * @param QueryExecutor|null $db
     * @param FromExpression|null $from
     * @param ValueListExpression|null $values
     * @param SelectQuery|null $query
     * @param AssignmentExpression|null $assignment
     * @param ListExpression|null $indexColumns
     */
    public function __construct(
        QueryExecutor $db = null,
        FromExpression $from = null,
        ValueListExpression $values = null,
        SelectQuery $query = null,
        AssignmentExpression $assignment = null,
        ListExpression $indexColumns = null
    )
    {
        $this->db = $db;
        $this->from = $from;
        $this->values = $values;
        $this->query = $query;
        $this->assignment = $assignment;
        $this->indexColumns = $indexColumns;
    }

    //region FROM

    public function into($table, $alias = null): InsertQuery
    {
        return $this->from($table, $alias);
    }

    //endregion

    //region COLUMNS & VALUES

    public function columns($columns): InsertQuery
    {
        $this->columns = $this->columns ?? new ListExpression();
        $this->columns->append($columns);
        $this->built = false;
        return $this;
    }

    public function values($values, $columns = null): InsertQuery
    {
        if ($columns !== null) {
            $this->columns($columns);
        }

        if (is_array($values)) {
            $first = reset($values);
            if ($this->isAssociativeArray($first)) {
                $this->columns(array_keys($first));
            } else if ($this->isAssociativeArray($values)) {
                $this->columns(array_keys($values));
            }
        }

        $this->values = $this->values ?? new ValueListExpression();
        $this->values->append($values);

        $this->built = false;
        return $this;
    }

    private function isAssociativeArray($items): bool
    {
        return is_array($items) && is_string(key($items));
    }

    //endregion

    //region SELECT

    public function select(SelectQuery $query): InsertQuery
    {
        $this->query = $query;
        $this->built = false;
        return $this;
    }

    //endregion

    //region ON DUPLICATE KEY UPDATE (MySQL)

    public function onDuplicateKeyUpdate($column, $value = null): InsertQuery
    {
        $this->assignment = $this->assignment ?? new AssignmentExpression();
        $this->assignment->append($column, $value);
        $this->built = false;
        return $this;
    }

    //endregion

    //region ON CONFLICT DO UPDATE (PostgreSQL)

    public function onConflictDoUpdate($indexColumn, $column = null, $value = null): InsertQuery
    {
        $this->indexColumns = $this->indexColumns ?? new ListExpression();
        $this->indexColumns->append($indexColumn);
        if ($column !== null) {
            $this->onDuplicateKeyUpdate($column, $value);
        }
        $this->built = false;
        return $this;
    }

    //endregion

    //region Execution

    /**
     * Executes this insert query.
     *
     * @param string|null $sequence Name of the sequence object from which the ID should be returned.
     * @return mixed Returns the ID of the last inserted row or sequence value.
     * @throws RuntimeException
     */
    public function exec(string $sequence = null)
    {
        $this->validateAndBuild();
        $sql = $this->toSql() . ($sequence !== null ? ' RETURNING ' . $sequence : '');
        return $this->db->insert($sql, $this->getParams(), $sequence);
    }

    //endregion

    //region Query Building

    public function build(): InsertQuery
    {
        if ($this->built) {
            return $this;
        }
        $this->sql = '';
        $this->params = [];
        $this->buildFrom();
        $this->buildColumns();
        $this->buildValues();
        $this->buildQuery();
        $this->buildOnDuplicateKeyUpdate();
        $this->buildOnConflictDoUpdate();
        $this->built = true;
        return $this;
    }

    private function buildFrom(): void
    {
        $this->sql .= 'INSERT INTO ';
        if ($this->from) {
            $this->sql .= $this->from->toSql();
            $this->addParams($this->from->getParams());
        }
    }

    private function buildColumns(): void
    {
        $this->sql .= ' (';
        if ($this->columns) {
            $this->sql .= $this->columns->toSql();
            $this->addParams($this->columns->getParams());
        }
        $this->sql .= ')';
    }

    private function buildValues(): void
    {
        if (!$this->query) {
            $this->sql .= ' VALUES ';
            if ($this->values) {
                $this->sql .= $this->values->toSql();
                $this->addParams($this->values->getParams());
            }
        }
    }

    private function buildQuery(): void
    {
        if ($this->query) {
            $this->sql .= $this->query->toSql();
            $this->addParams($this->query->getParams());
        }
    }

    private function buildOnDuplicateKeyUpdate(): void
    {
        if ($this->assignment && !$this->indexColumns) {
            $this->sql .= ' ON DUPLICATE KEY UPDATE ';
            $this->sql .= $this->assignment->toSql();
            $this->addParams($this->assignment->getParams());
        }
    }

    private function buildOnConflictDoUpdate(): void
    {
        if ($this->indexColumns) {
            $this->sql .= ' ON CONFLICT(' . $this->indexColumns->toSql() . ') DO UPDATE ';
            $this->sql .= $this->assignment ? 'SET ' . $this->assignment->toSql() : 'NOTHING';
            $this->addParams($this->assignment->getParams());
        }
    }

    //endregion
}
