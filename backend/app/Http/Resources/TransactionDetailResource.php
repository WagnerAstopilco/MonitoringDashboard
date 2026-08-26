<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionDetailResource extends JsonResource
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
            'transaction_id'=>$this->transaction_id,
            'service_id' => $this->service_id,
            'promotion_id' => $this->promotion_id,
            'unit_price' => $this->unit_price,
            'unit_cost' => $this->unit_cost,
            'profit' => $this->profit,
            'quantity' => $this->quantity,
            'subtotal' => $this->subtotal,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
