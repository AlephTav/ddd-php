<?php

namespace AlephTools\DDD\Common\Infrastructure;

use AlephTools\DDD\Common\Model\Assets\FileId;
use AlephTools\DDD\Common\Model\Exceptions\EntityNotFoundException;

interface FileStorage
{
    /**
     * Returns the list of files metadata by their unique identifiers.
     *
     * @param array $ids File identifiers.
     * @param int $downloadLinkExpiration
     * @return FileMetadata[]
     * @throws EntityNotFoundException
     */
    public function getMetadataList(array $ids, int $downloadLinkExpiration = 0): array;

    /**
     * Returns TRUE if file with the given identifier exists in our storage.
     *
     * @param mixed $fileId
     * @return bool
     */
    public function exists($fileId): bool;

    /**
     * Returns the file metadata by its unique identifier.
     *
     * @param mixed $fileId
     * @param int $downloadLinkExpiration
     * @return FileMetadata
     * @throws EntityNotFoundException
     */
    public function getMetadata($fileId, int $downloadLinkExpiration = 0): FileMetadata;

    /**
     * Returns a url to access the given public file.
     *
     * @param mixed $fileId
     * @return string
     */
    public function getUrl($fileId): string;

    /**
     * Returns the download link for a file.
     *
     * @param mixed $fileId
     * @param int $expirationInSeconds
     * @return string
     */
    public function getDownloadLink($fileId, int $expirationInSeconds): string;

    /**
     * Upload a file to storage.
     *
     * @param mixed $file
     * @param bool $isPrivate
     * @param string $path Optional path to the file in the storage.
     * @param int $downloadLinkExpiration
     * @return FileMetadata
     */
    public function upload($file, bool $isPrivate, string $path = '', int $downloadLinkExpiration = 0): FileMetadata;

    /**
     * Downloads a private file.
     *
     * @param mixed $fileId
     * @return mixed
     */
    public function download($fileId);

    /**
     * Deletes a file.
     *
     * @param mixed $fileId
     */
    public function delete($fileId): void;
}
