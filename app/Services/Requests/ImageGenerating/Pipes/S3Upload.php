<?php

namespace App\Services\Requests\ImageGenerating\Pipes;

use App\Pipe;
use App\Services\S3Object\Service;
use Closure;
use Exception;
use Illuminate\Http\File;

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
        /**
         * @var $img File
         */
        $img = $content['img'];
        $res = $img->move('out', $content->R_Id);
        $res = $this->service->upload(
            $res->getRealPath(),
            env('AWS_BUCKET_OUT'),
            $content->R_FileName
        );

        $content['R_URL'] = $res->toArray()['ObjectURL'];
        return $next($content);
    }
}
