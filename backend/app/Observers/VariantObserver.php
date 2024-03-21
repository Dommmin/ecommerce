<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Favorite;
use App\Models\Price;
use App\Models\Products\Variant;
use App\Notifications\PriceChanged;

class VariantObserver
{
    public function updated(Variant $variant): void
    {
        if ($variant->isDirty('price')) {
            Price::create(['price' => $variant->price, 'variant_id' => $variant->id]);

            if ($variant->getOriginal('price') > $variant->price) {
                $favorites = Favorite::where('variant_id', $variant->id)->with('user')->get();

                $variant->load('product.brand', 'color');

                foreach ($favorites as $favorite) {
                    $favorite->user->notify(new PriceChanged($variant));
                }
            }
        }
    }
}
