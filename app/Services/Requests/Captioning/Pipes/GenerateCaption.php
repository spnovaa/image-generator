<?php

namespace App\Services\Requests\Captioning\Pipes;

use App\Enums\Requests\Status;
use App\Exceptions\CaptionGeneratorException;
use App\Pipe;
use App\Services\CaptionGenerator\Service;
use Closure;
use Illuminate\Http\Client\ConnectionException;

class GenerateCaption implements Pipe
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
     * @throws CaptionGeneratorException
     * @throws ConnectionException
     */
    public function handle($content, Closure $next)
    {
        $content->fill([
            'R_Caption' => $this->service->generate($content['img']),
            'R_Status' => Status::READY
        ]);

        return $next($content);
    }
}
