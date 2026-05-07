<?php

namespace Tests\Fakes;

use App\Contracts\ImageGenerator;
use App\Exceptions\ImageGeneratorException;

final class FakeImageGenerator implements ImageGenerator
{
    /** @var list<string> */
    public array $receivedPrompts = [];

    public function __construct(
        private readonly string $bytesToReturn = 'fake-png-bytes',
        private readonly bool $shouldFail = false,
    ) {}

    public function generate(string $prompt): string
    {
        $this->receivedPrompts[] = $prompt;

        if ($this->shouldFail) {
            throw new ImageGeneratorException('forced fake failure');
        }

        return $this->bytesToReturn;
    }
}
