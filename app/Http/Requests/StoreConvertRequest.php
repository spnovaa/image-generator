<?php

namespace App\Http\Requests;

use App\Data\CreateRequestData;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Validates the payload submitted to {@see \App\Http\Controllers\Requests\ConvertorController::store()}.
 *
 * Also exposes a {@see toData()} method that converts the validated
 * input into the immutable {@see CreateRequestData} DTO — keeping the
 * controller free of mapping logic.
 */
final class StoreConvertRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>|string>
     */
    public function rules(): array
    {
        $config = config('image_generator.validation');

        return [
            'email' => ['required', 'email:rfc', 'max:128'],
            'img'   => [
                'required',
                'file',
                'image',
                'max:' . $config['max_image_kb'],
                'mimes:' . implode(',', $config['allowed_mimes']),
            ],
        ];
    }

    public function toData(): CreateRequestData
    {
        /** @var \Illuminate\Http\UploadedFile $upload */
        $upload = $this->file('img');

        return new CreateRequestData(
            email: (string) $this->validated('email'),
            image: $upload,
        );
    }
}
