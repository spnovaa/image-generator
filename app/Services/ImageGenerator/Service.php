<?php

namespace App\Services\ImageGenerator;

use App\Enums\HuggingFace\HuggingFaceEndPoints;
use App\Exceptions\ImageGeneratorException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class Service
{
    /**
     * @param string $caption
     * @return string
     * @throws ConnectionException
     * @throws ImageGeneratorException
     */
    public function generate(string $caption)
    {
        $res = Http::timeout(30)
            ->withToken(env('HUGGING_FACE_TOKEN'))
            ->post(
                HuggingFaceEndPoints::IMG_MAKER,
                ['inputs' => $caption]
            );
        if ($res->successful())
            return $res->body();

        throw new ImageGeneratorException();
    }
}
