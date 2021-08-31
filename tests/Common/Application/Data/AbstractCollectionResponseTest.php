<?php

declare(strict_types=1);

namespace AlephTools\DDD\Tests\Common\Application\Data;

use AlephTools\DDD\Common\Application\Data\AbstractCollectionResponse;
use PHPUnit\Framework\TestCase;

class CollectionResponseTestObject extends AbstractCollectionResponse
{
}

/**
 * @internal
 */
class AbstractCollectionResponseTest extends TestCase
{
    public function testEmptyCollectionCreation(): void
    {
        $collection = new CollectionResponseTestObject();

        self::assertSame([], $collection->items);
        self::assertSame(0, $collection->count);
    }

    public function testCollectionWithoutCount(): void
    {
        $items = [1, 2, 3];
        $collection = new CollectionResponseTestObject($items, null);

        self::assertSame($items, $collection->items);
        self::assertNull($collection->count);
        self::assertSame(['items' => $items], $collection->toArray());
        self::assertSame($collection->toArray(), $collection->toNestedArray());
    }

    public function testCollectionWithoutItems(): void
    {
        $collection = new CollectionResponseTestObject(null, 5);

        self::assertNull($collection->items);
        self::assertSame(5, $collection->count);
        self::assertSame(['count' => 5], $collection->toArray());
        self::assertSame($collection->toArray(), $collection->toNestedArray());
    }

    public function testCollectionWithItemsAndCount(): void
    {
        $items = [1, 2, 3];
        $collection = new CollectionResponseTestObject($items, 3);

        self::assertSame($items, $collection->items);
        self::assertSame(3, $collection->count);
        self::assertSame(['count' => 3, 'items' => $items], $collection->toArray());
        self::assertSame($collection->toArray(), $collection->toNestedArray());
    }
}
