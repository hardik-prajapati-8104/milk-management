<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'consumer_id' => $this->consumer_id,
            'name' => $this->name,
            'mobile' => $this->mobile,
            'email' => $this->email,
            'address' => $this->address,
            'village' => $this->whenLoaded('village', fn () => $this->village?->name),
            'area' => $this->whenLoaded('area', fn () => $this->area?->name),
            'route' => $this->whenLoaded('route', fn () => $this->route?->name),
            'milk_type' => $this->milk_type,
            'morning_rate' => (float) $this->morning_rate,
            'evening_rate' => (float) $this->evening_rate,
            'status' => $this->status,
            'outstanding_balance' => (float) $this->outstanding_balance,
            'advance_balance' => (float) $this->advance_balance,
            'joining_date' => $this->joining_date?->toDateString(),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
