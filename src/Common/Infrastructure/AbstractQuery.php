<?php

namespace AlephTools\DDD\Common\Infrastructure;

use DateTime;

/**
 * @property-read string|null $keyword
 * @property-read int|null $limit
 * @property-read int|null $offset
 * @property-read int|null $page
 * @property-read string[]|null $sort
 * @property-read string[]|null $fields
 * @property-read bool $withoutCount
 * @property-read bool $withoutItems
 */
abstract class AbstractQuery extends WeakDto
{
    //region Constants

    public const DEFAULT_PAGE_SIZE = 10;

    public const PAGE_MAX_SIZE = 100;

    //endregion

    //region Properties

    protected $keyword;
    protected $limit = self::DEFAULT_PAGE_SIZE;
    protected $offset;
    protected $page;
    protected $sort;
    protected $fields;
    protected $withoutCount = false;
    protected $withoutItems = false;

    //endregion

    /**
     * Returns TRUE if the fields is not set or if the given field within $fields array.
     *
     * @param string $field
     * @return bool
     */
    public function containsField(string $field): bool
    {
        return !$this->fields || in_array($field, $this->fields);
    }

    protected function toBoolean($value): bool
    {
        if (is_scalar($value)) {
            $value = strtolower(trim($value));
            return $value === 'true' || $value === '1' || $value === 'on';
        }

        return false;
    }

    protected function toDate($value): DateTime
    {
        return DateHelper::parse($value);
    }

    //region Setters

    protected function setKeyword($keyword): void
    {
        $this->keyword = is_scalar($keyword) ? (string)$keyword : null;
    }

    protected function setLimit($limit): void
    {
        $this->limit = is_numeric($limit) ? (int)$limit : static::DEFAULT_PAGE_SIZE;

        if ($this->limit > static::PAGE_MAX_SIZE) {
            $this->limit = static::PAGE_MAX_SIZE;
        }
    }

    protected function setOffset($offset): void
    {
        $this->offset = is_numeric($offset) ? (int)$offset : null;
    }

    protected function setPage($page): void
    {
        $this->page = is_numeric($page) ? (int)$page : null;
    }

    protected function setSort($sort): void
    {
        if (!is_string($sort) || $sort === '') {
            return;
        }

        $items = [];
        foreach (explode(',', $sort) as $item) {
            $item = trim($item);
            if ($item === '') {
                continue;
            }

            $first = $item[0];
            if ($first === '-') {
                $items[ltrim(substr($item, 1))] = 'DESC';
            } else if ($first === '+') {
                $items[ltrim(substr($item, 1))] = 'ASC';
            } else {
                $items[$item] = 'ASC';
            }
        }

        $this->sort = $items ?: null;
    }

    protected function setFields($fields): void
    {
        if (is_string($fields) && $fields !== '') {
            $this->fields = [];
            foreach (explode(',', $fields) as $field) {
                $field = trim($field);
                if ($field !== '') {
                    $this->fields[] = $field;
                }
            }
            $this->fields = $this->fields ?: null;
        }
    }

    protected function setWithoutCount($flag): void
    {
        $this->withoutCount = $this->toBoolean($flag);
    }

    protected function setWithoutItems($flag): void
    {
        $this->withoutItems = $this->toBoolean($flag);
    }

    //endregion
}
