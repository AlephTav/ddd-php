<?php

declare(strict_types=1);

namespace AlephTools\DDD\Tests\Application\Data;

use AlephTools\DDD\Common\Application\Data\FileMetadata;
use AlephTools\DDD\Common\Model\Assets\FileId;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class FileMetadataTest extends TestCase
{
    public function testCreation(): void
    {
        $properties = [
            'id' => FileId::create(),
            'isPrivate' => true,
            'createdAt' => new DateTimeImmutable(),
            'contentType' => 'application/json',
            'name' => 'test.txt',
            'baseName' => 'test',
            'extension' => 'txt',
            'suggestedExtension' => 'json',
            'path' => 'a/b/c',
            'size' => 10000,
            'url' => 'http://some.url',
            'downloadLink' => 'http://download.link',
            'ownerId' => 123,
        ];

        $data = new FileMetadata(...array_values($properties));

        self::assertSame($properties, $data->toArray());
    }
}
