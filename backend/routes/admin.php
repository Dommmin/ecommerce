<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ColorController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\SizeController;
use App\Http\Controllers\Admin\StatusController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VariantController;

Route::prefix('admin')->group(function (): void {
    Route::middleware('admin')->group(function (): void {
        Route::get('statuses', [StatusController::class, 'index']);
        Route::post('variants/{variant}/publish', [VariantController::class, 'publish']);
        Route::post('variants/{variant}/unpublish', [VariantController::class, 'unpublish']);
        Route::put('orders/{order}/update-status', [OrderController::class, 'updateStatus']);
        Route::post('products/import', [ProductController::class, 'import'])->name('products.import');
        Route::apiResource('colors', ColorController::class);
        Route::apiResource('sizes', SizeController::class);
        Route::apiResource('products', ProductController::class)->names('admin.products.');
        Route::apiResource('categories', CategoryController::class)->except('index');
        Route::apiResource('brands', BrandController::class)->except('index');
        Route::apiResource('variants', VariantController::class);
        Route::apiResource('users', UserController::class);
        Route::apiResource('orders', OrderController::class)->names('admin.orders.');
    });

    Route::apiResource('categories', CategoryController::class)->only('index');
    Route::apiResource('brands', BrandController::class)->only('index');
});
