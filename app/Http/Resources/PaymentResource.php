<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'receipt_number' => $this->receipt_number,
            'customer_id' => $this->customer_id,
            'customer_name' => $this->whenLoaded('customer', fn () => $this->customer?->name),
            'bill_id' => $this->bill_id,
            'payment_method' => $this->whenLoaded('paymentMethod', fn () => $this->paymentMethod?->name),
            'payment_date' => $this->payment_date->toDateString(),
            'amount' => (float) $this->amount,
            'discount' => (float) $this->discount,
            'adjustment' => (float) $this->adjustment,
            'net_amount' => (float) $this->netAmount(),
            'is_advance' => (bool) $this->is_advance,
            'status' => $this->status,
            'reference_number' => $this->reference_number,
        ];
    }
}
