<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Products\Variant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

/** @mixin Variant */
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
            'published' => $this->published,
            'main_photo' => $this->main_photo,
            'images' => $this->when(
                Str::contains($request->url(), '/api/products/'),
                fn () => json_decode($this->images, true) ?? []
            ),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'favorite' => $this->whenLoaded('isFavorite', fn () => $this->isFavorite),
            'lowest_price' => $this->whenLoaded('lowestPrice', fn () => $this->lowestPrice->price) ?? $this->price,
            'product' => $this->whenLoaded('product', fn () => new ProductResource($this->product)),
            'size' => $this->whenLoaded('size', fn () => new SizeResource($this->size)),
            'options' => $this->whenLoaded('options', fn () => $this->when($request->is('api/products/*'), function () {
                return OptionResource::collection($this->options);
            })),
            'color' => $this->whenLoaded('color', fn () => new ColorResource($this->color)),
        ];
    }
}
