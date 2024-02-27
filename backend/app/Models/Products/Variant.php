<?php

declare(strict_types=1);

namespace App\Models\Products;

use App\Models\Color;
use App\Models\Favorite;
use App\Models\Price;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

final class Variant extends Model
{
    use HasFactory;
    use HasSlug;

    protected $guarded = [];
    protected $appends = ['main_photo'];

    public static function getFeatureProducts()
    {
        //        $page = Paginator::resolveCurrentPage() ?? 1;

        //        return Cache::tags('feature-products')->remember("feature-products.page.{$page}", 60*60*24, function () {
        return Variant::query()
            ->published()
            ->with([
                'product' => function ($query): void {
                    $query->withAvg('ratings', 'value');
                },
                'favorite',
                'product.vote',
                'lowestPrice',
            ])
            ->whereHas('options')
            ->groupBy('variants.product_id', 'variants.id', 'variants.color_id', 'variants.url', 'variants.price', 'variants.images', 'variants.published', 'variants.thumbnail', 'variants.created_at', 'variants.updated_at')
            ->orderBy('price')
            ->simplePaginate(20);
        //        });
    }

    public static function getProducts($sort = 'id', $order = 'desc')
    {
        $request = request();

        return Variant::query()
            ->published()
            ->with([
                'product' => function ($query): void {
                    $query->withAvg('ratings', 'value')->withCount('ratings');
                },
                'favorite',
                'product.vote',
                'product.brand',
                'lowestPrice'
            ])
            ->withWhereHas('options')
            ->when($request->filled('category'), function ($query) use ($request) {
                // Use whereHas to filter based on the associated product's category_id
                return $query->whereHas('product', function ($productQuery) use ($request): void {
                    $productQuery->where('category_id', $request->get('category'));
                });
            })
            ->when($request->filled('brand'), function ($query) use ($request) {
                // Use whereHas to filter based on the associated product's brand_id
                return $query->whereHas('product', function ($productQuery) use ($request): void {
                    $productQuery->where('brand_id', $request->get('brand'));
                });
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                // Use whereHas to filter based on the associated product's search
                return $query->whereHas('product', function ($productQuery) use ($request): void {
                    $productQuery->where('name', 'like', '%' . $request->get('search') . '%');
                });
            })
            ->orderBy($sort, $order)
            ->groupBy('variants.product_id', 'variants.id', 'variants.color_id', 'variants.url', 'variants.price', 'variants.images', 'variants.published', 'variants.thumbnail', 'variants.created_at', 'variants.updated_at')
            ->simplePaginate(12);
    }

    public function options(): HasMany
    {
        return $this->hasMany(Option::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function color(): BelongsTo
    {
        return $this->belongsTo(Color::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function getPriceAttribute($value): string
    {
        return number_format($value, 2);
    }

    public function scopePublished($query)
    {
        return $query->where('published', true);
    }

    public function favorite(): HasOne
    {
        return $this->hasOne(Favorite::class)->where('user_id', auth()->id());
    }

    public function lowestPrice(): HasOne
    {
        return $this->hasOne(Price::class)->orderBy('price');
    }

    public function getMainPhotoAttribute()
    {
        $images = json_decode($this->images, true);

        return $images[0];
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(function ($model) {
                $model->load(['product.brand', 'color']);

                return $model->product->brand->name . ' ' . $model->product->name . ' ' . $model->color->name;
            })
            ->saveSlugsTo('url');
    }

    //    public function getRouteKeyName(): string
    //    {
    //        return 'url';
    //    }
}
