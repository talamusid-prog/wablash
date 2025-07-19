<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WhatsAppSessionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'session_id' => $this->session_id,
            'phone_number' => $this->phone_number,
            'status' => $this->status,
            'qr_code' => $this->qr_code,
            'is_active' => $this->is_active,
            'last_activity' => $this->last_activity?->toISOString(),
            'device_info' => $this->device_info,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
