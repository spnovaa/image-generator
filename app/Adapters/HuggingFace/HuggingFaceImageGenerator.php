<?php

namespace App\Adapters\HuggingFace;

use App\Contracts\ImageGenerator;
use App\Enums\HuggingFaceEndpoint;
use App\Exceptions\ImageGeneratorException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Factory as HttpFactory;

/**
 * Adapter: maps the {@see ImageGenerator} domain port to the HuggingFace
 * Inference API ({@see HuggingFaceEndpoint::ImageGeneration}).
 */
final readonly class HuggingFaceImageGenerator implements ImageGenerator
{
    public function __construct(
        private HttpFactory $http,
        private string $token,
        private int $timeout,
        private bool $verifyTls,
    ) {}

    public function generate(string $prompt): string
    {
        try {
            $response = $this->http
                ->timeout($this->timeout)
                ->withOptions(['verify' => $this->verifyTls])
                ->withToken($this->token)
                ->post(HuggingFaceEndpoint::ImageGeneration->url(), [
                    'inputs' => $prompt,
                ]);
        } catch (ConnectionException $e) {
            throw new ImageGeneratorException(previous: $e);
        }

        if (! $response->successful()) {
            throw new ImageGeneratorException();
        }

        return $response->body();
    }
}
