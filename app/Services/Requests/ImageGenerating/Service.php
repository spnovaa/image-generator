<?php

namespace App\Services\Requests\ImageGenerating;

use App\Models\RequestHistory;
use App\Services\Requests\ImageGenerating\Pipes\GenerateImage;
use App\Services\Requests\ImageGenerating\Pipes\S3Upload;
use App\Services\Requests\ImageGenerating\Pipes\SendMail;
use App\Services\Requests\ImageGenerating\Pipes\UpdateRecord;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\DB;
use Throwable;

class Service
{
    private array $pipes = [
        // here comes the steps of generating an image:
        GenerateImage::class,
        S3Upload::class,
        UpdateRecord::class,
        SendMail::class
    ];


    public function generate(RequestHistory $request): int
    {
        try {
            DB::beginTransaction();

            app(Pipeline::class)
                ->send($request)
                ->through($this->pipes)
                ->thenReturn();

            DB::commit();
            return 1;

        } catch (Throwable $exception) {
            DB::rollBack();
            throw $exception;
        }
    }
}
