<?php

namespace App;
use Closure;
interface Pipe
{
    public function handle($content, Closure $next);
}
