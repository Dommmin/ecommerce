<?php

declare(strict_types=1);

use App\Http\Controllers\CartController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RatingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['auth:sanctum'])->get('/user', fn (Request $request) => $request->user());

Route::get('cart-items', [CartController::class, 'items']);
Route::get('favorites-count', [FavoriteController::class, 'favCount']);
Route::apiResource('cart', CartController::class);
Route::get('/feature-products', [ProductController::class, 'featureProducts']);
Route::apiResource('products', ProductController::class)->names([
    'show' => 'products.show',
]);

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('favorites', FavoriteController::class);
    Route::apiResource('orders', OrderController::class);
    Route::apiResource('ratings', RatingController::class);
});

require __DIR__.'/admin.php';
