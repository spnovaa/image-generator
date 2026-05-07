<?php

namespace Tests\Fakes;

use App\Contracts\ObjectStorage;
use App\Exceptions\ObjectStorageException;

/**
 * In-memory object-storage fake for tests. Stores binary content in
 * a `bucket -> [key => bytes]` map and returns predictable URLs on
 * upload so assertions remain straightforward.
 */
final class FakeObjectStorage implements ObjectStorage
{
    /** @var array<string, array<string, string>> */
    public array $contents = [];

    /** @var list<array{op: string, bucket: string, key: string}> */
    public array $log = [];

    public function put(string $localPath, string $bucket, string $key): string
    {
        if (! is_file($localPath)) {
            throw ObjectStorageException::uploadFailed($bucket, $key);
        }

        $this->contents[$bucket][$key] = (string) file_get_contents($localPath);
        $this->log[] = ['op' => 'put', 'bucket' => $bucket, 'key' => $key];

        return "fake://{$bucket}/{$key}";
    }

    public function get(string $bucket, string $key): string
    {
        if (! isset($this->contents[$bucket][$key])) {
            throw ObjectStorageException::downloadFailed($bucket, $key);
        }

        $this->log[] = ['op' => 'get', 'bucket' => $bucket, 'key' => $key];

        return $this->contents[$bucket][$key];
    }

    public function seed(string $bucket, string $key, string $bytes): void
    {
        $this->contents[$bucket][$key] = $bytes;
    }
}
