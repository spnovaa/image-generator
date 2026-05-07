<?php

namespace App\Contracts;

use App\Exceptions\ImageGeneratorException;

/**
 * Port for any service capable of producing an image from a textual prompt.
 *
 * Implementations: {@see \App\Adapters\HuggingFace\HuggingFaceImageGenerator}.
 */
interface ImageGenerator
{
    /**
     * @param  string  $prompt  Text prompt describing the desired image.
     * @return string           Raw binary image bytes.
     *
     * @throws ImageGeneratorException
     */
    public function generate(string $prompt): string;
}
