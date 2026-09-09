<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BillItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'item_date' => $this->item_date?->toDateString(),
            'description' => $this->description,
            'quantity' => (float) $this->quantity,
            'rate' => (float) $this->rate,
            'amount' => (float) $this->amount,
        ];
    }
}
