<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CartStoreRequest;
use App\Models\Cart;
use App\Service\CartService;
use Exception;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        return Cart::query()
            ->where('user_id', auth()->id())
            ->with(['option.size', 'variant.product'])
            ->get();
    }

    /**
     * @throws Exception
     */
    public function store(CartStoreRequest $request)
    {
        $validatedData = $request->validated();

        try {
            CartService::createOrUpdate($validatedData);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json(['success' => true]);
    }

    public function update(Cart $cart, Request $request)
    {
        $this->authorize('update', $cart);

        try {
            CartService::updateQuantity($cart, $request);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json(['success' => true, 'message' => 'Quantity updated']);
    }

    public function destroy(Cart $cart)
    {
        $this->authorize('destroy', $cart);

        $cart->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product removed from cart',
        ]);
    }

    public function items()
    {
        return Cart::where('user_id', auth()->id())->sum('quantity');
    }
}
