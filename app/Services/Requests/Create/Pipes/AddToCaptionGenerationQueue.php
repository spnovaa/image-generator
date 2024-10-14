<?php

namespace App\Services\Requests\Create\Pipes;

use App\Enums\Requests\Status;
use App\Exceptions\GeneralDatabaseException;
use App\Exceptions\RabbitMQException;
use App\Jobs\GenerateImageCaption;
use App\Models\RequestHistory;
use App\Pipe;
use Closure;
use Throwable;
use App\Repositories\Requests\Repo as RequestHistoryRepo;

class AddToCaptionGenerationQueue implements Pipe
{

    /**
     * @param $content
     * @param Closure $next
     * @return mixed
     * @throws RabbitMQException
     */
    public function handle($content, Closure $next)
    {
        try {
            dispatch((new GenerateImageCaption($content->id)))->onQueue('GenerateImageCaption');

            return $next($content);
        } catch (Throwable) {
            throw new RabbitMQException();
        }
    }
}
