<?php

namespace Tests\Unit\ImageGenerating;

use App\Contracts\ImageGenerator;
use App\Contracts\ObjectStorage;
use App\Contracts\RequestHistoryRepository;
use App\Enums\RequestStatus;
use App\Events\ImageGenerated;
use App\Models\RequestHistory;
use App\Services\Requests\ImageGenerating\Service;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Tests\Fakes\FakeImageGenerator;
use Tests\Fakes\FakeObjectStorage;
use Tests\Fakes\InMemoryRequestHistoryRepository;
use Tests\TestCase;

/**
 * Verifies the image-generation pipeline runs end-to-end against fakes
 * and that the {@see ImageGenerated} domain event is dispatched.
 */
final class ImageGeneratingServiceTest extends TestCase
{
    public function test_it_generates_uploads_persists_and_announces(): void
    {
        // ── arrange ────────────────────────────────────────────────────────
        Event::fake([ImageGenerated::class]);
        Storage::fake('local');
        config()->set('image_generator.storage.output_bucket', 'out-bucket');

        $repository = new InMemoryRequestHistoryRepository();
        $generator  = new FakeImageGenerator(bytesToReturn: 'PNGDATA');
        $storage    = new FakeObjectStorage();

        $this->app->instance(RequestHistoryRepository::class, $repository);
        $this->app->instance(ImageGenerator::class,           $generator);
        $this->app->instance(ObjectStorage::class,            $storage);

        $history = $repository->save(new RequestHistory([
            'email'     => 'user@example.com',
            'status'    => RequestStatus::READY->value,
            'caption'   => 'a robot on the moon',
            'file_name' => '7.png',
        ]));

        // ── act ────────────────────────────────────────────────────────────
        /** @var Service $service */
        $service = $this->app->make(Service::class);
        $result  = $service->handle($history);

        // ── assert ─────────────────────────────────────────────────────────
        $this->assertSame('a robot on the moon', $generator->receivedPrompts[0] ?? null);
        $this->assertSame('PNGDATA', $storage->contents['out-bucket']['7.png'] ?? null);
        $this->assertSame('fake://out-bucket/7.png', $result->url);
        $this->assertSame(RequestStatus::DONE, $result->status);

        Event::assertDispatched(ImageGenerated::class, fn (ImageGenerated $e) => $e->history->id === $result->id);
    }
}
