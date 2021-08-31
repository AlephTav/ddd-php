<?php

declare(strict_types=1);

namespace AlephTools\DDD\Common\Infrastructure;

use AlephTools\DDD\Common\Application\Data\FileMetadata;
use AlephTools\DDD\Common\Model\Exceptions\EntityNotFoundException;

interface FileStorage
{
    /**
     * Returns TRUE if file with the given identifier exists in our storage.
     *
     * @param mixed $fileId
     * @param mixed $ownerId
     */
    public function exists($fileId, $ownerId = null): bool;

    /**
     * Returns the file metadata by its unique identifier.
     *
     * @param mixed $fileId
     * @param mixed $ownerId The file owner.
     * @throws EntityNotFoundException
     */
    public function getMetadata($fileId, int $linksExpirationInSeconds = 0, $ownerId = null): FileMetadata;

    /**
     * Returns the list of files metadata by their unique identifiers.
     *
     * @param array $ids File identifiers.
     * @param mixed $ownerId The file owner.
     * @return FileMetadata[]
     * @throws EntityNotFoundException
     */
    public function getMetadataList(array $ids, int $linksExpirationInSeconds = 0, $ownerId = null): array;

    /**
     * Returns a url to access the given public file.
     *
     * @param mixed $fileId
     * @param mixed $ownerId The file owner.
     */
    public function getUrl($fileId, int $expirationInSeconds = 0, $ownerId = null): string;

    /**
     * Returns the download link for a file.
     *
     * @param mixed $fileId
     * @param mixed $ownerId The file owner.
     */
    public function getDownloadLink($fileId, int $expirationInSeconds, $ownerId = null): string;

    /**
     * Upload a file to storage.
     *
     * @param mixed $file
     * @param string $path Optional path to the file in the storage.
     * @param mixed $ownerId The file owner.
     */
    public function upload(
        $file,
        bool $isPrivate,
        string $path = '',
        int $linksExpirationInSeconds = 0,
        $ownerId = null
    ): FileMetadata;

    /**
     * Downloads a private file.
     *
     * @param mixed $fileId
     * @param mixed $ownerId The file owner.
     * @return mixed
     */
    public function download($fileId, $ownerId = null);

    /**
     * Deletes a file.
     *
     * @param mixed $fileId
     * @param mixed $ownerId The file owner.
     */
    public function delete($fileId, $ownerId = null): void;
}
