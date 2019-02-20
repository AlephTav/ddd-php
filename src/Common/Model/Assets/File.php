<?php

namespace AlephTools\DDD\Common\Model\Assets;

use AlephTools\DDD\Common\Infrastructure\ApplicationContext;
use AlephTools\DDD\Common\Infrastructure\FileStorage;
use AlephTools\DDD\Common\Infrastructure\IdentifiedValueObject;

/**
 * @property-read FileId $id
 */
class File extends IdentifiedValueObject
{
    protected $id;

    public function __construct($id)
    {
        if (is_array($id)) {
            parent::__construct($id);
        } else {
            parent::__construct([
                'id' => $id
            ]);
        }
    }

    public function toUrl(): string
    {
        return $this->getFileStorage()->getUrl($this->id);
    }

    public function toDownloadLink(int $expirationInSeconds): string
    {
        return $this->getFileStorage()->getDownloadLink($this->id, $expirationInSeconds);
    }

    private function getFileStorage(): FileStorage
    {
        return ApplicationContext::get(FileStorage::class);
    }

    protected function setId(?FileId $id): void
    {
        $this->id = $id;
    }

    protected function validateId(): void
    {
        $this->assertArgumentNotNull($this->id, 'File identifier must not be null.');
    }
}
