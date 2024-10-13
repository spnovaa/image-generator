<?php

namespace App\Services\Requests\ImageGenerating\Pipes;

use App\Pipe;
use App\Services\S3Object\Service;
use Closure;
use Exception;

class S3Upload implements Pipe
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
     * @throws Exception
     */
    public function handle($content, Closure $next)
    {
        $res = $this->service->upload(
            $content['img']->getRealPath(),
            env('AWS_BUCKET_OUT'),
            $content->R_FileName
        );

        $content['R_URL'] = $res->toArray()['ObjectURL'];
        return $next($content);
    }
}
