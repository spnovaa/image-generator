<?php

namespace App\Services\Requests\Captioning;

use App\Models\RequestHistory;
use App\Services\Requests\Captioning\Pipes\DownloadImage;
use App\Services\Requests\Captioning\Pipes\GenerateCaption;
use App\Services\Requests\Captioning\Pipes\UpdateRecord;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\DB;
use Throwable;

class Service
{
    private array $pipes = [
        // here comes the steps of captioning an image:
        DownloadImage::class,
        GenerateCaption::class,
        UpdateRecord::class
    ];


    public function create(RequestHistory $request): int
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
