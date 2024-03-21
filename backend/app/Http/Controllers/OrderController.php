<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Jobs\SendOrderCompleteEmail;
use App\Models\Orders\Order;
use App\Service\OrderService;

class OrderController extends Controller
{
    public function index()
    {
        return Order::where('user_id', auth()->id())->with('items')->get();
    }

    public function show(Order $order)
    {
        return $order->load(['items.option.variant.product', 'items.option.size']);
    }

    public function store()
    {
        $order = OrderService::processOrder();

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
