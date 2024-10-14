<?php

namespace App\Http\Resources\Requests;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Throwable;

class RequestHistoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        try {
            if (!$this->id)
                return [];
        } catch (Throwable) {
            return [];
        }

        return [
            'id' => $this->id,
            'email' => $this->email,
            'status' => $this->status,
            'caption' => $this->caption,
            'url' => $this->url
        ];
    }
}
