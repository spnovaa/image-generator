<?php

namespace App\Services\Requests\Captioning\Pipes;

use App\Pipe;
use App\Services\S3Object\Service;
use Closure;

class DownloadImage implements Pipe
{
    public function __construct(private Service $service)
    {
    }

    public function handle($content, Closure $next)
    {
        $content['img'] = $this->service->download($content->R_FileName);

        return $next($content);
    }
}
