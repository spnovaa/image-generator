<?php

namespace App\Enums\Requests;

abstract class Status
{
    /**
     * db record created. to be added into the queue
     */
    const PENDING = 'pending';

    /**
     * added to the image generator queue
     */
    const READY = 'ready';

    /**
     * failure by any means
     */
    const FAILURE = 'failure';

    /**
     * the procedure ended with all steps done correctly.
     */
    const DONE = 'done';
}
