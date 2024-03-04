<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CartStoreRequest;
use App\Models\Cart;
use App\Models\Products\Option;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

final class CartController extends Controller
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

        $cart = Cart::firstWhere([
            'user_id' => auth()->id(),
            'variant_id' => $validatedData['variant_id'],
            'option_id' => $validatedData['option_id']
        ]);

        if ($cart) {
            $option = Option::where('id', $validatedData['option_id'])->first();

            if ($cart->quantity < $option->quantity) {
                $cart->update(['quantity' => $cart->quantity + 1]);
            } else {
                throw ValidationException::withMessages([
                    'event' => 'Quantity must be less than or equal to ' . $option->quantity,
                ]);
            }
        } else {
            Cart::create($validatedData + ['user_id' => auth()->id()]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Product added to cart'
        ]);
    }

    public function updateQuantity(Cart $cart)
    {
        if ($cart->user_id !== auth()->id()) {
            abort(403);
        }

        if ('add' === request('event')) {
            if ($cart->quantity < $cart->option->quantity) {
                return $cart->update(['quantity' => $cart->quantity + 1]);
            }
            throw ValidationException::withMessages([
                'event' => 'Quantity must be less than or equal to ' . $cart->option->quantity,
            ]);

        }

        if ('subtract' === request('event')) {
            if ($cart->quantity <= 1) {
                throw ValidationException::withMessages([
                    'event' => 'Quantity must be greater than 1',
                ]);
            }

            return $cart->update(['quantity' => $cart->quantity - 1]);
        }

        return response()->json(['success' => false]);
    }

    public function destroy(Cart $cart)
    {
        if ($cart->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ]);
        }

        $cart->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product removed from cart'
        ]);
    }

    public function items()
    {
        return Cart::where('user_id', auth()->id())->sum('quantity');
    }
}
