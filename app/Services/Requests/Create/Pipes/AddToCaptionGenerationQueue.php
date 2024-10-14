<?php

namespace App\Services\Requests\Create\Pipes;

use App\Enums\Requests\Status;
use App\Exceptions\GeneralDatabaseException;
use App\Exceptions\RabbitMQException;
use App\Jobs\GenerateImageCaption;
use App\Models\RequestHistory;
use App\Pipe;
use App\Services\Requests\Captioning\Service as CaptioningService;
use App\Services\Requests\Service as HistoryService;
use Closure;
use Throwable;
use App\Repositories\Requests\Repo as RequestHistoryRepo;

class AddToCaptionGenerationQueue implements Pipe
{
    private CaptioningService $cs;
    private HistoryService $hs;
public function __construct()
{
    $this->hs = app(HistoryService::class);
    $this->cs = app(CaptioningService::class);
}

    /**
     * @param $content
     * @param Closure $next
     * @return mixed
     * @throws RabbitMQException
     */
    public function handle($content, Closure $next)
    {
        try {
            dispatch((new GenerateImageCaption($content->R_Id)))->onQueue('GenerateImageCaption');

            $history = $content;
            $this->cs->create($history);
            return $next($content);
        } catch (Throwable $throwable) {
            throw new RabbitMQException();
        }
    }
}
