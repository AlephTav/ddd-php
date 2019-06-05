<?php

namespace AlephTools\DDD\Tests\Common\Application\Data;

use AlephTools\DDD\Common\Application\Data\AbstractCollectionResponse;
use PHPUnit\Framework\TestCase;

class CollectionResponseTestObject extends AbstractCollectionResponse {}

class AbstractCollectionResponseTest extends TestCase
{
    public function testEmptyCollectionCreation(): void
    {
        $collection = new CollectionResponseTestObject();

        $this->assertSame([], $collection->items);
        $this->assertSame(0, $collection->count);
    }

    public function testCollectionWithoutCount(): void
    {
        $items = [1, 2, 3];
        $collection = new CollectionResponseTestObject($items, null);

        $this->assertSame($items, $collection->items);
        $this->assertNull($collection->count);
        $this->assertSame(['items' => $items], $collection->toArray());
        $this->assertSame($collection->toArray(), $collection->toNestedArray());
    }

    public function testCollectionWithoutItems(): void
    {
        $collection = new CollectionResponseTestObject(null, 5);

        $this->assertNull($collection->items);
        $this->assertSame(5, $collection->count);
        $this->assertSame(['count' => 5], $collection->toArray());
        $this->assertSame($collection->toArray(), $collection->toNestedArray());
    }

    public function testCollectionWithItemsAndCount(): void
    {
        $items = [1, 2, 3];
        $collection = new CollectionResponseTestObject($items, 3);

        $this->assertSame($items, $collection->items);
        $this->assertSame(3, $collection->count);
        $this->assertSame(['count' => 3, 'items' => $items], $collection->toArray());
        $this->assertSame($collection->toArray(), $collection->toNestedArray());
    }
}
