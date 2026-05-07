<?php

namespace App\Services\Requests\Create\Pipes;

use App\Data\PipelinePayload;
use App\Exceptions\RabbitMQException;
use App\Jobs\GenerateImageCaption;
use Closure;
use Throwable;

/**
 * Pipeline step: enqueue the captioning job for the freshly created record.
 *
 * Any broker failure is normalised to {@see RabbitMQException} so the
 * controller can map it to the appropriate HTTP status.
 */
final class DispatchCaptioningJob
{
    /**
     * @throws RabbitMQException
     */
    public function handle(PipelinePayload $payload, Closure $next): mixed
    {
        try {
            $queue = (string) config('image_generator.queues.captioning');

            GenerateImageCaption::dispatch($payload->history->id)->onQueue($queue);
        } catch (Throwable $e) {
            throw new RabbitMQException(previous: $e);
        }

        return $next($payload);
    }
}
