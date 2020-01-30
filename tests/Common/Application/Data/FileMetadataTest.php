<?php

namespace AlephTools\DDD\Tests\Application\Data;

use PHPUnit\Framework\TestCase;
use AlephTools\DDD\Common\Application\Data\FileMetadata;
use AlephTools\DDD\Common\Model\Assets\FileId;

class FileMetadataTest extends TestCase
{
    public function testCreation(): void
    {
        $properties = [
            'id' => FileId::create(),
            'isPrivate' => true,
            'createdAt' => new \DateTimeImmutable(),
            'contentType' => 'application/json',
            'name' => 'test.txt',
            'baseName' => 'test',
            'extension' => 'txt',
            'suggestedExtension' => 'json',
            'path' => 'a/b/c',
            'size' => 10000,
            'url' => 'http://some.url',
            'downloadLink' => 'http://download.link',
            'ownerId' => 123
        ];

        $data = new FileMetadata(...array_values($properties));

        $this->assertSame($properties, $data->toArray());
    }
}
