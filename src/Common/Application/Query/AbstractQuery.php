<?php

namespace AlephTools\DDD\Common\Application\Query;

use AlephTools\DDD\Common\Application\TypeConversionAware;
use AlephTools\DDD\Common\Infrastructure\EnumHelper;
use AlephTools\DDD\Common\Infrastructure\WeakDto;
use AlephTools\DDD\Common\Model\Language;

/**
 * @property-read string|null $keyword
 * @property-read int|null $limit
 * @property-read int|null $offset
 * @property-read int|null $page
 * @property-read string[]|null $sort
 * @property-read string[]|null $group
 * @property-read string[]|null $fields
 * @property-read int|null $timezone The desire timezone offset in minutes.
 * @property-read Language|null $language
 * @property-read bool $withoutCount
 * @property-read bool $withoutItems
 */
abstract class AbstractQuery extends WeakDto
{
    use TypeConversionAware;

    public const DEFAULT_PAGE_SIZE = 10;
    public const DEFAULT_PAGE_MAX_SIZE = 1000;

    protected static int $pageMaxSize = self::DEFAULT_PAGE_MAX_SIZE;

    protected ?string $keyword = null;
    protected ?int $limit = self::DEFAULT_PAGE_SIZE;
    protected ?int $offset = null;
    protected ?int $page = null;
    protected ?array $sort = null;
    protected ?array $group = null;
    protected ?array $fields = null;
    protected ?int $timezone = null;
    protected ?Language $language = null;
    protected bool $withoutCount = false;
    protected bool $withoutItems = false;

    public static function getPageMaxSize(): int
    {
        return static::$pageMaxSize;
    }

    public static function setPageMaxSize(int $size = self::DEFAULT_PAGE_MAX_SIZE): void
    {
        static::$pageMaxSize = $size;
    }

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

    /**
     * Returns TRUE if the given sort field exists within this request.
     *
     * @param string $field
     * @return bool
     */
    public function containsSortField(string $field): bool
    {
        return $this->sort && isset($this->sort[$field]);
    }

    /**
     * Returns TRUE if the given field is passed with this request as sort or regular field..
     *
     * @param string $field
     * @return bool
     */
    public function usesField(string $field): bool
    {
        return $this->containsField($field) || $this->containsSortField($field);
    }

    //region Setters

    protected function setKeyword($keyword): void
    {
        $this->keyword = is_scalar($keyword) ? (string)$keyword : null;
    }

    protected function setLimit($limit): void
    {
        $this->limit = is_numeric($limit) ? abs((int)$limit) : static::DEFAULT_PAGE_SIZE;

        if ($this->limit > static::getPageMaxSize()) {
            $this->limit = static::getPageMaxSize();
        }
    }

    protected function setOffset($offset): void
    {
        $this->offset = is_numeric($offset) ? abs((int)$offset) : null;
    }

    protected function setPage($page): void
    {
        $this->page = is_numeric($page) ? abs((int)$page) : null;
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

    protected function setGroup($fields): void
    {
        $this->group = $this->fieldsToArray($fields);
    }

    protected function setFields($fields): void
    {
        $this->fields = $this->fieldsToArray($fields);
    }

    protected function setTimezone($timezone): void
    {
        $this->timezone = is_numeric($timezone) ? (int)$timezone : null;
    }

    protected function setLanguage(?string $language): void
    {
        $this->language = $language !== null ? EnumHelper::toEnum(Language::class, strtoupper($language)) : null;
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

    private function fieldsToArray($fields): ?array
    {
        if (!is_string($fields) || $fields === '') {
            return null;
        }

        $result = [];
        foreach (explode(',', $fields) as $field) {
            $field = trim($field);
            if ($field !== '') {
                $result[] = $field;
            }
        }
        return $result ?: null;
    }
}
