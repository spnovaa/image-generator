<?php

namespace App\Services\Requests\Create\Pipes;

use App\Pipe;
use App\Services\S3Object\Service;
use Closure;
use Exception;
use Illuminate\Http\UploadedFile;

readonly class S3Upload implements Pipe
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
         * @var $img UploadedFile
         */
        $img = $content['img'];

        $res = $this->service->upload(
            $img->getRealPath(),
            env('AWS_BUCKET'),
            $content->R_FileName
        );

        return $next($content);
    }
}
