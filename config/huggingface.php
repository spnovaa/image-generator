<?php

return [

    /*
    |--------------------------------------------------------------------------
    | HuggingFace Inference API
    |--------------------------------------------------------------------------
    |
    | Configuration for the HuggingFace inference client used by the
    | HuggingFaceImageCaptioner and HuggingFaceImageGenerator adapters.
    |
    */

    'base_url' => env('HUGGING_FACE_BASE_URL', 'https://api-inference.huggingface.co/models'),

    'token'    => env('HUGGING_FACE_TOKEN'),

    'timeout'  => (int) env('HUGGING_FACE_TIMEOUT', 30),

    'verify_tls' => (bool) env('HUGGING_FACE_VERIFY_TLS', true),

];
