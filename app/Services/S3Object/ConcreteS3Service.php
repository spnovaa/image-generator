<?php

namespace App\Services\S3Object;

use Aws\S3\S3Client;

class ConcreteS3Service
{
    protected $client;
    public function __construct()
    {
        require __DIR__ . '/vendor/autoload.php';

        // Instantiate the S3 class and point it at the desired host
        $this->client = new S3Client([
            'region' => env('AWS_DEFAULT_REGION'),
            'version' => '2006-03-01',
            'endpoint' => env('AWS_DEFAULT_REGION'),
            'credentials' => [
                'key' => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY')
            ],
            // Set the S3 class to use objects. arvanstorage.ir/bucket
            // instead of bucket.objects. arvanstorage.ir
            'use_path_style_endpoint' => true
        ]);
    }
}
