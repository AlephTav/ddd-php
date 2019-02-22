<?php

namespace AlephTools\DDD\Common\Infrastructure;

use DateTime;
use AlephTools\DDD\Common\Model\Assets\FileId;

/**
 * @property FileId $id
 * @property bool $isPrivate
 * @property DateTime $createdAt
 * @property string $contentType
 * @property string $name
 * @property string $baseName
 * @property string $extension
 * @property string $suggestedExtension
 * @property int $size
 */
class FileMetadata extends Dto
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
    private $size;

    //endregion

    //region Setters

    public function setId(FileId $id): void
    {
        $this->id = $id;
    }

    public function setIsPrivate(bool $isPrivate): void
    {
        $this->isPrivate = $isPrivate;
    }

    public function setCreatedAt(DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function setContentType(string $contentType): void
    {
        $this->contentType = $contentType;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setBaseName(string $baseName): void
    {
        $this->baseName = $baseName;
    }

    public function setExtension(string $extension): void
    {
        $this->extension = $extension;
    }

    public function setSuggestedExtension(string $suggestedExtension): void
    {
        $this->suggestedExtension = $suggestedExtension;
    }

    public function setSize(int $size): void
    {
        $this->size = $size;
    }

    //endregion
}
