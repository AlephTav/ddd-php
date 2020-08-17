<?php

namespace AlephTools\DDD\Common\Application\Data;

/**
 * @property-read array|null $items
 * @property-read int|null $count
 */
abstract class AbstractCollectionResponse extends AbstractDataResponse
{
    protected ?array $items = null;
    protected ?int $count = null;

    public function __construct(?array $items = [], ?int $count = 0)
    {
        parent::__construct([
            'items' => $items,
            'count' => $count
        ]);
    }

    public function toArray(): array
    {
        $output = [];

        if ($this->count !== null) {
            $output['count'] = $this->count;
        }
        if ($this->items !== null) {
            $output['items'] = $this->toCollection();
        }

        return $output;
    }

    public function toNestedArray(): array
    {
        return $this->toArray();
    }

    protected function toCollection(): array
    {
        return $this->items ?? [];
    }
}
