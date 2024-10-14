<?php

namespace App\Services\Requests\ImageGenerating\Pipes;

use App\Exceptions\ImageGeneratorException;
use App\Pipe;
use App\Services\ImageGenerator\Service;
use Closure;
use Illuminate\Http\Client\ConnectionException;

class GenerateImage implements Pipe
{
    public function __construct(
        private Service $service
    )
    {
    }

    /***
     * @param $content
     * @param Closure $next
     * @return mixed
     * @throws ConnectionException
     * @throws ImageGeneratorException
     */
    public function handle($content, Closure $next)
    {
        $content['img'] = $this->service->generate($content['caption']);

        return $next($content);
    }
}
