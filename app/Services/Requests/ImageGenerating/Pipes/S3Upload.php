<?php

namespace App\Services\Requests\ImageGenerating\Pipes;

use App\Pipe;
use App\Services\S3Object\Service;
use Closure;
use Exception;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;

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
        Storage::put($content->file_name, $content['img']);
        $path = Storage::path($content->file_name);
        $res = new File($path);
        $res = $this->service->upload(
            $res->getRealPath(),
            env('AWS_BUCKET_OUT'),
            $content->file_name
        );

        $content['url'] = $res->toArray()['ObjectURL'];
        return $next($content);
    }
}
