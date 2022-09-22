<?php

declare(strict_types=1);

namespace AlephTools\DDD\Common\Application\Data;

use AlephTools\DDD\Common\Infrastructure\StrictDto;
use AlephTools\DDD\Common\Model\Assets\FileId;
use DateTimeImmutable;

/**
 * @property-read FileId $id
 * @property-read bool $isPrivate
 * @property-read DateTimeImmutable $createdAt
 * @property-read string $contentType
 * @property-read string $name
 * @property-read string $baseName
 * @property-read string $extension
 * @property-read string $suggestedExtension
 * @property-read string $path
 * @property-read int $size
 * @property-read string|null $url
 * @property-read string|null $downloadLink
 * @property-read mixed|null $ownerId
 */
class FileMetadata extends StrictDto
{
    private FileId $id;
    private bool $isPrivate;
    private DateTimeImmutable $createdAt;
    private string $contentType;
    private string $name;
    private string $baseName;
    private string $extension;
    private string $suggestedExtension;
    private string $path;
    private int $size;
    private ?string $url;
    private ?string $downloadLink;
    private mixed $ownerId;

    public function __construct(
        FileId $id,
        bool $isPrivate,
        DateTimeImmutable $createdAt,
        string $contentType,
        string $name,
        string $baseName,
        string $extension,
        string $suggestedExtension,
        string $path,
        int $size,
        string $url = null,
        string $downloadLink = null,
        mixed $ownerId = null
    ) {
        parent::__construct([
            'id' => $id,
            'isPrivate' => $isPrivate,
            'createdAt' => $createdAt,
            'contentType' => $contentType,
            'name' => $name,
            'baseName' => $baseName,
            'extension' => $extension,
            'suggestedExtension' => $suggestedExtension,
            'path' => $path,
            'size' => $size,
            'url' => $url,
            'downloadLink' => $downloadLink,
            'ownerId' => $ownerId,
        ]);
    }
}
