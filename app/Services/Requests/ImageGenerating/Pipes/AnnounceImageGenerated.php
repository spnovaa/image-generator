<?php

namespace App\Services\Requests\ImageGenerating\Pipes;

use App\Data\PipelinePayload;
use App\Events\ImageGenerated;
use Closure;
use Illuminate\Contracts\Events\Dispatcher;

/**
 * Pipeline step: announce that an image has been generated.
 *
 * Listeners (mail, webhooks, analytics, …) react out-of-band — the
 * pipeline itself does not know or care what they do.
 */
final readonly class AnnounceImageGenerated
{
    public function __construct(
        private Dispatcher $events,
    ) {}

    public function handle(PipelinePayload $payload, Closure $next): mixed
    {
        $this->events->dispatch(new ImageGenerated($payload->history));

        return $next($payload);
    }
}
