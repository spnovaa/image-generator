<?php

namespace App\Events;

use App\Models\RequestHistory;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Fired after an image has been successfully generated, uploaded to
 * object storage, and persisted to the database.
 *
 * Side effects (e-mail notifications, analytics, webhooks, …) are
 * implemented as listeners — keeping the image-generation pipeline
 * focused on its primary responsibility.
 */
final class ImageGenerated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly RequestHistory $history,
    ) {}
}
