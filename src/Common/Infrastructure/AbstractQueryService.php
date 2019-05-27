<?php

namespace AlephTools\DDD\Common\Infrastructure;

use RuntimeException;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\Expressions\AbstractExpression;
use AlephTools\DDD\Common\Infrastructure\SqlBuilder\SelectQuery;
use AlephTools\DDD\Common\Model\Exceptions\InvalidArgumentException;

abstract class AbstractQueryService
{
    protected function applySelection(SelectQuery $query, ?array $fields, ?array $selectFields): SelectQuery
    {
        $selectFields = $selectFields ?: [];

        if ($fields) {
            foreach ($fields as $field) {
                if (isset($selectFields[$field])) {
                    $query->select($selectFields[$field], $field);
                } else {
                    throw new InvalidArgumentException("Incorrect field: $field.");
                }
            }
        } else {
            foreach ($selectFields as $alias => $column) {
                $query->select($column, $alias);
            }
        }

        return $query;
    }

    protected function applySorting(SelectQuery $query, ?array $sort, ?array $sortFields): SelectQuery
    {
        if ($sort) {
            $sortFields = $sortFields ?: [];

            foreach ($sort as $column => $order) {
                if (isset($sortFields[$column])) {
                    $query->orderBy($sortFields[$column], $order);
                } else {
                    throw new InvalidArgumentException("Incorrect sort field: $column.");
                }
            }
        }

        return $query;
    }

    protected function applyPagination(SelectQuery $query, AbstractQuery $request): SelectQuery
    {
        if ($request->limit !== null) {
            $query->limit($request->limit);
        }
        if ($request->offset !== null) {
            $query->offset($request->offset);
        } else if ($request->page !== null) {
            $query->paginate($request->page, $request->limit ?: AbstractQuery::DEFAULT_PAGE_SIZE);
        }
        return $query;
    }

    protected function applyDateRangeFiltering(AbstractExpression $query, string $column, AbstractQuery $request)
    {
        if (!method_exists($query,'where')) {
            throw new RuntimeException('Class ' . get_class($query) . ' does not support method "where".');
        }

        if ($request->from && $request->to) {
            $query->where($column, 'BETWEEN', [$request->from, $request->to]);
        } else if ($request->from) {
            $query->where($column, '>=', $request->from);
        } else if ($request->to) {
            $query->where($column, '<=', $request->to);
        }
    }
}
