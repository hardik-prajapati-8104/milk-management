<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DailyEntryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'customer_id' => $this->customer_id,
            'customer_name' => $this->whenLoaded('customer', fn () => $this->customer?->name),
            'consumer_id' => $this->whenLoaded('customer', fn () => $this->customer?->consumer_id),
            'entry_date' => $this->entry_date->toDateString(),
            'shift' => $this->shift,
            'milk_type' => $this->milk_type,
            'quantity' => (float) $this->quantity,
            'rate' => (float) $this->rate,
            'amount' => (float) $this->amount,
            'is_absent' => (bool) $this->is_absent,
            'is_holiday' => (bool) $this->is_holiday,
            'is_locked' => (bool) $this->is_locked,
            'remarks' => $this->remarks,
        ];
    }
}
