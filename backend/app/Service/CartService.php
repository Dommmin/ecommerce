<?php

namespace App\Service;

use App\Models\Cart;
use Illuminate\Http\Request;

class CartService
{
    public static function createOrUpdate($validatedData): void
    {
        $cart = Cart::firstOrNew([
            'user_id' => auth()->id(),
            'variant_id' => $validatedData['variant_id'],
            'option_id' => $validatedData['option_id'],
        ]);

        if ($cart->exists) {
            $cart->quantity += 1;
        } else {
            $cart->quantity = 1;
        }

        $cart->save();
    }

    public static function updateQuantity(Cart $cart, Request $request): void
    {
        if ($request->get('event') === 'add') {
            $cart->quantity += 1;
        }

        if ($request->get('event') === 'subtract') {
            $cart->quantity -= 1;
        }

        $cart->save();
    }
}
