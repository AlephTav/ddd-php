<?php

namespace AlephTools\DDD\Common\Infrastructure\SqlBuilder;

use RuntimeException;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Expressions\WithExpression;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Expressions\AbstractExpression;

abstract class AbstractQuery extends AbstractExpression
{
    /**
     * The query executor instance.
     *
     * @var QueryExecutor|null
     */
    protected ?QueryExecutor $db = null;

    /**
     * Contains TRUE if the query has built.
     *
     * @var bool
     */
    protected bool $built = false;

    /**
     * The WITH expression instance.
     *
     * @var WithExpression|null
     */
    protected ?WithExpression $with = null;

    public function getQueryExecutor(): ?QueryExecutor
    {
        return $this->db;
    }

    //region WITH

    /**
     * @param mixed $query
     * @param mixed $alias
     * @return $this
     */
    public function with($query, $alias = null)
    {
        $this->with = $this->with ?? new WithExpression();
        $this->with->append($query, $alias);
        $this->built = false;
        return $this;
    }

    /**
     * @param mixed $query
     * @param mixed $alias
     * @return $this
     */
    public function withRecursive($query, $alias = null)
    {
        $this->with = $this->with ?? new WithExpression();
        $this->with->append($query, $alias, true);
        $this->built = false;
        return $this;
    }

    //endregion

    //region Data Fetching

    /**
     * @return array
     * @throws RuntimeException
     */
    public function rows(): array
    {
        $this->validateAndBuild();
        return $this->db->rows($this->toSql(), $this->getParams());
    }

    /**
     * @param string $key
     * @param bool $removeKeyFromRow
     * @return array
     * @throws RuntimeException
     */
    public function rowsByKey(string $key, bool $removeKeyFromRow = false): array
    {
        $result = [];
        $rows = $this->rows();
        if ($rows && !array_key_exists($key, $rows[0])) {
            throw new RuntimeException("Key \"$key\" is not found in the row set.");
        }
        if ($removeKeyFromRow) {
            foreach ($rows as $row) {
                $keyValue = $row[$key];
                unset($row[$key]);
                $result[$keyValue] = $row;
            }
        } else {
            foreach ($rows as $row) {
                $result[$row[$key]] = $row;
            }
        }
        return $result;
    }

    /**
     * @param string $key
     * @param bool $removeKeyFromRow
     * @return array
     * @throws RuntimeException
     */
    public function rowsByGroup(string $key, bool $removeKeyFromRow = false): array
    {
        $result = [];
        $rows = $this->rows();
        if ($rows && !array_key_exists($key, $rows[0])) {
            throw new RuntimeException("Key \"$key\" is not found in the row set.");
        }
        if ($removeKeyFromRow) {
            foreach ($rows as $row) {
                $keyValue = $row[$key];
                unset($row[$key]);
                $result[$keyValue][] = $row;
            }
        } else {
            foreach ($rows as $row) {
                $result[$row[$key]][] = $row;
            }
        }
        return $result;
    }

    /**
     * @return array
     * @throws RuntimeException
     */
    public function row(): array
    {
        $this->validateAndBuild();
        return $this->db->row($this->toSql(), $this->getParams());
    }

    //endregion

    protected function buildWith(): void
    {
        if ($this->with) {
            $this->sql .= 'WITH ' . $this->with->toSql() . ' ';
            $this->addParams($this->with->getParams());
        }
    }

    /**
     * @return AbstractQuery
     */
    abstract public function build();

    public function toSql(): string
    {
        $this->build();
        return parent::toSql();
    }

    public function getParams(): array
    {
        $this->build();
        return parent::getParams();
    }

    /**
     * @return void
     * @throws RuntimeException
     */
    protected function validateAndBuild(): void
    {
        if ($this->db === null) {
            throw new RuntimeException('The query executor instance must not be null.');
        }
        $this->build();
    }
}
