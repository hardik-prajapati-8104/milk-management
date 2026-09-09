<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BillResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'bill_number' => $this->bill_number,
            'invoice_number' => $this->invoice_number,
            'customer_id' => $this->customer_id,
            'customer_name' => $this->whenLoaded('customer', fn () => $this->customer?->name),
            'period_start' => $this->period_start->toDateString(),
            'period_end' => $this->period_end->toDateString(),
            'morning_qty' => (float) $this->morning_qty,
            'evening_qty' => (float) $this->evening_qty,
            'total_qty' => (float) $this->total_qty,
            'milk_amount' => (float) $this->milk_amount,
            'total_amount' => (float) $this->total_amount,
            'paid_amount' => (float) $this->paid_amount,
            'outstanding_amount' => (float) $this->outstanding_amount,
            'status' => $this->status,
            'due_date' => $this->due_date?->toDateString(),
            'items' => BillItemResource::collection($this->whenLoaded('items')),
        ];
    }
}
