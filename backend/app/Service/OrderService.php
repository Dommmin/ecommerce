<?php

namespace App\Service;

use App\Models\Cart;
use App\Models\Orders\Item;
use App\Models\Orders\Order;
use App\Models\Shipment;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public static function processOrder(): Order
    {
        $cartItems = Cart::where('user_id', auth()->id());
        $order = new Order();

        self::createOrder($cartItems, $order);

        return $order;
    }

    /**
     * @param  Cart  $cartItems
     * @param  Order  $order
     *
     * @return void
     */
    private static function createOrder(Cart $cartItems, Order $order): void
    {
        DB::transaction(function () use ($cartItems, $order): void {
            $order->user_id = auth()->id();
            $order->shipment_id = Shipment::pluck('id')->random();
            $order->save();

            collect($cartItems->with('variant', 'option')->get())->each(function ($cartItem) use ($order): void {
                Item::create([
                    'order_id' => $order->id,
                    'option_id' => $cartItem->option_id,
                    'price' => $cartItem->variant->price,
                    'quantity' => $cartItem->quantity,
                ]);

                $cartItem->option->update(['quantity' => $cartItem->option->quantity - $cartItem->quantity]);
            });

            $cartItems->delete();
        });
    }
}
