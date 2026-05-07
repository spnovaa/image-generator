<?php

namespace Tests\Unit\Captioning;

use App\Contracts\ImageCaptioner;
use App\Contracts\ObjectStorage;
use App\Contracts\RequestHistoryRepository;
use App\Enums\RequestStatus;
use App\Models\RequestHistory;
use App\Services\Requests\Captioning\Service;
use Tests\Fakes\FakeImageCaptioner;
use Tests\Fakes\FakeObjectStorage;
use Tests\Fakes\InMemoryRequestHistoryRepository;
use Tests\TestCase;

/**
 * Demonstrates that the captioning pipeline can be exercised end-to-end
 * with zero infrastructure: HuggingFace, S3, the database — every
 * external dependency is replaced by an in-memory fake bound through
 * the container.
 */
final class CaptioningServiceTest extends TestCase
{
    public function test_it_downloads_image_generates_caption_and_marks_record_ready(): void
    {
        // ── arrange ────────────────────────────────────────────────────────
        config()->set('image_generator.storage.input_bucket', 'in-bucket');

        $repository = new InMemoryRequestHistoryRepository();
        $storage    = new FakeObjectStorage();
        $captioner  = new FakeImageCaptioner(captionToReturn: 'a cat on a sofa');

        $this->app->instance(RequestHistoryRepository::class, $repository);
        $this->app->instance(ObjectStorage::class,            $storage);
        $this->app->instance(ImageCaptioner::class,           $captioner);

        $history = $repository->save(new RequestHistory([
            'email'     => 'user@example.com',
            'status'    => RequestStatus::PENDING->value,
            'file_name' => '1.jpg',
        ]));
        $storage->seed('in-bucket', '1.jpg', 'binary-image-data');

        // ── act ────────────────────────────────────────────────────────────
        /** @var Service $service */
        $service = $this->app->make(Service::class);
        $result  = $service->handle($history);

        // ── assert ─────────────────────────────────────────────────────────
        $this->assertSame('a cat on a sofa', $result->caption);
        $this->assertSame(RequestStatus::READY, $result->status);
        $this->assertSame(['binary-image-data'], $captioner->receivedImages);
    }
}
