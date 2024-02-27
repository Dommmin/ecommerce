<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\VariantResource;
use App\Models\Products\Variant;
use Illuminate\Http\Request;

final class ProductController extends Controller
{
    public function index(Request $request)
    {
        $sortBy = $request->get('sortBy');

        $sortMappings = [
            'price_asc' => ['column' => 'price', 'order' => 'asc'],
            'price_desc' => ['column' => 'price', 'order' => 'desc'],
        ];

        $defaultSort = ['column' => 'id', 'order' => 'desc'];

        $sort = $sortMappings[$sortBy]['column'] ?? $defaultSort['column'];
        $order = $sortMappings[$sortBy]['order'] ?? $defaultSort['order'];

        $variants = Variant::getProducts(sort: $sort, order: $order);

        return VariantResource::collection($variants);
    }

    public function featureProducts()
    {
        $variants = Variant::getFeatureProducts();

        return VariantResource::collection($variants);
    }

    public function show($variantId)
    {
        $variant = Variant::query()
            ->where('id', $variantId)
            ->with([
                'product' => function ($query): void {
                    $query->withAvg('ratings', 'value')
                        ->with(['variants' => function ($query): void {
                            $query->where('published', true)
                                ->with('color');
                        }]);
                },
                'options.size',
                'favorite',
                'lowestPrice'
            ])
            ->published()
            ->first();

        if ( ! $variant) {
            return response()->json([
                'message' => 'Product not found'
            ], 404);
        }

        return new VariantResource($variant);
    }
}
