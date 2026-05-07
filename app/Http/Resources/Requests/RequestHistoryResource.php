<?php

namespace App\Http\Resources\Requests;

use App\Models\RequestHistory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin RequestHistory
 */
final class RequestHistoryResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'      => $this->id,
            'email'   => $this->email,
            'status'  => $this->status?->value,
            'caption' => $this->caption,
            'url'     => $this->url,
        ];
    }
}
