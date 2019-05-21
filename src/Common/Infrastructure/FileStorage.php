<?php

namespace AlephTools\DDD\Common\Infrastructure;

use AlephTools\DDD\Common\Model\Assets\FileId;
use AlephTools\DDD\Common\Model\Exceptions\EntityNotFoundException;

interface FileStorage
{
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
     * @return FileMetadata
     * @throws EntityNotFoundException
     */
    public function getMetadata($fileId): FileMetadata;

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
     * @return FileId
     */
    public function upload($file, bool $isPrivate): FileId;

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
