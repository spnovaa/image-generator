<?php

namespace App\Enums;

/**
 * HuggingFace inference endpoints used by the application.
 *
 * The full URL is built by prepending the HuggingFace inference base URL
 * (configured in `config/huggingface.php`).
 */
enum HuggingFaceEndpoint: string
{
    case Captioning      = 'Salesforce/blip-image-captioning-large';
    case ImageGeneration = 'ZB-Tech/Text-to-Image';

    public function url(): string
    {
        return rtrim(config('huggingface.base_url'), '/') . '/' . $this->value;
    }
}
