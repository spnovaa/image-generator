<?php

namespace App\Listeners;

use App\Events\ImageGenerated;
use App\Mail\ImageGeneratorMail;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Listener that sends the user an e-mail containing the URL of their
 * generated image when an {@see ImageGenerated} event is dispatched.
 *
 * Implements {@see ShouldQueue} so the mail is delivered asynchronously
 * — failures here do not poison the image-generation pipeline.
 */
final class SendGeneratedImageNotification implements ShouldQueue
{
    public function __construct(
        private readonly Mailer $mailer,
    ) {}

    public function handle(ImageGenerated $event): void
    {
        $history = $event->history;

        if (empty($history->email) || empty($history->url)) {
            return;
        }

        $this->mailer
            ->to($history->email)
            ->send(new ImageGeneratorMail(
                bodyHtml: $history->url,
            ));
    }
}
