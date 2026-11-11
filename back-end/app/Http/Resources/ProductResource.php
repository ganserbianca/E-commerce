<?php

namespace App\Http\Resources;

use App\Http\Resources\CategoryResource;
use App\Http\Resources\BrandResource;
use App\Http\Resources\AudienceResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => (float) $this->price,
            'stock_quantity' => $this->stock_quantity,
            'image' => $this->image,
            'available' => $this->available,

            'category' => new CategoryResource(
                $this->whenLoaded('category')
            ),

            'brand' => new BrandResource(
                $this->whenLoaded('brand')
            ),

            'audiences' => AudienceResource::collection(
                $this->whenLoaded('audiences')
            ),
        ];
    }
}
