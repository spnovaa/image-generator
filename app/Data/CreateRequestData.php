<?php

namespace App\Data;

use Illuminate\Http\UploadedFile;

/**
 * Immutable input DTO for the "create conversion request" use case.
 *
 * Replaces the previous practice of mutating an Eloquent model with
 * arbitrary attributes (`$model['img'] = ...`) and passing it as a
 * generic data carrier through the pipeline.
 */
final readonly class CreateRequestData
{
    public function __construct(
        public string $email,
        public UploadedFile $image,
    ) {}
}
