<?php

namespace App\Services\S3Object;

use App\Services\S3Object\Upload\Service as UploadService;
use App\Services\S3Object\Download\Service as DownloadService;
use Aws\Result;
use Exception;
use Illuminate\Support\Str;

class Service
{
    public function __construct(
        private UploadService $us,
        private DownloadService $ds
    )
    {
    }

    /**
     * @param string $file_path
     * @param string|null $bucket
     * @param string|null $name
     * @return Result
     * @throws Exception
     */
    public function upload(string $file_path, ?string $bucket = null, ?string $name = null)
    {
        if (!$bucket)
            $bucket = env('AWS_BUCKET');
        if (!$name)
            $name = Str::uuid();
        return $this->us->upload($file_path, $bucket, $name);
    }

    public function download(string $file_name, ?string $bucket = null)
    {
        if (!$bucket)
            $bucket = env('AWS_BUCKET');
        return $this->ds->download($file_name, $bucket);
    }
}
