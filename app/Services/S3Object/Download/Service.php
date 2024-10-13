<?php

namespace App\Services\S3Object\Download;

use App\Services\S3Object\ConcreteS3Service;

class Service extends ConcreteS3Service
{
    /**
     * @param string $file_name
     * @param string $bucket
     * @return mixed
     */
    public function download(string $file_name, string $bucket)
    {
        $object = $this->client->getObject([
            'Bucket' => $bucket,
            'Key' => $file_name]);
        return $object['Body']->getContents();
    }
}
