<?php

namespace App\Providers;

use App\Adapters\HuggingFace\HuggingFaceImageCaptioner;
use App\Adapters\HuggingFace\HuggingFaceImageGenerator;
use App\Adapters\Storage\S3ObjectStorage;
use App\Contracts\ImageCaptioner;
use App\Contracts\ImageGenerator;
use App\Contracts\ObjectStorage;
use App\Contracts\RequestHistoryRepository;
use App\Repositories\EloquentRequestHistoryRepository;
use Aws\S3\S3Client;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Support\ServiceProvider;

/**
 * Wires every domain port to its concrete adapter.
 *
 * This is the only place in the codebase where infrastructure libraries
 * (Aws\S3\S3Client, Illuminate\Http\Client\Factory) and `config()` are
 * directly referenced from outside config/. Domain code depends only on
 * the contracts under {@see \App\Contracts}.
 */
final class DomainServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(S3Client::class, function (): S3Client {
            $disk = config('filesystems.disks.s3');

            return new S3Client([
                'region'                  => $disk['region'],
                'version'                 => '2006-03-01',
                'endpoint'                => $disk['endpoint'],
                'scheme'                  => $disk['scheme'] ?? 'https',
                'use_path_style_endpoint' => (bool) ($disk['use_path_style_endpoint'] ?? true),
                'credentials'             => [
                    'key'    => $disk['key'],
                    'secret' => $disk['secret'],
                ],
            ]);
        });

        $this->app->singleton(ObjectStorage::class, S3ObjectStorage::class);

        $this->app->singleton(ImageCaptioner::class, fn (Application $app) => new HuggingFaceImageCaptioner(
            http:      $app->make(HttpFactory::class),
            token:     (string) config('huggingface.token'),
            timeout:   (int) config('huggingface.timeout'),
            verifyTls: (bool) config('huggingface.verify_tls'),
        ));

        $this->app->singleton(ImageGenerator::class, fn (Application $app) => new HuggingFaceImageGenerator(
            http:      $app->make(HttpFactory::class),
            token:     (string) config('huggingface.token'),
            timeout:   (int) config('huggingface.timeout'),
            verifyTls: (bool) config('huggingface.verify_tls'),
        ));

        $this->app->bind(RequestHistoryRepository::class, EloquentRequestHistoryRepository::class);
    }
}
