<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ProductStoreRequest;
use App\Models\Products\Product;
use App\Service\ProductImportService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController
{
    public function index()
    {
        return Product::query()
            ->with(['variants.color', 'brand'])
            ->latest()
            ->simplePaginate(10);
    }

    public function show(Product $product)
    {
        return $product->load('audits');
    }

    public function store(ProductStoreRequest $request)
    {
        return Product::create($request->validated());
    }

    public function import(Request $request)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');

            $fileName = $file->getClientOriginalName();
            $file->storeAs('public', $fileName);

            $path = Storage::path('public/'.$fileName);

            try {
                ProductImportService::processImport($path);
            } catch (Exception $e) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }

            return response()->json([
                'success' => true,
                'message' => 'Products imported successfully',
            ], 201);
        }

        return response()->json(['success' => false, 'message' => 'File not found'], 404);
    }
}
