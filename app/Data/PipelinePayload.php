<?php

namespace App\Data;

use App\Models\RequestHistory;
use Illuminate\Http\UploadedFile;

/**
 * Mutable payload carried through a request-processing pipeline.
 *
 * Each pipe reads the inputs it needs and writes the outputs it produces
 * onto explicit, typed properties. This replaces the previous pattern of
 * stuffing arbitrary attributes onto an Eloquent model instance — which
 * caused that model to act as a god-object and made data-flow impossible
 * to track without reading every pipe's source.
 */
final class PipelinePayload
{
    public function __construct(
        public RequestHistory $history,
        public ?UploadedFile $upload = null,
        public ?string $imageBytes = null,
        public ?string $caption = null,
        public ?string $generatedUrl = null,
    ) {}
}
