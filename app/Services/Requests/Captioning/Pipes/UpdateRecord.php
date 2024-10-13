<?php

namespace App\Services\Requests\Captioning\Pipes;

use App\Exceptions\GeneralDatabaseException;
use App\Pipe;
use App\Services\Requests\Service as RequestService;
use Closure;

readonly class UpdateRecord implements Pipe
{

    public function __construct(
        private RequestService $service
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
        unset($content['img']);
        $this->service->update($content);

        return $next($content);
    }
}
