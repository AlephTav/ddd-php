<?php

namespace AlephTools\DDD\Tests\Common\Infrastructure;

use PHPUnit\Framework\TestCase;
use AlephTools\DDD\Common\Infrastructure\FileMetadata;
use AlephTools\DDD\Common\Model\Assets\FileId;

class FileMetadataTest extends TestCase
{
    public function testCreation(): void
    {
        $id = FileId::create();
        $properties = [
            'id' => $id,
            'isPrivate' => true,
            'createdAt' => new \DateTime(),
            'contentType' => 'application/json',
            'baseName' => 'test',
            'extension' => 'txt',
            'suggestedExtension' => 'json',
            'size' => 10000
        ];

        $data = new FileMetadata($properties);

        $this->assertSame($properties, $data->toArray());
        $this->assertEquals('test.txt', $data->getFileName());

        $data->extension = '';
        $this->assertEquals('test.json', $data->getFileName());

        $data->suggestedExtension = '';
        $this->assertEquals('test', $data->getFileName());
    }
}