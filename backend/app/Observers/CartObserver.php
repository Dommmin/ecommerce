<?php

namespace App\Observers;

use App\Models\Cart;
use App\Models\Products\Option;
use Illuminate\Validation\ValidationException;

class CartObserver
{
    public function saving(Cart $cart): void
    {
        $option = Option::where('id', $cart->option_id)->first();

        if ($cart->quantity > $option->quantity) {
            throw ValidationException::withMessages([
                'event' => 'Quantity must be less than or equal to '.$option->quantity,
            ]);
        }

        if ($cart->quantity < 1) {
            throw ValidationException::withMessages([
                'event' => 'Quantity must be greater than or equal to 1',
            ]);
        }
    }
}
