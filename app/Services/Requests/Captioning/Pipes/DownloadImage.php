<?php

namespace App\Services\Requests\Captioning\Pipes;

use App\Pipe;
use App\Services\S3Object\Service;
use Closure;
use Illuminate\Http\File;

class DownloadImage implements Pipe
{
    public function __construct(private Service $service)
    {
    }

    public function handle($content, Closure $next)
    {
        $content['img'] = $this->base64ToFile($this->service->download($content->R_FileName))->move('in2', $content['R_FileName']);

        return $next($content);
    }

    function base64ToFile($base64String)
    {
        $base64String = preg_replace('#^data:image/w+;base64,#', '', $base64String);
        $fileContents = base64_decode($base64String);
        $tempFilePath = tempnam(sys_get_temp_dir(), 'image_');
        file_put_contents($tempFilePath, $fileContents);
        return new File($tempFilePath);
    }
}
