<?php

namespace App\Services\Requests\Create\Pipes;

use App\Exceptions\GeneralDatabaseException;
use App\Repositories\Requests\Repo as RequestHistoryRepo;
use App\Pipe;
use Closure;

class InsertModel implements Pipe
{
    public function __construct(
        private RequestHistoryRepo $repo
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
        $content = $this->repo->create($content);
        $content['R_FileName'] = $content['R_Id'] . '.'. $content['img']->getClientOriginalExtension();
        $this->repo->update($content);

        return $next($content);
    }
}
