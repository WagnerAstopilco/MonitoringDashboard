<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionPaymentResource extends JsonResource
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

            'transaction_id' => $this->transaction_id,

            'payment_method' => [
                'id' => $this->whenLoaded('paymentMethod')?->id,
                'name' => $this->whenLoaded('paymentMethod')?->name,
            ],

            'amount' => $this->amount,

            'payment_type' => $this->payment_type,

            'payment_date' => $this->payment_date,

            'created_at' => $this->created_at,
        ];
    }
}
