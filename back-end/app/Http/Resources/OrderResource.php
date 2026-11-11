<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    // OrderResource
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'status'       => $this->status,
            'total_amount' => (float) $this->total_amount,
            'ordered_at'   => $this->ordered_at,
            'paid_at'      => $this->paid_at,
            'address'      => [
                'street'       => $this->order_street,
                'number'       => $this->order_number,
                'cep'          => $this->order_cep,
                'neighborhood' => $this->order_neighborhood,
                'city'         => $this->order_city,
                'uf'           => $this->order_uf,
                'complement'   => $this->order_complement,
            ],
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
        ];
    }
}
