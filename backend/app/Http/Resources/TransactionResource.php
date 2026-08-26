<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
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
            'client_id' => $this->client_id,
            'user_id' => $this->user_id,
            'promotion_id' => $this->promotion_id,
            'transaction_date' => $this->transaction_date,
            'transaction_type' => $this->transaction_type,
            'annotations'=>$this->annotations,
            'amount' => $this->amount,
            'profit'=>$this->profit,
            'delivery_date' => $this->delivery_date,
            'responsible' => $this->responsible,
            'status' => $this->status,
            'delivery_status'=>$this->delivery_status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'user' => $this->whenLoaded('user'),
            'client' => $this->whenLoaded('client'),
            'promotion' => $this->whenLoaded('promotion'),
            'transactionDetails' => $this->whenLoaded('transactionDetails'),
            'transactionPayments' => $this->whenLoaded('transactionPayments'),
        ];
    }
}
