<?php

namespace App\Adapters\Storage;

use App\Contracts\ObjectStorage;
use App\Exceptions\ObjectStorageException;
use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;

/**
 * Adapter: maps the {@see ObjectStorage} domain port to an S3-compatible API.
 *
 * The {@see S3Client} is constructed once via the service container — see
 * {@see \App\Providers\DomainServiceProvider}.
 */
final readonly class S3ObjectStorage implements ObjectStorage
{
    public function __construct(
        private S3Client $client,
    ) {}

    public function put(string $localPath, string $bucket, string $key): string
    {
        try {
            $result = $this->client->putObject([
                'Bucket'     => $bucket,
                'Key'        => $key,
                'SourceFile' => $localPath,
            ]);
        } catch (S3Exception $e) {
            throw ObjectStorageException::uploadFailed($bucket, $key, $e);
        }

        return (string) ($result['ObjectURL'] ?? '');
    }

    public function get(string $bucket, string $key): string
    {
        try {
            $object = $this->client->getObject([
                'Bucket' => $bucket,
                'Key'    => $key,
            ]);
        } catch (S3Exception $e) {
            throw ObjectStorageException::downloadFailed($bucket, $key, $e);
        }

        return (string) $object['Body']->getContents();
    }
}
