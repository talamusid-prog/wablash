<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlastCampaignResource extends JsonResource
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
            'name' => $this->name,
            'message_template' => $this->message_template,
            'target_numbers' => $this->target_numbers,
            'status' => $this->status,
            'scheduled_at' => $this->scheduled_at?->toISOString(),
            'sent_count' => $this->sent_count,
            'failed_count' => $this->failed_count,
            'total_count' => $this->total_count,
            'session_id' => $this->session_id,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            'session' => $this->whenLoaded('session', function () {
                return [
                    'session_id' => $this->session->session_id,
                    'phone_number' => $this->session->phone_number,
                    'status' => $this->session->status,
                ];
            }),
        ];
    }
}
