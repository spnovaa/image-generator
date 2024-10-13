<?php

namespace App\Services\CaptionGenerator;

use App\Enums\HuggingFace\HuggingFaceEndPoints;
use App\Exceptions\CaptionGeneratorException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class Service
{
    /**
     * @param $img
     * @return string
     * @throws CaptionGeneratorException
     * @throws ConnectionException
     */
    public function generate($img): string
    {
        $res = Http::timeout(30)
            ->withToken(env('HUGGING_FACE_TOKEN'))
            ->post(
                HuggingFaceEndPoints::CAPTIONING,
                ['file' => $img]
            );
        if ($res->successful())
            return $res->json('generated_text');

        throw new CaptionGeneratorException();
    }
}
