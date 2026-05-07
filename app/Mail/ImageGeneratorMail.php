<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Notification e-mail informing the user that their generated image
 * is available at the URL contained in {@see $bodyHtml}.
 */
final class ImageGeneratorMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public readonly string $bodyHtml,
    ) {}

    public function envelope(): Envelope
    {
        $cfg = config('image_generator.mail');

        return new Envelope(
            from:    new Address($cfg['from_address'], $cfg['from_name']),
            subject: $cfg['subject'],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'image-mail',
            with: ['url' => $this->bodyHtml],
        );
    }
}
