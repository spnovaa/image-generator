<?php

namespace App\Contracts;

use App\Exceptions\CaptionGeneratorException;

/**
 * Port for any service capable of producing a textual caption from an image.
 *
 * Implementations: {@see \App\Adapters\HuggingFace\HuggingFaceImageCaptioner}.
 * Tests can swap in a fake via the container.
 */
interface ImageCaptioner
{
    /**
     * @param  string  $image  Raw binary image bytes.
     * @return string          Generated caption.
     *
     * @throws CaptionGeneratorException
     */
    public function caption(string $image): string;
}
