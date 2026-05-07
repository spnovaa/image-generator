<?php

namespace App\Services\Requests\Create\Pipes;

use App\Contracts\ObjectStorage;
use App\Data\PipelinePayload;
use App\Exceptions\ObjectStorageException;
use Closure;

/**
 * Pipeline step: move the uploaded file to a temporary local location
 * and push it to the input bucket through the {@see ObjectStorage} port.
 */
final readonly class UploadOriginalImage
{
    public function __construct(
        private ObjectStorage $storage,
    ) {}

    /**
     * @throws ObjectStorageException
     */
    public function handle(PipelinePayload $payload, Closure $next): mixed
    {
        $bucket    = (string) config('image_generator.storage.input_bucket');
        $directory = (string) config('image_generator.storage.inbound_directory');

        $localFile = $payload->upload->move($directory, $payload->history->file_name);

        $this->storage->put(
            localPath: $localFile->getRealPath(),
            bucket:    $bucket,
            key:       $payload->history->file_name,
        );

        // The upload is no longer needed downstream.
        $payload->upload = null;

        return $next($payload);
    }
}
