<?php

namespace App\Contracts;

use App\Exceptions\ObjectStorageException;

/**
 * Port for an object-storage backend (S3, MinIO, ArvanStorage, on-disk fake).
 *
 * The application code never depends on the concrete backend — only on this
 * narrow contract — which keeps the domain testable and swappable.
 */
interface ObjectStorage
{
    /**
     * Upload the contents of a local file to the given bucket / key.
     *
     * @param  string  $localPath  Absolute path to the file on local disk.
     * @param  string  $bucket
     * @param  string  $key        Object key (filename) inside the bucket.
     * @return string              Public URL of the uploaded object.
     *
     * @throws ObjectStorageException
     */
    public function put(string $localPath, string $bucket, string $key): string;

    /**
     * Download the object at $bucket/$key and return its raw bytes.
     *
     * @throws ObjectStorageException
     */
    public function get(string $bucket, string $key): string;
}
