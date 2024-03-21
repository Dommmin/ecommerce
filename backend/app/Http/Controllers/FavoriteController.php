<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\FavoriteStoreRequest;
use App\Models\Favorite;

class FavoriteController extends Controller
{
    public function index()
    {
        return Favorite::where('user_id', auth()->id())->with('variant.product')->get();
    }

    public function store(FavoriteStoreRequest $request)
    {
        Favorite::create($request->validated() + ['user_id' => auth()->id()]);

        return response()->noContent();
    }

    public function destroy(Favorite $favorite)
    {
        $favorite->delete();

        return response()->noContent();
    }

    public function favCount()
    {
        return Favorite::where('user_id', auth()->id())->with('variant.product')->count();
    }
}
