<?php

namespace App\Http\Controllers;

use App\Enums\ShipmentEnum;
use App\Jobs\SendOrderCompleteEmail;
use App\Models\Cart;
use App\Models\Orders\Item;
use App\Models\Orders\Order;
use App\Models\Products\Variant;
use App\Models\Shipment;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        return Order::where('user_id', auth()->id())->with('items')->get();
    }

    public function show($uuid)
    {
        return Order::where('uuid', $uuid)->with(['items.option.variant.product', 'items.option.size'])->first();
    }

    public function store()
    {
        $cartItems = Cart::where('user_id', auth()->id());
        $order = new Order();

        \DB::transaction(function () use ($cartItems, $order) {
            $order->user_id = auth()->id();
            $order->shipment_id = Shipment::pluck('id')->random();
            $order->save();

            collect($cartItems->get())->each(function ($cartItem) use ($order) {
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

        $order->load([
            'user',
            'items.option.variant.product',
            'items.option.variant.color',
            'items.option.size',
        ]);

        dispatch(new SendOrderCompleteEmail(auth()->user(), $order));

        return response('Order created successfully', 201);
    }
}
