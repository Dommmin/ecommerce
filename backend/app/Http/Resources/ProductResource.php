<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Products\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

/** @mixin Product
 *
 * @property float $ratings_avg_value
 * */
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
            'short_description' => Str::limit($this->description),
            'description' => $this->description,
            'price' => $this->price,
            'voted' => (bool) $this->vote,
            'rating' => isset($this->ratings_avg_value) ? number_format((int) $this->ratings_avg_value, 2) : false,
            'ratings_count' => $this->ratings_count ?? false,
            'brand' => $this->whenLoaded('brand', fn () => new BrandResource($this->brand)),
            'category' => $this->whenLoaded('category', fn () => new CategoryResource($this->category)),
            'variants' => $this->whenLoaded('variants', fn () => VariantResource::collection($this->variants)),
        ];
    }
}
