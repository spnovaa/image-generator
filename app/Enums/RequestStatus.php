<?php

namespace App\Enums;

/**
 * Lifecycle of a conversion request.
 *
 *  PENDING  → record created, original image uploaded, captioning queued
 *  READY    → caption generated, awaiting image-generation worker
 *  DONE     → image generated, uploaded, user notified
 *  FAILURE  → terminal failure at any step
 */
enum RequestStatus: string
{
    case PENDING = 'pending';
    case READY   = 'ready';
    case DONE    = 'done';
    case FAILURE = 'failure';
}
