<?php

namespace Tests\Fakes;

use App\Contracts\ImageCaptioner;
use App\Exceptions\CaptionGeneratorException;

final class FakeImageCaptioner implements ImageCaptioner
{
    /** @var list<string> */
    public array $receivedImages = [];

    public function __construct(
        private readonly string $captionToReturn = 'a fake caption',
        private readonly bool $shouldFail = false,
    ) {}

    public function caption(string $image): string
    {
        $this->receivedImages[] = $image;

        if ($this->shouldFail) {
            throw new CaptionGeneratorException('forced fake failure');
        }

        return $this->captionToReturn;
    }
}
