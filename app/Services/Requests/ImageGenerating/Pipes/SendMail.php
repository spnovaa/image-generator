<?php

namespace App\Services\Requests\ImageGenerating\Pipes;

use App\Pipe;
use App\Services\Mail\Service;
use Closure;

class SendMail implements Pipe
{
    public function __construct(
        private Service $service
    )
    {
    }

    public function handle($content, Closure $next)
    {
        $this->service->send($content['email'], $content['url']);
        return $next($content);
    }
}
