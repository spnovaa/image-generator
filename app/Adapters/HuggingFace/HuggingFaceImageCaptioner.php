<?php

namespace App\Adapters\HuggingFace;

use App\Contracts\ImageCaptioner;
use App\Enums\HuggingFaceEndpoint;
use App\Exceptions\CaptionGeneratorException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Factory as HttpFactory;
use Throwable;

/**
 * Adapter: maps the {@see ImageCaptioner} domain port to the HuggingFace
 * Inference API ({@see HuggingFaceEndpoint::Captioning}).
 */
final readonly class HuggingFaceImageCaptioner implements ImageCaptioner
{
    public function __construct(
        private HttpFactory $http,
        private string $token,
        private int $timeout,
        private bool $verifyTls,
    ) {}

    public function caption(string $image): string
    {
        try {
            $response = $this->http
                ->timeout($this->timeout)
                ->withOptions(['verify' => $this->verifyTls])
                ->withToken($this->token)
                ->withBody($image, 'image/jpeg')
                ->post(HuggingFaceEndpoint::Captioning->url());
        } catch (ConnectionException $e) {
            throw new CaptionGeneratorException(previous: $e);
        }

        if (! $response->successful()) {
            throw new CaptionGeneratorException();
        }

        try {
            return (string) $response->json('0.generated_text');
        } catch (Throwable $e) {
            throw new CaptionGeneratorException(previous: $e);
        }
    }
}
