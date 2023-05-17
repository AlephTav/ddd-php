<?php

declare(strict_types=1);

namespace Tests\AlephTools\DDD\Common\Model\Assets;

use AlephTools\DDD\Common\Infrastructure\ApplicationContext;
use AlephTools\DDD\Common\Infrastructure\FileStorage;
use AlephTools\DDD\Common\Model\Assets\File;
use AlephTools\DDD\Common\Model\Assets\FileId;
use AlephTools\DDD\Common\Model\Exceptions\InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockBuilder;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class FileTest extends TestCase
{
    public function testCreationFromScalar(): void
    {
        $file = new File($id = FileId::create());

        self::assertSame($id, $file->id);
    }

    public function testCreationFromArray(): void
    {
        $file = new File(['id' => $id = FileId::create()]);

        self::assertSame($id, $file->id);
    }

    public function testValidation(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('File identifier must not be null.');

        new File(null);
    }

    public function testToUrl(): void
    {
        $this->setUpFileStorageMock();
        $file = new File($id = FileId::create());

        self::assertSame($id->toString(), $file->toUrl());
    }

    public function testToDownloadLink(): void
    {
        $this->setUpFileStorageMock();
        $file = new File($id = FileId::create());

        self::assertSame($id->toString() . '17', $file->toDownloadLink(17));
    }

    private function setUpFileStorageMock(): void
    {
        /** @var MockBuilder $builder */
        $builder = $this->getMockBuilder(FileStorage::class);
        $storage = $builder->onlyMethods([
            'getMetadataList',
            'exists',
            'getMetadata',
            'getUrl',
            'getDownloadLink',
            'upload',
            'download',
            'delete',
        ])->getMock();

        $storage->method('getUrl')
            ->willReturnCallback(fn ($id) => (string)$id);

        $storage->method('getDownloadLink')
            ->willReturnCallback(fn ($id, int $expirationInSeconds) => (string)$id . (string)$expirationInSeconds);

        ApplicationContext::set(fn () => $storage);
    }
}
