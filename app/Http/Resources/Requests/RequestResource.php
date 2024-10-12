<?php

namespace App\Http\Resources\Requests;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RequestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        if(!$this->R_Id)
            return [];

        return [
            'id' => $this->id,
            'email' => $this->R_Email,
            'status' => $this->R_Status,
            'caption' => $this->R_Caption,
            'url' => $this->R_URL
        ];
    }
}
