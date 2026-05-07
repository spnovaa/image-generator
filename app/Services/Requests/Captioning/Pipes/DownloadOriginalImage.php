<?php

namespace App\Services\Requests\Captioning\Pipes;

use App\Contracts\ObjectStorage;
use App\Data\PipelinePayload;
use App\Exceptions\ObjectStorageException;
use Closure;

final readonly class DownloadOriginalImage
{
    public function __construct(
        private ObjectStorage $storage,
    ) {}

    /**
     * @throws ObjectStorageException
     */
    public function handle(PipelinePayload $payload, Closure $next): mixed
    {
        $bucket = (string) config('image_generator.storage.input_bucket');

        $payload->imageBytes = $this->storage->get(
            bucket: $bucket,
            key:    $payload->history->file_name,
        );

        return $next($payload);
    }
}
