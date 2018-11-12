<?php

namespace AlephTools\DDD\Common\Infrastructure\SqlBuilder;

/**
 * Interface for classes intended to execute arbitrary SQL queries.
 */
interface QueryExecutor
{
    /**
     * Executes the SQL statement.
     *
     * @param string $sql The SQL statement.
     * @param array $params The parameters to be bound to the SQL statement.
     * @return int The number of rows affected by the command execution.
     */
    public function execute(string $sql, array $params): int;

    /**
     * Executes an insert query.
     *
     * @param string $sql
     * @param array $params
     * @param string|null $sequence
     * @return mixed Returns the ID of the last inserted row or sequence value.
     */
    public function insert(string $sql, array $params, string $sequence = null);

    /**
     * Executes the SQL statement and returns all rows.
     *
     * @param string $sql The SQL statement.
     * @param array $params The parameters to be bound to the SQL statement.
     * @return array
     */
    public function rows(string $sql, array $params): array;

    /**
     * Executes the SQL statement and returns the first row of the result.
     *
     * @param string $sql The SQL statement.
     * @param array $params The parameters to be bound to the SQL statement.
     * @return array
     */
    public function row(string $sql, array $params): array;

    /**
     * Executes the SQL statement and returns the given column of the result.
     *
     * @param string $sql The SQL statement.
     * @param array $params The parameters to be bound to the SQL statement.
     * @return array
     */
    public function column(string $sql, array $params): array;

    /**
     * Executes the SQL statement and returns the first column's value of the first row.
     *
     * @param string $sql The SQL statement.
     * @param array $params The parameters to be bound to the SQL statement.
     * @return mixed
     */
    public function scalar(string $sql, array $params);
}