<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Products\Option;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Option */
final class OptionResource extends JsonResource
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
            'quantity' => $this->quantity,
            'size' => $this->whenLoaded('size', fn() => new SizeResource($this->size)),
        ];
    }
}
