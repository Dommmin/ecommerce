<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'name' => $this->name,
            'short_description' => \Str::limit($this->description),
            'description' => $this->description,
            'price' => $this->price,
            'voted' => (bool)$this->vote,
            'rating' => number_format($this->ratings_avg_value,2) ?? 0,
            'ratings_count' => $this->ratings_count,
            'brand' => $this->whenLoaded('brand', function () {
                return new BrandResource($this->brand);
            }),
            'category' => $this->whenLoaded('category', function () {
                return new CategoryResource($this->category);
            }),
            'variants' => $this->whenLoaded('variants', function () {
                return VariantResource::collection($this->variants);
            }),
            'option' => $this->whenLoaded('option', function () {
                return new OptionResource($this->option);
            })
        ];
    }
}
