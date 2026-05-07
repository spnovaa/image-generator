<?php

namespace App\Services\Requests\ImageGenerating\Pipes;

use App\Contracts\ImageGenerator;
use App\Data\PipelinePayload;
use App\Exceptions\ImageGeneratorException;
use Closure;

final readonly class GenerateImage
{
    public function __construct(
        private ImageGenerator $generator,
    ) {}

    /**
     * @throws ImageGeneratorException
     */
    public function handle(PipelinePayload $payload, Closure $next): mixed
    {
        $payload->imageBytes = $this->generator->generate(
            prompt: (string) $payload->history->caption,
        );

        return $next($payload);
    }
}
