<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Image Generator — Domain Configuration
    |--------------------------------------------------------------------------
    |
    | Centralised, type-safe configuration for the image-generation domain.
    | All pipes, jobs, and services should read settings from here rather
    | than calling `env()` directly.
    |
    */

    'storage' => [
        // Object-storage bucket holding original user-uploaded images
        'input_bucket'  => env('AWS_BUCKET'),

        // Object-storage bucket holding generated output images
        'output_bucket' => env('AWS_BUCKET_OUT'),

        // Filename prefix used for inbound uploads on the local disk
        'inbound_directory' => env('IMAGE_GENERATOR_INBOUND_DIR', 'in'),
    ],

    'queues' => [
        // Queue name for the captioning job dispatched after image upload
        'captioning' => env('IMAGE_GENERATOR_CAPTIONING_QUEUE', 'GenerateImageCaption'),
    ],

    'mail' => [
        // From-address for the user notification email
        'from_address' => env('MAIL_FROM_ADDRESS', 'no-reply@image-generator.local'),
        'from_name'    => env('MAIL_FROM_NAME', 'Image Generator'),
        'subject'      => env('IMAGE_GENERATOR_MAIL_SUBJECT', 'Your generated image is ready'),
    ],

    'validation' => [
        // Maximum upload size in kilobytes (Laravel "max" rule)
        'max_image_kb' => (int) env('IMAGE_GENERATOR_MAX_IMAGE_KB', 4096),

        // Allowed image mime types
        'allowed_mimes' => ['jpeg', 'jpg', 'png', 'webp'],
    ],

];
