<?php

namespace App\Services\Requests\ImageGenerating\Pipes;

use App\Enums\Requests\Status;
use App\Exceptions\GeneralDatabaseException;
use App\Pipe;
use App\Services\Requests\Service;
use Closure;

class UpdateRecord implements Pipe
{
    public function __construct(
        private Service $service
    )
    {
    }

    /**
     * @param $content
     * @param Closure $next
     * @return mixed
     * @throws GeneralDatabaseException
     */
    public function handle($content, Closure $next)
    {
        // remove overloaded attribute before db query execution
        unset($content['img']);
        $content['status'] = Status::DONE;

        $this->service->update($content);

        return $next($content);
    }
}
