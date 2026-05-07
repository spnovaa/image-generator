<?php

namespace App\Services\Requests\Captioning\Pipes;

use App\Contracts\ImageCaptioner;
use App\Data\PipelinePayload;
use App\Enums\RequestStatus;
use App\Exceptions\CaptionGeneratorException;
use Closure;

final readonly class GenerateCaption
{
    public function __construct(
        private ImageCaptioner $captioner,
    ) {}

    /**
     * @throws CaptionGeneratorException
     */
    public function handle(PipelinePayload $payload, Closure $next): mixed
    {
        $caption = $this->captioner->caption($payload->imageBytes ?? '');

        $payload->caption          = $caption;
        $payload->history->caption = $caption;
        $payload->history->markAs(RequestStatus::READY);
        $payload->imageBytes       = null;

        return $next($payload);
    }
}
