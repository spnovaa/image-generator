<?php

namespace App\Services\CaptionGenerator;

use App\Enums\HuggingFace\HuggingFaceEndPoints;
use App\Exceptions\CaptionGeneratorException;
use GuzzleHttp\Client;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\File;

class Service
{
    /**
     * @param File $img
     * @return string
     * @throws CaptionGeneratorException
     * @throws ConnectionException
     */
    public function generate(File $img): string
    {
        $res = Http::timeout(30)
            ->withOptions(['verify' => false])
            ->withToken(env('HUGGING_FACE_TOKEN'))
            ->withBody( $img->openFile('r'), 'image/jpeg')
            ->post(HuggingFaceEndPoints::CAPTIONING);
        if ($res->successful())
            return $res->json('generated_text');

        throw new CaptionGeneratorException();
    }
}
