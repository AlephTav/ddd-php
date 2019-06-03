<?php

namespace AlephTools\DDD\Common\Infrastructure;

use DateTimeImmutable;
use AlephTools\DDD\Common\Model\Assets\FileId;

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
 */
class FileMetadata extends StrictDto
{
    //region Properties

    private $id;
    private $isPrivate;
    private $createdAt;
    private $contentType;
    private $name;
    private $baseName;
    private $extension;
    private $suggestedExtension;
    private $path;
    private $size;
    private $url;
    private $downloadLink;

    //endregion

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
        string $downloadLink = null
    )
    {
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
            'downloadLink' => $downloadLink
        ]);
    }
}
