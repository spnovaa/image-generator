<?php

namespace App\Exceptions;

use RuntimeException;

class ObjectStorageException extends RuntimeException
{
    public static function uploadFailed(string $bucket, string $key, ?\Throwable $previous = null): self
    {
        return new self("Object storage upload failed for {$bucket}/{$key}.", 0, $previous);
    }

    public static function downloadFailed(string $bucket, string $key, ?\Throwable $previous = null): self
    {
        return new self("Object storage download failed for {$bucket}/{$key}.", 0, $previous);
    }
}
