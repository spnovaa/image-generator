<?php

namespace App\Services\S3Object\Upload;

use App\Services\S3Object\ConcreteS3Service;
use Aws\Result;
use Aws\S3\Exception\S3Exception;
use Exception;

class Service extends ConcreteS3Service
{
    /**
     * @param string $file
     * @param string $bucket
     * @param string $name
     * @return Result
     * @throws Exception
     */
    public function upload(string $file, string $bucket, string $name)
    {
        try {
            return $this->client->putObject([
                'Bucket' => $bucket,
                'Key' => $name,
                'SourceFile' => $file,
            ]);
        } catch (S3Exception $e) {
            throw new Exception('S3 Upload Error!');
        }
    }
}
