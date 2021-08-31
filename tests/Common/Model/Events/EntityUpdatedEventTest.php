<?php

declare(strict_types=1);

namespace AlephTools\DDD\Tests\Common\Model\Events;

use AlephTools\DDD\Common\Model\Events\EntityUpdated;
use AlephTools\DDD\Common\Model\Identity\GlobalId;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * @internal
 */
class EntityUpdatedEventTest extends TestCase
{
    public function testEntityUpdatedEventCreation(): void
    {
        $id = GlobalId::create();
        $oldProperties = ['a' => 1, 'b' => 2];
        $newProperties = ['a' => 2, 'b' => 3];
        $event = new EntityUpdated(stdClass::class, $id, $oldProperties, $newProperties);

        self::assertSame($id, $event->id);
        self::assertSame(stdClass::class, $event->entity);
        self::assertSame($oldProperties, $event->oldProperties);
        self::assertSame($newProperties, $event->newProperties);
    }

    public function testMerge(): void
    {
        $event = new EntityUpdated(
            stdClass::class,
            GlobalId::create(),
            [
                'a' => [
                    'p1' => 1,
                    'p2' => 2,
                    'p3' => 3,
                ],
                'b' => true,
            ],
            [
                'a' => [
                    'p2' => 222,
                ],
                'b' => false,
            ]
        );

        $event = $event->merge(
            new EntityUpdated(
                $event->entity,
                $event->id,
                [
                    'a' => [
                        'p1' => 1,
                        'p2' => 222,
                        'p3' => 3,
                    ],
                    'b' => false,
                    'c' => 123,
                ],
                [
                    'a' => [
                        'p1' => 111,
                        'p3' => 333,
                    ],
                    'b' => true,
                    'c' => 321,
                ]
            )
        );

        self::assertEquals([
            'a' => [
                'p1' => 1,
                'p2' => 2,
                'p3' => 3,
            ],
            'b' => true,
            'c' => 123,
        ], $event->oldProperties);

        self::assertEquals([
            'a' => [
                'p1' => 111,
                'p2' => 222,
                'p3' => 333,
            ],
            'c' => 321,
        ], $event->newProperties);
    }
}
