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
        $cartQuery = Cart::query()->whereUserId(auth()->id());
        $order = new Order();

        self::createOrder($cartQuery, $order);

        return $order;
    }

    /**
     * @param $cartQuery
     * @param  Order  $order
     *
     * @return void
     */
    private static function createOrder($cartQuery, Order $order): void
    {
        DB::transaction(function () use ($cartQuery, $order): void {
            $order->user_id = auth()->id();
            $order->shipment_id = Shipment::pluck('id')->random();
            $order->save();

            collect($cartQuery->with('variant', 'option')->get())->each(function ($cartItem) use ($order): void {
                Item::create([
                    'order_id' => $order->id,
                    'option_id' => $cartItem->option_id,
                    'price' => $cartItem->variant->price,
                    'quantity' => $cartItem->quantity,
                ]);

                $cartItem->option->update(['quantity' => $cartItem->option->quantity - $cartItem->quantity]);
            });

            $cartQuery->delete();
        });
    }
}
