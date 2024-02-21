<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Str;

class VariantResource extends JsonResource
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
            'price' => $this->price,
            'published' => (bool)$this->published,
            'main_photo' => $this->main_photo,
            'images' => $this->when(Str::contains($request->url(), '/api/products/'), function () {
                return json_decode($this->images, true) ?? [];
            }),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'favorite' => $this->favorite ?? false,
            'lowest_price' => $this->lowestPrice ? $this->lowestPrice->price : $this->price,
            'product' => $this->whenLoaded('product', function () {
                return new ProductResource($this->product);
            }),
            'options' => $this->whenLoaded('options', function () {
                return OptionResource::collection($this->options);
            }),
            'color' => $this->whenLoaded('color', function () {
                return new ColorResource($this->color);
            }),
        ];
    }
}
